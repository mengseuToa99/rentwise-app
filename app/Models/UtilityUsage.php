<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityUsage extends Model
{
    use HasFactory;

    protected $table = 'utility_usages';
    protected $primaryKey = 'usage_id';
    public $timestamps = true;

    protected $fillable = [
        'meter_id',
        'room_id',
        'utility_id',
        'rental_id',
        'recorded_by_user_id',
        'reading_type',
        'usage_date',
        'period_start',
        'period_end',
        'old_meter_reading',
        'new_meter_reading',
        'amount_used',
        'notes',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'old_meter_reading' => 'decimal:3',
        'new_meter_reading' => 'decimal:3',
        'amount_used' => 'decimal:3',
    ];

    public function utility()
    {
        return $this->belongsTo(Utility::class, 'utility_id', 'utility_id');
    }

    public function room()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    public function meter()
    {
        return $this->belongsTo(UtilityMeter::class, 'meter_id', 'meter_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id', 'user_id');
    }

    /**
     * Resolve the rental that was active for this room on the usage_date —
     * used to auto-populate rental_id when a reading is recorded.
     */
    public function resolveActiveRental(): ?Rental
    {
        if (!$this->room_id || !$this->usage_date) {
            return null;
        }
        return Rental::where('room_id', $this->room_id)
            ->where('start_date', '<=', $this->usage_date)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $this->usage_date);
            })
            ->whereIn('status', ['active', 'expired', 'terminated'])
            ->orderByDesc('start_date')
            ->first();
    }

    public function calculateUsage()
    {
        return $this->new_meter_reading - $this->old_meter_reading;
    }

    public function calculateCharge()
    {
        $propertyId = $this->meter?->property_id ?? $this->room?->property_id;
        $price = $this->utility->getCurrentPrice($propertyId);
        if (!$price) {
            return 0;
        }
        return $this->amount_used * $price->price;
    }

    public function scopeForRental($q, $rentalId)
    {
        return $q->where('rental_id', $rentalId);
    }

    public function scopeForTenant($q, $tenantId)
    {
        return $q->whereHas('rental', fn ($r) => $r->where('tenant_id', $tenantId));
    }

    public function scopeForUtility($q, $utilityId)
    {
        return $q->where('utility_id', $utilityId);
    }

    /**
     * Auto-attribute reading to the active rental on save if not already set.
     */
    protected static function booted()
    {
        static::saving(function (UtilityUsage $usage) {
            if (!$usage->rental_id && $usage->room_id && $usage->usage_date) {
                $rental = $usage->resolveActiveRental();
                if ($rental) {
                    $usage->rental_id = $rental->rental_id;
                }
            }
        });
    }
}
