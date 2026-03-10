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
        'return_date' => 'date'
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'borrowed' => 'primary',
            'partial' => 'warning',
            'returned' => 'success',
            'damaged' => 'danger',
            'lost' => 'dark'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
}