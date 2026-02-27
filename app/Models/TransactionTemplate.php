<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'account_id', 'category_id', 'type',
        'amount', 'description', 'tags', 'icon', 'color',
    ];

    protected $casts = [
        'tags' => 'array',
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
}
