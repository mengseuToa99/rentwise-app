<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\UtilityUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantStats = $this->getTenantStats();
        return view('dashboard', ['tenantStats' => $tenantStats]);
    }

    private function getTenantStats()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $stats = [];

        // Get tenant's invoices
        $invoices = $tenant ? $tenant->invoices()->with('unit.property')->get() : collect();
        
        // Get pending and paid invoices
        $stats['pendingInvoices'] = $invoices->where('status', 'pending')->count();
        $stats['paidInvoices'] = $invoices->where('status', 'paid')->count();
        
        // Get upcoming invoices (due in next 30 days)
        $upcomingInvoices = $invoices->where('status', 'pending')
            ->sortBy('due_date')
            ->take(5)
            ->map(function ($invoice) {
                $dueDate = Carbon::parse($invoice->due_date);
                $daysUntilDue = Carbon::now()->diffInDays($dueDate, false);
                
                return [
                    'id' => $invoice->id,
                    'property_name' => $invoice->unit->property->property_name ?? 'Unknown Property',
                    'unit_name' => $invoice->unit->unit_number ?? 'Unknown Unit',
                    'amount' => $invoice->amount,
                    'due_date' => $dueDate->format('M d, Y'),
                    'days_until_due' => $daysUntilDue
                ];
            });
        
        $stats['upcomingInvoices'] = $upcomingInvoices;
        
        // Get tenant's leases
        $leases = $tenant ? $tenant->leases()->with('unit.property')->get() : collect();
        
        // Get expiring leases (expiring in next 90 days)
        $expiringLeases = $leases->where('status', 'active')
            ->map(function ($lease) {
                $endDate = Carbon::parse($lease->end_date);
                $daysUntilExpiry = Carbon::now()->diffInDays($endDate, false);
                
                return [
                    'id' => $lease->id,
                    'property_name' => $lease->unit->property->property_name ?? 'Unknown Property',
                    'unit_name' => $lease->unit->unit_number ?? 'Unknown Unit',
                    'end_date' => $endDate->format('M d, Y'),
                    'days_until_expiry' => $daysUntilExpiry
                ];
            })
            ->sortBy('days_until_expiry');
        
        $stats['expiringLeases'] = $expiringLeases;

        // Add spending history data for charts
        $stats['spendingHistory'] = $this->getTenantSpendingHistory($tenant);
        
        // Add utility usage data for charts
        $stats['utilityUsage'] = $this->getTenantUtilityUsage($tenant);
        
        return $stats;
    }
    
    /**
     * Get spending history data for tenant charts
     * 
     * @param Tenant $tenant
     * @return array
     */
    private function getTenantSpendingHistory($tenant)
    {
        if (!$tenant) {
            return [
                'six_months' => $this->getDefaultSpendingData(6),
                'twelve_months' => $this->getDefaultSpendingData(12)
            ];
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
                    $monthlyData[$month]['rent'] += $invoice->amount;
                } elseif ($invoice->type === 'utility') {
                    $monthlyData[$month]['utilities'] += $invoice->amount;
                } else {
                    $monthlyData[$month]['other'] += $invoice->amount;
                }
            }
        }
        
        // Format for chart.js
        $result = [
            'six_months' => $this->formatChartData(array_slice($monthlyData, -6, 6)),
            'twelve_months' => $this->formatChartData($monthlyData)
        ];
        
        return $result;
    }
    
    /**
     * Get utility usage data for tenant charts
     * 
     * @param Tenant $tenant
     * @return array
     */
    private function getTenantUtilityUsage($tenant)
    {
        // For demonstration, we'll return sample utility data
        // In a real app, this would come from a utility_usage table or similar
        
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $months[] = Carbon::now()->subMonths(5 - $i)->format('M');
        }
        
        return [
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
    
    /**
     * Format monthly data for Chart.js
     * 
     * @param array $monthlyData
     * @return array
     */
    private function formatChartData($monthlyData)
    {
        $labels = array_keys($monthlyData);
        $rentData = array_column($monthlyData, 'rent');
        $utilitiesData = array_column($monthlyData, 'utilities');
        $otherData = array_column($monthlyData, 'other');
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rent',
                    'data' => $rentData
                ],
                [
                    'label' => 'Utilities',
                    'data' => $utilitiesData
                ],
                [
                    'label' => 'Other Fees',
                    'data' => $otherData
                ]
            ]
        ];
    }
    
    /**
     * Get default spending data when no invoices exist
     * 
     * @param int $months
     * @return array
     */
    private function getDefaultSpendingData($months)
    {
        $labels = [];
        $rentData = [];
        $utilitiesData = [];
        $otherData = [];
        
        for ($i = 0; $i < $months; $i++) {
            $labels[] = Carbon::now()->subMonths($months - 1 - $i)->format('M Y');
            $rentData[] = 0;
            $utilitiesData[] = 0;
            $otherData[] = 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rent',
                    'data' => $rentData
                ],
                [
                    'label' => 'Utilities',
                    'data' => $utilitiesData
                ],
                [
                    'label' => 'Other Fees',
                    'data' => $otherData
                ]
            ]
        ];
    }
} 