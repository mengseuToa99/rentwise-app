<?php

namespace App\Livewire\SimpleMode;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\Utility;
use App\Models\UtilityUsage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SimpleMoveOut extends Component
{
    public int $step = 1;
    public string $search = '';
    public string $selectedRental = '';
    public string $move_out_date = '';

    /** Occupied rooms (active rentals) for this landlord. */
    public array $rentals = [];
    public array $readings = [];
    public array $selectedRentalSummary = [];

    public function mount(): void
    {
        // Keep the simplified (locked) chrome while on this page.
        session(['simple_mode' => true]);

        $this->move_out_date = Carbon::now()->toDateString();
        $this->loadRentals();
    }

    public function loadRentals(): void
    {
        $user = Auth::user();

        $this->rentals = Rental::query()
            ->with([
                'tenant:user_id,first_name,last_name,email,phone_number',
                'unit:room_id,property_id,room_number,room_name',
                'unit.property:property_id,property_name',
            ])
            ->forLandlord($user->user_id)
            ->active()
            ->get()
            ->map(function (Rental $rental) {
                $tenantName = trim(($rental->tenant?->first_name ?? '') . ' ' . ($rental->tenant?->last_name ?? ''));
                $roomLabel = $rental->unit?->room_name ?: ('Room ' . ($rental->unit?->room_number ?? '-'));

                return [
                    'rental_id' => (string) $rental->rental_id,
                    'tenant_name' => $tenantName !== '' ? $tenantName : ($rental->tenant?->email ?? 'Unknown Tenant'),
                    'property_name' => $rental->unit?->property?->property_name ?? 'Unknown Property',
                    'room_label' => $roomLabel,
                    'room_id' => $rental->room_id,
                    'property_id' => $rental->unit?->property_id,
                ];
            })
            ->values()
            ->all();
    }

    public function getFilteredRentalsProperty(): Collection
    {
        $search = mb_strtolower(trim($this->search));

        return collect($this->rentals)->filter(function (array $rental) use ($search) {
            if ($search === '') {
                return true;
            }

            return str_contains(mb_strtolower($rental['tenant_name']), $search)
                || str_contains(mb_strtolower($rental['property_name']), $search)
                || str_contains(mb_strtolower($rental['room_label']), $search);
        })->values();
    }

    public function selectRental(string $rentalId): void
    {
        $this->selectedRental = $rentalId;
        $this->loadSelectedRentalSummary();
        $this->loadReadings();
        $this->step = 2;
    }

    public function loadSelectedRentalSummary(): void
    {
        $this->selectedRentalSummary = collect($this->rentals)
            ->firstWhere('rental_id', (string) $this->selectedRental) ?? [];
    }

    public function loadReadings(): void
    {
        $this->readings = [];

        if (!$this->selectedRentalSummary) {
            return;
        }

        $roomId = $this->selectedRentalSummary['room_id'] ?? null;
        $propertyId = $this->selectedRentalSummary['property_id'] ?? null;

        foreach (Utility::orderBy('utility_name')->get() as $utility) {
            $lastReading = UtilityUsage::where('room_id', $roomId)
                ->where('utility_id', $utility->utility_id)
                ->orderByDesc('usage_date')
                ->first();

            $previousReading = $lastReading?->new_meter_reading ?? 0;
            $price = $utility->getCurrentPrice($propertyId);

            $this->readings[$utility->utility_id] = [
                'utility_name' => $utility->utility_name,
                'unit_of_measure' => $utility->unit_of_measure ?: 'units',
                'previous_reading' => (float) $previousReading,
                'previous_date' => $lastReading?->usage_date?->format('Y-m-d'),
                'new_reading' => '',
                'amount_used' => 0,
                'rate' => (float) ($price?->price ?? 0),
                'total_charge' => 0,
            ];
        }
    }

    public function getInvoiceTotalProperty(): float
    {
        return collect($this->readings)->sum(fn (array $r) => (float) ($r['total_charge'] ?? 0));
    }

    public function updatedReadings($value, $key): void
    {
        if (str_contains($key, '.new_reading')) {
            $utilityId = explode('.', $key)[0];
            $this->recalculateReading((int) $utilityId);
        }
    }

    public function recalculateReading(int $utilityId): void
    {
        if (!isset($this->readings[$utilityId])) {
            return;
        }

        $reading = &$this->readings[$utilityId];

        if ($reading['new_reading'] === '' || !is_numeric($reading['new_reading'])) {
            $reading['amount_used'] = 0;
            $reading['total_charge'] = 0;
            return;
        }

        $reading['amount_used'] = max(0, (float) $reading['new_reading'] - (float) $reading['previous_reading']);
        $reading['total_charge'] = $reading['amount_used'] * (float) ($reading['rate'] ?? 0);
    }

    public function backToList(): void
    {
        $this->step = 1;
    }

    public function moveOut()
    {
        $this->validate([
            'selectedRental' => 'required|exists:rental_details,rental_id',
            'move_out_date' => 'required|date',
        ], [
            'selectedRental.required' => 'Pick the room the tenant is leaving.',
            'move_out_date.required' => 'Choose the move-out date.',
        ]);

        try {
            DB::beginTransaction();

            // Scope to the current landlord so one landlord can't end another's lease.
            $rental = Rental::where('rental_id', $this->selectedRental)
                ->where('landlord_id', Auth::id())
                ->firstOrFail();

            // Record any final meter readings that were entered, and gather the
            // pieces needed to bill them on a closing invoice.
            $usageIds = [];
            $descriptions = [];
            $invoiceTotal = 0.0;

            foreach ($this->readings as $utilityId => $reading) {
                if ($reading['new_reading'] === '' || !is_numeric($reading['new_reading'])) {
                    continue;
                }

                if ((float) $reading['new_reading'] < (float) $reading['previous_reading']) {
                    $this->addError("readings.{$utilityId}.new_reading", 'Final reading must be higher than the last reading.');
                    DB::rollBack();
                    return null;
                }

                $this->recalculateReading((int) $utilityId);
                $reading = $this->readings[$utilityId];

                $usage = UtilityUsage::create([
                    'room_id' => $rental->room_id,
                    'rental_id' => $rental->rental_id,
                    'utility_id' => $utilityId,
                    'recorded_by_user_id' => Auth::id(),
                    'reading_type' => 'move_out',
                    'usage_date' => $this->move_out_date,
                    'old_meter_reading' => $reading['previous_reading'],
                    'new_meter_reading' => $reading['new_reading'],
                    'amount_used' => $reading['amount_used'],
                ]);

                $usageIds[] = $usage->usage_id;
                $invoiceTotal += (float) $reading['total_charge'];
                $descriptions[] = sprintf(
                    '%s: %s %s',
                    $reading['utility_name'],
                    rtrim(rtrim(number_format((float) $reading['amount_used'], 2), '0'), '.'),
                    $reading['unit_of_measure']
                );
            }

            // Create the closing invoice for the final utility usage (only when
            // at least one reading was entered).
            $invoice = null;
            if (!empty($usageIds)) {
                $invoice = Invoice::create([
                    'rental_id' => $rental->rental_id,
                    'amount_due' => $invoiceTotal,
                    'amount_paid' => 0,
                    'issue_date' => $this->move_out_date,
                    'due_date' => $this->move_out_date,
                    'payment_status' => 'pending',
                    'notes' => "Move-out final invoice\n" . implode("\n", $descriptions),
                ]);

                foreach ($usageIds as $usageId) {
                    DB::table('invoice_utility_usages')->insert([
                        'invoice_id' => $invoice->invoice_id,
                        'usage_id' => $usageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // End the lease. The Rental "updated" model event frees the unit
            // (status => vacant, available => true); we also set it explicitly
            // as a safety net in case the event is ever bypassed.
            $rental->status = 'terminated';
            $rental->end_date = $this->move_out_date;
            $rental->save();

            Unit::where('room_id', $rental->room_id)->update([
                'available' => true,
                'status' => 'vacant',
            ]);

            DB::commit();

            $name = $this->selectedRentalSummary['tenant_name'] ?? 'Tenant';
            $room = $this->selectedRentalSummary['room_label'] ?? '';
            $msg = trim("{$name} moved out. {$room} is now free.");
            if ($invoice) {
                $msg .= ' ' . __('app.simple_mode.mo_invoice_created', ['total' => number_format($invoiceTotal, 2)]);
            }
            session()->flash('success', $msg);

            return redirect()->route('simple-mode.home');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Could not complete move-out: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.simple-mode.simple-move-out');
    }
}
