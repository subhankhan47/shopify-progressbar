<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'type' => 'RECURRING',
                'name' => 'Basic',
                'price' => 3.99,
                'interval' => 'EVERY_30_DAYS',
                'capped_amount' => null,
                'terms' => '2-day free trial',
                'trial_days' => 2,
                'test' => false,
                'on_install' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'RECURRING',
                'name' => 'Basic',
                'price' => 35.00,
                'interval' => 'ANNUAL',
                'capped_amount' => null,
                'terms' => '2-day free trial',
                'trial_days' => 2,
                'test' => false,
                'on_install' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
