<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'borrowing_number',
        'user_id',
        'approved_by',
        'borrowing_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'notes',
        'rejection_reason',
        'total_items'
    ];

    protected $casts = [
        'borrowing_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')
            ->where('expected_return_date', '<', now());
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'info',
            'borrowed' => 'primary',
            'returned' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'cancelled' => 'Dibatalkan'
        ];
        
        return $texts[$this->status] ?? $this->status;
    }

    public function isOverdue()
    {
        return $this->status == 'borrowed' && $this->expected_return_date < now();
    }

    // public function calculatePenalty()
    // {
    //     if (!$this->isOverdue()) {
    //         return 0;
    //     }
        
    //     $daysLate = now()->diffInDays($this->expected_return_date);
    //     return $daysLate * 1000; // Rp 1000 per hari
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($borrowing) {
            $borrowing->borrowing_number = 'BRW-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}