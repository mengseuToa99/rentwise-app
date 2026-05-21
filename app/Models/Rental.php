<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Rental extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'rental_details';
    protected $primaryKey = 'rental_id';
    public $timestamps = true;

    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'security_deposit',
        'agreement_file_path',
        'terms_conditions',
        'signed_by_tenant',
        'signed_by_landlord',
        'signed_at',
        'lease_agreement',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime',
        'signed_by_tenant' => 'boolean',
        'signed_by_landlord' => 'boolean',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id', 'user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id', 'user_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'room_id', 'room_id');
    }

    /** Backwards-compat alias used by some code (lease_agreement code path) */
    public function room()
    {
        return $this->unit();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rental_id', 'rental_id');
    }

    public function property()
    {
        return $this->hasOneThrough(
            Property::class,
            Unit::class,
            'room_id',     // FK on units
            'property_id', // PK on properties
            'room_id',     // FK on rentals
            'property_id'  // FK on units
        );
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function utilityUsages()
    {
        return $this->hasMany(UtilityUsage::class, 'rental_id', 'rental_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'rental_id', 'rental_id');
    }

    /**
     * Total amount used for a specific utility during this tenancy.
     */
    public function totalUtilityAmount(int $utilityId): float
    {
        return (float) $this->utilityUsages()
            ->where('utility_id', $utilityId)
            ->sum('amount_used');
    }

    /**
     * Per-utility breakdown for this tenancy:
     *   [['utility_id' => 1, 'utility_name' => 'Electricity', 'total' => 450.5], ...]
     */
    public function utilityBreakdown(): array
    {
        return $this->utilityUsages()
            ->selectRaw('utility_id, SUM(amount_used) as total')
            ->groupBy('utility_id')
            ->with('utility:utility_id,utility_name,unit_of_measure')
            ->get()
            ->map(fn ($row) => [
                'utility_id' => $row->utility_id,
                'utility_name' => $row->utility?->utility_name,
                'unit_of_measure' => $row->utility?->unit_of_measure,
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (!$this->end_date || now()->lte($this->end_date));
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->status === 'active' && $this->end_date && now()->gt($this->end_date));
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeForTenant($q, $tenantId)
    {
        return $q->where('tenant_id', $tenantId);
    }

    public function scopeForLandlord($q, $landlordId)
    {
        return $q->where('landlord_id', $landlordId);
    }

    protected static function booted()
    {
        static::created(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logCreated('rental', "Rental #{$rental->rental_id}", "New rental agreement: {$tenantName} -> {$unitName}");

            $unit = $rental->unit;
            if ($unit) {
                $unit->available = false;
                $unit->status = 'occupied';
                $unit->save();
            }
        });

        static::updated(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logUpdated('rental', "Rental #{$rental->rental_id}", "Rental updated: {$tenantName} -> {$unitName} (Status: {$rental->status})");

            $unit = $rental->unit;
            if ($unit) {
                if (in_array($rental->status, ['expired', 'terminated', 'cancelled'])) {
                    $unit->available = true;
                    $unit->status = 'vacant';
                } elseif ($rental->status === 'active') {
                    $unit->available = false;
                    $unit->status = 'occupied';
                }
                $unit->save();
            }
        });

        static::deleted(function ($rental) {
            $tenantName = $rental->tenant ? $rental->tenant->username : 'Unknown Tenant';
            $unitName = $rental->unit ? ($rental->unit->room_name ?: "Room #{$rental->unit->room_number}") : 'Unknown Unit';
            $rental->logDeleted('rental', "Rental #{$rental->rental_id}", "Rental terminated: {$tenantName} -> {$unitName}");

            $unit = $rental->unit;
            if ($unit) {
                $unit->available = true;
                $unit->status = 'vacant';
                $unit->save();
            }
        });
    }
}
