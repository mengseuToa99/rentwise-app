<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Utility;
use App\Models\UtilityUsage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BatchInvoiceForm extends Component
{
    public int $stage = 1;
    public int $currentIndex = 0;
    public string $search = '';
    public string $defaultDueDate = '';
    public string $payment_status = 'pending';

    public array $rentals = [];
    public array $selectedRentalIds = [];
    public array $roomReadings = [];
    public array $roomDueDates = [];

    public ?string $selectedPropertyId = null;

    public function mount(): void
    {
        $this->defaultDueDate = now()->addDays(15)->format('Y-m-d');
        $this->loadRentals();
    }

    public function loadRentals(): void
    {
        $user = Auth::user();

        $this->rentals = Rental::query()
            ->with([
                'tenant:user_id,first_name,last_name,email',
                'unit:room_id,property_id,room_number,room_name,floor_number',
                'unit.property:property_id,property_name',
            ])
            ->forLandlord($user->user_id)
            ->active()
            ->get()
            ->map(function (Rental $rental) {
                $first = $rental->tenant?->first_name ?? '';
                $last = $rental->tenant?->last_name ?? '';
                $name = trim($first . ' ' . $last);
                if ($name === '') {
                    $name = $rental->tenant?->email ?? 'Unknown Tenant';
                }

                $floor = $rental->unit?->floor_number;
                $roomNumber = $rental->unit?->room_number ?? '-';
                $roomLabel = $rental->unit?->room_name
                    ?: (($floor !== null ? "F{$floor} · " : '') . "Room {$roomNumber}");

                $initials = $floor !== null
                    ? 'F' . $floor
                    : strtoupper(mb_substr($name, 0, 2));

                return [
                    'rental_id' => (string) $rental->rental_id,
                    'tenant_name' => $name,
                    'initials' => $initials,
                    'property_name' => $rental->unit?->property?->property_name ?? 'Unknown Property',
                    'property_id' => $rental->unit?->property_id,
                    'room_id' => $rental->room_id,
                    'floor_number' => $floor,
                    'room_number' => $roomNumber,
                    'room_label' => $roomLabel,
                ];
            })
            ->values()
            ->all();
    }

    public function getFilteredRentalsProperty(): Collection
    {
        $search = mb_strtolower(trim($this->search));
        $propertyId = $this->selectedPropertyId;

        return collect($this->rentals)->filter(function (array $rental) use ($search, $propertyId) {
            if ($propertyId !== null && (string) $rental['property_id'] !== $propertyId) {
                return false;
            }
            if ($search === '') {
                return true;
            }
            return str_contains(mb_strtolower($rental['tenant_name']), $search)
                || str_contains(mb_strtolower($rental['property_name']), $search)
                || str_contains(mb_strtolower($rental['room_label']), $search);
        })->values();
    }

    public function getPropertiesProperty(): Collection
    {
        return collect($this->rentals)
            ->groupBy('property_id')
            ->map(fn ($group) => [
                'property_id' => (string) $group->first()['property_id'],
                'property_name' => $group->first()['property_name'],
                'room_count' => $group->count(),
            ])
            ->values();
    }

    public function selectProperty(string $propertyId): void
    {
        $this->selectedPropertyId = $propertyId;
        $this->search = '';
    }

    public function clearProperty(): void
    {
        $this->selectedPropertyId = null;
        $this->search = '';
        $this->clearAll();
    }

    public function toggleRental(string $rentalId): void
    {
        if (in_array($rentalId, $this->selectedRentalIds, true)) {
            $this->selectedRentalIds = array_values(array_diff($this->selectedRentalIds, [$rentalId]));
            unset($this->roomReadings[$rentalId], $this->roomDueDates[$rentalId]);
            return;
        }

        $this->selectedRentalIds[] = $rentalId;
        $this->initReadingsFor($rentalId);
        $this->roomDueDates[$rentalId] = $this->defaultDueDate;
    }

    public function selectAll(): void
    {
        foreach ($this->filteredRentals as $rental) {
            $id = $rental['rental_id'];
            if (!in_array($id, $this->selectedRentalIds, true)) {
                $this->selectedRentalIds[] = $id;
                $this->initReadingsFor($id);
                $this->roomDueDates[$id] = $this->defaultDueDate;
            }
        }
    }

    public function clearAll(): void
    {
        $this->selectedRentalIds = [];
        $this->roomReadings = [];
        $this->roomDueDates = [];
    }

    private function initReadingsFor(string $rentalId): void
    {
        $rental = collect($this->rentals)->firstWhere('rental_id', $rentalId);
        if (!$rental) {
            return;
        }

        $readings = [];
        foreach (Utility::orderBy('utility_name')->get() as $utility) {
            $lastReading = UtilityUsage::where('room_id', $rental['room_id'])
                ->where('utility_id', $utility->utility_id)
                ->orderByDesc('usage_date')
                ->first();

            $price = $utility->getCurrentPrice($rental['property_id']);

            $readings[$utility->utility_id] = [
                'utility_name' => $utility->utility_name,
                'unit_of_measure' => $utility->unit_of_measure ?: 'units',
                'previous_reading' => (float) ($lastReading?->new_meter_reading ?? 0),
                'previous_date' => $lastReading?->usage_date?->format('Y-m-d'),
                'new_reading' => '',
                'amount_used' => 0,
                'rate' => (float) ($price?->price ?? 0),
                'total_charge' => 0,
            ];
        }

        $this->roomReadings[$rentalId] = $readings;
    }

    public function startFilling(): void
    {
        if (empty($this->selectedRentalIds)) {
            return;
        }
        $this->stage = 2;
        $this->currentIndex = 0;
    }

    public function backToPick(): void
    {
        $this->stage = 1;
    }

    public function goToReview(): void
    {
        $this->stage = 2;
    }

    public function nextRoom(): void
    {
        if ($this->currentIndex + 1 < count($this->selectedRentalIds)) {
            $this->currentIndex++;
        } else {
            $this->stage = 3;
        }
    }

    public function previousRoom(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function jumpToRoom(int $index): void
    {
        if ($index >= 0 && $index < count($this->selectedRentalIds)) {
            $this->stage = 2;
            $this->currentIndex = $index;
        }
    }

    public function skipCurrentRoom(): void
    {
        if (empty($this->selectedRentalIds)) {
            return;
        }

        $idToRemove = $this->selectedRentalIds[$this->currentIndex] ?? null;
        if ($idToRemove === null) {
            return;
        }

        $this->removeRoom($idToRemove);
    }

    public function removeRoom(string $rentalId): void
    {
        if (!in_array($rentalId, $this->selectedRentalIds, true)) {
            return;
        }

        $this->selectedRentalIds = array_values(array_diff($this->selectedRentalIds, [$rentalId]));
        unset($this->roomReadings[$rentalId], $this->roomDueDates[$rentalId]);

        if (empty($this->selectedRentalIds)) {
            $this->stage = 1;
            $this->currentIndex = 0;
            return;
        }

        if ($this->currentIndex >= count($this->selectedRentalIds)) {
            $this->currentIndex = count($this->selectedRentalIds) - 1;
        }
    }

    public function updatedRoomReadings($value, $key): void
    {
        if (str_contains($key, '.new_reading')) {
            $parts = explode('.', $key);
            $rentalId = $parts[0];
            $utilityId = (int) $parts[1];
            $this->recalculate($rentalId, $utilityId);
        }
    }

    private function recalculate(string $rentalId, int $utilityId): void
    {
        if (!isset($this->roomReadings[$rentalId][$utilityId])) {
            return;
        }

        $reading = &$this->roomReadings[$rentalId][$utilityId];

        if ($reading['new_reading'] === '' || !is_numeric($reading['new_reading'])) {
            $reading['amount_used'] = 0;
            $reading['total_charge'] = 0;
            return;
        }

        $newReading = (float) $reading['new_reading'];
        $previousReading = (float) $reading['previous_reading'];

        $reading['amount_used'] = max(0, $newReading - $previousReading);
        $reading['total_charge'] = $reading['amount_used'] * (float) $reading['rate'];
    }

    public function adjustReading(string $rentalId, int $utilityId, float $delta): void
    {
        if (!isset($this->roomReadings[$rentalId][$utilityId])) {
            return;
        }

        $current = $this->roomReadings[$rentalId][$utilityId]['new_reading'];
        $previous = (float) $this->roomReadings[$rentalId][$utilityId]['previous_reading'];

        $base = ($current === '' || !is_numeric($current)) ? $previous : (float) $current;
        $next = max($previous, $base + $delta);

        $this->roomReadings[$rentalId][$utilityId]['new_reading'] = $this->formatNumber($next);
        $this->recalculate($rentalId, $utilityId);
    }

    public function setSameAsLast(string $rentalId, int $utilityId): void
    {
        if (!isset($this->roomReadings[$rentalId][$utilityId])) {
            return;
        }

        $previous = (float) $this->roomReadings[$rentalId][$utilityId]['previous_reading'];
        $this->roomReadings[$rentalId][$utilityId]['new_reading'] = $this->formatNumber($previous);
        $this->recalculate($rentalId, $utilityId);
    }

    private function formatNumber(float $value): string
    {
        if (fmod($value, 1.0) === 0.0) {
            return (string) (int) $value;
        }
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }

    public function setDueDateQuick(string $rentalId, string $type): void
    {
        $date = match ($type) {
            '7_days' => now()->addDays(7),
            '15_days' => now()->addDays(15),
            'month_end' => now()->endOfMonth(),
            default => now()->addDays(15),
        };

        $this->roomDueDates[$rentalId] = $date->format('Y-m-d');
    }

    public function setBatchDueDate(string $type): void
    {
        $date = match ($type) {
            '7_days' => now()->addDays(7),
            '15_days' => now()->addDays(15),
            'month_end' => now()->endOfMonth(),
            default => now()->addDays(15),
        };

        $this->defaultDueDate = $date->format('Y-m-d');
        foreach ($this->selectedRentalIds as $id) {
            $this->roomDueDates[$id] = $this->defaultDueDate;
        }
    }

    public function getUtilityColumnsProperty(): array
    {
        return Utility::orderBy('utility_name')->get()->map(fn ($u) => [
            'utility_id' => $u->utility_id,
            'utility_name' => $u->utility_name,
            'unit_of_measure' => $u->unit_of_measure ?: 'units',
        ])->all();
    }

    public function getCurrentRentalProperty(): array
    {
        $id = $this->selectedRentalIds[$this->currentIndex] ?? null;
        if ($id === null) {
            return [];
        }
        return collect($this->rentals)->firstWhere('rental_id', $id) ?? [];
    }

    public function roomTotal(string $rentalId): float
    {
        if (!isset($this->roomReadings[$rentalId])) {
            return 0;
        }
        return collect($this->roomReadings[$rentalId])->sum(fn ($r) => (float) $r['total_charge']);
    }

    public function roomHasReading(string $rentalId): bool
    {
        if (!isset($this->roomReadings[$rentalId])) {
            return false;
        }
        return collect($this->roomReadings[$rentalId])->contains(function (array $reading) {
            return $reading['new_reading'] !== '' && is_numeric($reading['new_reading']);
        });
    }

    public function getBatchTotalProperty(): float
    {
        return collect($this->selectedRentalIds)->sum(fn ($id) => $this->roomTotal($id));
    }

    public function getReadyCountProperty(): int
    {
        return collect($this->selectedRentalIds)->filter(fn ($id) => $this->roomHasReading($id))->count();
    }

    public function createAll()
    {
        $readyIds = collect($this->selectedRentalIds)
            ->filter(fn ($id) => $this->roomHasReading($id))
            ->values()
            ->all();

        if (empty($readyIds)) {
            session()->flash('error', 'No rooms have readings entered yet.');
            return null;
        }

        $invoicesCreated = 0;
        $readingDate = now()->toDateString();

        try {
            DB::beginTransaction();

            foreach ($readyIds as $rentalId) {
                $rental = Rental::find($rentalId);
                if (!$rental) {
                    continue;
                }

                $dueDate = $this->roomDueDates[$rentalId] ?? $this->defaultDueDate;
                $utilityUsages = [];
                $descriptions = [];

                foreach ($this->roomReadings[$rentalId] as $utilityId => $reading) {
                    if ($reading['new_reading'] === '' || !is_numeric($reading['new_reading'])) {
                        continue;
                    }

                    if ((float) $reading['new_reading'] < (float) $reading['previous_reading']) {
                        DB::rollBack();
                        session()->flash('error', "Reading for {$reading['utility_name']} on " . $this->rentalLabel($rentalId) . ' is lower than previous.');
                        return null;
                    }

                    $usage = UtilityUsage::create([
                        'room_id' => $rental->room_id,
                        'rental_id' => $rental->rental_id,
                        'utility_id' => $utilityId,
                        'recorded_by_user_id' => Auth::id(),
                        'usage_date' => $readingDate,
                        'old_meter_reading' => $reading['previous_reading'],
                        'new_meter_reading' => $reading['new_reading'],
                        'amount_used' => $reading['amount_used'],
                    ]);

                    $utilityUsages[] = $usage->usage_id;

                    $previousDate = $reading['previous_date']
                        ? Carbon::parse($reading['previous_date'])->format('d M Y')
                        : 'initial reading';

                    $descriptions[] = sprintf(
                        '%s usage (%.2f %s) from %s to %s',
                        $reading['utility_name'],
                        $reading['amount_used'],
                        $reading['unit_of_measure'],
                        $previousDate,
                        Carbon::parse($readingDate)->format('d M Y')
                    );
                }

                $invoice = Invoice::create([
                    'rental_id' => $rental->rental_id,
                    'amount_due' => $this->roomTotal($rentalId),
                    'amount_paid' => 0,
                    'issue_date' => $readingDate,
                    'due_date' => $dueDate,
                    'payment_status' => $this->payment_status,
                    'notes' => implode("\n", $descriptions),
                ]);

                foreach ($utilityUsages as $usageId) {
                    DB::table('invoice_utility_usages')->insert([
                        'invoice_id' => $invoice->invoice_id,
                        'usage_id' => $usageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $invoicesCreated++;
            }

            DB::commit();

            session()->flash('success', "Created {$invoicesCreated} invoice" . ($invoicesCreated === 1 ? '' : 's') . '.');

            return redirect()->route(session('simple_mode') ? 'simple-mode.home' : 'invoices.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create invoices: ' . $e->getMessage());
        }

        return null;
    }

    private function rentalLabel(string $rentalId): string
    {
        $r = collect($this->rentals)->firstWhere('rental_id', $rentalId);
        return $r ? "{$r['tenant_name']} ({$r['room_label']})" : 'a room';
    }

    public function render()
    {
        return view('livewire.invoices.batch-invoice-form');
    }
}
