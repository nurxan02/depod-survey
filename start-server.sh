#!/bin/bash

# Depod Survey - PHP Development Server
# Bu skript Apache olmadan tətbiqi işə salır

echo "======================================"
echo "Depod Survey - Server Başladılır"
echo "======================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP tapılmadı."
    exit 1
fi

echo "✅ PHP tapıldı ($(php -r 'echo PHP_VERSION;'))"
echo ""

# Get current directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "📁 İş qovluğu: $DIR"
echo ""

# Check if database is configured
if grep -q "define('DB_PASS', '');" "$DIR/config/config.php" 2>/dev/null; then
    echo "⚠️  Database şifrəsi boşdur. Normal olaraq işləməyə bilər."
    echo ""
fi

# Start PHP built-in server
echo "🚀 Server başladılır..."
echo ""
echo "   İstifadəçi Tərəfi: http://localhost:3169/"
echo "   Admin Panel:       http://localhost:3169/admin/login.php"
echo ""
echo "   Default Admin: admin / admin123"
echo ""
echo "⏹️  Dayandırmaq üçün: Ctrl+C"
echo ""
echo "======================================"
echo ""

# Start server
php -S localhost:3169 -t "$DIR"
