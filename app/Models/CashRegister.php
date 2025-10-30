<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashRegister extends Model
{
    use HasFactory;

    protected $table = 'cash_registers';

    protected $fillable = [
        'date',
        'opening_cash_pen',
        'opening_cash_bob',
        'opening_cash_usd',
        'opening_gold',
        'balance_pen',
        'balance_bob',
        'balance_usd',
        'closing_cash_pen',
        'closing_cash_bob',
        'closing_cash_usd',
        'closing_gold',
        'opened_by',
        'closed_by',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_cash_pen' => 'decimal:2',
        'opening_cash_bob' => 'decimal:2',
        'opening_cash_usd' => 'decimal:2',
        'opening_gold'     => 'decimal:3',
        'closing_cash_pen' => 'decimal:2',
        'closing_cash_bob' => 'decimal:2',
        'closing_cash_usd' => 'decimal:2',
        'closing_gold'     => 'decimal:3',
        'balance_pen'      => 'decimal:2',
        'balance_bob'      => 'decimal:2',
        'balance_usd'      => 'decimal:2',
    ];

    // 🔹 Usuario que abrió la caja
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    // 🔹 Usuario que cerró la caja
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // 🔹 Transacciones asociadas a esta caja
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'cash_register_id');
    }
}
