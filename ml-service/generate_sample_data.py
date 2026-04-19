"""
Generate sample sales data for testing ML forecasting
Run this script to populate the database with historical sales
"""

import mysql.connector
import os
from datetime import datetime, timedelta
import random
from dotenv import load_dotenv

load_dotenv()

# Database configuration
db_config = {
    'host': os.getenv('DB_HOST', '127.0.0.1'),
    'port': int(os.getenv('DB_PORT', 3306)),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_DATABASE', 'rew_optimized')
}

def generate_sales_data():
    """Generate sample sales data for products"""
    
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    
    # Get existing products
    cursor.execute("SELECT id FROM products LIMIT 10")
    product_ids = [row[0] for row in cursor.fetchall()]
    
    if not product_ids:
        print("No products found. Run seeder first: php artisan db:seed")
        return
    
    print(f"Found {len(product_ids)} products. Generating sales data...")
    
    # Delete existing sales (for clean slate)
    cursor.execute("DELETE FROM sales")
    
    # Generate sales for last 60 days
    sales_data = []
    end_date = datetime.now()
    start_date = end_date - timedelta(days=60)
    
    for product_id in product_ids:
        # Each product gets different sales patterns
        base_qty = random.randint(10, 50)
        
        current_date = start_date
        while current_date <= end_date:
            # Add some randomness and weekly patterns
            day_of_week = current_date.weekday()
            
            # Higher sales on weekdays (Monday-Friday)
            weekday_multiplier = 1.3 if day_of_week < 5 else 0.8
            
            # Random variation
            random_factor = random.uniform(0.7, 1.3)
            
            quantity = int(base_qty * weekday_multiplier * random_factor)
            quantity = max(1, quantity)  # At least 1
            
            # Random price with variation
            price = random.randint(1000, 50000)
            revenue = quantity * price
            
            sales_data.append((
                product_id,
                quantity,
                revenue,
                current_date.strftime('%Y-%m-%d')
            ))
            
            current_date += timedelta(days=1)
    
    # Insert all sales
    insert_query = """
        INSERT INTO sales (product_id, quantity, revenue, sale_date, created_at, updated_at)
        VALUES (%s, %s, %s, %s, NOW(), NOW())
    """
    
    cursor.executemany(insert_query, sales_data)
    conn.commit()
    
    print(f"✓ Generated {len(sales_data)} sales records!")
    print(f"  Products: {len(product_ids)}")
    print(f"  Date range: {start_date.strftime('%Y-%m-%d')} to {end_date.strftime('%Y-%m-%d')}")
    print(f"  Days: 60")
    
    # Show sample data
    cursor.execute("""
        SELECT 
            p.name,
            COUNT(*) as sale_days,
            SUM(s.quantity) as total_qty,
            SUM(s.revenue) as total_revenue
        FROM sales s
        JOIN products p ON s.product_id = p.id
        GROUP BY p.name
        LIMIT 5
    """)
    
    print("\nSample Sales Summary:")
    print("-" * 70)
    for row in cursor.fetchall():
        print(f"  {row[0][:30]:30} | {row[1]:3} days | {row[2]:4} units | Rs. {row[3]:,}")
    print("-" * 70)
    
    cursor.close()
    conn.close()

if __name__ == "__main__":
    try:
        generate_sales_data()
        print("\n✓ Sales data generated successfully!")
        print("  You can now run forecasts using the ML service.")
    except Exception as e:
        print(f"\n✗ Error: {e}")
        print("  Make sure:")
        print("    1. XAMPP MySQL is running")
        print("    2. Database 'rew_optimized' exists")
        print("    3. .env file has correct database credentials")
