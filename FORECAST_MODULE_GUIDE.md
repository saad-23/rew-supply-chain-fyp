# Demand Forecasting Module - Complete Guide

## 📊 Overview
Yeh module **AI-driven demand forecasting** provide karta hai jo **Facebook Prophet ML model** use karta hai. Month-wise future predictions dekh sakte hain kis product ki kitni demand hogi.

## 🎯 Features Implemented

### 1. **Month-Wise Forecasting (3 Months)**
- Daily forecasts ko monthly groups mein aggregate kiya
- Chart mein month-wise total demand dikhata hai
- Proper visualization with bar chart

### 2. **ML Service Integration**
- **Python Flask API** (Port 5000) se forecasts fetch hote hain
- **Prophet Model** use hoti hai time-series forecasting ke liye
- Automatic fallback to simple statistical method agar ML service unavailable ho

### 3. **Interactive Dashboard**
Route: `/forecast`

**Features:**
- Product dropdown se select karo
- "Generate ML Forecast" button se on-demand forecasting
- Auto-load existing forecasts agar available hain
- Download CSV report
- Real-time chart updates

### 4. **Forecast Statistics Cards**
- **Total 30-Day Demand**: Next month ki total predicted quantity
- **Daily Average**: Per day average demand
- **ML Model Used**: Kon sa model use hua (Prophet ML / Simple MA)
- **Current Stock**: Product ki available inventory

### 5. **Inventory Optimization Metrics**
- **Reorder Point**: Stock kab order karna hai
- **Safety Stock**: Buffer stock for emergencies
- **EOQ (Economic Order Quantity)**: Ideal order quantity

---

## 🔄 Complete Flow

### Step-by-Step Execution:

1. **User Opens /forecast Route**
   ```
   URL: http://localhost:8000/forecast
   ```

2. **ForecastDashboard Component Loads**
   - First product auto-select hota hai
   - Check karta hai ke forecast data exists ya nahi

3. **If No Data → Auto Generate Forecast**
   - `generateForecast()` method call hoti hai
   - ForecastingService `generateMLForecasts()` ko call karti hai

4. **ML Service API Call**
   ```
   POST http://localhost:5000/api/forecast
   Body: {
       "product_id": 1,
       "days": 90
   }
   ```

5. **Python Prophet Model**
   - Database se sales history fetch karta hai
   - Prophet model train karta hai
   - 90 days ka forecast generate karta hai
   - Confidence scores calculate karta hai

6. **Laravel Database Save**
   - Python API se forecasts receive hoti hain
   - `demand_forecasts` table mein save hoti hain
   - Each entry:
     - forecast_date
     - predicted_quantity
     - model_used: "Prophet ML"
     - confidence_score: 0-100

7. **Monthly Aggregation**
   - Daily forecasts ko month-wise group kiya
   - Total monthly demand calculate hoti hai
   - Chart data prepare hota hai

8. **Chart Rendering**
   - Chart.js bar chart render hota hai
   - Month labels (Apr 2026, May 2026, etc.)
   - Monthly total demand values
   - Interactive tooltips with details

---

## 🗄️ Database Structure

### Table: `demand_forecasts`
```sql
CREATE TABLE demand_forecasts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT NOT NULL,
    forecast_date DATE NOT NULL,
    predicted_quantity INT NOT NULL,
    model_used VARCHAR(255) DEFAULT 'Prophet ML',
    confidence_score INT DEFAULT 80,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### Sample Data:
```sql
INSERT INTO demand_forecasts (product_id, forecast_date, predicted_quantity, model_used, confidence_score)
VALUES 
    (1, '2026-04-20', 25, 'Prophet ML', 85),
    (1, '2026-04-21', 28, 'Prophet ML', 87),
    ...
```

---

## 🐍 Python ML Service

### Location: `ml-service/`

### Files:
- **app.py**: Flask API server
- **models/demand_forecast.py**: Prophet model implementation
- **requirements.txt**: Python dependencies
- **.env**: Database configuration

### Key Components:

#### 1. DemandForecaster Class
```python
class DemandForecaster:
    def predict(self, product_id, days=30):
        # Fetch sales history from MySQL
        df = self.get_sales_history(product_id)
        
        # Train Prophet model
        model = Prophet(
            yearly_seasonality=True,
            weekly_seasonality=True,
            changepoint_prior_scale=0.05
        )
        model.fit(df)
        
        # Generate forecasts
        future = model.make_future_dataframe(periods=days)
        forecast = model.predict(future)
        
        # Return formatted results
        return formatted_forecasts
```

#### 2. API Endpoints:
- `GET /api/health` - Health check
- `POST /api/forecast` - Generate forecast for single product
- `POST /api/forecast/batch` - Batch forecasting for multiple products

### Database Connection:
Prophet model directly MySQL se connect hota hai:
```python
db_config = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'root',
    'password': '',
    'database': 'rew_optimized'
}
```

---

## 🚀 How to Run

### Prerequisites:
1. **XAMPP Running**
   - Apache
   - MySQL
   - Database: `rew_optimized`

2. **Python Virtual Environment**
   ```powershell
   cd ml-service
   .\.venv\Scripts\Activate.ps1
   ```

3. **Install Python Dependencies**
   ```bash
   pip install -r requirements.txt
   ```

### Start Services:

#### 1. Laravel Application
```powershell
cd c:\xampp\htdocs\rew-optimized
php artisan serve
```

#### 2. Python ML Service
```powershell
cd ml-service
python app.py
```
ML Service runs on: `http://localhost:5000`

#### 3. Access Dashboard
```
http://localhost:8000/forecast
```

---

## 🧪 Testing the Module

### Manual Testing:

1. **Open Forecast Page**
   - Navigate to `/forecast`
   - Should auto-select first product

2. **Check Auto-Generation**
   - If no data, should auto-generate forecast
   - Watch for loading spinner
   - Success notification should appear

3. **Generate Fresh Forecast**
   - Click "Generate ML Forecast" button
   - Wait for completion (may take 10-30 seconds)
   - Chart should update with new data

4. **Select Different Product**
   - Use dropdown to change product
   - Should auto-load or generate forecast
   - Chart should update

5. **Download Report**
   - Click "Download CSV" button
   - Should download forecast data

### Check ML Service:

**Test Health Endpoint:**
```bash
curl http://localhost:5000/api/health
```

**Expected Response:**
```json
{
    "status": "healthy",
    "service": "ml-forecasting",
    "timestamp": "2026-04-19 12:00:00"
}
```

**Test Forecast API:**
```bash
curl -X POST http://localhost:5000/api/forecast \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "days": 30}'
```

**Expected Response:**
```json
{
    "success": true,
    "product_id": 1,
    "days": 30,
    "model_used": "Prophet",
    "forecasts": [
        {
            "date": "2026-04-20",
            "predicted_quantity": 25,
            "confidence_lower": 20,
            "confidence_upper": 30,
            "confidence_score": 0.85
        },
        ...
    ]
}
```

---

## 📁 Modified Files

### Laravel Files:
1. **app/Livewire/Analytics/ForecastDashboard.php**
   - Added `generateForecast()` method
   - Added `forecastDays = 90` property
   - Monthly aggregation in `loadData()`
   - Forecast statistics calculation

2. **resources/views/livewire/analytics/forecast-dashboard.blade.php**
   - Added "Generate ML Forecast" button
   - 4 statistics cards (Total, Daily Avg, Model, Stock)
   - Bar chart instead of line chart
   - Month-wise labels
   - Better tooltips with daily average

3. **app/Services/ForecastingService.php**
   - Made `generateMLForecasts()` public
   - Can be called from Livewire component

### Python Files:
- **ml-service/app.py**: Flask API endpoints
- **ml-service/models/demand_forecast.py**: Prophet implementation
- **ml-service/.env**: Database configuration

---

## 🎨 UI Components

### Statistics Cards (Top Row):
1. **Blue Card**: Total 30-day predicted demand
2. **Green Card**: Daily average demand
3. **Purple Card**: ML model name
4. **Orange Card**: Current stock level

### Chart Section:
- **Type**: Bar chart (better for monthly data)
- **X-Axis**: Month labels (Apr 2026, May 2026, Jun 2026)
- **Y-Axis**: Total monthly demand in units
- **Tooltip**: Shows total + daily average

### Action Buttons:
- **Generate ML Forecast**: Triggers fresh forecast via Python ML
- **Download CSV**: Exports forecast data
- **Product Dropdown**: Select product to forecast

---

## 🔧 Troubleshooting

### Issue 1: ML Service Not Available
**Symptom**: "Using fallback" in logs

**Solution:**
```powershell
cd ml-service
python app.py
```
Verify at: http://localhost:5000/api/health

### Issue 2: No Chart Showing
**Symptom**: "No forecast data available"

**Solution:**
- Click "Generate ML Forecast" button
- Check if Python service is running
- Check database connection in ml-service/.env

### Issue 3: Database Connection Error
**Check:**
```sql
-- Verify database exists
SHOW DATABASES LIKE 'rew_optimized';

-- Check tables
USE rew_optimized;
SHOW TABLES;

-- Check sample data
SELECT * FROM products LIMIT 5;
SELECT * FROM sales LIMIT 5;
```

### Issue 4: Slow Forecast Generation
**Reason**: Prophet model training takes time with large datasets

**Normal Duration:**
- Small data (<100 rows): 5-10 seconds
- Medium data (100-1000 rows): 10-30 seconds
- Large data (>1000 rows): 30-60 seconds

---

## 📊 How Prophet Works

### Why Prophet for REW System?

1. **Handles Seasonality**: Weekend vs weekday sales patterns
2. **Trend Detection**: Automatically detects increasing/decreasing trends
3. **Holiday Effects**: Can add custom holidays
4. **Missing Data**: Robust to missing sales days
5. **Confidence Intervals**: Provides uncertainty quantification

### Prophet Training Process:

1. **Data Preparation**:
   ```python
   df = pd.DataFrame({
       'ds': ['2026-01-01', '2026-01-02', ...],  # Dates
       'y': [25, 30, 28, ...]  # Sales quantities
   })
   ```

2. **Model Configuration**:
   - Yearly seasonality (if >365 days data)
   - Weekly seasonality (if >14 days data)
   - Monthly custom seasonality

3. **Training**:
   ```python
   model = Prophet()
   model.fit(df)
   ```

4. **Prediction**:
   ```python
   future = model.make_future_dataframe(periods=90)
   forecast = model.predict(future)
   ```

5. **Results**:
   - `yhat`: Predicted value
   - `yhat_lower`: Lower confidence bound
   - `yhat_upper`: Upper confidence bound

---

## 🎯 Real-World Usage Examples

### Example 1: Inventory Planning
```
Product: Industrial Bearing 6204
Current Stock: 150 units
Forecast (Next 30 days): 420 units
Daily Average: 14 units/day

Action: Order 300 units to maintain buffer stock
```

### Example 2: Seasonal Trend Detection
```
Month      | Predicted Demand
-----------|------------------
Apr 2026   | 420 units
May 2026   | 580 units  ← High season
Jun 2026   | 450 units

Insight: May has 38% higher demand, prepare extra stock
```

### Example 3: Reorder Point Alert
```
Reorder Point: 180 units
Current Stock: 150 units
Status: ⚠️ REORDER NOW

Safety Stock: 120 units
EOQ: 360 units
```

---

## 📈 Performance Metrics

### Confidence Score Interpretation:
- **95%+**: Very High Confidence (sufficient historical data)
- **85-95%**: High Confidence (good data quality)
- **75-85%**: Medium Confidence (limited data)
- **<75%**: Low Confidence (very limited data / high variance)

### Model Accuracy (MAPE):
- **<10%**: Excellent forecast
- **10-20%**: Good forecast
- **20-30%**: Acceptable forecast
- **>30%**: Poor forecast (consider data quality)

---

## 🔮 Future Enhancements

### Potential Improvements:
1. **Multi-Product Comparison**: Compare forecasts of multiple products
2. **What-If Analysis**: Simulate different scenarios
3. **Promotion Impact**: Model promotional campaign effects
4. **External Factors**: Weather, events, economic indicators
5. **Auto-Ordering**: Automatically create purchase orders
6. **Alert System**: Notifications when stock reaches reorder point

---

## ✅ Success Indicators

Module is working properly if:

✔️ Page loads without errors  
✔️ Product dropdown populated  
✔️ Statistics cards show correct values  
✔️ Chart displays monthly bars  
✔️ "Generate ML Forecast" button works  
✔️ Download CSV generates file  
✔️ Product switch updates chart  
✔️ ML Service responds (check logs)  
✔️ Database has forecast records  

---

## 📞 Support & Debugging

### Check Logs:

**Laravel Logs:**
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

**Python Logs:**
```bash
# In terminal running Python service
# Logs appear in console
```

**Browser Console:**
```
F12 → Console Tab
# Check for JavaScript errors
# Check for Chart.js errors
```

### Database Verification:
```sql
-- Check forecast data
SELECT 
    p.name,
    COUNT(*) as forecast_count,
    MIN(df.forecast_date) as first_date,
    MAX(df.forecast_date) as last_date,
    df.model_used
FROM demand_forecasts df
JOIN products p ON df.product_id = p.id
GROUP BY p.id, df.model_used;
```

---

## 🎓 Summary

Yeh **Demand Forecasting Module** ab fully functional hai with:

1. ✅ **Month-wise visualization** (90 days forecast)
2. ✅ **Python Prophet ML integration**
3. ✅ **Interactive dashboard** with real-time updates
4. ✅ **Statistics cards** for quick insights
5. ✅ **On-demand forecast generation**
6. ✅ **Automatic fallback** if ML service unavailable
7. ✅ **CSV export** functionality
8. ✅ **Inventory optimization** metrics

**Technology Stack:**
- Laravel 12 + Livewire (Frontend & Backend)
- Python Flask + Prophet (ML Service)
- Chart.js (Visualization)
- MySQL (Data Storage)
- Facebook Prophet (Time Series Forecasting)

Bas ML service ko start karo aur `/forecast` page open karo to poora system work karega! 🚀
