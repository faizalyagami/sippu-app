<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_number',
        'user_id',
        'book_id',
        'book_condition_id',
        'reservation_type',
        'reservation_date',
        'expected_available_date',
        'expiry_date',
        'priority',
        'status',
        'queue_position',
        'notes',
        'cancellation_reason',
        'notification_sent',
        'notified_at',
        'reminder_count',
        'pickup_deadline',
        'picked_up_at',
        'borrowing_id',
        'converted_at',
        'queue_number',
        'estimated_waiting_days'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'expected_available_date' => 'date',
        'expiry_date' => 'date',
        'notified_at' => 'datetime',
        'pickup_deadline' => 'datetime',
        'picked_up_at' => 'datetime',
        'converted_at' => 'datetime',
        'notification_sent' => 'boolean',
        'reminder_count' => 'integer',
        'queue_position' => 'integer',
        'queue_number' => 'integer',
        'estimated_waiting_days' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookCondition()
    {
        return $this->belongsTo(BookCondition::class);
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'waiting', 'ready_for_pickup']);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeReadyForPickup($query)
    {
        return $query->where('status', 'ready_for_pickup');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'waiting' => 'warning',
            'ready_for_pickup' => 'info',
            'fulfilled' => 'primary',
            'expired' => 'secondary',
            'cancelled' => 'danger',
            'transferred' => 'dark'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'active' => 'Aktif',
            'waiting' => 'Menunggu',
            'ready_for_pickup' => 'Siap Diambil',
            'fulfilled' => 'Selesai',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
            'transferred' => 'Ditransfer'
        ];
        
        return $texts[$this->status] ?? $this->status;
    }

    // Methods
    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    public function markAsReady()
    {
        $this->status = 'ready_for_pickup';
        $this->pickup_deadline = now()->addDays(2);
        $this->save();
    }

    public function markAsFulfilled($borrowingId)
    {
        $this->status = 'fulfilled';
        $this->borrowing_id = $borrowingId;
        $this->converted_at = now();
        $this->save();
    }

    public function markAsExpired()
    {
        $this->status = 'expired';
        $this->save();
    }

    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->save();
    }
}