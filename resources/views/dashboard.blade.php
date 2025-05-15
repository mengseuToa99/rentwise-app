<x-layouts.app :title="__('Dashboard')">
    <div class="container mx-auto px-4">
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <div class="grid gap-6 md:grid-cols-2">
                <x-charts.spending-chart 
                    id="monthly-spending-chart"
                    title="Monthly Spending"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
                    :amounts="[1200, 1100, 1350, 1000, 1050, 1150, 1250, 1300, 1150, 1200, 1100, 1250]"
                    data-persist-chart="true"
                />

                <x-charts.utility-chart 
                    id="utility-usage-chart"
                    title="Utility Usage"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
                    :electricity="[230, 210, 240, 200, 250, 260, 280, 300, 270, 240, 230, 250]"
                    :water="[140, 130, 150, 135, 145, 160, 170, 180, 165, 150, 140, 155]"
                    :gas="[60, 70, 65, 50, 45, 40, 35, 45, 60, 70, 75, 80]"
                    data-persist-chart="true"
                />
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-3">
                <div class="flex flex-col rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Current Month Spending</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">$1,250</p>
                    <span class="mt-2 text-sm text-gray-500 dark:text-gray-400">4% increase from last month</span>
                </div>

                <div class="flex flex-col rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Electricity Usage</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">250 kWh</p>
                    <span class="mt-2 text-sm text-gray-500 dark:text-gray-400">8% increase from last month</span>
                </div>

                <div class="flex flex-col rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Water Usage</h3>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">155 gallons</p>
                    <span class="mt-2 text-sm text-gray-500 dark:text-gray-400">10% increase from last month</span>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
