@props([
    'id' => 'utility-chart',
    'title' => 'Utility Usage',
    'labels' => [], // e.g. ["Jan", "Feb", "Mar"]
    'electricity' => [], // e.g. [200, 220, 190] (kWh)
    'water' => [], // e.g. [150, 165, 140] (gallons)
    'gas' => [], // e.g. [50, 60, 45] (therms)
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
            electricity: @json($electricity),
            water: @json($water),
            gas: @json($gas)
        };
        
        // Initialize the chart if it's in the current view
        if (document.getElementById('{{ $id }}')) {
            window.initializeUtilityChart('#{{ $id }}', data);
        }
    });
</script>
@endpush 