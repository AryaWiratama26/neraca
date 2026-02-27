<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'type', 'amount',
        'description', 'frequency', 'start_date', 'next_due', 'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_due' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'amount' => 'encrypted',
        'description' => 'encrypted',
    ];

    public function getAmountAttribute($value)
    {
        $decrypted = $this->castAttribute('amount', $value);
        return (float) ($decrypted ?? 0);
    }

    public function user() { return $this->belongsTo(User::class); }
    public function account() { return $this->belongsTo(Account::class); }
    public function category() { return $this->belongsTo(Category::class); }

    public function getFrequencyLabelAttribute()
    {
        return match($this->frequency) {
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
            default => $this->frequency,
        };
    }

    public function calculateNextDue(): string
    {
        return match($this->frequency) {
            'daily' => $this->next_due->addDay()->toDateString(),
            'weekly' => $this->next_due->addWeek()->toDateString(),
            'monthly' => $this->next_due->addMonth()->toDateString(),
            'yearly' => $this->next_due->addYear()->toDateString(),
        };
    }
}
