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
        'utilityUsage' => [],
        'landlordIncomeHistory' => [],
        'landlordOccupancyHistory' => [],
        'landlordRentCollection' => []
    ];

    public function mount()
    {
        $this->loadDashboardStats();
        
        // Load tenant-specific data if user is a tenant
        $user = Auth::user();
        if ($user && $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'tenant';
        })) {
            $this->loadTenantSpendingHistory($user);
            $this->loadTenantUtilityUsage($user);
        }
        
        // Load landlord-specific data if user is a landlord
        if ($user && $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            $this->loadLandlordIncomeHistory($user);
            $this->loadLandlordOccupancyHistory($user);
            $this->loadLandlordRentCollection($user);
        }
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
            $monthlyIncome = Invoice::select(
                    DB::raw('EXTRACT(MONTH FROM updated_at) as month'),
                    DB::raw('SUM(amount_due) as total_amount')
                )
                ->where('payment_status', 'paid')
                ->whereYear('updated_at', $currentYear)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM updated_at)'))
                ->orderBy('month')
                ->get();
                
            // Initialize all months with zero
            $monthlyIncomeData = array_fill(1, 12, 0);
            
            // Fill in actual data
            foreach ($monthlyIncome as $record) {
                $monthlyIncomeData[(int)$record->month] = $record->total_amount;
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
                ->select(DB::raw('EXTRACT(MONTH FROM updated_at) as month'), DB::raw('SUM(amount_due) as total_amount'))
                ->where('payment_status', 'paid')
                ->whereYear('updated_at', $currentYear)
                ->groupBy(DB::raw('EXTRACT(MONTH FROM updated_at)'))
                ->orderBy('month')
                ->get();
                
            // Initialize all months with zero
            $monthlyIncomeData = array_fill(1, 12, 0);
            
            // Fill in actual data
            foreach ($monthlyIncome as $record) {
                $monthlyIncomeData[(int)$record->month] = $record->total_amount;
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
        
        \Log::info('Loading calendar events for user: ' . $user->user_id . ' with roles: ' . $user->roles->pluck('role_name')->implode(', '));
        
        if ($user->roles->contains(function($role) { return strtolower($role->role_name) === 'admin'; })) {
            \Log::info('Loading admin calendar events');
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
            \Log::info('Loading landlord calendar events');
            // Get landlord's properties
            $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
            $units = Unit::whereIn('property_id', $properties)->pluck('room_id')->toArray();
            $rentals = Rental::whereIn('room_id', $units)->pluck('rental_id')->toArray();
            
            \Log::info('Landlord has ' . count($properties) . ' properties, ' . count($units) . ' units, ' . count($rentals) . ' rentals');
            
            // Landlord's upcoming invoices due dates
            $invoiceDueDates = Invoice::whereIn('rental_id', $rentals)
                ->where('payment_status', 'pending')
                ->where('due_date', '>=', now()->startOfMonth())
                ->where('due_date', '<=', now()->addMonths(3)->endOfMonth())
                ->with(['rental', 'rental.tenant', 'rental.unit'])
                ->get();
                
            \Log::info('Found ' . $invoiceDueDates->count() . ' upcoming invoices for landlord');
                
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
                
            \Log::info('Found ' . $leaseExpirations->count() . ' expiring leases for landlord');
                
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
            \Log::info('Loading tenant calendar events');
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
        
        \Log::info('Total calendar events generated: ' . count($events));
        $this->stats['calendarEvents'] = $events;
    }

    /**
     * Load tenant spending history for charts
     */
    protected function loadTenantSpendingHistory($user)
    {
        // Get tenant ID
        if (!$user) {
            $this->addDefaultSpendingData();
            return;
        }
        
        // Get past 12 months of invoices
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        
        // Use direct query to get all tenant's invoices with payment_status = 'paid'
        $invoices = Invoice::whereHas('rental', function($query) use ($user) {
                $query->where('tenant_id', $user->user_id);
            })
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();
            
        // If no invoices exist, add sample data for demo purposes
        if ($invoices->isEmpty()) {
            $this->addSampleSpendingData();
            return;
        }
            
        $monthlyData = [];
        
        // Initialize with zero values for all months
        for ($i = 0; $i < 12; $i++) {
            $monthDate = Carbon::now()->subMonths(11 - $i);
            $month = $monthDate->format('M Y');
            $monthlyData[$month] = [
                'total' => 0,
                'month_num' => $monthDate->month,
                'year' => $monthDate->year
            ];
        }
        
        // Fill with actual invoice data
        foreach ($invoices as $invoice) {
            $invoiceDate = Carbon::parse($invoice->created_at);
            $month = $invoiceDate->format('M Y');
            
            if (isset($monthlyData[$month])) {
                $monthlyData[$month]['total'] += $invoice->amount_due;
            }
        }
        
        // Extract data for the chart
        $labels = array_keys($monthlyData);
        $amounts = array_column($monthlyData, 'total');
        
        // Format data for ApexCharts
        $this->stats['spendingHistory'] = [
            'apex' => [
                'labels' => $labels,
                'amounts' => $amounts
            ]
        ];
    }
    
    /**
     * Load tenant utility usage data for charts
     */
    protected function loadTenantUtilityUsage($user)
    {
        // If no user or not a tenant, use sample data
        if (!$user) {
            $this->addSampleUtilityData();
            return;
        }
        
        // Get all utility-related invoices
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $utilityInvoices = Invoice::whereHas('rental', function($query) use ($user) {
                $query->where('tenant_id', $user->user_id);
            })
            ->where(function($query) {
                $query->where('payment_status', 'paid')
                      ->orWhere('payment_status', 'pending');
            })
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();
            
        // If no invoices exist, use sample data
        if ($utilityInvoices->isEmpty()) {
            $this->addSampleUtilityData();
            return;
        }
        
        // Setup data arrays
        $months = [];
        $electricityData = [];
        $waterData = [];
        $gasData = [];
        $monthlyUsage = [];
        
        // Initialize with zero values for all months
        for ($i = 0; $i < 12; $i++) {
            $monthDate = Carbon::now()->subMonths(11 - $i);
            $monthKey = $monthDate->format('m-Y');
            $monthDisplay = $monthDate->format('M');
            
            $months[] = $monthDisplay;
            $monthlyUsage[$monthKey] = [
                'electricity' => 0,
                'water' => 0,
                'gas' => 0
            ];
        }
        
        // Calculate average unit costs for estimation
        $electricityRate = 0.15; // $0.15 per kWh
        $waterRate = 0.01;       // $0.01 per gallon
        $gasRate = 1.50;         // $1.50 per therm
        
        // Process invoices
        foreach ($utilityInvoices as $invoice) {
            $date = Carbon::parse($invoice->created_at);
            $monthKey = $date->format('m-Y');
            
            // For simplicity and since we don't have detailed utility breakdowns,
            // we'll distribute the total amount across utilities in a realistic ratio
            $totalAmount = $invoice->amount_due;
            
            // Split based on season - more electricity in summer, more gas in winter
            $month = $date->month;
            
            if ($month >= 6 && $month <= 8) {
                // Summer: 60% electricity, 30% water, 10% gas
                $electricityAmount = $totalAmount * 0.6;
                $waterAmount = $totalAmount * 0.3;
                $gasAmount = $totalAmount * 0.1;
            } elseif ($month >= 12 || $month <= 2) {
                // Winter: 30% electricity, 20% water, 50% gas
                $electricityAmount = $totalAmount * 0.3;
                $waterAmount = $totalAmount * 0.2;
                $gasAmount = $totalAmount * 0.5;
            } else {
                // Spring/Fall: 40% electricity, 40% water, 20% gas
                $electricityAmount = $totalAmount * 0.4;
                $waterAmount = $totalAmount * 0.4;
                $gasAmount = $totalAmount * 0.2;
            }
            
            // Convert amounts to usage units
            $electricityUsage = $electricityAmount / $electricityRate;
            $waterUsage = $waterAmount / $waterRate;
            $gasUsage = $gasAmount / $gasRate;
            
            // Add to monthly totals
            $monthlyUsage[$monthKey]['electricity'] += $electricityUsage;
            $monthlyUsage[$monthKey]['water'] += $waterUsage;
            $monthlyUsage[$monthKey]['gas'] += $gasUsage;
        }
        
        // Extract the data in the correct order
        foreach ($monthlyUsage as $data) {
            $electricityData[] = round($data['electricity']);
            $waterData[] = round($data['water']);
            $gasData[] = round($data['gas']);
        }
        
        $this->stats['utilityUsage'] = [
            'electricity' => [
                'labels' => $months,
                'data' => $electricityData
            ],
            'water' => [
                'labels' => $months,
                'data' => $waterData
            ],
            'gas' => [
                'labels' => $months,
                'data' => $gasData
            ],
            // ApexCharts format
            'apex' => [
                'labels' => $months,
                'electricity' => $electricityData,
                'water' => $waterData,
                'gas' => $gasData
            ]
        ];
    }

    /**
     * Add default spending data when no tenant data exists
     */
    protected function addDefaultSpendingData()
    {
        $labels = [];
        $amounts = [];
        
        for ($i = 0; $i < 12; $i++) {
            $labels[] = Carbon::now()->subMonths(11 - $i)->format('M Y');
            $amounts[] = 0; // Zero values
        }
        
        $this->stats['spendingHistory'] = [
            'apex' => [
                'labels' => $labels,
                'amounts' => $amounts
            ]
        ];
    }
    
    /**
     * Add sample spending data for demonstration when no real data exists
     */
    protected function addSampleSpendingData()
    {
        $labels = [];
        $amounts = [];
        
        // Generate sample data for 12 months
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths(11 - $i)->format('M Y');
            $labels[] = $month;
            
            // Generate realistic looking random data with slight upward trend
            $base = 1000 + ($i * 20); // Base amount increases slightly each month
            $variation = rand(-100, 150); // Random variation
            $amount = $base + $variation;
            
            $amounts[] = $amount;
        }
        
        // Set the spending history with sample data
        $this->stats['spendingHistory'] = [
            'apex' => [
                'labels' => $labels,
                'amounts' => $amounts
            ]
        ];
    }
    
    /**
     * Add sample utility data when no real data exists
     */
    protected function addSampleUtilityData()
    {
        $months = [];
        $electricityData = [];
        $waterData = [];
        $gasData = [];
        
        // Generate last 12 months of utility data
        for ($i = 0; $i < 12; $i++) {
            $months[] = Carbon::now()->subMonths(11 - $i)->format('M');
            
            // Create realistic patterns with seasonal variations
            $monthNum = (Carbon::now()->month - 11 + $i) % 12; // 0 = January, 11 = December
            if ($monthNum < 0) $monthNum += 12;
            
            // Electricity - higher in summer months (air conditioning)
            $baseElectricity = 280;
            $seasonalFactor = $monthNum >= 5 && $monthNum <= 8 ? 1.4 : 1.0; // Summer months (June-Sep)
            $electricityData[] = round($baseElectricity * $seasonalFactor * (0.9 + (mt_rand(0, 20) / 100)));
            
            // Water - higher in summer months (lawn watering)
            $baseWater = 1400;
            $waterSeasonalFactor = $monthNum >= 5 && $monthNum <= 8 ? 1.3 : 1.0;
            $waterData[] = round($baseWater * $waterSeasonalFactor * (0.9 + (mt_rand(0, 20) / 100)));
            
            // Gas - higher in winter months (heating)
            $baseGas = 40;
            $gasSeasonalFactor = $monthNum <= 2 || $monthNum >= 10 ? 1.5 : 1.0; // Winter months
            $gasData[] = round($baseGas * $gasSeasonalFactor * (0.9 + (mt_rand(0, 20) / 100)));
        }
        
        $this->stats['utilityUsage'] = [
            'electricity' => [
                'labels' => $months,
                'data' => $electricityData
            ],
            'water' => [
                'labels' => $months,
                'data' => $waterData
            ],
            'gas' => [
                'labels' => $months,
                'data' => $gasData
            ],
            // ApexCharts format
            'apex' => [
                'labels' => $months,
                'electricity' => $electricityData,
                'water' => $waterData,
                'gas' => $gasData
            ]
        ];
    }

    /**
     * Load landlord income history data for income chart
     */
    protected function loadLandlordIncomeHistory($user)
    {
        if (!$user) {
            $this->addSampleLandlordIncomeData();
            return;
        }
        
        // Get past 12 months of income
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        
        // Query for all invoices paid to this landlord's properties
        $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
        
        if (empty($properties)) {
            $this->addSampleLandlordIncomeData();
            return;
        }
        
        $invoices = Invoice::whereHas('rental.unit', function($query) use ($properties) {
                $query->whereIn('property_id', $properties);
            })
            ->where('payment_status', 'paid')
            ->where('updated_at', '>=', $startDate)
            ->orderBy('updated_at')
            ->get();
            
        // If no invoices exist, add sample data for demo purposes
        if ($invoices->isEmpty()) {
            $this->addSampleLandlordIncomeData();
            return;
        }
        
        $monthlyData = [];
        
        // Initialize with zero values for all months
        for ($i = 0; $i < 12; $i++) {
            $monthDate = Carbon::now()->subMonths(11 - $i);
            $month = $monthDate->format('M Y');
            $monthlyData[$month] = [
                'total' => 0,
                'month_num' => $monthDate->month,
                'year' => $monthDate->year
            ];
        }
        
        // Fill with actual invoice data
        foreach ($invoices as $invoice) {
            $invoiceDate = Carbon::parse($invoice->updated_at);
            $month = $invoiceDate->format('M Y');
            
            if (isset($monthlyData[$month])) {
                $monthlyData[$month]['total'] += $invoice->amount_due;
            }
        }
        
        // Extract data for the chart
        $labels = array_keys($monthlyData);
        $amounts = array_column($monthlyData, 'total');
        
        // Format data for ApexCharts
        $this->stats['landlordIncomeHistory'] = [
            'apex' => [
                'labels' => $labels,
                'amounts' => $amounts
            ]
        ];
    }

    /**
     * Load landlord occupancy rate history data
     */
    protected function loadLandlordOccupancyHistory($user)
    {
        if (!$user) {
            $this->addSampleLandlordOccupancyData();
            return;
        }
        
        // Get properties owned by this landlord
        $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
        
        if (empty($properties)) {
            $this->addSampleLandlordOccupancyData();
            return;
        }
        
        // Calculate occupancy rates for the past 12 months
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $occupancyData = [];
        $labels = [];
        
        // We'll query rentals table to determine historical occupancy
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = Carbon::now()->subMonths(11 - $i);
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            $monthLabel = $currentMonth->format('M Y');
            
            $labels[] = $monthLabel;
            
            // Count total units
            $totalUnits = Unit::whereIn('property_id', $properties)->count();
            
            if ($totalUnits === 0) {
                $occupancyData[] = 0;
                continue;
            }
            
            // Count occupied units (units with active rentals during that month)
            $occupiedUnits = Rental::whereHas('unit', function($query) use ($properties) {
                    $query->whereIn('property_id', $properties);
                })
                ->where(function($query) use ($monthStart, $monthEnd) {
                    $query->where(function($q) use ($monthStart, $monthEnd) {
                        // Rental was active during the month
                        $q->where('start_date', '<=', $monthEnd)
                          ->where(function($subQ) use ($monthStart) {
                              $subQ->where('end_date', '>=', $monthStart)
                                  ->orWhereNull('end_date');
                          });
                    });
                })
                ->count();
            
            // Calculate occupancy rate
            $occupancyRate = ($occupiedUnits / $totalUnits) * 100;
            $occupancyData[] = round($occupancyRate);
        }
        
        // Format data for ApexCharts
        $this->stats['landlordOccupancyHistory'] = [
            'apex' => [
                'labels' => $labels,
                'rates' => $occupancyData
            ]
        ];
    }

    /**
     * Load landlord rent collection performance (paid vs pending)
     */
    protected function loadLandlordRentCollection($user)
    {
        if (!$user) {
            $this->addSampleLandlordRentCollectionData();
            return;
        }
        
        // Get properties owned by this landlord
        $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
        
        if (empty($properties)) {
            $this->addSampleLandlordRentCollectionData();
            return;
        }
        
        // Calculate rent collection stats for the past 6 months
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $pendingAmounts = [];
        $paidAmounts = [];
        $labels = [];
        
        for ($i = 0; $i < 6; $i++) {
            $currentMonth = Carbon::now()->subMonths(5 - $i);
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            $monthLabel = $currentMonth->format('M Y');
            
            $labels[] = $monthLabel;
            
            // Get invoices due in this month
            $paidAmount = Invoice::whereHas('rental.unit', function($query) use ($properties) {
                    $query->whereIn('property_id', $properties);
                })
                ->where('payment_status', 'paid')
                ->whereBetween('due_date', [$monthStart, $monthEnd])
                ->sum('amount_due');
                
            $pendingAmount = Invoice::whereHas('rental.unit', function($query) use ($properties) {
                    $query->whereIn('property_id', $properties);
                })
                ->where('payment_status', 'pending')
                ->whereBetween('due_date', [$monthStart, $monthEnd])
                ->sum('amount_due');
            
            $paidAmounts[] = round($paidAmount);
            $pendingAmounts[] = round($pendingAmount);
        }
        
        // Format data for ApexCharts
        $this->stats['landlordRentCollection'] = [
            'apex' => [
                'labels' => $labels,
                'paid' => $paidAmounts,
                'pending' => $pendingAmounts
            ]
        ];
    }

    /**
     * Add sample landlord income data when no real data exists
     */
    protected function addSampleLandlordIncomeData()
    {
        $labels = [];
        $amounts = [];
        
        // Generate sample data for 12 months
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths(11 - $i)->format('M Y');
            $labels[] = $month;
            
            // Generate realistic looking random data (higher than tenant spending)
            $base = 5000 + ($i * 100); // Base amount increases each month
            $variation = rand(-500, 700); // Random variation
            $amount = $base + $variation;
            
            $amounts[] = $amount;
        }
        
        // Set the income history with sample data
        $this->stats['landlordIncomeHistory'] = [
            'apex' => [
                'labels' => $labels,
                'amounts' => $amounts
            ]
        ];
    }

    /**
     * Add sample landlord occupancy data when no real data exists
     */
    protected function addSampleLandlordOccupancyData()
    {
        $labels = [];
        $rates = [];
        
        // Generate sample data for 12 months
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths(11 - $i)->format('M Y');
            $labels[] = $month;
            
            // Generate realistic occupancy rates (typically 70-95%)
            $baseRate = 80;
            $variation = rand(-10, 15);
            $rate = min(98, max(65, $baseRate + $variation));
            
            $rates[] = $rate;
        }
        
        // Set the occupancy history with sample data
        $this->stats['landlordOccupancyHistory'] = [
            'apex' => [
                'labels' => $labels,
                'rates' => $rates
            ]
        ];
    }

    /**
     * Add sample landlord rent collection data when no real data exists
     */
    protected function addSampleLandlordRentCollectionData()
    {
        $labels = [];
        $paidAmounts = [];
        $pendingAmounts = [];
        
        // Generate sample data for past 6 months
        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths(5 - $i)->format('M Y');
            $labels[] = $month;
            
            // Generate realistic rent collection data
            $totalRent = 8000 + rand(-1000, 1000);
            $collectionRate = rand(75, 95) / 100; // 75% to 95% collection rate
            
            $paidAmount = round($totalRent * $collectionRate);
            $pendingAmount = $totalRent - $paidAmount;
            
            $paidAmounts[] = $paidAmount;
            $pendingAmounts[] = $pendingAmount;
        }
        
        // Set the rent collection with sample data
        $this->stats['landlordRentCollection'] = [
            'apex' => [
                'labels' => $labels,
                'paid' => $paidAmounts,
                'pending' => $pendingAmounts
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