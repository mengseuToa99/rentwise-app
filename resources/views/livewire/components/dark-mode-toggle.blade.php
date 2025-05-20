<div
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
            darkMode = true;
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
            darkMode = false;
        }
    "
    class="inline-block"
>
    <button
        @click="
            darkMode = !darkMode;
            localStorage.setItem('darkMode', darkMode);
            if (darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        "
        class="flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500"
        aria-label="Toggle Dark Mode"
    >
        <svg
            x-show="!darkMode"
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
        <svg
            x-show="darkMode"
            class="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
    </button>
</div> 