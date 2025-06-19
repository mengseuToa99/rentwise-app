#!/bin/bash

# Emergency Rollback Script
# Use this if the performance optimizations break the application

echo "🚨 Emergency Rollback - Reverting Performance Changes"
echo "===================================================="

# Clear all caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rollback database migration
echo "Rolling back performance indexes..."
php artisan migrate:rollback --path=database/migrations/2025_06_18_000001_add_performance_indexes.php

# Reset JavaScript to simple version
echo "Resetting JavaScript..."
cat > resources/js/app.js << 'EOF'
// Simple version without complex optimizations
import './theme.js';

// Global theme toggle function
window.themeToggle = () => ({
    dark: localStorage.theme === 'dark',
    init() {
        if (!localStorage.theme) {
            localStorage.theme = 'light';
        }
    },
    toggle() {
        this.dark = !this.dark;
        localStorage.theme = this.dark ? 'dark' : 'light';
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

// Simple initialization
document.addEventListener('DOMContentLoaded', () => {
    // Let Livewire handle Alpine.js
    console.log('Simple app.js loaded');
});
EOF

# Build assets
echo "Building simple assets..."
npm run build

echo "✅ Emergency rollback complete!"
echo "The application should now work with basic functionality."
echo "You can restart the server and test navigation." 