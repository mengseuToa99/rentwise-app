<?php

namespace App\Livewire\Utilities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Utility;
use App\Models\UtilityUsage;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UtilityUsageHistory extends Component
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
        'selectedMonth' => ['except' => '']
    ];
    
    public function mount()
    {
        // Set default year and month to current
        $this->selectedYear = $this->selectedYear ?? now()->year;
        $this->selectedMonth = $this->selectedMonth ?? now()->month;
    }
    
    public function getPropertiesProperty()
    {
        $user = Auth::user();
        
        $query = Property::query();
        
        if (!$user->hasRole('admin')) {
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
        $currentYear = now()->year;
        
        return range($startYear, $currentYear);
    }
    
    public function getMonthsProperty()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }
    
    public function render()
    {
        $query = UtilityUsage::query()
            ->join('utilities', 'utility_usages.utility_id', '=', 'utilities.utility_id')
            ->join('room_details', 'utility_usages.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select(
                'utility_usages.*',
                'utilities.utility_name',
                'room_details.room_number',
                'property_details.property_name'
            );
            
        // Apply filters
        if ($this->selectedProperty) {
            $query->where('property_details.property_id', $this->selectedProperty);
        }
        
        if ($this->selectedUtility) {
            $query->where('utilities.utility_id', $this->selectedUtility);
        }
        
        if ($this->selectedYear && $this->selectedMonth) {
            $query->whereYear('usage_date', $this->selectedYear)
                  ->whereMonth('usage_date', $this->selectedMonth);
        }
        
        $usages = $query->orderBy('usage_date', 'desc')
            ->paginate(10);
            
        return view('livewire.utilities.utility-usage-history', [
            'usages' => $usages,
            'properties' => $this->properties,
            'utilities' => $this->utilities,
            'years' => $this->years,
            'months' => $this->months
        ]);
    }
} 