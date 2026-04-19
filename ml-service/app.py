"""
Flask ML Service for Demand Forecasting
This microservice handles machine learning predictions for the Laravel application
"""

from flask import Flask, jsonify, request
from flask_cors import CORS
from models.demand_forecast import DemandForecaster
from models.anomaly_detection import AnomalyDetector
import os
import pandas as pd
from dotenv import load_dotenv
import logging

# Load environment variables
load_dotenv()

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel to communicate

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# Initialize ML models
forecaster = DemandForecaster()
anomaly_detector = AnomalyDetector()

@app.route('/', methods=['GET'])
def index():
    """Root endpoint"""
    return jsonify({
        'service': 'ML Forecasting Service',
        'version': '1.0.0',
        'status': 'running',
        'endpoints': {
            'health': '/api/health',
            'forecast': '/api/forecast [POST]',
            'batch_forecast': '/api/forecast/batch [POST]',
            'detect_anomalies': '/api/anomalies [POST]'
        }
    })

@app.route('/api/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'ml-forecasting',
        'timestamp': str(pd.Timestamp.now())
    })

@app.route('/api/forecast', methods=['POST'])
def generate_forecast():
    """
    Generate demand forecast for a single product
    
    Request JSON:
    {
        "product_id": 1,
        "days": 30
    }
    
    Response:
    {
        "success": true,
        "product_id": 1,
        "forecasts": [
            {
                "date": "2026-02-08",
                "predicted_quantity": 25,
                "confidence_lower": 20,
                "confidence_upper": 30,
                "confidence_score": 0.85
            },
            ...
        ]
    }
    """
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({
                'success': False,
                'error': 'No JSON data provided'
            }), 400
        
        product_id = data.get('product_id')
        days = data.get('days', 30)
        
        if not product_id:
            return jsonify({
                'success': False,
                'error': 'product_id is required'
            }), 400
        
        if not isinstance(days, int) or days < 1 or days > 365:
            return jsonify({
                'success': False,
                'error': 'days must be an integer between 1 and 365'
            }), 400
        
        logger.info(f"Generating forecast for product {product_id} for {days} days")
        
        # Generate forecasts using Prophet model with error handling
        try:
            forecasts = forecaster.predict(product_id, days)
            
            if not forecasts or len(forecasts) == 0:
                logger.warning(f"No forecasts generated for product {product_id}")
                return jsonify({
                    'success': False,
                    'error': 'Failed to generate forecasts. Check if product has sales history.'
                }), 500
            
            return jsonify({
                'success': True,
                'product_id': product_id,
                'days': days,
                'model_used': 'Prophet',
                'forecasts': forecasts
            })
            
        except Exception as forecast_error:
            logger.error(f"Forecasting error for product {product_id}: {str(forecast_error)}")
            return jsonify({
                'success': False,
                'error': f'Forecast generation failed: {str(forecast_error)}'
            }), 500
        
    except Exception as e:
        logger.error(f"Error generating forecast: {str(e)}", exc_info=True)
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/forecast/batch', methods=['POST'])
def batch_forecast():
    """
    Generate forecasts for multiple products at once
    
    Request JSON:
    {
        "product_ids": [1, 2, 3],
        "days": 30
    }
    """
    try:
        data = request.get_json()
        product_ids = data.get('product_ids', [])
        days = data.get('days', 30)
        
        if not product_ids:
            return jsonify({
                'success': False,
                'error': 'product_ids array is required'
            }), 400
        
        results = []
        for product_id in product_ids:
            try:
                forecasts = forecaster.predict(product_id, days)
                results.append({
                    'product_id': product_id,
                    'success': True,
                    'forecasts': forecasts
                })
            except Exception as e:
                logger.error(f"Error forecasting product {product_id}: {str(e)}")
                results.append({
                    'product_id': product_id,
                    'success': False,
                    'error': str(e)
                })
        
        return jsonify({
            'success': True,
            'results': results
        })
        
    except Exception as e:
        logger.error(f"Error in batch forecast: {str(e)}", exc_info=True)
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/anomalies', methods=['POST'])
def detect_anomalies():
    """
    Detect anomalies in sales data for a product
    
    Request JSON:
    {
        "product_id": 1,
        "days": 30  // Look back period
    }
    """
    try:
        data = request.get_json()
        product_id = data.get('product_id')
        days = data.get('days', 30)
        
        if not product_id:
            return jsonify({
                'success': False,
                'error': 'product_id is required'
            }), 400
        
        logger.info(f"Detecting anomalies for product {product_id}")
        
        anomalies = anomaly_detector.detect(product_id, days)
        
        return jsonify({
            'success': True,
            'product_id': product_id,
            'anomalies': anomalies
        })
        
    except Exception as e:
        logger.error(f"Error detecting anomalies: {str(e)}", exc_info=True)
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'success': False,
        'error': 'Endpoint not found'
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'success': False,
        'error': 'Internal server error'
    }), 500

if __name__ == '__main__':
    # Get configuration from environment
    port = int(os.getenv('PORT', 5000))
    debug = os.getenv('DEBUG', 'False').lower() == 'true'
    host = os.getenv('HOST', '0.0.0.0')
    
    # Print startup banner
    print("=" * 60)
    print("  REW OPTIMIZED - ML FORECASTING SERVICE")
    print("=" * 60)
    print(f"  Host: {host}")
    print(f"  Port: {port}")
    print(f"  Debug: {debug}")
    print(f"  Model: Facebook Prophet (Time Series Forecasting)")
    print("=" * 60)
    print("")
    print("  API Endpoints:")
    print(f"    Health Check:  http://localhost:{port}/api/health")
    print(f"    Forecast:      http://localhost:{port}/api/forecast")
    print(f"    Batch Forecast: http://localhost:{port}/api/forecast/batch")
    print(f"    Anomaly Detection: http://localhost:{port}/api/anomalies")
    print("")
    print("=" * 60)
    print("  Press Ctrl+C to stop the service")
    print("=" * 60)
    print("")
    
    logger.info(f"Starting ML Forecasting Service on {host}:{port}")
    logger.info(f"Debug mode: {debug}")
    
    try:
        app.run(host=host, port=port, debug=debug, threaded=True)
    except KeyboardInterrupt:
        logger.info("Service stopped by user")
        print("\n\nService stopped. Goodbye!")
    except Exception as e:
        logger.error(f"Service crashed: {e}")
        print(f"\n\n❌ Service crashed: {e}")
        raise
