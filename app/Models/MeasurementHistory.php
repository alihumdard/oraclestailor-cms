<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementHistory extends Model
{
    protected $fillable = [
        'customer_id',
        'tailor_order_id',
        'measurements',
        'measurement_unit',
        'measurement_notes',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'measurements' => 'array',
            'measured_at' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(TailorOrder::class, 'tailor_order_id');
    }
}
