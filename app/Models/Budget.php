<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'amount', 'month', 'year', 'spent',
    ];

    protected $casts = [
        'amount' => 'encrypted',
        'spent' => 'encrypted',
    ];

    public function getAmountAttribute($value)
    {
        $decrypted = $this->castAttribute('amount', $value);
        return (float) ($decrypted ?? 0);
    }

    public function getSpentAttribute($value)
    {
        $decrypted = $this->castAttribute('spent', $value);
        return (float) ($decrypted ?? 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->amount <= 0) return 0;
        return min(round(($this->spent / $this->amount) * 100, 1), 100);
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->amount - $this->spent);
    }

    public function getStatusAttribute()
    {
        $pct = $this->percentage;
        if ($pct >= 100) return 'exceeded';
        if ($pct >= 90) return 'danger';
        if ($pct >= 75) return 'warning';
        return 'normal';
    }
}
