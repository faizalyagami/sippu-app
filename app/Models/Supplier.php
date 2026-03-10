<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone_number',
        'address',
        'npwp',
        'contact_person',
        'cp_phone',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function procurements()
    {
        return $this->hasMany(Procurement::class, 'vendor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalProcurementsAttribute()
    {
        return $this->procurements()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->procurements()
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }
}