<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCondition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'condition_code',
        'book_id',
        'book_code',
        'condition',
        'previous_condition',
        'condition_description',
        'damage_description',
        'damage_details',
        'physical_state',
        'current_borrowing_id',
        'last_borrowing_id',
        'times_borrowed',
        'last_check_date',
        'next_check_date',
        'checked_by',
        'check_notes',
        'needs_repair',
        'repair_date',
        'repair_completion_date',
        'repair_notes',
        'repair_cost',
        'is_available',
        'is_reference_only',
        'is_digital',
        'digital_file_path'
    ];

    protected $casts = [
        'last_check_date' => 'date',
        'next_check_date' => 'date',
        'repair_date' => 'date',
        'repair_completion_date' => 'date',
        'repair_cost' => 'decimal:2',
        'is_available' => 'boolean',
        'is_reference_only' => 'boolean',
        'is_digital' => 'boolean',
        'damage_details' => 'array',
        'times_borrowed' => 'integer'
    ];

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function currentBorrowing()
    {
        return $this->belongsTo(Borrowing::class, 'current_borrowing_id');
    }

    public function lastBorrowing()
    {
        return $this->belongsTo(Borrowing::class, 'last_borrowing_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function histories()
    {
        return $this->hasMany(BookConditionHistory::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeNeedsRepair($query)
    {
        return $query->where('needs_repair', true);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    // Accessors
    public function getConditionBadgeAttribute()
    {
        $badges = [
            'new' => 'success',
            'good' => 'info',
            'fair' => 'primary',
            'poor' => 'warning',
            'damaged' => 'danger',
            'lost' => 'dark',
            'under_repair' => 'secondary',
            'withdrawn' => 'secondary'
        ];
        
        return $badges[$this->condition] ?? 'secondary';
    }

    public function getConditionTextAttribute()
    {
        $texts = [
            'new' => 'Baru',
            'good' => 'Baik',
            'fair' => 'Cukup',
            'poor' => 'Kurang Baik',
            'damaged' => 'Rusak',
            'lost' => 'Hilang',
            'under_repair' => 'Dalam Perbaikan',
            'withdrawn' => 'Ditarik'
        ];
        
        return $texts[$this->condition] ?? $this->condition;
    }

    public function getPhysicalStateTextAttribute()
    {
        $texts = [
            'sangat_baik' => 'Sangat Baik',
            'baik' => 'Baik',
            'cukup' => 'Cukup',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat'
        ];
        
        return $texts[$this->physical_state] ?? $this->physical_state;
    }

    // Methods
    public function markAsBorrowed($borrowingId)
    {
        $this->current_borrowing_id = $borrowingId;
        $this->is_available = false;
        $this->times_borrowed++;
        $this->save();
    }

    public function markAsReturned($condition = 'good')
    {
        $this->last_borrowing_id = $this->current_borrowing_id;
        $this->current_borrowing_id = null;
        $this->is_available = true;
        
        if ($condition !== 'good') {
            $this->condition = $condition;
            $this->needs_repair = ($condition === 'damaged');
        }
        
        $this->save();
    }

    public function updateCondition($newCondition, $notes = null)
    {
        $this->previous_condition = $this->condition;
        $this->condition = $newCondition;
        $this->condition_description = $notes;
        $this->save();

        // Create history
        BookConditionHistory::create([
            'book_condition_id' => $this->id,
            'book_id' => $this->book_id,
            'previous_condition' => $this->previous_condition,
            'new_condition' => $newCondition,
            'reason' => $notes,
            'changed_by' => auth()->id()
        ]);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bookCondition) {
            if (empty($bookCondition->condition_code)) {
                $bookCondition->condition_code = 'BC-' . $bookCondition->book_id . '-' . 
                    str_pad(BookCondition::where('book_id', $bookCondition->book_id)->count() + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}