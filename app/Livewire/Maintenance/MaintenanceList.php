<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class MaintenanceList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $perPage = 10;
    
    protected $queryString = ['search', 'statusFilter', 'priorityFilter', 'perPage'];
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        
        $query = MaintenanceRequest::query()
            ->with(['tenant', 'property', 'room'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('property', function($q) {
                          $q->where('property_name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('room', function($q) {
                          $q->where('room_number', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function($query) {
                $query->where('priority', $this->priorityFilter);
            });
        
        // Filter based on user role
        if ($user->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'tenant';
        })) {
            $query->where('tenant_id', $user->user_id);
        } elseif ($user->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'landlord';
        })) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->user_id);
            });
        }
        
        $query->orderBy('created_at', 'desc');
        
        $maintenanceRequests = $this->perPage === 'all' 
            ? $query->get() 
            : $query->paginate($this->perPage);
        
        return view('livewire.maintenance.maintenance-list', [
            'maintenanceRequests' => $maintenanceRequests,
            'statuses' => [
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'rejected' => 'Rejected'
            ],
            'priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent'
            ],
            'paginationOptions' => [
                10 => '10 per page',
                25 => '25 per page',
                50 => '50 per page',
                100 => '100 per page',
                'all' => 'Show All'
            ]
        ]);
    }
} 