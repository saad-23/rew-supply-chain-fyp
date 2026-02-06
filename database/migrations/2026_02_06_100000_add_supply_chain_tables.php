<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Users Table (FR1, FR2)
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->enum('role', ['admin', 'manager', 'staff'])->default('staff')->after('email');
            $table->boolean('is_active')->default(true)->after('password');
        });

        // 2. Suppliers Table (FR3, FR4)
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('lead_time_days')->default(7); // Delivery time
            $table->timestamps();
        });

        // 3. Sales History (FR3)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->date('sale_date');
            $table->timestamps();
        });

        // 4. Deliveries / Routes (FR5)
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('address'); // Used for geocoding
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('priority')->default(1); // 1 = Normal, 2 = High
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->date('delivery_date');
            $table->timestamps();
        });

        // 5. Anomalies / Alerts (FR6, FR8)
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'price_surge', 'delivery_delay'
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('detected_at');
            $table->timestamps(); // created_at can be detected_at
        });

        // 6. Forecasts (FR3 Cache)
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('forecast_date');
            $table->integer('predicted_quantity');
            $table->string('model_used')->default('simple_moving_average'); // LSTM, ARIMA, etc.
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0-100%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
        Schema::dropIfExists('anomalies');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('suppliers');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'role', 'is_active']);
        });
    }
};
