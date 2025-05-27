<div class="py-4 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 lg:px-6">
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Property</h1>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">Back to Dashboard</a>
            </div>
        </div>
        
        @if($property)
            <div class="bg-white dark:bg-zinc-900 shadow-md rounded-lg overflow-hidden mb-6">
                <!-- Property Header with Image -->
                <div class="relative">
                    @if($property->image_url)
                        <img src="{{ $property->image_url }}" alt="{{ $property->property_name }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                        <h2 class="text-3xl font-bold text-white">{{ $property->property_name }}</h2>
                    </div>
                </div>

                <!-- Property Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Property Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</h4>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $property->address }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">City</h4>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $property->city }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</h4>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $property->property_type ?? 'Residential' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Built</h4>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $property->year_built ?? 'Not specified' }}</p>
                                </div>
                            </div>

                            @if($property->amenities)
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Property Amenities</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach(explode(',', $property->amenities) as $amenity)
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-gray-700 dark:text-gray-300">{{ trim($amenity) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">My Unit: {{ $unit->room_name ?? 'N/A' }}</h3>
                            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Type</h4>
                                        <p class="text-base text-gray-900 dark:text-white">{{ $unit->unit_type ?? 'Apartment' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Floor</h4>
                                        <p class="text-base text-gray-900 dark:text-white">{{ $unit->floor ?? 'Ground' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</h4>
                                        <p class="text-base text-gray-900 dark:text-white">{{ $unit->square_feet ?? '0' }} sq ft</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrooms</h4>
                                        <p class="text-base text-gray-900 dark:text-white">{{ $unit->bedrooms ?? '1' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bathrooms</h4>
                                        <p class="text-base text-gray-900 dark:text-white">{{ $unit->bathrooms ?? '1' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            Occupied
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($rental)
                                <div class="mt-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Lease Information</h4>
                                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</h4>
                                                <p class="text-base text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rental->start_date)->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</h4>
                                                <p class="text-base text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($rental->end_date)->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</h4>
                                                <p class="text-base text-gray-900 dark:text-white">${{ number_format($rental->rental_amount, 2) }}</p>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Security Deposit</h4>
                                                <p class="text-base text-gray-900 dark:text-white">${{ number_format($rental->security_deposit ?? 0, 2) }}</p>
                                            </div>
                                            <div class="col-span-2">
                                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Status</h4>
                                                @php
                                                    $daysUntilExpiry = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($rental->end_date), false);
                                                @endphp
                                                
                                                @if($daysUntilExpiry < 0)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                        Expired {{ abs($daysUntilExpiry) }} days ago
                                                    </span>
                                                @elseif($daysUntilExpiry <= 30)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                        Expires in {{ $daysUntilExpiry }} days
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        Active ({{ $daysUntilExpiry }} days remaining)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Landlord -->
            <div class="bg-white dark:bg-zinc-900 shadow-md rounded-lg overflow-hidden p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Contact Landlord</h3>
                <div class="flex items-center justify-between">
                    <p class="text-gray-700 dark:text-gray-300">Need assistance or have questions about your property?</p>
                    <a href="{{ route('chat') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-zinc-900 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                        </svg>
                        Message Landlord
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 shadow-md rounded-lg p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <h3 class="mt-2 text-base font-medium text-gray-900 dark:text-white">No Property Information Available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't currently have an active lease. Property details will appear here once you have an active rental agreement.</p>
            </div>
        @endif
    </div>
</div> 