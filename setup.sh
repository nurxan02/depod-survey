#!/bin/bash

# Depod Survey - Sürətli Quraşdırma
# Bu skript sadələşdirilmiş quraşdırma prosesidir

echo "======================================"
echo "Depod Survey - Sürətli Quraşdırma"
echo "======================================"
echo ""

# Default values
DB_HOST="localhost"
DB_NAME="depod_survey"
DB_USER="root"
DB_PASS=""

echo "ℹ️  Database məlumatlarını daxil edin (Enter = default):"
echo ""

read -p "DB Host [$DB_HOST]: " input
DB_HOST=${input:-$DB_HOST}

read -p "DB Name [$DB_NAME]: " input
DB_NAME=${input:-$DB_NAME}

read -p "DB User [$DB_USER]: " input
DB_USER=${input:-$DB_USER}

read -sp "DB Password [boş]: " input
DB_PASS=${input:-$DB_PASS}
echo ""
echo ""

# Test MySQL connection
echo "🔍 MySQL bağlantısı test edilir..."
if command -v mysql &> /dev/null; then
    if [ -z "$DB_PASS" ]; then
        mysql -h "$DB_HOST" -u "$DB_USER" -e ";" 2>/dev/null
    else
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e ";" 2>/dev/null
    fi
    
    if [ $? -eq 0 ]; then
        echo "✅ MySQL bağlantısı uğurlu"
        
        # Create database
        echo "📦 Database yaradılır..."
        if [ -z "$DB_PASS" ]; then
            mysql -h "$DB_HOST" -u "$DB_USER" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        else
            mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        fi
        
        if [ $? -eq 0 ]; then
            echo "✅ Database yaradıldı: $DB_NAME"
            
            # Import schema
            echo "📊 Schema import edilir..."
            if [ -z "$DB_PASS" ]; then
                mysql -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" < database/schema.sql 2>/dev/null
            else
                mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql 2>/dev/null
            fi
            
            if [ $? -eq 0 ]; then
                echo "✅ Schema import edildi"
            else
                echo "⚠️  Schema import edilərkən xəta (ola bilsin artıq import olunub)"
            fi
        else
            echo "⚠️  Database yaradılmadı (ola bilsin artıq mövcuddur)"
        fi
    else
        echo "❌ MySQL bağlantısı uğursuz. Məlumatları yoxlayın."
        exit 1
    fi
else
    echo "⚠️  MySQL tapılmadı. Əl ilə database yaradın."
fi

echo ""
echo "⚙️  Konfiqurasiya faylı yenilənir..."

# Escape special characters for sed
DB_PASS_ESCAPED=$(echo "$DB_PASS" | sed 's/[\/&]/\\&/g')

# Update config file
cp config/config.php config/config.php.backup 2>/dev/null
sed -i "s/define('DB_HOST', 'localhost');/define('DB_HOST', '$DB_HOST');/" config/config.php
sed -i "s/define('DB_NAME', 'depod_survey');/define('DB_NAME', '$DB_NAME');/" config/config.php
sed -i "s/define('DB_USER', 'root');/define('DB_USER', '$DB_USER');/" config/config.php
sed -i "s/define('DB_PASS', '');/define('DB_PASS', '$DB_PASS_ESCAPED');/" config/config.php

echo "✅ Konfiqurasiya yeniləndi"
echo ""

echo "======================================"
echo "✅ Quraşdırma tamamlandı!"
echo "======================================"
echo ""
echo "🚀 Tətbiqi işə salmaq üçün:"
echo ""
echo "   ./start-server.sh"
echo ""
echo "Və ya brauzer-də açın:"
echo ""
echo "   İstifadəçi: http://localhost:8000/"
echo "   Admin:      http://localhost:8000/admin/login.php"
echo ""
echo "Default admin: admin / admin123"
echo ""
echo "⚠️  İlk girişdən sonra admin şifrəsini dəyişdirin!"
echo ""
