<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call specific seeders in order
        $this->call([
            RewSeeder::class,
            SalesDataSeeder::class,
        ]);
        
        $this->command->info('✅ Database seeding completed!');
    }
}