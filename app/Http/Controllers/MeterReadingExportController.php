<?php

namespace App\Http\Controllers;

use App\Support\MeterReadingQuery;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Exports utility meter readings as CSV, Excel (.xlsx) or a print-ready PDF
 * page. All three formats pull their rows through MeterReadingQuery so they
 * match the card view exactly, and respect the same property/utility/year/month
 * filters passed as query-string parameters.
 *
 * No third-party packages: CSV uses fputcsv, Excel uses SimpleXlsx (ZipArchive),
 * and "PDF" is a print-optimised HTML page the browser saves as PDF.
 */
class MeterReadingExportController extends Controller
{
    /** Column headers, in order, translated for the current locale. */
    private function headers(): array
    {
        return [
            __('app.utility_usage_history.col_date'),
            __('app.utility_usage_history.col_property'),
            __('app.utility_usage_history.col_room'),
            __('app.meter_reading_cards.col_tenant'),
            __('app.utility_usage_history.col_utility'),
            __('app.utility_usage_history.col_previous_reading'),
            __('app.utility_usage_history.col_new_reading'),
            __('app.utility_usage_history.col_usage'),
            __('app.utility_usage_history.col_charge'),
        ];
    }

    /**
     * Fetch the filtered readings as plain rows.
     *
     * @return array<int,array{date:string,property:string,room:mixed,utility:string,previous:float,new:float,usage:float,charge:float}>
     */
    private function rows(Request $request): array
    {
        $filters = [
            'property' => $request->query('property'),
            'utility' => $request->query('utility'),
            'year' => $request->query('year'),
            'month' => $request->query('month'),
        ];

        return MeterReadingQuery::build($filters, Auth::user())
            ->get()
            ->map(fn ($usage) => [
                'date' => $usage->usage_date?->format('Y-m-d') ?? '',
                'property' => (string) $usage->property_name,
                'room' => $usage->room_number,
                'tenant' => $this->tenantName($usage),
                'utility' => (string) $usage->utility_name,
                'previous' => (float) $usage->old_meter_reading,
                'new' => (float) $usage->new_meter_reading,
                'usage' => (float) $usage->amount_used,
                'charge' => round((float) $usage->calculateCharge(), 2),
            ])
            ->all();
    }

    /** Resolve a tenant's display name from the reading's rental, mirroring the invoice screens. */
    private function tenantName($usage): string
    {
        $tenant = $usage->rental?->tenant;
        if (! $tenant) {
            return '—';
        }

        $name = trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? ''));

        return $name !== '' ? $name : ($tenant->email ?? $tenant->username ?? '—');
    }

    private function filename(string $extension): string
    {
        return 'meter-readings-' . now()->format('Y-m-d') . '.' . $extension;
    }

    public function csv(Request $request): StreamedResponse
    {
        $headers = $this->headers();
        $rows = $this->rows($request);
        $filename = $this->filename('csv');

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens Khmer/accented text correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['property'],
                    $row['room'],
                    $row['tenant'],
                    $row['utility'],
                    number_format($row['previous'], 2, '.', ''),
                    number_format($row['new'], 2, '.', ''),
                    number_format($row['usage'], 2, '.', ''),
                    number_format($row['charge'], 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function excel(Request $request)
    {
        $headers = $this->headers();
        $rows = array_map(fn ($row) => [
            $row['date'],
            $row['property'],
            (string) $row['room'],
            $row['tenant'],
            $row['utility'],
            $row['previous'],
            $row['new'],
            $row['usage'],
            $row['charge'],
        ], $this->rows($request));

        $path = SimpleXlsx::create($headers, $rows, 'Meter Readings');

        return response()->download($path, $this->filename('xlsx'), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function pdf(Request $request)
    {
        return response()->view('utilities.meter-readings-print', [
            'headers' => $this->headers(),
            'rows' => $this->rows($request),
            'generatedAt' => now(),
        ]);
    }
}
