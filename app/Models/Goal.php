<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'target_amount', 'current_amount', 'deadline', 'icon', 'color',
    ];

    protected $casts = [
        'deadline' => 'date',
        'name' => 'encrypted',
        'target_amount' => 'encrypted',
        'current_amount' => 'encrypted',
    ];

    public function getTargetAmountAttribute($value)
    {
        $decrypted = $this->castAttribute('target_amount', $value);
        return (float) ($decrypted ?? 0);
    }

    public function getCurrentAmountAttribute($value)
    {
        $decrypted = $this->castAttribute('current_amount', $value);
        return (float) ($decrypted ?? 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return min(round(($this->current_amount / $this->target_amount) * 100, 1), 100);
    }
}
