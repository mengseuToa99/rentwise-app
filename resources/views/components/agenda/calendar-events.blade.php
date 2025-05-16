@props([
    'events' => [],
    'title' => 'Upcoming Events',
    'maxEvents' => 5,
    'showMore' => true,
])

<div {{ $attributes->merge(['class' => 'w-full bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden']) }}>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h5 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h5>
        
        @if($showMore)
        <a href="{{ route('tenant.calendar') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">
            View all
        </a>
        @endif
    </div>
    
    <div class="p-4">
        @if(count($events) > 0)
            <div class="space-y-4">
                @foreach(array_slice($events, 0, $maxEvents) as $event)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center 
                            @if($event['type'] == 'invoice') bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400
                            @elseif($event['type'] == 'lease') bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400
                            @elseif($event['type'] == 'maintenance') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                            @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                            @endif">
                            @if($event['type'] == 'invoice')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            @elseif($event['type'] == 'lease')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($event['type'] == 'maintenance')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $event['title'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
                                {{ \Carbon\Carbon::parse($event['start'])->format('M d, Y') }}
                                @if($event['type'] == 'invoice')
                                    <span class="inline-flex items-center ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if(\Carbon\Carbon::parse($event['start'])->isPast()) bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                        @elseif(\Carbon\Carbon::parse($event['start'])->diffInDays(now()) <= 7) bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                        @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                        @endif">
                                        @if(\Carbon\Carbon::parse($event['start'])->isPast())
                                            Overdue
                                        @elseif(\Carbon\Carbon::parse($event['start'])->diffInDays(now()) <= 7)
                                            Due soon
                                        @else
                                            Upcoming
                                        @endif
                                    </span>
                                @endif
                            </p>
                            @if(!empty($event['description']))
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    {{ $event['description'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($events) > $maxEvents && $showMore)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="{{ route('tenant.calendar') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 hover:underline">
                        View {{ count($events) - $maxEvents }} more events
                    </a>
                </div>
            @endif
        @else
            <div class="py-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-gray-500 dark:text-gray-400">No upcoming events</p>
            </div>
        @endif
    </div>
</div> 