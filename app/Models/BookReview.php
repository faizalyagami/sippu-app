<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'book_id',
        'user_id',
        'borrowing_id',
        'rating',
        'review',
        'pros',
        'cons',
        'is_recommended',
        'read_status',
        'tags',
        'helpful_count',
        'unhelpful_count',
        'is_approved',
        'approved_by'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_recommended' => 'boolean',
        'is_approved' => 'boolean',
        'tags' => 'array',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer'
    ];

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpfulVote::class, 'review_id');
    }

    public function comments()
    {
        return $this->hasMany(BookReviewComment::class, 'review_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    // Accessors
    public function getRatingStarsAttribute()
    {
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars
        ];
    }

    public function getReadStatusTextAttribute()
    {
        $texts = [
            'belum_dibaca' => 'Belum Dibaca',
            'sedang_dibaca' => 'Sedang Dibaca',
            'selesai_dibaca' => 'Selesai Dibaca'
        ];
        
        return $texts[$this->read_status] ?? $this->read_status;
    }

    // Methods
    public function incrementHelpful()
    {
        $this->increment('helpful_count');
    }

    public function incrementUnhelpful()
    {
        $this->increment('unhelpful_count');
    }

    public function hasUserVoted($userId)
    {
        return $this->helpfulVotes()->where('user_id', $userId)->exists();
    }

    public function getUserVote($userId)
    {
        return $this->helpfulVotes()->where('user_id', $userId)->first();
    }
}