<?php

namespace App\Livewire\SimpleMode;

use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SimpleAddTenant extends Component
{
    public int $step = 1;

    public string $roomId = '';
    public string $start_date = '';
    public string $monthly_rent = '';

    public array $availableRooms = [];

    // Tenant search
    public string $tenantSearch = '';
    public array $tenantResults = [];
    public ?int $existingTenantId = null;
    public ?array $selectedTenant = null;

    public function mount(): void
    {
        $this->start_date = Carbon::now()->toDateString();
        $this->loadAvailableRooms();
    }

    public function updatedTenantSearch(): void
    {
        $term = trim($this->tenantSearch);

        if (strlen($term) < 2) {
            $this->tenantResults = [];
            return;
        }

        $this->tenantResults = User::query()
            ->whereHas('roles', fn ($q) => $q->where('role_name', 'tenant'))
            ->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like);
            })
            ->select('user_id', 'first_name', 'last_name', 'email', 'phone_number')
            ->orderBy('first_name')
            ->limit(6)
            ->get()
            ->map(fn ($u) => [
                'user_id' => (int) $u->user_id,
                'first_name' => (string) $u->first_name,
                'last_name' => (string) $u->last_name,
                'email' => (string) $u->email,
                'phone_number' => (string) $u->phone_number,
            ])
            ->all();
    }

    public function selectExistingTenant(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $this->existingTenantId = (int) $user->user_id;
        $this->selectedTenant = [
            'user_id' => (int) $user->user_id,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'email' => (string) $user->email,
            'phone_number' => (string) $user->phone_number,
        ];
        $this->tenantSearch = '';
        $this->tenantResults = [];
        $this->resetErrorBag(['existingTenantId']);
    }

    public function clearExistingTenant(): void
    {
        $this->existingTenantId = null;
        $this->selectedTenant = null;
    }

    public function loadAvailableRooms(): void
    {
        $this->availableRooms = Unit::query()
            ->select('room_details.room_id', 'room_details.room_number', 'room_details.floor_number', 'room_details.rent_amount', 'property_details.property_name')
            ->join('property_details', 'property_details.property_id', '=', 'room_details.property_id')
            ->where('property_details.landlord_id', Auth::id())
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('rental_details')
                    ->whereColumn('rental_details.room_id', 'room_details.room_id')
                    ->where('rental_details.status', 'active');
            })
            ->orderBy('property_details.property_name')
            ->orderBy('room_details.floor_number')
            ->orderBy('room_details.room_number')
            ->get()
            ->map(fn ($r) => [
                'room_id' => (string) $r->room_id,
                'label' => "F{$r->floor_number} · Room {$r->room_number}",
                'property_name' => $r->property_name,
                'rent_amount' => (float) $r->rent_amount,
            ])
            ->all();
    }

    public function selectRoom(string $roomId): void
    {
        $this->roomId = $roomId;
        $room = collect($this->availableRooms)->firstWhere('room_id', $roomId);
        if ($room && $this->monthly_rent === '') {
            $this->monthly_rent = (string) $room['rent_amount'];
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'existingTenantId' => 'required|exists:users,user_id',
            ], [
                'existingTenantId.required' => 'Pick a tenant.',
                'existingTenantId.exists' => 'That tenant no longer exists.',
            ]);
            $this->step = 2;
            return;
        }

        if ($this->step === 2) {
            $this->validate([
                'roomId' => 'required|exists:room_details,room_id',
            ], [
                'roomId.required' => 'Pick a room.',
            ]);
            $this->step = 3;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function save()
    {
        $this->validate([
            'existingTenantId' => 'required|exists:users,user_id',
            'roomId' => 'required|exists:room_details,room_id',
            'start_date' => 'required|date',
            'monthly_rent' => 'required|numeric|min:0',
        ], [
            'existingTenantId.required' => 'Pick a tenant.',
        ]);

        try {
            DB::beginTransaction();

            $tenant = User::find($this->existingTenantId);
            if (!$tenant) {
                throw new \RuntimeException('Tenant not found.');
            }

            Rental::create([
                'landlord_id' => Auth::id(),
                'tenant_id' => $tenant->user_id,
                'room_id' => $this->roomId,
                'start_date' => $this->start_date,
                'end_date' => Carbon::parse($this->start_date)->addYear(),
                'monthly_rent' => $this->monthly_rent,
                'security_deposit' => $this->monthly_rent,
                'status' => 'active',
            ]);

            Unit::where('room_id', $this->roomId)->update([
                'available' => false,
                'status' => 'occupied',
            ]);

            DB::commit();

            session()->flash('success', "{$tenant->first_name} {$tenant->last_name} moved in.");

            return redirect()->route('simple-mode.home');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Could not add tenant: ' . $e->getMessage());
            return null;
        }
    }

    public function getSelectedRoomProperty(): ?array
    {
        return collect($this->availableRooms)->firstWhere('room_id', $this->roomId);
    }

    public function render()
    {
        return view('livewire.simple-mode.simple-add-tenant');
    }
}
