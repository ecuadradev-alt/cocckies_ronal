<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashRegister;
use App\Models\Company;
use App\Models\User;

class CashRegisterSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'demo-company')->first();
        $admin   = User::where('email', 'admin@demo.com')->first();

        CashRegister::firstOrCreate(
            [
                'company_id' => $company->id,
                'date' => now()->toDateString(),
            ],
            [
                'opening_cash_pen' => 1000,
                'opening_cash_bob' => 1800,
                'opening_cash_usd' => 250,
                'opening_gold' => 50,
                'balance_pen' => 1000,
                'balance_bob' => 1800,
                'balance_usd' => 250,
                'opened_by' => $admin->id,
            ]
        );
    }
}
