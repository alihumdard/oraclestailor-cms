<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = [
        'name',
        'gender',
        'phone',
        'email',
        'password',
        'measurements',
        'measurement_unit',
        'measurement_notes',
        'measured_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'measurements' => 'array',
            'measured_at' => 'date',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(TailorOrder::class);
    }

    public function measurementHistories(): HasMany
    {
        return $this->hasMany(MeasurementHistory::class)->latest('measured_at');
    }
}
