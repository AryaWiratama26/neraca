<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'type', 'balance', 'icon', 'color', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'encrypted',
        'balance' => 'encrypted',
    ];

    public function getBalanceAttribute($value)
    {
        $decrypted = $this->castAttribute('balance', $value);
        return (float) ($decrypted ?? 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'cash' => 'Uang Tunai',
            'bank' => 'Bank',
            'ewallet' => 'E-Wallet',
            'savings' => 'Tabungan',
            default => $this->type,
        };
    }
}
