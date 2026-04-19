"""
Test script to verify ML Service setup and functionality
Run this to ensure everything is configured correctly
"""

import requests
import json
import time
from datetime import datetime

ML_SERVICE_URL = "http://localhost:5000"

def print_header(text):
    """Print formatted header"""
    print("\n" + "="*60)
    print(f"  {text}")
    print("="*60)

def test_health_check():
    """Test if the ML service is running"""
    print_header("Test 1: Health Check")
    
    try:
        response = requests.get(f"{ML_SERVICE_URL}/api/health", timeout=5)
        
        if response.status_code == 200:
            data = response.json()
            print("✓ ML Service is running!")
            print(f"  Status: {data.get('status')}")
            print(f"  Service: {data.get('service')}")
            return True
        else:
            print(f"✗ Service returned status code: {response.status_code}")
            return False
            
    except requests.exceptions.ConnectionError:
        print("✗ Could not connect to ML Service")
        print(f"  Make sure the service is running on {ML_SERVICE_URL}")
        print("  Run: python app.py")
        return False
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return False

def test_forecast_endpoint():
    """Test forecast generation"""
    print_header("Test 2: Forecast Generation")
    
    try:
        payload = {
            "product_id": 1,
            "days": 7
        }
        
        print(f"Request: POST /api/forecast")
        print(f"Payload: {json.dumps(payload, indent=2)}")
        print("\nGenerating forecast... (this may take a few seconds)")
        
        start_time = time.time()
        response = requests.post(
            f"{ML_SERVICE_URL}/api/forecast",
            json=payload,
            timeout=60
        )
        elapsed = time.time() - start_time
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                print(f"✓ Forecast generated successfully in {elapsed:.2f}s")
                print(f"  Product ID: {data.get('product_id')}")
                print(f"  Model: {data.get('model_used')}")
                print(f"  Forecasts returned: {len(data.get('forecasts', []))}")
                
                # Show first forecast
                if data.get('forecasts'):
                    forecast = data['forecasts'][0]
                    print(f"\n  Sample Forecast (Day 1):")
                    print(f"    Date: {forecast.get('date')}")
                    print(f"    Predicted Quantity: {forecast.get('predicted_quantity')}")
                    print(f"    Confidence: {forecast.get('confidence_score')}")
                    print(f"    Range: {forecast.get('confidence_lower')} - {forecast.get('confidence_upper')}")
                
                return True
            else:
                print(f"✗ Forecast failed: {data.get('error')}")
                return False
        else:
            print(f"✗ Request failed with status code: {response.status_code}")
            print(f"  Response: {response.text}")
            return False
            
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return False

def test_batch_forecast():
    """Test batch forecasting"""
    print_header("Test 3: Batch Forecast")
    
    try:
        payload = {
            "product_ids": [1, 2, 3],
            "days": 7
        }
        
        print(f"Request: POST /api/forecast/batch")
        print(f"Payload: {json.dumps(payload, indent=2)}")
        print("\nGenerating batch forecasts...")
        
        response = requests.post(
            f"{ML_SERVICE_URL}/api/forecast/batch",
            json=payload,
            timeout=120
        )
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                results = data.get('results', [])
                successful = sum(1 for r in results if r.get('success'))
                
                print(f"✓ Batch forecast completed")
                print(f"  Products processed: {len(results)}")
                print(f"  Successful: {successful}")
                print(f"  Failed: {len(results) - successful}")
                
                return True
            else:
                print(f"✗ Batch forecast failed")
                return False
        else:
            print(f"✗ Request failed with status code: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return False

def test_anomaly_detection():
    """Test anomaly detection"""
    print_header("Test 4: Anomaly Detection")
    
    try:
        payload = {
            "product_id": 1,
            "days": 30
        }
        
        print(f"Request: POST /api/anomalies")
        print(f"Payload: {json.dumps(payload, indent=2)}")
        print("\nDetecting anomalies...")
        
        response = requests.post(
            f"{ML_SERVICE_URL}/api/anomalies",
            json=payload,
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                anomalies = data.get('anomalies', [])
                
                print(f"✓ Anomaly detection completed")
                print(f"  Product ID: {data.get('product_id')}")
                print(f"  Anomalies detected: {len(anomalies)}")
                
                if anomalies:
                    print(f"\n  Sample Anomaly:")
                    anomaly = anomalies[0]
                    print(f"    Date: {anomaly.get('date')}")
                    print(f"    Type: {anomaly.get('type')}")
                    print(f"    Severity: {anomaly.get('severity')}")
                    print(f"    Quantity: {anomaly.get('quantity')}")
                    print(f"    Expected: {anomaly.get('expected_quantity')}")
                    print(f"    Description: {anomaly.get('description')}")
                else:
                    print(f"  No anomalies found (this is normal if data is consistent)")
                
                return True
            else:
                print(f"✗ Anomaly detection failed: {data.get('error')}")
                return False
        else:
            print(f"✗ Request failed with status code: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"✗ Error: {str(e)}")
        return False

def test_database_connection():
    """Test database connectivity"""
    print_header("Test 5: Database Connection")
    
    try:
        import mysql.connector
        import os
        from dotenv import load_dotenv
        
        load_dotenv()
        
        config = {
            'host': os.getenv('DB_HOST', '127.0.0.1'),
            'port': int(os.getenv('DB_PORT', 3306)),
            'user': os.getenv('DB_USERNAME', 'root'),
            'password': os.getenv('DB_PASSWORD', ''),
            'database': os.getenv('DB_DATABASE', 'rew_optimized')
        }
        
        print(f"Testing connection to {config['host']}:{config['port']}")
        print(f"Database: {config['database']}")
        
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor()
        
        # Test query
        cursor.execute("SELECT COUNT(*) FROM products")
        product_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM sales")
        sales_count = cursor.fetchone()[0]
        
        cursor.close()
        conn.close()
        
        print("✓ Database connection successful!")
        print(f"  Products in database: {product_count}")
        print(f"  Sales records: {sales_count}")
        
        if sales_count == 0:
            print("\n  ⚠ Warning: No sales data found")
            print("  Run the seeder to generate sample data:")
            print("    php artisan db:seed")
        
        return True
        
    except Exception as e:
        print(f"✗ Database connection failed: {str(e)}")
        print("\n  Check your ml-service/.env file:")
        print("    DB_HOST=127.0.0.1")
        print("    DB_USERNAME=root")
        print("    DB_PASSWORD=")
        print("    DB_DATABASE=rew_optimized")
        return False

def main():
    """Run all tests"""
    print("\n" + "="*60)
    print("  ML SERVICE TEST SUITE")
    print("  " + datetime.now().strftime("%Y-%m-%d %H:%M:%S"))
    print("="*60)
    
    results = []
    
    # Test 1: Health Check
    results.append(("Health Check", test_health_check()))
    
    if not results[0][1]:
        print("\n✗ ML Service is not running. Start it with: python app.py")
        return
    
    # Test 2: Database
    results.append(("Database Connection", test_database_connection()))
    
    # Test 3: Forecast
    results.append(("Forecast Generation", test_forecast_endpoint()))
    
    # Test 4: Batch Forecast
    results.append(("Batch Forecasting", test_batch_forecast()))
    
    # Test 5: Anomaly Detection
    results.append(("Anomaly Detection", test_anomaly_detection()))
    
    # Summary
    print_header("Test Summary")
    
    passed = sum(1 for _, result in results if result)
    total = len(results)
    
    for test_name, result in results:
        status = "✓ PASS" if result else "✗ FAIL"
        print(f"  {status}  {test_name}")
    
    print(f"\n  Total: {passed}/{total} tests passed")
    
    if passed == total:
        print("\n  🎉 All tests passed! ML Service is ready to use.")
    else:
        print("\n  ⚠ Some tests failed. Check the output above for details.")
    
    print("\n" + "="*60 + "\n")

if __name__ == "__main__":
    main()
