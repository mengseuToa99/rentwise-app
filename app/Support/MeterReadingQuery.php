<?php

namespace App\Support;

use App\Models\UtilityUsage;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared query builder for utility meter readings.
 *
 * Used by both the card-view Livewire page (paginated display) and the
 * export controller (CSV / Excel / PDF) so the rows shown and the rows
 * exported always match. Mirrors the joins used by the legacy table view
 * but adds landlord scoping so a landlord only ever sees their own data.
 */
class MeterReadingQuery
{
    /**
     * @param  array{property?:mixed,utility?:mixed,year?:mixed,month?:mixed}  $filters
     * @param  \App\Models\User  $user
     */
    public static function build(array $filters, $user): Builder
    {
        $query = UtilityUsage::query()
            ->join('utilities', 'utility_usages.utility_id', '=', 'utilities.utility_id')
            ->join('room_details', 'utility_usages.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->with(['utility', 'meter', 'room', 'rental.tenant'])
            ->select(
                'utility_usages.*',
                'utilities.utility_name',
                'room_details.room_number',
                'property_details.property_name'
            );

        // A landlord may only see readings for their own properties.
        if (! $user->hasRole('admin')) {
            $query->where('property_details.landlord_id', $user->user_id);
        }

        if (! empty($filters['property'])) {
            $query->where('property_details.property_id', $filters['property']);
        }

        if (! empty($filters['utility'])) {
            $query->where('utilities.utility_id', $filters['utility']);
        }

        if (! empty($filters['year']) && ! empty($filters['month'])) {
            $query->whereYear('usage_date', $filters['year'])
                ->whereMonth('usage_date', $filters['month']);
        }

        return $query->orderBy('usage_date', 'desc');
    }
}
