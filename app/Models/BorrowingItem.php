<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowingItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'borrowing_id',
        'book_id',
        'quantity',
        'returned_quantity',
        'damaged_quantity',
        'lost_quantity',
        'status',
        'condition_notes',
        'return_date'
    ];

    protected $casts = [
        'return_date' => 'date',
        'quantity' => 'integer',
        'returned_quantity' => 'integer',
        'damaged_quantity' => 'integer',
        'lost_quantity' => 'integer'
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeReject($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['bg-warning', 'Menunggu'],
            'approved' => ['bg-success', 'Disetujui'],
            'rejected' => ['bg-danger', 'Ditolak'],
            'completed' => ['bg-info', 'Selesai']
        ];

        $badge = $badges[$this->status] ?? ['bg-secondary', $this->status];
        return '<span class="badge ' . $badge[0] . ' bg-opacity-10 text-' . str_replace('bg-', '', $badge[0]) . ' px-3 py-2">' . $badge[1] . '</span>';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai'
        ];
        return $texts[$this->status] ?? $this->status;
    }
}
