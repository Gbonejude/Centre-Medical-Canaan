#!/bin/bash

# Function to handle script termination
cleanup() {
    echo ""
    echo "Stopping all processes..."
    kill $(jobs -p)
    exit
}

# Trap SIGINT (Ctrl+C) and SIGTERM
trap cleanup SIGINT SIGTERM

echo "------------------------------------------------"
echo "🚀 Starting Centre Medical Canaan Development Services"
echo "------------------------------------------------"

# Start Backend (PHP Artisan Serve)
echo "📦 Starting Ziggy: php artisan ziggy:generate"
php artisan ziggy:generate
echo "✅ Ziggy generated"

# Start Optimize
echo "📦 Starting Optimize: php artisan optimize:clear"
php artisan optimize:clear
echo "✅ Optimize cleared"

# Start Backend (PHP Artisan Serve)
echo "📦 Starting Backend: php artisan serve"
php artisan serve &
echo "✅ Backend started"

# Start Frontend (NPM Run Dev)
echo "🎨 Starting Frontend: npm run dev"
npm run dev &
echo "✅ Frontend started"

# Wait for background processes
wait
echo "✅ Frontend started"
