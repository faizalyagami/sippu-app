<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number',
        'user_id',
        'book_title',
        'author',
        'isbn',
        'publisher',
        'publication_year',
        'edition',
        'category_id',
        'quantity_requested',
        'reason',
        'specifications',
        'priority',
        'status',
        'estimated_budget',
        'actual_cost',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_date',
        'procurement_id'
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'estimated_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger'
        ];
        
        return $badges[$this->priority] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'procured' => 'primary',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'completed' => 'dark'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            $request->request_number = 'REQ-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}