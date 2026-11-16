<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name',
        'tagline',
        'address_line',
        'city',
        'region',
        'country',
        'phone',
        'email',
        'hours',
        'next_eid_date',
    ];

    protected function casts(): array
    {
        return [
            'next_eid_date' => 'date',
        ];
    }

    public static function current(): self
    {
        return static::first() ?? static::create([
            'business_name' => 'Saim Tailors',
            'tagline' => 'One tailor. Every detail.',
            'address_line' => 'Main Bazaar',
            'city' => 'Sargodha',
            'region' => 'Punjab',
            'country' => 'Pakistan',
            'phone' => '+92 318 3998120',
            'email' => 'info@saimtailors.com',
            'hours' => 'Mon – Sat, 10am – 8pm',
        ]);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address_line, $this->city, $this->region, $this->country])
            ->filter()
            ->implode(', ');
    }
}
