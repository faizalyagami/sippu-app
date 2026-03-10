<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationQueue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'current_queue',
        'total_queue',
        'estimated_available_date',
        'queue_data'
    ];

    protected $casts = [
        'estimated_available_date' => 'date',
        'queue_data' => 'array',
        'current_queue' => 'integer',
        'total_queue' => 'integer'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function updateQueue()
    {
        $this->total_queue = BookReservation::where('book_id', $this->book_id)
            ->whereIn('status', ['active', 'waiting'])
            ->count();
        
        $this->save();
    }

    public function getNextInQueue()
    {
        return BookReservation::where('book_id', $this->book_id)
            ->whereIn('status', ['active', 'waiting'])
            ->orderBy('created_at')
            ->first();
    }

    public function processNextInQueue()
    {
        $next = $this->getNextInQueue();
        
        if ($next) {
            $availableCondition = BookCondition::where('book_id', $this->book_id)
                ->where('is_available', true)
                ->first();

            if ($availableCondition) {
                $next->book_condition_id = $availableCondition->id;
                $next->markAsReady();
                
                $this->current_queue++;
                $this->save();
                
                return $next;
            }
        }
        
        return null;
    }
}