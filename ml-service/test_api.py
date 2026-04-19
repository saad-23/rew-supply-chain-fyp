"""
Simple test to call ML service
"""
import requests
import json

# Test health
print("Testing ML Service Health...")
response = requests.get('http://localhost:5000/api/health', timeout=5)
print(f"Health Check: {response.status_code}")
print(json.dumps(response.json(), indent=2))

# Test forecast
print("\nTesting Forecast Generation...")
payload = {
    "product_id": 2,
    "days": 7
}

try:
    response = requests.post(
        'http://localhost:5000/api/forecast',
        json=payload,
        timeout=60
    )
    print(f"Forecast Status: {response.status_code}")
    
    if response.status_code == 200:
        data = response.json()
        print(json.dumps(data, indent=2))
    else:
        print(response.text)
except Exception as e:
    print(f"Error: {e}")
