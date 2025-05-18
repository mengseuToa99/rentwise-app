<div class="flex items-center">
    <div class="flex items-center space-x-2 rtl:space-x-reverse">
        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
            {{ auth()->user() && method_exists(auth()->user(), 'initials') ? auth()->user()->initials() : 'R' }}
        </div>
        <div class="ms-1 grid flex-1 text-start text-sm">
            <span class="mb-0.5 truncate leading-none font-semibold">{{ auth()->user() ? auth()->user()->name : 'Rentwise' }}</span>
        </div>
    </div>
</div>
