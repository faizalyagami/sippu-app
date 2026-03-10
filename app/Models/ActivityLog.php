<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'log_number',
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'module',
        'sub_module',
        'event',
        'description',
        'old_data',
        'new_data',
        'changes',
        'model',
        'model_id',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device',
        'url',
        'method',
        'request_data',
        'response_status',
        'duration'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'changes' => 'array',
        'request_data' => 'array',
        'response_status' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'stock_add' => 'success',
            'stock_remove' => 'warning',
            'approve' => 'success',
            'reject' => 'danger',
            'return' => 'info',
            'borrow' => 'primary'
        ];
        
        return $badges[$this->action] ?? 'secondary';
    }

    public function getActionTextAttribute()
    {
        $texts = [
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'stock_add' => 'Tambah Stok',
            'stock_remove' => 'Kurangi Stok',
            'approve' => 'Setujui',
            'reject' => 'Tolak',
            'return' => 'Kembali',
            'borrow' => 'Pinjam'
        ];
        
        return $texts[$this->action] ?? $this->action;
    }
}