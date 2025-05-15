<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $stats = [
        'totalProperties' => 0,
        'totalUnits' => 0,
        'occupiedUnits' => 0,
        'vacantUnits' => 0,
        'totalRentals' => 0,
        'activeRentals' => 0,
        'pendingInvoices' => 0,
        'paidInvoices' => 0,
        'totalIncome' => 0,
        'pendingIncome' => 0,
        'propertiesByLandlord' => [],
        'topProperties' => [],
        'unitTypeDistribution' => [],
        'unitsByStatus' => [],
        'occupancyRate' => 0,
        'upcomingInvoices' => [],
        'recentPayments' => [],
        'expiringLeases' => [],
        'maintenanceTickets' => [],
        'monthlyIncomeStats' => [],
        'calendarEvents' => [],
        'spendingHistory' => [],
        'utilityUsage' => []
    ];

    public function mount()
    {
        $this->loadDashboardStats();
    }

    public function loadDashboardStats()
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        // Get user roles or empty collection if null
        $userRoles = $user->roles ?? collect([]);
        
        // For admin, show all stats
        if ($userRoles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            $this->stats['totalProperties'] = Property::count();
            $this->stats['totalUnits'] = Unit::count();
            $this->stats['occupiedUnits'] = Unit::where('status', 'occupied')->count();
            $this->stats['vacantUnits'] = Unit::where('status', 'vacant')->count();
            $this->stats['totalRentals'] = Rental::count();
            $this->stats['activeRentals'] = Rental::where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = Invoice::where('payment_status', 'paid')->sum('amount_due');
            $this->stats['pendingIncome'] = Invoice::where('payment_status', 'pending')->sum('amount_due');
            
            // Calculate occupancy rate
            $this->stats['occupancyRate'] = $this->stats['totalUnits'] > 0 ? 
                round(($this->stats['occupiedUnits'] / $this->stats['totalUnits']) * 100) : 0;

            // Properties by landlord - group by landlord and count
            $this->stats['propertiesByLandlord'] = Property::select('landlord_id')
                ->selectRaw('COUNT(*) as property_count')
                ->groupBy('landlord_id')
                ->with('landlord:user_id,first_name,last_name')
                ->orderByDesc('property_count')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'landlord_name' => $item->landlord ? $item->landlord->first_name . ' ' . $item->landlord->last_name : 'Unknown',
                        'count' => $item->property_count
                    ];
                });

            // Top properties by unit count
            $this->stats['topProperties'] = Property::select('property_id', 'property_name')
                ->withCount('units')
                ->orderByDesc('units_count')
                ->limit(5)
                ->get()
                ->map(function($property) {
                    $occupiedCount = Unit::where('property_id', $property->property_id)
                        ->where('status', 'occupied')
                        ->count();
                    return [
                        'name' => $property->property_name,
                        'total_units' => $property->units_count,
                        'occupied_units' => $occupiedCount,
                        'occupancy_rate' => $property->units_count > 0 ? 
                            round(($occupiedCount / $property->units_count) * 100) : 0
                    ];
                });

            // Unit type distribution
            $this->stats['unitTypeDistribution'] = Unit::select('room_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('room_type')
                ->orderByDesc('count')
                ->get()
                ->map(function($item) {
                    return [
                        'type' => $item->room_type ?: 'Not specified',
                        'count' => $item->count
                    ];
                });

            // Units by status
            $this->stats['unitsByStatus'] = [
                ['status' => 'Occupied', 'count' => $this->stats['occupiedUnits']],
                ['status' => 'Vacant', 'count' => $this->stats['vacantUnits']]
            ];
            
            // Get upcoming invoices due in next 30 days
            $this->stats['upcomingInvoices'] = Invoice::where('payment_status', 'pending')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(30))
                ->with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
                ->orderBy('due_date')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'due_date' => $invoice->due_date->format('M d, Y'),
                        'tenant_name' => $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number,
                        'days_until_due' => now()->diffInDays($invoice->due_date, false)
                    ];
                });
                
            // Get recent payments in last 30 days
            $this->stats['recentPayments'] = Invoice::where('payment_status', 'paid')
                ->where('updated_at', '>=', now()->subDays(30))
                ->with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'paid_date' => $invoice->updated_at->format('M d, Y'),
                        'tenant_name' => $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number
                    ];
                });
                
            // Get leases expiring in next 90 days
            $this->stats['expiringLeases'] = Rental::where('status', 'active')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(90))
                ->with(['tenant', 'unit', 'unit.property'])
                ->orderBy('end_date')
                ->limit(10)
                ->get()
                ->map(function($rental) {
                    return [
                        'id' => $rental->rental_id,
                        'end_date' => $rental->end_date->format('M d, Y'),
                        'tenant_name' => $rental->tenant->first_name . ' ' . $rental->tenant->last_name,
                        'property_name' => $rental->unit->property->property_name,
                        'unit_name' => $rental->unit->room_number,
                        'days_until_expiry' => now()->diffInDays($rental->end_date, false)
                    ];
                });
                
            // Monthly income statistics for the current year
            $currentYear = date('Y');
            $monthlyIncome = Invoice::select(DB::raw('MONTH(updated_at) as month'), DB::raw('SUM(amount_due) as total_amount'))
                ->where('payment_status', 'paid')
                ->whereYear('updated_at', $currentYear)
                ->groupBy(DB::raw('MONTH(updated_at)'))
                ->orderBy('month')
                ->get();
                
            // Initialize all months with zero
            $monthlyIncomeData = array_fill(1, 12, 0);
            
            // Fill in actual data
            foreach ($monthlyIncome as $record) {
                $monthlyIncomeData[$record->month] = $record->total_amount;
            }
            
            // Format for chart
            $this->stats['monthlyIncomeStats'] = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => array_values($monthlyIncomeData)
            ];
            
            // Calendar events
            $this->loadCalendarEvents();
        } 
        // For landlord, show only their properties
        elseif ($userRoles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
            $units = Unit::whereIn('property_id', $properties)->pluck('room_id')->toArray();
            $rentals = Rental::whereIn('room_id', $units)->pluck('rental_id')->toArray();
            
            $this->stats['totalProperties'] = count($properties);
            $this->stats['totalUnits'] = count($units);
            $this->stats['occupiedUnits'] = Unit::whereIn('room_id', $units)->where('status', 'occupied')->count();
            $this->stats['vacantUnits'] = Unit::whereIn('room_id', $units)->where('status', 'vacant')->count();
            $this->stats['totalRentals'] = count($rentals);
            $this->stats['activeRentals'] = Rental::whereIn('rental_id', $rentals)->where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->sum('amount_due');
            $this->stats['pendingIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->sum('amount_due');
            
            // Calculate occupancy rate
            $this->stats['occupancyRate'] = $this->stats['totalUnits'] > 0 ? 
                round(($this->stats['occupiedUnits'] / $this->stats['totalUnits']) * 100) : 0;
                
            // Get upcoming invoices due in next 30 days
            $this->stats['upcomingInvoices'] = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'pending')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(30))
                ->with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
                ->orderBy('due_date')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'due_date' => $invoice->due_date->format('M d, Y'),
                        'tenant_name' => $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number,
                        'days_until_due' => now()->diffInDays($invoice->due_date, false)
                    ];
                });
                
            // Get recent payments in last 30 days
            $this->stats['recentPayments'] = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'paid')
                ->where('updated_at', '>=', now()->subDays(30))
                ->with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'paid_date' => $invoice->updated_at->format('M d, Y'),
                        'tenant_name' => $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number
                    ];
                });
                
            // Get leases expiring in next 90 days
            $this->stats['expiringLeases'] = Rental::whereIn('rental_id', $rentals)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(90))
                ->with(['tenant', 'unit', 'unit.property'])
                ->orderBy('end_date')
                ->limit(10)
                ->get()
                ->map(function($rental) {
                    return [
                        'id' => $rental->rental_id,
                        'end_date' => $rental->end_date->format('M d, Y'),
                        'tenant_name' => $rental->tenant->first_name . ' ' . $rental->tenant->last_name,
                        'property_name' => $rental->unit->property->property_name,
                        'unit_name' => $rental->unit->room_number,
                        'days_until_expiry' => now()->diffInDays($rental->end_date, false)
                    ];
                });
                
            // Monthly income statistics for the current year
            $currentYear = date('Y');
            $monthlyIncome = Invoice::whereIn('rental_id', $rentals)
                ->select(DB::raw('MONTH(updated_at) as month'), DB::raw('SUM(amount_due) as total_amount'))
                ->where('payment_status', 'paid')
                ->whereYear('updated_at', $currentYear)
                ->groupBy(DB::raw('MONTH(updated_at)'))
                ->orderBy('month')
                ->get();
                
            // Initialize all months with zero
            $monthlyIncomeData = array_fill(1, 12, 0);
            
            // Fill in actual data
            foreach ($monthlyIncome as $record) {
                $monthlyIncomeData[$record->month] = $record->total_amount;
            }
            
            // Format for chart
            $this->stats['monthlyIncomeStats'] = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => array_values($monthlyIncomeData)
            ];
            
            // Calendar events
            $this->loadCalendarEvents();
        }
        // For tenant, show only their related data
        elseif ($userRoles->contains(function($role) {
            return strtolower($role->role_name) === 'tenant';
        })) {
            $rentals = Rental::where('tenant_id', $user->user_id)->pluck('rental_id')->toArray();
            
            $this->stats['totalRentals'] = count($rentals);
            $this->stats['activeRentals'] = Rental::where('tenant_id', $user->user_id)->where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = 0; // Tenants don't see income
            $this->stats['pendingIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->sum('amount_due');
            
            // Get upcoming invoices due in next 30 days (for tenant)
            $this->stats['upcomingInvoices'] = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'pending')
                ->where('due_date', '>=', now())
                ->with(['rental', 'rental.unit', 'rental.unit.property'])
                ->orderBy('due_date')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'due_date' => $invoice->due_date->format('M d, Y'),
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number,
                        'days_until_due' => now()->diffInDays($invoice->due_date, false)
                    ];
                });
                
            // Get recent payments in last 30 days (for tenant)
            $this->stats['recentPayments'] = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'paid')
                ->where('updated_at', '>=', now()->subDays(30))
                ->with(['rental', 'rental.unit', 'rental.unit.property'])
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->invoice_id,
                        'amount' => $invoice->amount_due,
                        'paid_date' => $invoice->updated_at->format('M d, Y'),
                        'property_name' => $invoice->rental->unit->property->property_name,
                        'unit_name' => $invoice->rental->unit->room_number
                    ];
                });
                
            // Get tenant's active leases with expiry dates
            $this->stats['expiringLeases'] = Rental::where('tenant_id', $user->user_id)
                ->where('status', 'active')
                ->with(['unit', 'unit.property'])
                ->orderBy('end_date')
                ->get()
                ->map(function($rental) {
                    return [
                        'id' => $rental->rental_id,
                        'start_date' => $rental->start_date->format('M d, Y'),
                        'end_date' => $rental->end_date->format('M d, Y'),
                        'property_name' => $rental->unit->property->property_name,
                        'unit_name' => $rental->unit->room_number,
                        'days_until_expiry' => now()->diffInDays($rental->end_date, false)
                    ];
                });
                
            // Calendar events for tenant
            $this->loadCalendarEvents();

            // Add spending history data
            $this->loadTenantSpendingHistory($user);
            
            // Add utility usage data
            $this->loadTenantUtilityUsage($user);
        }
        // Default case for other roles
        else {
            // Handle other roles or default scenario
        }
    }
    
    protected function loadCalendarEvents()
    {
        $user = Auth::user();
        $events = [];
        
        if ($user->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; })) {
            // All upcoming invoices due dates
            $invoiceDueDates = Invoice::where('payment_status', 'pending')
                ->where('due_date', '>=', now()->startOfMonth())
                ->where('due_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['rental', 'rental.tenant', 'rental.unit'])
                ->get();
                
            foreach ($invoiceDueDates as $invoice) {
                $events[] = [
                    'id' => 'inv-' . $invoice->invoice_id,
                    'title' => 'Invoice Due: $' . number_format($invoice->amount_due, 2),
                    'start' => $invoice->due_date->format('Y-m-d'),
                    'description' => 'Invoice #' . $invoice->invoice_id . ' due for ' . 
                        $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                    'type' => 'invoice',
                    'color' => '#EF4444' // Red color
                ];
            }
            
            // All lease expirations
            $leaseExpirations = Rental::where('status', 'active')
                ->where('end_date', '>=', now()->startOfMonth())
                ->where('end_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['tenant', 'unit', 'unit.property'])
                ->get();
                
            foreach ($leaseExpirations as $lease) {
                $events[] = [
                    'id' => 'lease-' . $lease->rental_id,
                    'title' => 'Lease Expiry: ' . $lease->tenant->first_name . ' ' . $lease->tenant->last_name,
                    'start' => $lease->end_date->format('Y-m-d'),
                    'description' => 'Lease expiry for ' . $lease->unit->property->property_name . 
                        ' - Unit ' . $lease->unit->room_number,
                    'type' => 'lease',
                    'color' => '#F59E0B' // Amber color
                ];
            }
        } 
        elseif ($user->roles->contains(function($role) { return strtolower($role->role_name) === 'landlord'; })) {
            // Get landlord's properties
            $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
            $units = Unit::whereIn('property_id', $properties)->pluck('room_id')->toArray();
            $rentals = Rental::whereIn('room_id', $units)->pluck('rental_id')->toArray();
            
            // Landlord's upcoming invoices due dates
            $invoiceDueDates = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'pending')
                ->where('due_date', '>=', now()->startOfMonth())
                ->where('due_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['rental', 'rental.tenant', 'rental.unit'])
                ->get();
                
            foreach ($invoiceDueDates as $invoice) {
                $events[] = [
                    'id' => 'inv-' . $invoice->invoice_id,
                    'title' => 'Invoice Due: $' . number_format($invoice->amount_due, 2),
                    'start' => $invoice->due_date->format('Y-m-d'),
                    'description' => 'Invoice #' . $invoice->invoice_id . ' due for ' . 
                        $invoice->rental->tenant->first_name . ' ' . $invoice->rental->tenant->last_name,
                    'type' => 'invoice',
                    'color' => '#EF4444' // Red color
                ];
            }
            
            // Landlord's lease expirations
            $leaseExpirations = Rental::whereIn('rental_id', $rentals)
                ->where('status', 'active')
                ->where('end_date', '>=', now()->startOfMonth())
                ->where('end_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['tenant', 'unit', 'unit.property'])
                ->get();
                
            foreach ($leaseExpirations as $lease) {
                $events[] = [
                    'id' => 'lease-' . $lease->rental_id,
                    'title' => 'Lease Expiry: ' . $lease->tenant->first_name . ' ' . $lease->tenant->last_name,
                    'start' => $lease->end_date->format('Y-m-d'),
                    'description' => 'Lease expiry for ' . $lease->unit->property->property_name . 
                        ' - Unit ' . $lease->unit->room_number,
                    'type' => 'lease',
                    'color' => '#F59E0B' // Amber color
                ];
            }
        } 
        elseif ($user->roles->contains(function($role) { return strtolower($role->role_name) === 'tenant'; })) {
            // Get tenant's rentals
            $rentals = Rental::where('tenant_id', $user->user_id)->pluck('rental_id')->toArray();
            
            // Tenant's upcoming invoices due dates
            $invoiceDueDates = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'pending')
                ->where('due_date', '>=', now()->startOfMonth())
                ->where('due_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['rental', 'rental.unit'])
                ->get();
                
            foreach ($invoiceDueDates as $invoice) {
                $events[] = [
                    'id' => 'inv-' . $invoice->invoice_id,
                    'title' => 'Invoice Due: $' . number_format($invoice->amount_due, 2),
                    'start' => $invoice->due_date->format('Y-m-d'),
                    'description' => 'Invoice #' . $invoice->invoice_id . ' for ' . 
                        $invoice->rental->unit->property->property_name . ' - Unit ' . 
                        $invoice->rental->unit->room_number,
                    'type' => 'invoice',
                    'color' => '#EF4444' // Red color
                ];
            }
            
            // Tenant's lease expirations
            $leaseExpirations = Rental::whereIn('rental_id', $rentals)
                ->where('status', 'active')
                ->with(['unit', 'unit.property'])
                ->get();
                
            foreach ($leaseExpirations as $lease) {
                $events[] = [
                    'id' => 'lease-' . $lease->rental_id,
                    'title' => 'Lease Expiry',
                    'start' => $lease->end_date->format('Y-m-d'),
                    'description' => 'Your lease expiry for ' . $lease->unit->property->property_name . 
                        ' - Unit ' . $lease->unit->room_number,
                    'type' => 'lease',
                    'color' => '#F59E0B' // Amber color
                ];
            }
        }
        
        $this->stats['calendarEvents'] = $events;
    }

    /**
     * Load tenant spending history for charts
     */
    protected function loadTenantSpendingHistory($user)
    {
        // Get tenant ID
        $tenant = $user->tenant;
        if (!$tenant) {
            $this->addDefaultSpendingData();
            return;
        }
        
        // Get past 12 months of invoices
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $invoices = $tenant->invoices()
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();
            
        $monthlyData = [];
        
        // Initialize with zero values for all months
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths(11 - $i)->format('M Y');
            $monthlyData[$month] = [
                'rent' => 0,
                'utilities' => 0,
                'other' => 0
            ];
        }
        
        // Fill with actual invoice data
        foreach ($invoices as $invoice) {
            $month = Carbon::parse($invoice->created_at)->format('M Y');
            
            if (isset($monthlyData[$month])) {
                if ($invoice->type === 'rent') {
                    $monthlyData[$month]['rent'] += $invoice->amount_due;
                } elseif ($invoice->type === 'utility') {
                    $monthlyData[$month]['utilities'] += $invoice->amount_due;
                } else {
                    $monthlyData[$month]['other'] += $invoice->amount_due;
                }
            }
        }
        
        // Format for chart.js
        $this->formatSpendingChartData($monthlyData);
    }
    
    /**
     * Format spending data for Chart.js
     */
    protected function formatSpendingChartData($monthlyData)
    {
        $labels = array_keys($monthlyData);
        $rentData = array_column($monthlyData, 'rent');
        $utilitiesData = array_column($monthlyData, 'utilities');
        $otherData = array_column($monthlyData, 'other');
        
        // Format data for six months and twelve months
        $this->stats['spendingHistory'] = [
            'six_months' => [
                'labels' => array_slice($labels, -6),
                'datasets' => [
                    [
                        'label' => 'Rent',
                        'data' => array_slice($rentData, -6),
                        'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Utilities',
                        'data' => array_slice($utilitiesData, -6),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Other Fees',
                        'data' => array_slice($otherData, -6),
                        'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                        'borderColor' => 'rgba(245, 158, 11, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ]
                ]
            ],
            'twelve_months' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Rent',
                        'data' => $rentData,
                        'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Utilities',
                        'data' => $utilitiesData,
                        'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Other Fees',
                        'data' => $otherData,
                        'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                        'borderColor' => 'rgba(245, 158, 11, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Add default spending data when no tenant data exists
     */
    protected function addDefaultSpendingData()
    {
        $sixMonthLabels = [];
        $twelveMonthLabels = [];
        
        for ($i = 0; $i < 6; $i++) {
            $sixMonthLabels[] = Carbon::now()->subMonths(5 - $i)->format('M Y');
        }
        
        for ($i = 0; $i < 12; $i++) {
            $twelveMonthLabels[] = Carbon::now()->subMonths(11 - $i)->format('M Y');
        }
        
        $this->stats['spendingHistory'] = [
            'six_months' => [
                'labels' => $sixMonthLabels,
                'datasets' => [
                    [
                        'label' => 'Rent',
                        'data' => array_fill(0, 6, 0),
                        'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Utilities',
                        'data' => array_fill(0, 6, 0),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Other Fees',
                        'data' => array_fill(0, 6, 0),
                        'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                        'borderColor' => 'rgba(245, 158, 11, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ]
                ]
            ],
            'twelve_months' => [
                'labels' => $twelveMonthLabels,
                'datasets' => [
                    [
                        'label' => 'Rent',
                        'data' => array_fill(0, 12, 0),
                        'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Utilities',
                        'data' => array_fill(0, 12, 0),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ],
                    [
                        'label' => 'Other Fees',
                        'data' => array_fill(0, 12, 0),
                        'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                        'borderColor' => 'rgba(245, 158, 11, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.3
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Load tenant utility usage data for charts
     */
    protected function loadTenantUtilityUsage($user)
    {
        // For demonstration, we'll return sample utility data
        // In a real app, this would come from a utility_usage table or similar
        
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $months[] = Carbon::now()->subMonths(5 - $i)->format('M');
        }
        
        $this->stats['utilityUsage'] = [
            'electricity' => [
                'labels' => $months,
                'data' => [320, 350, 300, 360, 380, 340]
            ],
            'water' => [
                'labels' => $months,
                'data' => [1500, 1600, 1450, 1700, 1550, 1500]
            ],
            'gas' => [
                'labels' => $months,
                'data' => [50, 65, 55, 40, 30, 35]
            ]
        ];
    }

    public function render()
    {
        // If user is admin, use admin layout, otherwise use default
        $layout = 'components.layouts.app';
        if (Auth::user()->roles->contains('role_name', 'admin')) {
            $layout = 'layouts.admin';
        }
        
        return view('livewire.dashboard')
            ->layout($layout);
    }
} 