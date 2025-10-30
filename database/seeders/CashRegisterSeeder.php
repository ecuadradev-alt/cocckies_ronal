<?php

namespace Database\Seeders;

use App\Models\CashRegister;
use Illuminate\Database\Seeder;

class CashRegisterSeeder extends Seeder
{
    public function run(): void
    {
        CashRegister::create([
            'date' => now()->toDateString(),
            'opening_cash_pen' => 1000.00,
            'opening_cash_bob' => 1800.00,
            'opening_cash_usd' => 250.00,
            'opening_gold' => 50.00, // gramos
            'balance_pen' => 1000.00,
            'balance_bob' => 1800.00,
            'balance_usd' => 250.00,
            'opened_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
