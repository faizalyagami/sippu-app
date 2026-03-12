<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'data',
        'is_read',
        'read_at',
        'expired_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>', now());
        });
    }

    // Methods
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'info' => 'info',
            'success' => 'success',
            'warning' => 'warning',
            'danger' => 'danger'
        ];
        
        return $badges[$this->type] ?? 'secondary';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function isExpired()
    {
        return $this->expired_at && $this->expired_at < now();
    }

    // Static Methods
    public static function send($userId, $title, $message, $type = 'info', $link = null, $data = [])
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'data' => $data,
            'expired_at' => $data['expired_at'] ?? null
        ]);
    }

    public static function sendToRole($roleName, $title, $message, $type = 'info', $link = null, $data = [])
    {
        $users = User::whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        })->get();

        foreach ($users as $user) {
            self::send($user->id, $title, $message, $type, $link, $data);
        }
    }
}