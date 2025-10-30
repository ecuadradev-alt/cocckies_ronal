<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\CashRegister;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $cashRegister = CashRegister::first();

        if (!$cashRegister) {
            return;
        }

        Transaction::create([
            'cash_register_id'     => $cashRegister->id,
            'type'                 => 'compra',
            'metal_type'           => 'oro',
            'grams'                => 500.00,
            'purity'               => 0.9000,
            'discount_percentage'  => 10.00,
            'price_per_gram_pen'   => 330.57172022,
            'price_per_gram_bob'   => 627.50,
            'price_per_gram_usd'   => 88.15245873,
            'price_per_oz'         => 3385.00,
            'total_pen'            => 165285.86,
            'total_bob'            => 313000.00,
            'total_usd'            => 44076.23,
            'moneda'               => 'PEN',
            'exchange_rate_pen_bob'=> 1.89,
            'exchange_rate_pen_usd'=> 3.75,
            'client_name'          => 'Minería Los Andes SAC',
            'tipo_venta'           => 'empresa',
            'hora'                 => now()->format('H:i:s'),
            'created_by'           => 1,
        ]);

        Transaction::create([
            'cash_register_id'     => $cashRegister->id,
            'type'                 => 'venta',
            'metal_type'           => 'oro',
            'grams'                => 250.00,
            'purity'               => 0.9999,
            'discount_percentage'  => 5.00,
            'price_per_gram_pen'   => 350.00,
            'price_per_gram_bob'   => 661.50,
            'price_per_gram_usd'   => 93.33,
            'price_per_oz'         => 3620.00,
            'total_pen'            => 87500.00,
            'total_bob'            => 165375.00,
            'total_usd'            => 23333.33,
            'moneda'               => 'BOB',
            'exchange_rate_pen_bob'=> 1.89,
            'exchange_rate_pen_usd'=> 3.75,
            'client_name'          => 'Cliente Regular',
            'tipo_venta'           => 'regular',
            'hora'                 => now()->format('H:i:s'),
            'created_by'           => 1,
        ]);
    }
}
