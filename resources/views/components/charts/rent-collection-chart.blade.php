@props([
    'id' => 'rent-collection-chart',
    'title' => 'Rent Collection',
    'labels' => [], // e.g. ["Jan", "Feb", "Mar"]
    'paid' => [], // e.g. [5000, 5200, 4800]
    'pending' => [], // e.g. [1000, 800, 1200]
])

<div {{ $attributes->merge(['class' => 'w-full bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden']) }}>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h5 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h5>
    </div>
    
    <div class="p-4">
        <div id="{{ $id }}" class="w-full" style="min-height: 350px;"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data for the chart
        const data = {
            labels: @json($labels),
            paid: @json($paid),
            pending: @json($pending)
        };
        
        // Store the data but don't initialize immediately to prevent flickering
        // Just store the data in the global registry
        window.chartOptions['#{{ $id }}'] = {
            type: 'rentCollection',
            data: data
        };
        
        // Let the global debounce mechanism handle initialization
    });
</script>
@endpush 