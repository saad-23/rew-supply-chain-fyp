"""
Anomaly Detection in Sales Data using Statistical Methods

This module detects unusual patterns in sales data that might indicate:
- Sudden spikes (viral products, promotions)
- Sudden drops (stockouts, quality issues)
- Data quality issues
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import IsolationForest
import mysql.connector
import os
from datetime import datetime, timedelta
import logging

logger = logging.getLogger(__name__)


class AnomalyDetector:
    """
    Detects anomalies in sales patterns using multiple methods:
    1. Isolation Forest (ML-based)
    2. Statistical Z-score method
    3. Moving average deviation
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
        logger.info("AnomalyDetector initialized")
    
    def get_connection(self):
        """Create and return a database connection"""
        try:
            conn = mysql.connector.connect(**self.db_config)
            return conn
        except mysql.connector.Error as e:
            logger.error(f"Database connection error: {e}")
            raise
    
    def get_sales_data(self, product_id, lookback_days=30):
        """
        Fetch recent sales data for anomaly detection
        
        Args:
            product_id: The product ID to analyze
            lookback_days: Number of days to look back
            
        Returns:
            pandas.DataFrame with sales data
        """
        try:
            conn = self.get_connection()
            cursor = conn.cursor(dictionary=True)
            
            cutoff_date = (datetime.now() - timedelta(days=lookback_days)).strftime('%Y-%m-%d')
            
            query = """
                SELECT 
                    sale_date,
                    quantity,
                    total_amount as revenue
                FROM sales
                WHERE product_id = %s
                AND sale_date >= %s
                ORDER BY sale_date ASC
            """
            
            cursor.execute(query, (product_id, cutoff_date))
            data = cursor.fetchall()
            
            cursor.close()
            conn.close()
            
            if not data:
                logger.warning(f"No sales data found for product {product_id}")
                return pd.DataFrame()
            
            df = pd.DataFrame(data)
            df['sale_date'] = pd.to_datetime(df['sale_date'])
            
            logger.info(f"Retrieved {len(df)} sales records for anomaly detection")
            return df
            
        except Exception as e:
            logger.error(f"Error fetching sales data: {e}")
            raise
    
    def detect(self, product_id, lookback_days=30):
        """
        Detect anomalies in sales data
        
        Args:
            product_id: The product ID to analyze
            lookback_days: Number of days to look back
            
        Returns:
            List of detected anomalies
        """
        try:
            # Get sales data
            df = self.get_sales_data(product_id, lookback_days)
            
            if df.empty or len(df) < 5:
                logger.info(f"Insufficient data for anomaly detection on product {product_id}")
                return []
            
            # Aggregate by day
            daily_sales = df.groupby('sale_date').agg({
                'quantity': 'sum',
                'revenue': 'sum'
            }).reset_index()
            
            anomalies = []
            
            # Method 1: Statistical Z-score
            z_score_anomalies = self._detect_zscore(daily_sales)
            anomalies.extend(z_score_anomalies)
            
            # Method 2: Isolation Forest (if enough data)
            if len(daily_sales) >= 10:
                iso_anomalies = self._detect_isolation_forest(daily_sales)
                anomalies.extend(iso_anomalies)
            
            # Method 3: Moving Average Deviation
            ma_anomalies = self._detect_moving_average(daily_sales)
            anomalies.extend(ma_anomalies)
            
            # Remove duplicates and sort by date
            unique_anomalies = self._deduplicate_anomalies(anomalies)
            
            logger.info(f"Detected {len(unique_anomalies)} anomalies for product {product_id}")
            return unique_anomalies
            
        except Exception as e:
            logger.error(f"Error detecting anomalies: {e}", exc_info=True)
            return []
    
    def _detect_zscore(self, daily_sales, threshold=2.5):
        """
        Detect anomalies using Z-score method
        Points with |z-score| > threshold are considered anomalies
        """
        anomalies = []
        
        try:
            qty_mean = daily_sales['quantity'].mean()
            qty_std = daily_sales['quantity'].std()
            
            if qty_std == 0:
                return anomalies
            
            daily_sales['z_score'] = (daily_sales['quantity'] - qty_mean) / qty_std
            
            for _, row in daily_sales.iterrows():
                if abs(row['z_score']) > threshold:
                    anomaly_type = 'spike' if row['z_score'] > 0 else 'drop'
                    severity = 'high' if abs(row['z_score']) > 3 else 'medium'
                    
                    anomalies.append({
                        'date': row['sale_date'].strftime('%Y-%m-%d'),
                        'quantity': int(row['quantity']),
                        'expected_quantity': int(qty_mean),
                        'deviation': round(float(row['z_score']), 2),
                        'type': anomaly_type,
                        'severity': severity,
                        'method': 'z-score',
                        'description': f"Sales {anomaly_type}: {row['quantity']} units ({abs(row['z_score']):.1f} std deviations from mean)"
                    })
            
        except Exception as e:
            logger.warning(f"Z-score detection failed: {e}")
        
        return anomalies
    
    def _detect_isolation_forest(self, daily_sales):
        """
        Detect anomalies using Isolation Forest algorithm
        """
        anomalies = []
        
        try:
            # Prepare features
            features = daily_sales[['quantity', 'revenue']].values
            
            # Create and fit Isolation Forest model
            clf = IsolationForest(
                contamination=0.1,  # Expect ~10% anomalies
                random_state=42,
                n_estimators=100
            )
            
            predictions = clf.fit_predict(features)
            scores = clf.score_samples(features)
            
            # Extract anomalies (prediction = -1)
            for idx, pred in enumerate(predictions):
                if pred == -1:
                    row = daily_sales.iloc[idx]
                    anomaly_score = abs(scores[idx])
                    
                    severity = 'high' if anomaly_score > 0.5 else 'medium'
                    
                    # Determine type based on quantity deviation
                    qty_mean = daily_sales['quantity'].mean()
                    anomaly_type = 'spike' if row['quantity'] > qty_mean else 'drop'
                    
                    anomalies.append({
                        'date': row['sale_date'].strftime('%Y-%m-%d'),
                        'quantity': int(row['quantity']),
                        'expected_quantity': int(qty_mean),
                        'deviation': round(anomaly_score, 2),
                        'type': anomaly_type,
                        'severity': severity,
                        'method': 'isolation-forest',
                        'description': f"Unusual sales pattern detected: {row['quantity']} units"
                    })
            
        except Exception as e:
            logger.warning(f"Isolation Forest detection failed: {e}")
        
        return anomalies
    
    def _detect_moving_average(self, daily_sales, window=7, threshold=2.0):
        """
        Detect anomalies using moving average deviation
        """
        anomalies = []
        
        try:
            if len(daily_sales) < window:
                return anomalies
            
            # Calculate moving average and standard deviation
            daily_sales['ma'] = daily_sales['quantity'].rolling(window=window).mean()
            daily_sales['ma_std'] = daily_sales['quantity'].rolling(window=window).std()
            
            for idx, row in daily_sales.iterrows():
                if pd.isna(row['ma']) or pd.isna(row['ma_std']) or row['ma_std'] == 0:
                    continue
                
                deviation = abs(row['quantity'] - row['ma']) / row['ma_std']
                
                if deviation > threshold:
                    anomaly_type = 'spike' if row['quantity'] > row['ma'] else 'drop'
                    severity = 'high' if deviation > 3 else 'medium'
                    
                    anomalies.append({
                        'date': row['sale_date'].strftime('%Y-%m-%d'),
                        'quantity': int(row['quantity']),
                        'expected_quantity': int(row['ma']),
                        'deviation': round(float(deviation), 2),
                        'type': anomaly_type,
                        'severity': severity,
                        'method': 'moving-average',
                        'description': f"Sales {anomaly_type}: {row['quantity']} units (expected ~{int(row['ma'])} based on {window}-day trend)"
                    })
            
        except Exception as e:
            logger.warning(f"Moving average detection failed: {e}")
        
        return anomalies
    
    def _deduplicate_anomalies(self, anomalies):
        """
        Remove duplicate anomalies detected by multiple methods
        Keep the one with highest severity
        """
        if not anomalies:
            return []
        
        # Group by date
        by_date = {}
        for anomaly in anomalies:
            date = anomaly['date']
            if date not in by_date:
                by_date[date] = []
            by_date[date].append(anomaly)
        
        # For each date, keep the anomaly with highest severity and confidence
        unique = []
        severity_order = {'high': 3, 'medium': 2, 'low': 1}
        
        for date, date_anomalies in by_date.items():
            # Sort by severity and deviation
            best = sorted(
                date_anomalies,
                key=lambda x: (severity_order.get(x['severity'], 0), x['deviation']),
                reverse=True
            )[0]
            unique.append(best)
        
        # Sort by date
        unique.sort(key=lambda x: x['date'], reverse=True)
        
        return unique
