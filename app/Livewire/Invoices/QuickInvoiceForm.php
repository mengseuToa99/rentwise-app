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

class QuickInvoiceForm extends Component
{
    public string $search = '';
    public string $selectedRental = '';
    public string $due_date = '';
    public string $payment_status = 'pending';
    public bool $showAllTenants = false;

    public array $rentals = [];
    public array $recentRentalIds = [];
    public array $readings = [];
    public array $selectedRentalSummary = [];

    protected $rules = [
        'selectedRental' => 'required|exists:rental_details,rental_id',
        'due_date' => 'required|date',
        'payment_status' => 'required|in:pending,paid,overdue',
    ];

    public function mount(): void
    {
        $this->due_date = now()->addDays(15)->format('Y-m-d');
        $this->loadRentals();
        $this->loadRecentRentals();
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
                $first = $rental->tenant?->first_name ?? '';
                $last = $rental->tenant?->last_name ?? '';
                $tenantName = trim($first . ' ' . $last);
                if ($tenantName === '') {
                    $tenantName = $rental->tenant?->email ?? 'Unknown Tenant';
                }

                $initials = strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
                if ($initials === '') {
                    $initials = strtoupper(mb_substr($tenantName, 0, 2));
                }

                $roomLabel = $rental->unit?->room_name ?: ('Room ' . ($rental->unit?->room_number ?? '-'));

                return [
                    'rental_id' => (string) $rental->rental_id,
                    'tenant_name' => $tenantName,
                    'initials' => $initials,
                    'property_name' => $rental->unit?->property?->property_name ?? 'Unknown Property',
                    'room_label' => $roomLabel,
                    'room_id' => $rental->room_id,
                    'property_id' => $rental->unit?->property_id,
                ];
            })
            ->values()
            ->all();
    }

    public function loadRecentRentals(): void
    {
        $user = Auth::user();

        $this->recentRentalIds = Invoice::query()
            ->select('invoice_details.rental_id')
            ->join('rental_details', 'rental_details.rental_id', '=', 'invoice_details.rental_id')
            ->where('rental_details.landlord_id', $user->user_id)
            ->orderByDesc('invoice_details.issue_date')
            ->orderByDesc('invoice_details.invoice_id')
            ->limit(20)
            ->pluck('invoice_details.rental_id')
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->take(5)
            ->values()
            ->all();
    }

    public function getRecentRentalsProperty(): Collection
    {
        if (empty($this->recentRentalIds)) {
            return collect();
        }

        $byId = collect($this->rentals)->keyBy('rental_id');

        return collect($this->recentRentalIds)
            ->map(fn (string $id) => $byId->get($id))
            ->filter()
            ->values();
    }

    public function getFilteredRentalsProperty(): Collection
    {
        $search = mb_strtolower(trim($this->search));
        $recent = array_flip($this->recentRentalIds);

        return collect($this->rentals)->filter(function (array $rental) use ($search, $recent) {
            if ($search === '' && isset($recent[$rental['rental_id']])) {
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

    public function selectRental(string $rentalId): void
    {
        $this->selectedRental = $rentalId;
        $this->selectedRentalSummary = collect($this->rentals)
            ->firstWhere('rental_id', $rentalId) ?? [];
        $this->loadReadings();
        $this->showAllTenants = false;
        $this->search = '';
    }

    public function clearRental(): void
    {
        $this->selectedRental = '';
        $this->selectedRentalSummary = [];
        $this->readings = [];
    }

    public function loadReadings(): void
    {
        $this->readings = [];

        if (!$this->selectedRentalSummary) {
            return;
        }

        $propertyId = $this->selectedRentalSummary['property_id'] ?? null;
        $roomId = $this->selectedRentalSummary['room_id'] ?? null;

        foreach (Utility::orderBy('utility_name')->get() as $utility) {
            $lastReading = UtilityUsage::where('room_id', $roomId)
                ->where('utility_id', $utility->utility_id)
                ->orderByDesc('usage_date')
                ->first();

            $previousReading = (float) ($lastReading?->new_meter_reading ?? 0);
            $price = $utility->getCurrentPrice($propertyId);

            $this->readings[$utility->utility_id] = [
                'utility_name' => $utility->utility_name,
                'unit_of_measure' => $utility->unit_of_measure ?: 'units',
                'previous_reading' => $previousReading,
                'previous_date' => $lastReading?->usage_date?->format('Y-m-d'),
                'new_reading' => '',
                'amount_used' => 0,
                'rate' => (float) ($price?->price ?? 0),
                'total_charge' => 0,
            ];
        }
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

        $newReading = (float) $reading['new_reading'];
        $previousReading = (float) $reading['previous_reading'];

        $reading['amount_used'] = max(0, $newReading - $previousReading);
        $reading['total_charge'] = $reading['amount_used'] * (float) $reading['rate'];
    }

    public function adjustReading(int $utilityId, float $delta): void
    {
        if (!isset($this->readings[$utilityId])) {
            return;
        }

        $current = $this->readings[$utilityId]['new_reading'];
        $previous = (float) $this->readings[$utilityId]['previous_reading'];

        if ($current === '' || !is_numeric($current)) {
            $base = $previous;
        } else {
            $base = (float) $current;
        }

        $next = max($previous, $base + $delta);
        $this->readings[$utilityId]['new_reading'] = $this->formatNumber($next);
        $this->recalculateReading($utilityId);
    }

    public function setSameAsLast(int $utilityId): void
    {
        if (!isset($this->readings[$utilityId])) {
            return;
        }

        $previous = (float) $this->readings[$utilityId]['previous_reading'];
        $this->readings[$utilityId]['new_reading'] = $this->formatNumber($previous);
        $this->recalculateReading($utilityId);
    }

    private function formatNumber(float $value): string
    {
        if (fmod($value, 1.0) === 0.0) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
    }

    public function setDueDateQuick(string $type): void
    {
        $date = match ($type) {
            '7_days' => now()->addDays(7),
            '15_days' => now()->addDays(15),
            'month_end' => now()->endOfMonth(),
            default => now()->addDays(15),
        };

        $this->due_date = $date->format('Y-m-d');
    }

    public function getInvoiceTotalProperty(): float
    {
        return collect($this->readings)->sum(fn (array $reading) => (float) $reading['total_charge']);
    }

    public function hasAtLeastOneReading(): bool
    {
        return collect($this->readings)->contains(function (array $reading) {
            return $reading['new_reading'] !== '' && is_numeric($reading['new_reading']);
        });
    }

    public function save()
    {
        $this->validate();

        if (!$this->hasAtLeastOneReading()) {
            $this->addError('readings', 'Enter at least one meter reading.');
            return null;
        }

        try {
            DB::beginTransaction();

            $selectedRental = Rental::findOrFail($this->selectedRental);
            $readingDate = now()->toDateString();
            $utilityUsages = [];
            $descriptions = [];

            foreach ($this->readings as $utilityId => $reading) {
                if ($reading['new_reading'] === '' || !is_numeric($reading['new_reading'])) {
                    continue;
                }

                if ((float) $reading['new_reading'] < (float) $reading['previous_reading']) {
                    $this->addError("readings.{$utilityId}.new_reading", 'New reading must be higher than the previous reading.');
                    DB::rollBack();
                    return null;
                }

                $usage = UtilityUsage::create([
                    'room_id' => $selectedRental->room_id,
                    'rental_id' => $selectedRental->rental_id,
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
                'rental_id' => $selectedRental->rental_id,
                'amount_due' => $this->invoiceTotal,
                'amount_paid' => 0,
                'issue_date' => now()->toDateString(),
                'due_date' => $this->due_date,
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

            DB::commit();

            session()->flash('success', 'Invoice created successfully.');

            return redirect()->route(session('simple_mode') ? 'simple-mode.home' : 'invoices.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create invoice: ' . $e->getMessage());
        }

        return null;
    }

    public function render()
    {
        return view('livewire.invoices.quick-invoice-form');
    }
}
