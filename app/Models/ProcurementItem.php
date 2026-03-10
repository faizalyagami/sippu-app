<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'procurement_id',
        'book_id',
        'quantity',
        'received_quantity',
        'damaged_quantity',
        'unit_price',
        'total_price',
        'notes',
        'status'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'partial' => 'info',
            'completed' => 'success'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
}