<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookConditionHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_condition_id',
        'book_id',
        'previous_condition',
        'new_condition',
        'reason',
        'notes',
        'details',
        'changed_by',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function bookCondition()
    {
        return $this->belongsTo(BookCondition::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            $model = "App\\Models\\" . $this->reference_type;
            return $this->belongsTo($model, 'reference_id');
        }
        return null;
    }
}