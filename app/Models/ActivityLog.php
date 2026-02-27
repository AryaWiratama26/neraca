<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'description', 'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public static function log(string $action, $model, string $description, ?array $changes = null): self
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id ?? null,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }

    public function getActionBadgeAttribute(): string
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            default => 'default',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'export' => 'Export',
            'import' => 'Import',
            '2fa_enabled' => '2FA Diaktifkan',
            '2fa_disabled' => '2FA Dinonaktifkan',
            default => ucfirst($this->action),
        };
    }
}
