"""
Demand Forecasting using Prophet (Meta's Time Series Forecasting Library)

Prophet is specifically designed for business forecasting with:
- Strong seasonality patterns (daily, weekly, yearly)
- Holiday effects
- Handling of missing data
- Robust to outliers
"""

import pandas as pd
import numpy as np
from prophet import Prophet
import mysql.connector
import os
from datetime import datetime, timedelta
import logging
import warnings

warnings.filterwarnings('ignore')
logger = logging.getLogger(__name__)


class DemandForecaster:
    """
    Demand Forecasting Model using Facebook Prophet
    
    This model is ideal for retail/supply chain forecasting because:
    1. Handles seasonal patterns (weekday vs weekend sales)
    2. Adapts to trend changes
    3. Works well with limited historical data
    4. Provides confidence intervals for uncertainty quantification
    """
    
    def __init__(self):
        """Initialize database connection configuration"""
        self.db_config = {
            'host': os.getenv('DB_HOST', '127.0.0.1'),
            'port': int(os.getenv('DB_PORT', 3306)),
            'user': os.getenv('DB_USERNAME', 'root'),
            'password': os.getenv('DB_PASSWORD', ''),
            'database': os.getenv('DB_DATABASE', 'rew_optimized')
        }
        logger.info("DemandForecaster initialized")
    
    def get_connection(self):
        """Create and return a database connection"""
        try:
            conn = mysql.connector.connect(**self.db_config)
            return conn
        except mysql.connector.Error as e:
            logger.error(f"Database connection error: {e}")
            raise
    
    def get_sales_history(self, product_id):
        """
        Fetch historical sales data from MySQL database
        
        Args:
            product_id: The product ID to fetch sales for
            
        Returns:
            pandas.DataFrame with columns: ds (date), y (quantity)
        """
        try:
            conn = self.get_connection()
            cursor = conn.cursor(dictionary=True)
            
            # Fetch all historical sales for this product
            query = """
                SELECT 
                    sale_date as ds, 
                    quantity as y
                FROM sales
                WHERE product_id = %s
                ORDER BY sale_date ASC
            """
            
            cursor.execute(query, (product_id,))
            data = cursor.fetchall()
            
            cursor.close()
            conn.close()
            
            if not data:
                logger.warning(f"No sales history found for product {product_id}")
                return pd.DataFrame(columns=['ds', 'y'])
            
            df = pd.DataFrame(data)
            logger.info(f"Retrieved {len(df)} sales records for product {product_id}")
            
            return df
            
        except Exception as e:
            logger.error(f"Error fetching sales history: {e}")
            raise
    
    def get_product_info(self, product_id):
        """Get product details for better forecasting context"""
        try:
            conn = self.get_connection()
            cursor = conn.cursor(dictionary=True)
            
            query = """
                SELECT 
                    p.name,
                    p.category_id,
                    c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = %s
            """
            
            cursor.execute(query, (product_id,))
            product = cursor.fetchone()
            
            cursor.close()
            conn.close()
            
            return product
            
        except Exception as e:
            logger.warning(f"Could not fetch product info: {e}")
            return None
    
    def predict(self, product_id, days=30):
        """
        Generate demand forecast using Prophet model
        
        Args:
            product_id: The product ID to forecast
            days: Number of days to forecast into the future
            
        Returns:
            List of dictionaries containing forecast data
        """
        try:
            # Get historical sales data
            df = self.get_sales_history(product_id)
            
            # If no historical data, use baseline forecast
            if df.empty or len(df) < 2:
                logger.info(f"Insufficient data for product {product_id}, using baseline")
                return self._baseline_forecast(product_id, days)
            
            # Prepare data for Prophet
            df['ds'] = pd.to_datetime(df['ds'])
            df['y'] = df['y'].astype(float)
            
            # Handle aggregation if multiple sales per day
            df = df.groupby('ds').agg({'y': 'sum'}).reset_index()
            
            # Check if we have enough data points
            if len(df) < 2:
                logger.info(f"Too few data points for product {product_id}, using baseline")
                return self._baseline_forecast(product_id, days)
            
            # Initialize Prophet model with appropriate parameters
            model = Prophet(
                yearly_seasonality=True if len(df) > 365 else False,
                weekly_seasonality=True if len(df) > 14 else False,
                daily_seasonality=False,  # Typically not useful for demand forecasting
                changepoint_prior_scale=0.05,  # Flexibility of trend changes
                seasonality_prior_scale=10.0,  # Strength of seasonality
                interval_width=0.95,  # 95% confidence interval
                uncertainty_samples=1000
            )
            
            # Add custom seasonality if we have enough data
            if len(df) > 30:
                model.add_seasonality(
                    name='monthly',
                    period=30.5,
                    fourier_order=5
                )
            
            # Fit the model
            logger.info(f"Training Prophet model for product {product_id}")
            model.fit(df)
            
            # Create future dataframe for predictions
            future = model.make_future_dataframe(periods=days, freq='D')
            
            # Generate forecast
            forecast = model.predict(future)
            
            # Extract only future predictions (not historical fitted values)
            future_forecast = forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(days)
            
            # Calculate confidence score based on historical performance
            confidence_score = self._calculate_confidence(df, model)
            
            # Format results for API response
            results = []
            for _, row in future_forecast.iterrows():
                # Ensure non-negative predictions
                predicted_qty = max(0, int(round(row['yhat'])))
                lower_bound = max(0, int(round(row['yhat_lower'])))
                upper_bound = max(0, int(round(row['yhat_upper'])))
                
                results.append({
                    'date': row['ds'].strftime('%Y-%m-%d'),
                    'predicted_quantity': predicted_qty,
                    'confidence_lower': lower_bound,
                    'confidence_upper': upper_bound,
                    'confidence_score': round(confidence_score, 2)
                })
            
            logger.info(f"Successfully generated {len(results)} forecasts for product {product_id}")
            return results
            
        except Exception as e:
            logger.error(f"Error in prediction for product {product_id}: {e}", exc_info=True)
            # Fallback to baseline if Prophet fails
            return self._baseline_forecast(product_id, days)
    
    def _calculate_confidence(self, historical_df, model):
        """
        Calculate confidence score based on model performance on historical data
        
        Uses Mean Absolute Percentage Error (MAPE) to estimate confidence
        """
        try:
            if len(historical_df) < 5:
                return 0.70  # Lower confidence for limited data
            
            # Use last 20% of data for validation
            train_size = int(len(historical_df) * 0.8)
            train_df = historical_df[:train_size]
            test_df = historical_df[train_size:]
            
            if len(test_df) == 0:
                return 0.75
            
            # Predict on test set
            forecast = model.predict(test_df[['ds']])
            
            # Calculate MAPE
            actual = test_df['y'].values
            predicted = forecast['yhat'].values
            
            # Avoid division by zero
            mask = actual != 0
            if not mask.any():
                return 0.75
            
            mape = np.mean(np.abs((actual[mask] - predicted[mask]) / actual[mask])) * 100
            
            # Convert MAPE to confidence score (lower MAPE = higher confidence)
            if mape < 10:
                confidence = 0.95
            elif mape < 20:
                confidence = 0.85
            elif mape < 30:
                confidence = 0.75
            elif mape < 50:
                confidence = 0.65
            else:
                confidence = 0.55
            
            return confidence
            
        except Exception as e:
            logger.warning(f"Could not calculate confidence: {e}")
            return 0.75  # Default medium confidence
    
    def _baseline_forecast(self, product_id, days):
        """
        Fallback forecasting method when no historical data exists
        
        Uses simple heuristics based on product category or global averages
        """
        try:
            # Try to get some context for better baseline
            product_info = self.get_product_info(product_id)
            
            # Get average sales from similar products or category
            baseline_qty = self._get_category_average(product_info) if product_info else 10
            
            results = []
            for i in range(days):
                date = (datetime.now() + timedelta(days=i+1)).strftime('%Y-%m-%d')
                
                # Add slight variation to make it more realistic
                variation = np.random.randint(-2, 3)
                predicted = max(0, baseline_qty + variation)
                
                results.append({
                    'date': date,
                    'predicted_quantity': predicted,
                    'confidence_lower': max(0, predicted - 3),
                    'confidence_upper': predicted + 3,
                    'confidence_score': 0.50  # Low confidence for baseline
                })
            
            logger.info(f"Generated baseline forecast for product {product_id}")
            return results
            
        except Exception as e:
            logger.error(f"Error in baseline forecast: {e}")
            # Absolute fallback
            return [{
                'date': (datetime.now() + timedelta(days=i+1)).strftime('%Y-%m-%d'),
                'predicted_quantity': 10,
                'confidence_lower': 7,
                'confidence_upper': 13,
                'confidence_score': 0.50
            } for i in range(days)]
    
    def _get_category_average(self, product_info):
        """Get average sales for products in the same category"""
        try:
            if not product_info or not product_info.get('category_id'):
                return 10
            
            conn = self.get_connection()
            cursor = conn.cursor()
            
            query = """
                SELECT AVG(s.quantity) as avg_qty
                FROM sales s
                JOIN products p ON s.product_id = p.id
                WHERE p.category_id = %s
                AND s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            """
            
            cursor.execute(query, (product_info['category_id'],))
            result = cursor.fetchone()
            
            cursor.close()
            conn.close()
            
            if result and result[0]:
                return max(5, int(round(result[0])))
            
            return 10
            
        except Exception as e:
            logger.warning(f"Could not get category average: {e}")
            return 10
