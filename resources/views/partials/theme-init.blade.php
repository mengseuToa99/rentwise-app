{{--
    Synchronous theme primitive. Runs before Alpine/Livewire/Flux boot so that
    x-data="themeToggle()" can resolve against window.themeToggle.
    Defines: window.Theme, window.themeToggle. Also registers Alpine.data on alpine:init.

    Flux integration: Flux maintains its own preference in localStorage["flux.appearance"]
    (defaults to "system" — which follows OS dark mode and overrides ours on every nav).
    We mirror our theme into "flux.appearance" so Flux stays in sync.
--}}
<script>
    (function () {
        var KEY = 'theme';
        var FLUX_KEY = 'flux.appearance';

        if (!localStorage.getItem(KEY)) localStorage.setItem(KEY, 'light');

        function isDark() { return localStorage.getItem(KEY) === 'dark'; }
        function apply(theme) {
            var dark = theme === 'dark';
            document.documentElement.classList.toggle('dark', dark);
            // Keep Flux in lockstep so it doesn't revert to "system" on navigation.
            localStorage.setItem(FLUX_KEY, theme);
            if (window.$flux && 'appearance' in window.$flux) window.$flux.appearance = theme;
            window.dispatchEvent(new CustomEvent('theme:changed', { detail: { theme: theme, isDark: dark } }));
        }
        function set(theme) {
            var t = theme === 'dark' ? 'dark' : 'light';
            localStorage.setItem(KEY, t);
            apply(t);
            return t;
        }

        function enforce() {
            // Re-apply immediately and after the next ticks so other initializers
            // (including Flux appearance hooks) cannot override user preference.
            window.Theme.apply();
            requestAnimationFrame(function () { window.Theme.apply(); });
            setTimeout(function () { window.Theme.apply(); }, 50);
        }

        apply(isDark() ? 'dark' : 'light');

        window.Theme = {
            get: function () { return isDark() ? 'dark' : 'light'; },
            set: set,
            toggle: function () { return set(isDark() ? 'light' : 'dark'); },
            apply: function () { apply(isDark() ? 'dark' : 'light'); }
        };

        var factory = function () {
            return {
                dark: isDark(),
                init: function () {
                    var self = this;
                    window.addEventListener('theme:changed', function (e) {
                        self.dark = !!(e.detail && e.detail.isDark);
                    });
                },
                toggle: function () { this.dark = window.Theme.toggle() === 'dark'; }
            };
        };

        // Path A: x-data="themeToggle()" resolves against window globals.
        window.themeToggle = factory;

        // Path B: x-data="themeToggle" (no parens) resolves via Alpine.data registry.
        document.addEventListener('alpine:init', function () {
            if (window.Alpine && typeof window.Alpine.data === 'function') {
                window.Alpine.data('themeToggle', factory);
            }
        });

        // Re-apply on Livewire navigation — beats Flux's default "system" behavior.
        document.addEventListener('livewire:navigated', function () {
            enforce();
        });

        document.addEventListener('DOMContentLoaded', enforce);
        window.addEventListener('load', enforce);
    })();
</script>
