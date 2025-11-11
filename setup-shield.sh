#!/bin/bash

###############################################################################
# Majalis - Shield Setup Script
#
# This script properly sets up Filament Shield with all required permissions.
# It handles the common issue where screens disappear after generating shields.
#
# Usage: bash setup-shield.sh
###############################################################################

echo "🛡️  Starting Majalis Shield Setup..."
echo ""

# Step 1: Clear all caches to avoid conflicts
echo "1️⃣  Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 2: Install Shield if not already installed
echo ""
echo "2️⃣  Checking Shield installation..."
if ! grep -q "bezhanSalleh/filament-shield" composer.json; then
    echo "Installing Filament Shield..."
    composer require bezhansalleh/filament-shield
fi

# Step 3: Publish Shield configuration and migrations
echo ""
echo "3️⃣  Publishing Shield configuration..."
php artisan vendor:publish --tag="filament-shield-config"

# Step 4: Run migrations to create permissions tables
echo ""
echo "4️⃣  Running migrations..."
php artisan migrate

# Step 5: Generate Shield resources (policies and permissions)
echo ""
echo "5️⃣  Generating Shield policies and permissions..."
php artisan shield:generate --all

# Step 6: Run Shield seeder to create super admin
echo ""
echo "6️⃣  Creating super admin and assigning permissions..."
php artisan db:seed --class=ShieldSeeder

# Step 7: Clear cache again after setup
echo ""
echo "7️⃣  Final cache clear..."
php artisan optimize:clear

echo ""
echo "✅ Shield setup completed successfully!"
echo ""
echo "📋 Next Steps:"
echo "   1. Visit: http://your-domain/admin"
echo "   2. Login with: admin@majalis.om"
echo "   3. Password: password"
echo "   4. ⚠️  IMMEDIATELY change the password!"
echo "   5. Navigate to Shield > Roles to manage permissions"
echo ""
