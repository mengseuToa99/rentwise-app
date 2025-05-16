/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './node_modules/flowbite/**/*.js'
    ],
    
    darkMode: 'class',
    
    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Figtree',
                    'sans-serif'
                ]
            },
            borderRadius: {
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)'
            },
            colors: {
                background: 'hsl(var(--background))',
                foreground: 'hsl(var(--foreground))',
                card: {
                    DEFAULT: 'hsl(var(--card))',
                    foreground: 'hsl(var(--card-foreground))'
                },
                popover: {
                    DEFAULT: 'hsl(var(--popover))',
                    foreground: 'hsl(var(--popover-foreground))'
                },
                primary: {
                    DEFAULT: 'hsl(var(--primary))',
                    foreground: 'hsl(var(--primary-foreground))'
                },
                secondary: {
                    DEFAULT: 'hsl(var(--secondary))',
                    foreground: 'hsl(var(--secondary-foreground))'
                },
                muted: {
                    DEFAULT: 'hsl(var(--muted))',
                    foreground: 'hsl(var(--muted-foreground))'
                },
                accent: {
                    DEFAULT: 'hsl(var(--accent))',
                    foreground: 'hsl(var(--accent-foreground))'
                },
                destructive: {
                    DEFAULT: 'hsl(var(--destructive))',
                    foreground: 'hsl(var(--destructive-foreground))'
                },
                border: 'hsl(var(--border))',
                input: 'hsl(var(--input))',
                ring: 'hsl(var(--ring))',
                chart: {
                    '1': 'hsl(var(--chart-1))',
                    '2': 'hsl(var(--chart-2))',
                    '3': 'hsl(var(--chart-3))',
                    '4': 'hsl(var(--chart-4))',
                    '5': 'hsl(var(--chart-5))'
                },
                sidebar: {
                    DEFAULT: 'hsl(var(--sidebar-background))',
                    foreground: 'hsl(var(--sidebar-foreground))',
                    primary: 'hsl(var(--sidebar-primary))',
                    'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
                    accent: 'hsl(var(--sidebar-accent))',
                    'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
                    border: 'hsl(var(--sidebar-border))',
                    ring: 'hsl(var(--sidebar-ring))'
                }
            },
            perspective: {
                '1000': '1000px',
            },
            transformStyle: {
                'preserve-3d': 'preserve-3d',
            },
            backfaceVisibility: {
                hidden: 'hidden',
            },
            transform: {
                'rotateY-180': 'rotateY(180deg)',
            },
            size: {
                '4': '1rem',
                '5': '1.25rem',
            }
        }
    },
    
    plugins: [
        require('@tailwindcss/forms'),
        require('flowbite/plugin'),
        function({ addUtilities }) {
            const newUtilities = {
                '.perspective-1000': {
                    perspective: '1000px',
                },
                '.transform-style-preserve-3d': {
                    transformStyle: 'preserve-3d',
                },
                '.backface-hidden': {
                    backfaceVisibility: 'hidden',
                },
                '.rotate-y-180': {
                    transform: 'rotateY(180deg)',
                },
                '.size-4': {
                    width: '1rem',
                    height: '1rem',
                },
                '.size-5': {
                    width: '1.25rem',
                    height: '1.25rem',
                },
                '.size-9': {
                    width: '2.25rem',
                    height: '2.25rem',
                },
            }
            addUtilities(newUtilities)
        },
    ],
}; 