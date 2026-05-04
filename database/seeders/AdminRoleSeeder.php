<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    /**
     * Promote all existing users to 'admin' role.
     * Run once to ensure no existing account gets locked out.
     */
    public function run(): void
    {
        $updated = DB::table('users')
            ->whereIn('role', ['staff', 'manager'])
            ->update(['role' => 'admin']);

        $this->command->info("✅ AdminRoleSeeder: {$updated} user(s) updated to 'admin' role.");
    }
}
