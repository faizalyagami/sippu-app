<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role_id',
        'phone_number',
        'nip',
        'department',
        'faculty',
        'address',
        'photo',
        'is_active',
        'email_verified_at',
        'supplier_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function approvedBorrowings()
    {
        return $this->hasMany(Borrowing::class, 'approved_by');
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function bookReviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
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

    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // Accessors
    public function getPhotoUrlAttribute()
    {
        return $this->photo 
            ? asset('storage/' . $this->photo) 
            : asset('images/default-avatar.png');
    }

    public function getIsAdminAttribute()
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function getIsKaprodiAttribute()
    {
        return $this->role && $this->role->name === 'kaprodi';
    }

    public function getIsSupplierAttribute()
    {
        return $this->role && $this->role->name === 'supplier';
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getActiveBorrowingsCountAttribute()
    {
        return $this->borrowings()
            ->whereIn('status', ['approved', 'borrowed'])
            ->count();
    }

    // Methods
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function hasAnyRole(array $roles)
    {
        return $this->role && in_array($this->role->name, $roles);
    }
}