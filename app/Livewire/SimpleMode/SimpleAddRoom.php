<?php

namespace App\Livewire\SimpleMode;

use App\Models\Property;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SimpleAddRoom extends Component
{
    public string $propertyId = '';
    public int $floorNumber = 1;
    public string $roomNumber = '';
    public string $roomType = 'studio';
    public string $rentAmount = '';

    public array $properties = [];

    protected $rules = [
        'propertyId' => 'required|exists:property_details,property_id',
        'floorNumber' => 'required|integer|min:1|max:50',
        'roomNumber' => 'required|string|max:20',
        'roomType' => 'required|in:studio,one_bedroom,two_bedroom,three_bedroom',
        'rentAmount' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'propertyId.required' => 'Pick a property first.',
        'roomNumber.required' => 'Enter the room number (e.g. 101).',
        'rentAmount.required' => 'Enter monthly rent.',
        'rentAmount.numeric' => 'Rent must be a number.',
    ];

    public function mount(): void
    {
        $this->properties = Property::where('landlord_id', Auth::id())
            ->orderBy('property_name')
            ->pluck('property_name', 'property_id')
            ->toArray();

        if (count($this->properties) === 1) {
            $this->propertyId = (string) array_key_first($this->properties);
        }
    }

    public function adjustFloor(int $delta): void
    {
        $this->floorNumber = max(1, min(50, $this->floorNumber + $delta));
    }

    public function save()
    {
        $this->validate();

        Unit::create([
            'property_id' => $this->propertyId,
            'room_number' => $this->roomNumber,
            'floor_number' => $this->floorNumber,
            'room_name' => 'Room ' . $this->roomNumber,
            'room_type' => $this->roomType,
            'rent_amount' => $this->rentAmount,
            'available' => true,
            'status' => 'vacant',
            'due_date' => Carbon::now()->addMonth(),
        ]);

        session()->flash('success', "Room {$this->roomNumber} added.");

        return redirect()->route('simple-mode.home');
    }

    public function render()
    {
        return view('livewire.simple-mode.simple-add-room');
    }
}
