<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TailorOrder extends Model
{
    public const STATUSES = [
        'new',
        'confirmed',
        'cutting',
        'stitching',
        'fitting',
        'ready',
        'delivered',
        'cancelled',
    ];

    public const STATUS_LABELS = [
        'new' => 'New enquiry',
        'confirmed' => 'Confirmed',
        'cutting' => 'Cutting',
        'stitching' => 'Stitching',
        'fitting' => 'Fitting',
        'ready' => 'Ready for pickup',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const GARMENTS = [
        'Shalwar kameez',
        'Two-piece suit',
        'Three-piece suit',
        'Waistcoat',
        'Sherwani',
        'Blazer',
    ];

    public const PACKAGES = [
        'everyday' => 'Everyday tailoring',
        'premium' => 'Premium finish',
        'occasion' => 'Occasion wear',
    ];

    public const FABRICS = [
        'Lawn',
        'Cotton',
        'Wash & wear',
        'Linen',
        'Karandi',
        'Blended suiting',
        'My own fabric',
    ];

    public const GENDERS = [
        'male' => 'Male',
        'female' => 'Female',
        'kids' => 'Kids',
    ];

    public const SHIRT_FIELDS = [
        'shirt_length' => 'Shirt length (Lambai)',
        'chest' => 'Chest (Chhati)',
        'shoulder' => 'Shoulder (Tera)',
        'sleeve' => 'Sleeve (Bazu)',
        'neck' => 'Neck (Gala)',
        'waist' => 'Waist (Kamar)',
    ];

    public const TROUSER_FIELDS = [
        'trouser_length' => 'Trouser length',
        'trouser_waist' => 'Trouser waist',
        'seat' => 'Hip (Seat)',
        'thigh' => 'Thigh',
        'bottom' => 'Bottom (Pancha)',
    ];

    public const VEST_FIELDS = [
        'vest_length' => 'Vest length',
        'vest_chest' => 'Vest chest',
        'vest_shoulder' => 'Vest shoulder',
    ];

    public const MEASUREMENT_FIELDS = self::SHIRT_FIELDS + self::TROUSER_FIELDS + self::VEST_FIELDS;

    public static function inchOptions(): array
    {
        return array_map(fn (int $half): float => $half / 2, range(16, 120));
    }

    protected $fillable = [
        'customer_id',
        'assigned_to',
        'garment',
        'package',
        'fabric',
        'standard_size',
        'measurement_unit',
        'measurements',
        'style_notes',
        'status',
        'due_date',
        'total',
        'advance',
    ];

    protected function casts(): array
    {
        return [
            'measurements' => 'array',
            'due_date' => 'date',
            'total' => 'integer',
            'advance' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getBalanceAttribute(): int
    {
        return max(0, $this->total - $this->advance);
    }
}
