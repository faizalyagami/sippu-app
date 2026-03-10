<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeAdmin($query)
    {
        return $query->where('name', 'admin');
    }

    public function scopeKaprodi($query)
    {
        return $query->where('name', 'kaprodi');
    }

    public function scopeSupplier($query)
    {
        return $query->where('name', 'supplier');
    }
}