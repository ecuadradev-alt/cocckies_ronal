<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;
use App\Models\User;
use App\Models\Transaction;

class CashRegister extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
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
        'status',
        'notes',
        'opened_by',
        'closed_by',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_cash_pen' => 'decimal:2',
        'opening_cash_bob' => 'decimal:2',
        'opening_cash_usd' => 'decimal:2',
        'opening_gold' => 'decimal:3',
        'closing_cash_pen' => 'decimal:2',
        'closing_cash_bob' => 'decimal:2',
        'closing_cash_usd' => 'decimal:2',
        'closing_gold' => 'decimal:3',
        'balance_pen' => 'decimal:2',
        'balance_bob' => 'decimal:2',
        'balance_usd' => 'decimal:2',
    ];

    /** Usuario que abrió la caja */
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /** Usuario que cerró la caja */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'abierta';
    }

    public function closeRegister(array $closingData = []): void
    {
        $this->update(array_merge($closingData, [
            'status' => 'cerrada',
            'closed_by' => auth()->id(),
        ]));
    }

    public function applyTransaction(Transaction $transaction)
    {
        if (!$this->isOpen()) {
            return;
        }

        match ($transaction->moneda) {
            'PEN' => $this->balance_pen += $transaction->total_pen,
            'USD' => $this->balance_usd += $transaction->total_usd,
            'BOB' => $this->balance_bob += $transaction->total_bob,
        };

        $this->save();
    }
}
