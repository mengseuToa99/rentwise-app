<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SystemLogs extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    
    public $search = '';
    public $actionFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];
    
    public function mount()
    {
        // Check if user has permission to view logs
        if (!Auth::user()->hasPermission('view_system_logs')) {
            abort(403, 'Unauthorized action.');
        }
    }
    
    public function render()
    {
        $query = Log::query()->with('user');
        
        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('action', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('username', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Apply action type filter
        if (!empty($this->actionFilter)) {
            $query->where('action', $this->actionFilter);
        }
        
        // Apply date range filter
        if (!empty($this->dateFrom)) {
            $query->whereDate('timestamp', '>=', $this->dateFrom);
        }
        
        if (!empty($this->dateTo)) {
            $query->whereDate('timestamp', '<=', $this->dateTo);
        }
        
        // Order by most recent first
        $query->orderBy('timestamp', 'desc');
        
        // Get unique action types for filter dropdown
        $actionTypes = Log::select('action')->distinct()->pluck('action');
        
        return view('livewire.admin.system-logs', [
            'logs' => $query->paginate(15),
            'actionTypes' => $actionTypes
        ])
        ->layout('layouts.admin');
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function clearFilters()
    {
        $this->search = '';
        $this->actionFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
    }
    
    public function exportLogs()
    {
        // This would be implemented to export logs to CSV or PDF
        session()->flash('info', 'Export functionality will be implemented soon.');
    }
} 