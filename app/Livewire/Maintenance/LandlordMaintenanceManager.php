<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LandlordMaintenanceManager extends Component
{
    use AuthorizesRequests;

    public $requestId;
    public $status;
    public $landlord_notes;
    public $maintenanceRequest;

    protected $rules = [
        'status' => 'required|in:pending,in_progress,completed,rejected',
        'landlord_notes' => 'nullable|string',
    ];

    public function mount($requestId)
    {
        $this->requestId = $requestId;
        $this->loadMaintenanceRequest();
    }

    public function loadMaintenanceRequest()
    {
        $this->maintenanceRequest = MaintenanceRequest::findOrFail($this->requestId);
        $this->authorize('update', $this->maintenanceRequest);
        
        $this->status = $this->maintenanceRequest->status;
        $this->landlord_notes = $this->maintenanceRequest->landlord_notes;
    }

    public function updateStatus()
    {
        $this->validate();
        
        $this->authorize('updateStatus', $this->maintenanceRequest);

        $this->maintenanceRequest->update([
            'status' => $this->status,
            'landlord_notes' => $this->landlord_notes,
            'completed_at' => $this->status === 'completed' ? now() : null,
        ]);

        session()->flash('success', 'Maintenance request status updated successfully.');
        return redirect()->route('maintenance.index');
    }

    public function render()
    {
        return view('livewire.maintenance.landlord-maintenance-manager', [
            'statuses' => [
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'rejected' => 'Rejected'
            ],
        ]);
    }
} 