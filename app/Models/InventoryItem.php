<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'unit', 'quantity', 'low_stock_threshold'];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'low_stock_threshold' => 'float',
        ];
    }

    public function getIsLowAttribute(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }
}
