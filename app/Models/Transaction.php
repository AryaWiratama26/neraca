<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'type', 'amount', 'description', 'date', 'tags',
    ];

    protected $casts = [
        'date' => 'date',
        'tags' => 'array',
        'description' => 'encrypted',
        'amount' => 'encrypted',
    ];

    public function getAmountAttribute($value)
    {
        $decrypted = $this->castAttribute('amount', $value);
        return (float) ($decrypted ?? 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }
}
