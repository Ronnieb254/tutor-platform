<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'currency'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2'];
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class); }

    public function credit(float $amount, string $description, ?string $reference = null): WalletTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type'          => 'credit',
            'amount'        => $amount,
            'balance_after' => $this->fresh()->balance,
            'description'   => $description,
            'reference'     => $reference,
        ]);
    }

    public function debit(float $amount, string $description, ?string $reference = null): WalletTransaction
    {
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type'          => 'debit',
            'amount'        => $amount,
            'balance_after' => $this->fresh()->balance,
            'description'   => $description,
            'reference'     => $reference,
        ]);
    }
}
