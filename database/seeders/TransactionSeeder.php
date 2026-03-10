<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\CashRegister;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $cashRegister = CashRegister::first();
        $admin = User::where('email', 'admin@demo.com')->first();

        if (!$cashRegister) return;

        Transaction::create([
            'company_id'            => $cashRegister->company_id,
            'cash_register_id'      => $cashRegister->id,
            'metal_type'            => 'oro',
            'grams'                 => 500,
            'purity'                => 0.9,
            'discount_percentage'   => 10,
            'price_per_gram_pen'    => 330.57,
            'price_per_gram_usd'    => 88.15,
            'price_per_gram_bob'    => 330.57,
            'price_per_oz'          => 3385,
            'total_pen'             => 165285.86,
            'total_usd'             => 44076.23,
            'total_bob'             => 165285.86,
            'moneda'                => 'PEN',
            'exchange_rate_pen_usd' => 3.75,
            'client_name'           => 'Minería Los Andes SAC',
            'tipo_venta'            => 'regular',
            'hora'                  => now()->format('H:i:s'),
            'created_by'            => $admin->id,
        ]);
    }
}
