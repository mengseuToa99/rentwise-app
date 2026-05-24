<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.meter_reading_cards.title') }} — {{ $generatedAt->format('Y-m-d') }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, 'Khmer OS', sans-serif;
            color: #111827;
            margin: 24px;
            font-size: 12px;
        }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .meta { color: #6b7280; font-size: 11px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: .03em; }
        td.num, th.num { text-align: right; }
        tbody tr:nth-child(even) { background: #fafafa; }
        tfoot td { font-weight: bold; background: #f9fafb; }
        .empty { padding: 24px; text-align: center; color: #6b7280; }
        .print-hint {
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            color: #1e40af;
        }
        .print-hint button {
            margin-left: 10px;
            padding: 6px 12px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; }
            .print-hint { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-hint">
        {{ __('app.meter_reading_cards.print_hint') }}
        <button type="button" onclick="window.print()">{{ __('app.meter_reading_cards.print_button') }}</button>
    </div>

    <h1>{{ __('app.meter_reading_cards.title') }}</h1>
    <div class="meta">{{ __('app.meter_reading_cards.generated_on', ['date' => $generatedAt->format('M d, Y H:i')]) }}</div>

    @php $totalCharge = collect($rows)->sum('charge'); @endphp

    <table>
        <thead>
            <tr>
                @foreach($headers as $i => $header)
                    <th class="{{ $i >= 5 ? 'num' : '' }}">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['property'] }}</td>
                    <td>{{ __('app.utility_usage_history.room_n', ['n' => $row['room']]) }}</td>
                    <td>{{ $row['tenant'] }}</td>
                    <td>{{ $row['utility'] }}</td>
                    <td class="num">{{ number_format($row['previous'], 2) }}</td>
                    <td class="num">{{ number_format($row['new'], 2) }}</td>
                    <td class="num">{{ number_format($row['usage'], 2) }}</td>
                    <td class="num">${{ number_format($row['charge'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">{{ __('app.utility_usage_history.no_records') }}</td></tr>
            @endforelse
        </tbody>
        @if(count($rows))
            <tfoot>
                <tr>
                    <td colspan="8" class="num">{{ __('app.meter_reading_cards.total') }}</td>
                    <td class="num">${{ number_format($totalCharge, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <script>
        // Auto-open the print dialog so the user can "Save as PDF".
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>
