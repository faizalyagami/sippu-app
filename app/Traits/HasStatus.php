<?php
// app/Traits/HasStatus.php

namespace App\Traits;

trait HasStatus
{
    public function getStatusBadgeAttribute()
    {
        $badges = $this->statusBadges ?? [];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = $this->statusTexts ?? [];
        return $texts[$this->status] ?? $this->status;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function activate()
    {
        $this->status = 'active';
        $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }
}