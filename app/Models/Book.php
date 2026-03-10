<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'publication_year',
        'category_id',
        'description',
        'language',
        'pages',
        'cover_image',
        'location_rack',
        'total_stock',
        'available_stock',
        'borrowed_stock',
        'damaged_stock',
        'lost_stock',
        'price',
        'is_active'
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'total_stock' => 'integer',
        'available_stock' => 'integer',
        'borrowed_stock' => 'integer',
        'damaged_stock' => 'integer',
        'lost_stock' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function procurementItems()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function borrowingItems()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function bookConditions()
    {
        return $this->hasMany(BookCondition::class);
    }

    public function bookReviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function reservations()
    {
        return $this->hasMany(BookReservation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('publisher', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getCoverUrlAttribute()
    {
        return $this->cover_image 
            ? asset('storage/' . $this->cover_image) 
            : asset('images/default-book-cover.jpg');
    }

    public function getStockStatusAttribute()
    {
        if ($this->available_stock > 10) {
            return 'Tersedia Banyak';
        } elseif ($this->available_stock > 0) {
            return 'Tersedia ' . $this->available_stock . ' Eksemplar';
        } else {
            return 'Stok Habis';
        }
    }

    public function getStockStatusColorAttribute()
    {
        if ($this->available_stock > 10) {
            return 'success';
        } elseif ($this->available_stock > 0) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    // Methods
    public function decreaseStock($quantity = 1)
    {
        if ($this->available_stock >= $quantity) {
            $this->available_stock -= $quantity;
            $this->borrowed_stock += $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function increaseStock($quantity = 1)
    {
        $this->available_stock += $quantity;
        $this->total_stock += $quantity;
        $this->save();
        return true;
    }

    public function returnBook($quantity = 1)
    {
        if ($this->borrowed_stock >= $quantity) {
            $this->available_stock += $quantity;
            $this->borrowed_stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }
}