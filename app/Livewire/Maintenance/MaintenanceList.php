<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MaintenanceList extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $perPage = 10;
    public $isLandlord;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        $this->isLandlord = Auth::user()->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        });
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function quickAction($requestId, $action)
    {
        $request = MaintenanceRequest::findOrFail($requestId);
        $this->authorize('updateStatus', $request);

        $message = '';
        switch ($action) {
            case 'in_progress':
                $request->status = 'in_progress';
                $request->landlord_notes = ($request->landlord_notes ? $request->landlord_notes . "\n\n" : '') . 
                    "Request accepted on " . now()->format('M d, Y H:i') . ".";
                $message = 'Maintenance request accepted successfully.';
                break;
            case 'rejected':
                $request->status = 'rejected';
                $request->landlord_notes = ($request->landlord_notes ? $request->landlord_notes . "\n\n" : '') . 
                    "Request rejected on " . now()->format('M d, Y H:i') . ".";
                $message = 'Maintenance request rejected.';
                break;
            case 'completed':
                $request->status = 'completed';
                $request->completed_at = now();
                $request->landlord_notes = ($request->landlord_notes ? $request->landlord_notes . "\n\n" : '') . 
                    "Request marked as completed on " . now()->format('M d, Y H:i') . ".";
                $message = 'Maintenance request marked as completed.';
                break;
            default:
                return;
        }

        $request->save();
        session()->flash('success', $message);
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
        if ($this->isLandlord) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->user_id);
            });
        } else {
            $query->where('tenant_id', $user->user_id);
        }
        
        $query->orderBy('created_at', 'desc');
        
        return view('livewire.maintenance.maintenance-list', [
            'maintenanceRequests' => $query->paginate($this->perPage),
            'isLandlord' => $this->isLandlord,
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
            ]
        ]);
    }
} 