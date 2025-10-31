<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'cash_register_id',
        'type',
        'metal_type',
        'grams',
        'purity',
        'discount_percentage',
        'price_per_gram_pen',
        'price_per_gram_usd',
        'price_per_gram_bob',
        'price_per_oz',
        'total_pen',
        'total_usd',
        'total_bob',
        'exchange_rate_pen_usd',
        'moneda',
        'tipo_venta',
        'client_name',
        'hora',
        'created_by',
    ];

    protected $casts = [
        'grams' => 'decimal:3',
        'purity' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'price_per_gram_pen' => 'decimal:8',
        'price_per_gram_usd' => 'decimal:8',
        'price_per_gram_bob' => 'decimal:8',
        'price_per_oz' => 'decimal:2',
        'total_pen' => 'decimal:8',
        'total_usd' => 'decimal:8',
        'total_bob' => 'decimal:8',
        'exchange_rate_pen_usd' => 'decimal:3',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
