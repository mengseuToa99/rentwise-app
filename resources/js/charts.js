import ApexCharts from 'apexcharts';

// Chart instances registry to keep track of created charts
window.chartInstances = {};
window.chartOptions = {};

// Debounce mechanism to prevent excessive chart redraws
window.chartDebounceTimer = null;
window.lastChartInitTime = 0;
window.initializingCharts = false;

// Debounced version of reinitializeAllCharts to prevent multiple rapid calls
window.debouncedReinitializeCharts = function(immediate = false) {
    const now = Date.now();
    const timeSinceLastInit = now - window.lastChartInitTime;
    
    // Clear any pending reinitialization
    if (window.chartDebounceTimer) {
        clearTimeout(window.chartDebounceTimer);
    }
    
    // If charts are currently being initialized, delay any new attempt
    if (window.initializingCharts) {
        window.chartDebounceTimer = setTimeout(() => window.debouncedReinitializeCharts(), 500);
        return;
    }
    
    // If it's been less than 300ms since last init and not immediate, debounce
    if (timeSinceLastInit < 300 && !immediate) {
        window.chartDebounceTimer = setTimeout(() => window.debouncedReinitializeCharts(), 300);
        return;
    }
    
    // Otherwise process the reinitialization
    window.chartDebounceTimer = setTimeout(() => {
        window.initializingCharts = true;
        window.lastChartInitTime = Date.now();
        
        try {
            window.reinitializeAllCharts();
        } catch (error) {
            console.error('Error during chart reinitialization:', error);
        } finally {
            window.initializingCharts = false;
        }
    }, immediate ? 0 : 100);
};

// Initialize spending chart
window.initializeSpendingChart = (elementId, data) => {
    // Store the data in the global state
    window.chartOptions[elementId] = {
        type: 'spending',
        data: data
    };
    
    // Check if a chart instance already exists, destroy it if it does
    if (window.chartInstances[elementId]) {
        window.chartInstances[elementId].destroy();
    }
    
    const options = {
        series: [{
            name: 'Spending',
            data: data.amounts || []
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            },
            foreColor: '#64748b',
            fontFamily: 'Figtree, sans-serif',
            animations: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return '$' + val.toFixed(0);
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#3b82f6'],
        markers: {
            size: 5,
            colors: ['#3b82f6'],
            strokeColors: '#ffffff',
            strokeWidth: 2
        },
        xaxis: {
            categories: data.labels || [],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '$' + val.toFixed(0);
                }
            },
            min: function(min) {
                // Start y-axis just below the lowest value
                return min > 0 ? 0 : min;
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return '$' + val.toFixed(2);
                }
            }
        }
    };

    // Handle case when element doesn't exist yet
    const element = document.querySelector(elementId);
    if (!element) {
        console.warn(`Element ${elementId} not found. Chart not rendered.`);
        return null;
    }

    const chart = new ApexCharts(element, options);
    chart.render();
    
    // Store the chart instance
    window.chartInstances[elementId] = chart;
    
    return chart;
};

// Initialize utility usage chart
window.initializeUtilityChart = (elementId, data) => {
    // Store the data in the global state
    window.chartOptions[elementId] = {
        type: 'utility',
        data: data
    };
    
    // Check if a chart instance already exists, destroy it if it does
    if (window.chartInstances[elementId]) {
        window.chartInstances[elementId].destroy();
    }
    
    const options = {
        series: [
            {
                name: 'Electricity (kWh)',
                data: data.electricity || []
            },
            {
                name: 'Water (gallons)', 
                data: data.water || []
            },
            {
                name: 'Gas (therms)',
                data: data.gas || []
            }
        ],
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            },
            foreColor: '#64748b',
            fontFamily: 'Figtree, sans-serif',
            animations: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: [3, 3, 3],
            dashArray: [0, 0, 0]
        },
        colors: ['#4ade80', '#3b82f6', '#f97316'],
        markers: {
            size: 5,
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            categories: data.labels || [],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: [
            {
                title: {
                    text: 'Electricity (kWh)'
                },
                labels: {
                    formatter: function(val) {
                        return val.toFixed(0) + ' kWh';
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Water & Gas'
                },
                labels: {
                    formatter: function(val) {
                        return val.toFixed(0);
                    }
                }
            }
        ],
        tooltip: {
            y: {
                formatter: function(val, { seriesIndex }) {
                    const units = ['kWh', 'gallons', 'therms'];
                    return val + ' ' + (units[seriesIndex] || 'units');
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    // Handle case when element doesn't exist yet
    const element = document.querySelector(elementId);
    if (!element) {
        console.warn(`Element ${elementId} not found. Chart not rendered.`);
        return null;
    }

    const chart = new ApexCharts(element, options);
    chart.render();
    
    // Store the chart instance
    window.chartInstances[elementId] = chart;
    
    return chart;
};

// Initialize occupancy rate chart
window.initializeOccupancyChart = (elementId, data) => {
    // Store the data in the global state
    window.chartOptions[elementId] = {
        type: 'occupancy',
        data: data
    };
    
    // Check if a chart instance already exists, destroy it if it does
    if (window.chartInstances[elementId]) {
        window.chartInstances[elementId].destroy();
    }
    
    const options = {
        series: [{
            name: 'Occupancy Rate',
            data: data.rates || []
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            foreColor: '#64748b',
            fontFamily: 'Figtree, sans-serif',
            animations: {
                enabled: true
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + '%';
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#6366f1'], // Indigo color
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        markers: {
            size: 5,
            colors: ['#6366f1'],
            strokeColors: '#ffffff',
            strokeWidth: 2
        },
        xaxis: {
            categories: data.labels || [],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            min: 0,
            max: 100,
            labels: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        }
    };

    // Handle case when element doesn't exist yet
    const element = document.querySelector(elementId);
    if (!element) {
        console.warn(`Element ${elementId} not found. Chart not rendered.`);
        return null;
    }

    const chart = new ApexCharts(element, options);
    chart.render();
    
    // Store the chart instance
    window.chartInstances[elementId] = chart;
    
    return chart;
};

// Initialize rent collection chart
window.initializeRentCollectionChart = (elementId, data) => {
    // Store the data in the global state
    window.chartOptions[elementId] = {
        type: 'rentCollection',
        data: data
    };
    
    // Check if a chart instance already exists, destroy it if it does
    if (window.chartInstances[elementId]) {
        window.chartInstances[elementId].destroy();
    }
    
    const options = {
        series: [
            {
                name: 'Paid',
                data: data.paid || []
            },
            {
                name: 'Pending',
                data: data.pending || []
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: false
            },
            foreColor: '#64748b',
            fontFamily: 'Figtree, sans-serif',
            animations: {
                enabled: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 3
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 0
        },
        colors: ['#10b981', '#f59e0b'], // Green for paid, amber for pending
        fill: {
            opacity: 1
        },
        xaxis: {
            categories: data.labels || [],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '$' + val.toFixed(0);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return '$' + val.toFixed(2);
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    // Handle case when element doesn't exist yet
    const element = document.querySelector(elementId);
    if (!element) {
        console.warn(`Element ${elementId} not found. Chart not rendered.`);
        return null;
    }

    const chart = new ApexCharts(element, options);
    chart.render();
    
    // Store the chart instance
    window.chartInstances[elementId] = chart;
    
    return chart;
};

// Function to reinitialize all charts
window.reinitializeAllCharts = () => {
    console.log('Reinitializing all charts...');
    console.log('Chart options:', Object.keys(window.chartOptions));
    
    for (const elementId in window.chartOptions) {
        const chartData = window.chartOptions[elementId];
        const element = document.querySelector(elementId);
        
        if (element) {
            console.log(`Reinitializing chart: ${elementId}, type: ${chartData.type}`);
            
            try {
                if (chartData.type === 'spending') {
                    window.initializeSpendingChart(elementId, chartData.data);
                } else if (chartData.type === 'utility') {
                    window.initializeUtilityChart(elementId, chartData.data);
                } else if (chartData.type === 'occupancy') {
                    window.initializeOccupancyChart(elementId, chartData.data);
                } else if (chartData.type === 'rentCollection') {
                    window.initializeRentCollectionChart(elementId, chartData.data);
                }
            } catch (error) {
                console.error(`Error reinitializing chart ${elementId}:`, error);
            }
        } else {
            console.warn(`Element ${elementId} not found for reinitialization`);
        }
    }
};

// Watch for DOM mutations to detect when charts need to be reinitialized
let observer = new MutationObserver(function(mutations) {
    for (const mutation of mutations) {
        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
            // Check if any charts need to be re-rendered
            for (const elementId in window.chartOptions) {
                if (document.querySelector(elementId) && !window.chartInstances[elementId]) {
                    window.reinitializeAllCharts();
                    break;
                }
            }
        }
    }
});

// Immediately attempt to render charts on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up chart observers');
    
    // Start observing DOM changes to detect chart containers
    observer.observe(document.body, { 
        childList: true,
        subtree: true
    });
    
    // Immediate first attempt to render charts
    window.debouncedReinitializeCharts(true);
});

// Re-render all charts on tab visibility change
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        console.log('Tab became visible, reinitializing charts');
        // Re-render all charts when tab becomes visible
        window.debouncedReinitializeCharts();
    }
});

// Also handle navigation events via Livewire
document.addEventListener('livewire:navigated', function() {
    console.log('Livewire navigation detected, reinitializing charts');
    window.debouncedReinitializeCharts();
});

// Also handle Livewire updates which might affect charts
document.addEventListener('livewire:update', function() {
    console.log('Livewire update detected, reinitializing charts');
    window.debouncedReinitializeCharts();
});

// Reinitialize all charts when the page loads
window.addEventListener('load', function() {
    setTimeout(window.reinitializeAllCharts, 100);
});

// Reinitialize all charts when window is resized
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(window.reinitializeAllCharts, 250);
}); 