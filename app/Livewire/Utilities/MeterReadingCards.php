<?php

namespace App\Livewire\Utilities;

use App\Models\Property;
use App\Models\Utility;
use App\Models\UtilityUsage;
use App\Support\MeterReadingQuery;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Mobile-friendly card view of utility meter readings, with CSV / Excel / PDF
 * export. Shares its query with the export controller via MeterReadingQuery so
 * the cards on screen and the exported file always contain the same rows.
 */
class MeterReadingCards extends Component
{
    use WithPagination;

    public $selectedProperty = '';
    public $selectedUtility = '';
    public $selectedYear;
    public $selectedMonth;

    protected $queryString = [
        'selectedProperty' => ['except' => ''],
        'selectedUtility' => ['except' => ''],
        'selectedYear' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
    ];

    public function mount()
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
        $this->selectedMonth = $this->selectedMonth ?? now()->month;
    }

    public function updating($name)
    {
        // Any filter change should reset pagination.
        if (str_starts_with($name, 'selected')) {
            $this->resetPage();
        }
    }

    public function getPropertiesProperty()
    {
        $user = Auth::user();

        $query = Property::query();

        if (! $user->hasRole('admin')) {
            $query->where('landlord_id', $user->user_id);
        }

        return $query->orderBy('property_name')
            ->pluck('property_name', 'property_id')
            ->toArray();
    }

    public function getUtilitiesProperty()
    {
        return Utility::orderBy('utility_name')
            ->pluck('utility_name', 'utility_id')
            ->toArray();
    }

    public function getYearsProperty()
    {
        $firstUsage = UtilityUsage::orderBy('usage_date')->first();
        $startYear = $firstUsage ? Carbon::parse($firstUsage->usage_date)->year : now()->year;

        return range($startYear, now()->year);
    }

    public function getMonthsProperty()
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    }

    /** Current filters as a query-string array for the export links. */
    public function getExportParamsProperty(): array
    {
        return array_filter([
            'property' => $this->selectedProperty,
            'utility' => $this->selectedUtility,
            'year' => $this->selectedYear,
            'month' => $this->selectedMonth,
        ], fn ($v) => $v !== '' && $v !== null);
    }

    /** Resolve a tenant's display name from a reading's rental. */
    private function tenantName($usage): string
    {
        $tenant = $usage?->rental?->tenant;
        if (! $tenant) {
            return '—';
        }

        $name = trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? ''));

        return $name !== '' ? $name : ($tenant->email ?? $tenant->username ?? '—');
    }

    public function render()
    {
        $filters = [
            'property' => $this->selectedProperty,
            'utility' => $this->selectedUtility,
            'year' => $this->selectedYear,
            'month' => $this->selectedMonth,
        ];

        // Group every matching reading by room so each room shows as a single
        // card listing all of its utility readings, rather than one card each.
        $rooms = MeterReadingQuery::build($filters, Auth::user())
            ->get()
            ->groupBy('room_id')
            ->map(function ($readings) {
                $readings = $readings->sortByDesc('usage_date')->values();
                $latest = $readings->first();

                return [
                    'room_id' => $latest->room_id,
                    'property_name' => $latest->property_name,
                    'room_number' => $latest->room_number,
                    'tenant' => $this->tenantName($latest),
                    'readings' => $readings,
                    'total_charge' => $readings->sum(fn ($u) => (float) $u->calculateCharge()),
                    'latest_date' => $latest->usage_date,
                ];
            })
            ->sortByDesc('latest_date')
            ->values();

        // Manual pagination over the grouped rooms (9 cards per page).
        $perPage = 9;
        $page = $this->getPage();
        $usages = new LengthAwarePaginator(
            $rooms->forPage($page, $perPage)->values(),
            $rooms->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page']
        );

        return view('livewire.utilities.meter-reading-cards', [
            'usages' => $usages,
            'properties' => $this->properties,
            'utilities' => $this->utilities,
            'years' => $this->years,
            'months' => $this->months,
            'exportParams' => $this->exportParams,
        ]);
    }
}
