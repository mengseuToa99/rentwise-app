<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\User;
use App\Models\Rental;
use App\Models\InvoiceDetail;
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
        if (!Auth::user()->roles->contains('role_name', 'Admin')) {
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
        $totalUnits = \App\Models\RoomDetail::count();
        $occupiedUnits = \App\Models\RoomDetail::where('status', 'occupied')->count();
        $this->stats['occupancy_rate'] = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 2) : 0;
        
        // Financial stats
        $this->loadFinancialStats();
        
        // Maintenance stats
        $this->loadMaintenanceStats();
    }
    
    public function loadFinancialStats()
    {
        // Payment status summary
        $this->stats['payment_status'] = InvoiceDetail::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('count', 'payment_status')
            ->toArray();
        
        // Calculate total revenue
        $this->stats['total_revenue'] = InvoiceDetail::where('payment_status', 'paid')->sum('amount_due');
        
        // Calculate pending payments
        $this->stats['pending_payments'] = InvoiceDetail::where('payment_status', 'pending')->sum('amount_due');
        
        // Calculate overdue payments
        $this->stats['overdue_payments'] = InvoiceDetail::where('payment_status', 'overdue')->sum('amount_due');
        
        // Revenue by time period (month by default)
        $dateColumn = 'created_at';
        $format = '%Y-%m';
        $days = 30;
        
        if ($this->timeframe == 'year') {
            $format = '%Y';
            $days = 365;
        } elseif ($this->timeframe == 'week') {
            $format = '%Y-%u'; // Year and week number
            $days = 7;
        }
        
        $this->revenueSummary = InvoiceDetail::where('payment_status', 'paid')
            ->where($dateColumn, '>=', now()->subDays($days))
            ->select(DB::raw("DATE_FORMAT($dateColumn, '$format') as period"), DB::raw('SUM(amount_due) as revenue'))
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
        
        // Maintenance requests by category
        $this->stats['maintenance_by_category'] = MaintenanceRequest::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();
        
        // Average resolution time (for completed requests)
        $completedRequests = MaintenanceRequest::where('status', 'completed')->get();
        $totalResolutionTime = 0;
        $count = 0;
        
        foreach ($completedRequests as $request) {
            $created = Carbon::parse($request->created_at);
            $updated = Carbon::parse($request->updated_at);
            $totalResolutionTime += $created->diffInHours($updated);
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
    
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
} 