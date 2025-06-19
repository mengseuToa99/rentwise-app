#!/bin/bash

# Performance Test Script for Navigation Optimizations
# Run this script to verify navigation performance improvements

echo "🚀 RentWise Navigation Performance Test"
echo "======================================="
echo

# Check if server is running
if ! curl -f -s http://127.0.0.1:8000 > /dev/null; then
    echo "❌ Laravel server is not running on http://127.0.0.1:8000"
    echo "Please start the server with: php artisan serve"
    exit 1
fi

echo "✅ Server is running"
echo

# Test dashboard page load time
echo "📊 Testing Dashboard Performance..."
dashboard_time=$(curl -w "%{time_total}" -o /dev/null -s http://127.0.0.1:8000/dashboard)
echo "Dashboard load time: ${dashboard_time} seconds"

if (( $(echo "$dashboard_time < 1.0" | bc -l) )); then
    echo "✅ Dashboard performance: EXCELLENT (< 1s)"
elif (( $(echo "$dashboard_time < 2.0" | bc -l) )); then
    echo "⚠️  Dashboard performance: GOOD (< 2s)"
else
    echo "❌ Dashboard performance: NEEDS IMPROVEMENT (> 2s)"
fi
echo

# Test JavaScript bundle size
echo "📦 Testing JavaScript Bundle Size..."
app_js_size=$(stat -c%s public/build/js/app-*.js 2>/dev/null | head -1)
if [ -n "$app_js_size" ]; then
    app_js_kb=$((app_js_size / 1024))
    echo "Main JavaScript bundle: ${app_js_kb} KB"
    
    if [ $app_js_kb -lt 50 ]; then
        echo "✅ Bundle size: EXCELLENT (< 50KB)"
    elif [ $app_js_kb -lt 100 ]; then
        echo "⚠️  Bundle size: GOOD (< 100KB)"
    else
        echo "❌ Bundle size: NEEDS IMPROVEMENT (> 100KB)"
    fi
else
    echo "❌ Could not find JavaScript bundle file"
fi
echo

# Test CSS bundle size
echo "🎨 Testing CSS Bundle Size..."
app_css_size=$(stat -c%s public/build/assets/app-*.css 2>/dev/null | head -1)
if [ -n "$app_css_size" ]; then
    app_css_kb=$((app_css_size / 1024))
    echo "Main CSS bundle: ${app_css_kb} KB"
    
    if [ $app_css_kb -lt 200 ]; then
        echo "✅ CSS size: EXCELLENT (< 200KB)"
    elif [ $app_css_kb -lt 400 ]; then
        echo "⚠️  CSS size: GOOD (< 400KB)"
    else
        echo "❌ CSS size: NEEDS IMPROVEMENT (> 400KB)"
    fi
else
    echo "❌ Could not find CSS bundle file"
fi
echo

# Test database indexes
echo "🗄️  Testing Database Indexes..."
index_count=$(php artisan tinker --execute="echo count(\DB::select(\"SELECT indexname FROM pg_indexes WHERE tablename IN ('users', 'rental_details', 'invoice_details', 'property_details', 'room_details')\"));" 2>/dev/null | tail -1)
echo "Database indexes created: ${index_count}"

if [ "$index_count" -gt 20 ]; then
    echo "✅ Database indexes: EXCELLENT (${index_count} indexes)"
elif [ "$index_count" -gt 10 ]; then
    echo "⚠️  Database indexes: GOOD (${index_count} indexes)"
else
    echo "❌ Database indexes: NEEDS IMPROVEMENT (${index_count} indexes)"
fi
echo

# Test cache configuration
echo "💾 Testing Cache Configuration..."
cache_driver=$(php artisan tinker --execute="echo config('cache.default');" 2>/dev/null | tail -1)
echo "Cache driver: ${cache_driver}"

if [ "$cache_driver" = "redis" ]; then
    echo "✅ Cache: EXCELLENT (Redis)"
elif [ "$cache_driver" = "file" ]; then
    echo "⚠️  Cache: GOOD (File)"
else
    echo "❌ Cache: NEEDS IMPROVEMENT (${cache_driver})"
fi
echo

# Performance Summary
echo "📋 Performance Summary"
echo "====================="
echo "Run this script regularly to monitor performance"
echo "Target metrics:"
echo "- Dashboard load time: < 1 second"
echo "- JavaScript bundle: < 50KB"
echo "- CSS bundle: < 200KB"
echo "- Database indexes: > 20"
echo "- Cache driver: Redis (production)"
echo
echo "🎯 For detailed performance monitoring in development:"
echo "   Open browser console and run: window.getPerformanceStats()"
echo
echo "🔧 To run database performance analysis:"
echo "   php artisan db:monitor"
echo
echo "✨ Optimization complete! Happy coding! 🚀" 