#!/bin/bash

echo "------------------------------------------------"
echo "🛠️  Setting up Centre Medical Canaan project"
echo "------------------------------------------------"

# 1. Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📄 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  NOTE: Please ensure you have created the database 'centre_medical_canaan' manually."
else
    echo "ℹ️  .env file already exists, skipping copy."
fi

# 2. Install PHP dependencies
echo "📦 Installing PHP dependencies (ignoring platform issues)..."
# Using --ignore-platform-reqs to bypass missing/mismatched PHP extensions
composer install --ignore-platform-reqs

# 3. Generate Application Key
echo "🔑 Generating Laravel Application Key..."
php artisan key:generate

# 4. Run Migrations and Seed Database
echo "🗄️  Running Database Migrations and Seeding..."
php artisan migrate:fresh --seed

# 5. Install Node dependencies
echo "📦 Installing Node dependencies (NPM)..."
npm install

echo "------------------------------------------------"
echo "✅ Setup complete!"
echo "🚀 You can now start the project using ./start.sh"
echo "------------------------------------------------"
