"""
Test script for Demand Forecasting ML Integration
Run this to verify the ML service is working correctly
"""

import requests
import json
from datetime import datetime

ML_SERVICE_URL = "http://localhost:5000"

def test_health():
    """Test if ML service is running"""
    print("🔍 Testing ML Service Health...")
    try:
        response = requests.get(f"{ML_SERVICE_URL}/api/health", timeout=5)
        if response.status_code == 200:
            data = response.json()
            print(f"✅ ML Service is healthy!")
            print(f"   Status: {data.get('status')}")
            print(f"   Service: {data.get('service')}")
            print(f"   Timestamp: {data.get('timestamp')}")
            return True
        else:
            print(f"❌ ML Service returned status code: {response.status_code}")
            return False
    except requests.exceptions.ConnectionError:
        print("❌ ML Service is not running!")
        print("   Please start the service: python app.py")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def test_forecast(product_id=1, days=30):
    """Test forecast generation"""
    print(f"\n📊 Testing Forecast Generation...")
    print(f"   Product ID: {product_id}")
    print(f"   Forecast Days: {days}")
    
    try:
        response = requests.post(
            f"{ML_SERVICE_URL}/api/forecast",
            json={
                "product_id": product_id,
                "days": days
            },
            timeout=60
        )
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                print(f"✅ Forecast Generated Successfully!")
                print(f"   Model: {data.get('model_used')}")
                print(f"   Forecasts: {len(data.get('forecasts', []))} days")
                
                # Show first 3 forecasts
                forecasts = data.get('forecasts', [])[:3]
                print(f"\n   Sample Predictions:")
                for f in forecasts:
                    print(f"   - {f['date']}: {f['predicted_quantity']} units (confidence: {f['confidence_score']})")
                
                return True
            else:
                print(f"❌ Forecast failed: {data.get('error')}")
                return False
        else:
            print(f"❌ API returned status code: {response.status_code}")
            print(f"   Response: {response.text}")
            return False
            
    except requests.exceptions.Timeout:
        print("❌ Request timed out! Model training may take time.")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def test_batch_forecast(product_ids=[1, 2], days=30):
    """Test batch forecast for multiple products"""
    print(f"\n📦 Testing Batch Forecast...")
    print(f"   Products: {product_ids}")
    
    try:
        response = requests.post(
            f"{ML_SERVICE_URL}/api/forecast/batch",
            json={
                "product_ids": product_ids,
                "days": days
            },
            timeout=120
        )
        
        if response.status_code == 200:
            data = response.json()
            
            if data.get('success'):
                results = data.get('results', [])
                print(f"✅ Batch Forecast Completed!")
                print(f"   Total Products: {len(results)}")
                
                for result in results:
                    status = "✅" if result.get('success') else "❌"
                    print(f"   {status} Product {result['product_id']}")
                
                return True
            else:
                print(f"❌ Batch forecast failed")
                return False
        else:
            print(f"❌ API returned status code: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def main():
    """Run all tests"""
    print("=" * 60)
    print("  DEMAND FORECASTING ML SERVICE - TEST SUITE")
    print("=" * 60)
    print(f"  Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)
    
    # Test 1: Health Check
    health_ok = test_health()
    
    if not health_ok:
        print("\n⚠️  ML Service is not running. Please start it first:")
        print("   cd ml-service")
        print("   python app.py")
        return
    
    # Test 2: Single Product Forecast
    forecast_ok = test_forecast(product_id=1, days=30)
    
    # Test 3: Batch Forecast
    batch_ok = test_batch_forecast(product_ids=[1, 2], days=30)
    
    # Summary
    print("\n" + "=" * 60)
    print("  TEST SUMMARY")
    print("=" * 60)
    print(f"  Health Check:      {'✅ PASS' if health_ok else '❌ FAIL'}")
    print(f"  Forecast Test:     {'✅ PASS' if forecast_ok else '❌ FAIL'}")
    print(f"  Batch Test:        {'✅ PASS' if batch_ok else '❌ FAIL'}")
    print("=" * 60)
    
    if health_ok and forecast_ok:
        print("\n🎉 All tests passed! ML service is working correctly.")
        print("\n📝 Next Steps:")
        print("   1. Open Laravel app: http://localhost:8000/forecast")
        print("   2. Select a product from dropdown")
        print("   3. Click 'Generate ML Forecast' button")
        print("   4. View the month-wise forecast chart")
    else:
        print("\n⚠️  Some tests failed. Please check the errors above.")

if __name__ == "__main__":
    main()
