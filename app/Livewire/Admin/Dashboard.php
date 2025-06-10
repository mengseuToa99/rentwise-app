<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\User;
use App\Models\Rental;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $stats = [];
    public $propertyCount = 0;
    public $rentalCount = 0;
    public $userCount = 0;
    public $revenueSummary = [];
    public $maintenanceSummary = [];
    public $timeframe = 'month';
    
    public function mount()
    {
        // Check if user has admin role
        if (!Auth::user()->roles->contains(function($role) {
            return strtolower($role->role_name) === 'admin';
        })) {
            abort(403, 'Unauthorized access');
        }
        
        $this->loadStats();
    }
    
    public function loadStats()
    {
        // Basic stats
        $this->propertyCount = Property::count();
        $this->rentalCount = Rental::where('status', 'active')->count();
        $this->userCount = User::count();
        
        // User stats by role
        $this->stats['users_by_role'] = User::join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->select('roles.role_name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.role_name')
            ->get()
            ->pluck('count', 'role_name')
            ->toArray();
        
        // Property occupancy rate
        $totalUnits = \App\Models\Unit::count();
        $occupiedUnits = \App\Models\Unit::where('status', 'occupied')->count();
        $this->stats['occupancy_rate'] = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 2) : 0;
        
        // Financial stats
        $this->loadFinancialStats();
        
        // Maintenance stats
        $this->loadMaintenanceStats();
        
        // Calendar events
        $this->loadCalendarEvents();
    }
    
    public function loadFinancialStats()
    {
        // Payment status summary
        $this->stats['payment_status'] = Invoice::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('count', 'payment_status')
            ->toArray();
        
        // Calculate total revenue
        $this->stats['total_revenue'] = Invoice::where('payment_status', 'paid')->sum('amount_due');
        
        // Calculate pending payments
        $this->stats['pending_payments'] = Invoice::where('payment_status', 'pending')->sum('amount_due');
        
        // Calculate overdue payments
        $this->stats['overdue_payments'] = Invoice::where('payment_status', 'overdue')->sum('amount_due');
        
        // Revenue by time period (month by default)
        $dateColumn = 'created_at';
        $format = 'YYYY-MM';
        $days = 30;
        
        if ($this->timeframe == 'year') {
            $format = 'YYYY';
            $days = 365;
        } elseif ($this->timeframe == 'week') {
            $format = 'YYYY-IW'; // Year and ISO week number
            $days = 7;
        }
        
        $this->revenueSummary = Invoice::where('payment_status', 'paid')
            ->where($dateColumn, '>=', now()->subDays($days))
            ->select(DB::raw("TO_CHAR($dateColumn, '$format') as period"), DB::raw('SUM(amount_due) as revenue'))
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();
    }
    
    public function loadMaintenanceStats()
    {
        // Maintenance requests by status
        $this->stats['maintenance_by_status'] = MaintenanceRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        // Maintenance requests by priority instead of category
        $this->stats['maintenance_by_priority'] = MaintenanceRequest::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();
        
        // Average resolution time (for completed requests)
        $completedRequests = MaintenanceRequest::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();
        $totalResolutionTime = 0;
        $count = 0;
        
        foreach ($completedRequests as $request) {
            $created = Carbon::parse($request->created_at);
            $completed = Carbon::parse($request->completed_at);
            $totalResolutionTime += $created->diffInHours($completed);
            $count++;
        }
        
        $this->stats['avg_resolution_time'] = $count > 0 ? round($totalResolutionTime / $count, 2) : 0;
        
        // Recent maintenance requests
        $this->maintenanceSummary = MaintenanceRequest::with(['tenant', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
    
    public function updatedTimeframe()
    {
        $this->loadFinancialStats();
    }
    
    // Add calendar events to stats
    public function loadCalendarEvents()
    {
        // Initialize empty calendar events array
        $this->stats['calendarEvents'] = [];
        
        try {
            // Add upcoming invoice due dates
            $upcomingInvoices = Invoice::where('payment_status', 'pending')
                ->whereDate('due_date', '>=', now())
                ->whereDate('due_date', '<=', now()->addDays(30))
                ->get();
                
            foreach ($upcomingInvoices as $invoice) {
                $this->stats['calendarEvents'][] = [
                    'id' => 'invoice-' . $invoice->invoice_id,
                    'title' => 'Invoice Due: $' . number_format($invoice->amount_due, 2),
                    'start' => $invoice->due_date,
                    'color' => '#f43f5e', // Red
                    'description' => 'Invoice #' . $invoice->invoice_number . ' is due'
                ];
            }
            
            // Add maintenance appointments
            $upcomingMaintenance = MaintenanceRequest::whereIn('status', ['approved', 'pending'])
                ->whereNotNull('scheduled_date')
                ->whereDate('scheduled_date', '>=', now())
                ->whereDate('scheduled_date', '<=', now()->addDays(30))
                ->get();
                
            foreach ($upcomingMaintenance as $maintenance) {
                $this->stats['calendarEvents'][] = [
                    'id' => 'maintenance-' . $maintenance->request_id,
                    'title' => 'Maintenance: ' . ucfirst($maintenance->category),
                    'start' => $maintenance->scheduled_date,
                    'color' => '#3b82f6', // Blue
                    'description' => $maintenance->description
                ];
            }
        } catch (\Exception $e) {
            // If anything fails, just leave the array empty
            \Log::error('Error loading calendar events: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
} 