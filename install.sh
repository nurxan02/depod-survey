#!/bin/bash

# Depod Survey Installation Script
# This script helps automate the installation process

echo "======================================"
echo "Depod Survey - Quraşdırma Skripti"
echo "======================================"
echo ""

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL tapılmadı. Zəhmət olmasa MySQL-i quraşdırın."
    exit 1
fi

echo "✅ MySQL tapıldı"

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP tapılmadı. Zəhmət olmasa PHP-ni quraşdırın."
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✅ PHP tapıldı (Versiya: $PHP_VERSION)"

# Check PDO MySQL extension
if ! php -m | grep -q "pdo_mysql"; then
    echo "❌ PDO MySQL extension tapılmadı. Zəhmət olmasa quraşdırın."
    exit 1
fi

echo "✅ PDO MySQL extension mövcuddur"

echo ""
echo "Verilənlər bazası məlumatlarını daxil edin:"
echo "------------------------------------"

# Database configuration
read -p "DB Host (default: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "DB Name (default: depod_survey): " DB_NAME
DB_NAME=${DB_NAME:-depod_survey}

read -p "DB User (default: root): " DB_USER
DB_USER=${DB_USER:-root}

read -sp "DB Password: " DB_PASS
echo ""

# Test database connection
echo ""
echo "Verilənlər bazası bağlantısı yoxlanılır..."

if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e ";" 2>/dev/null; then
    echo "✅ Verilənlər bazası bağlantısı uğurlu"
else
    echo "❌ Verilənlər bazasına bağlanmaq alınmadı"
    exit 1
fi

# Create database
echo ""
echo "Verilənlər bazası yaradılır..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Verilənlər bazası yaradıldı: $DB_NAME"
else
    echo "❌ Verilənlər bazası yaradılmadı"
    exit 1
fi

# Import schema
echo ""
echo "Schema import edilir..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Schema uğurla import edildi"
else
    echo "❌ Schema import edilərkən xəta baş verdi"
    exit 1
fi

# Update config file
echo ""
echo "Konfiqurasiya faylı yenilənir..."

sed -i "s/define('DB_HOST', 'localhost');/define('DB_HOST', '$DB_HOST');/" config/config.php
sed -i "s/define('DB_NAME', 'depod_survey');/define('DB_NAME', '$DB_NAME');/" config/config.php
sed -i "s/define('DB_USER', 'root');/define('DB_USER', '$DB_USER');/" config/config.php
sed -i "s/define('DB_PASS', '');/define('DB_PASS', '$DB_PASS');/" config/config.php

echo "✅ Konfiqurasiya faylı yeniləndi"

# Set permissions
echo ""
echo "Fayl icazələri təyin edilir..."
chmod 644 config/config.php
chmod 644 .htaccess

echo "✅ Fayl icazələri təyin edildi"

# Installation complete
echo ""
echo "======================================"
echo "✅ Quraşdırma tamamlandı!"
echo "======================================"
echo ""
echo "İndi tətbiqi açın:"
echo "http://localhost/depod-survey/"
echo ""
echo "Admin Panel:"
echo "http://localhost/depod-survey/admin/login.php"
echo ""
echo "Default admin məlumatları:"
echo "İstifadəçi adı: admin"
echo "Şifrə: admin123"
echo ""
echo "⚠️ ÖNƏMLİ: İlk girişdən sonra admin şifrəsini dəyişdirin!"
echo ""
