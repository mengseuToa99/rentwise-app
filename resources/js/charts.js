import ApexCharts from 'apexcharts';

// Chart instances registry to keep track of created charts
window.chartInstances = {};
window.chartOptions = {};

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

// Function to reinitialize all charts
window.reinitializeAllCharts = () => {
    for (const elementId in window.chartOptions) {
        const chartData = window.chartOptions[elementId];
        const element = document.querySelector(elementId);
        
        if (element) {
            if (chartData.type === 'spending') {
                window.initializeSpendingChart(elementId, chartData.data);
            } else if (chartData.type === 'utility') {
                window.initializeUtilityChart(elementId, chartData.data);
            }
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

// Start observing the body for changes
document.addEventListener('DOMContentLoaded', function() {
    observer.observe(document.body, { 
        childList: true,
        subtree: true
    });
});

// Re-render all charts on tab visibility change
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Re-render all charts when tab becomes visible
        setTimeout(window.reinitializeAllCharts, 100);
    }
});

// Also handle navigation events via Livewire
document.addEventListener('livewire:navigated', function() {
    setTimeout(window.reinitializeAllCharts, 100);
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