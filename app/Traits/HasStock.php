<?php

namespace App\Traits;

trait HasStock
{
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

    public function returnStock($quantity = 1)
    {
        if ($this->borrowed_stock >= $quantity) {
            $this->available_stock += $quantity;
            $this->borrowed_stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    public function hasStock($quantity = 1)
    {
        return $this->available_stock >= $quantity;
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
}