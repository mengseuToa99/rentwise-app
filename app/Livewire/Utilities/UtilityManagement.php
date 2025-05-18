<?php

namespace App\Livewire\Utilities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Utility;
use App\Models\UtilityPrice;
use Illuminate\Support\Facades\Auth;

class UtilityManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $utilityId;
    public $utilityName;
    public $description;
    public $price;
    public $isEditing = false;
    public $modalOpen = false;
    
    public $perPage = 10;
    
    protected $rules = [
        'utilityName' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0.01',
    ];
    
    protected $queryString = ['search', 'perPage'];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }
    
    public function openModal($mode = 'create', $id = null)
    {
        $this->resetValidation();
        $this->isEditing = ($mode === 'edit');
        
        if ($this->isEditing && $id) {
            $utility = Utility::find($id);
            if ($utility) {
                $this->utilityId = $utility->utility_id;
                $this->utilityName = $utility->utility_name;
                $this->description = $utility->description;
                
                $currentPrice = $utility->getCurrentPrice();
                $this->price = $currentPrice ? $currentPrice->price : 0;
            }
        } else {
            $this->resetForm();
        }
        
        $this->modalOpen = true;
    }
    
    public function closeModal()
    {
        $this->modalOpen = false;
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->utilityId = null;
        $this->utilityName = '';
        $this->description = '';
        $this->price = '';
        $this->isEditing = false;
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            if ($this->isEditing) {
                $utility = Utility::find($this->utilityId);
                if (!$utility) {
                    session()->flash('error', 'Utility not found');
                    return;
                }
                
                $utility->update([
                    'utility_name' => $this->utilityName,
                    'description' => $this->description
                ]);
                
                $currentPrice = $utility->getCurrentPrice();
                if (!$currentPrice || $currentPrice->price != $this->price) {
                    UtilityPrice::create([
                        'utility_id' => $utility->utility_id,
                        'price' => $this->price,
                        'effective_date' => now()
                    ]);
                }
                
                session()->flash('success', 'Utility updated successfully');
            } else {
                // Check for duplicate name
                $exists = Utility::where('utility_name', $this->utilityName)->exists();
                if ($exists) {
                    session()->flash('error', 'A utility with this name already exists');
                    return;
                }
                
                // Create new utility
                $utility = Utility::create([
                    'utility_name' => $this->utilityName,
                    'description' => $this->description
                ]);
                
                // Create initial price record
                UtilityPrice::create([
                    'utility_id' => $utility->utility_id,
                    'price' => $this->price,
                    'effective_date' => now()
                ]);
                
                session()->flash('success', 'Utility created successfully');
            }
            
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving utility: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        // Check if utility is in use by any usages
        $utility = Utility::find($id);
        $inUse = $utility && $utility->usages()->exists();
        
        if ($inUse) {
            session()->flash('error', 'Cannot delete this utility as it has usage records');
            return;
        }
        
        $this->utilityId = $id;
        $this->dispatch('confirm-delete-utility');
    }
    
    public function deleteUtility()
    {
        try {
            $utility = Utility::find($this->utilityId);
            if (!$utility) {
                session()->flash('error', 'Utility not found');
                return;
            }
            
            $utility->prices()->delete(); // Delete all price records
            $utility->delete(); // Delete utility
            
            $this->utilityId = null;
            session()->flash('success', 'Utility deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting utility: ' . $e->getMessage());
        }
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $utilities = Utility::query()
            ->when($this->search, function ($query) {
                return $query->where('utility_name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('utility_name')
            ->paginate($this->perPage);
            
        $paginationOptions = [
            10 => '10',
            25 => '25',
            50 => '50',
            100 => '100',
            'all' => 'All'
        ];
        
        return view('livewire.utilities.utility-management', [
            'utilities' => $utilities,
            'paginationOptions' => $paginationOptions
        ]);
    }
} 