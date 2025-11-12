#!/bin/bash
set -e

# Install PHP and dependencies
composer install --no-dev --optimize-autoloader

# Create necessary directories
mkdir -p public/uploads
chmod 755 public/uploads

# Create data directory if not exists
mkdir -p data
chmod 755 data

echo "Build completed successfully"
