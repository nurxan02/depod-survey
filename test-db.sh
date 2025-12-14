#!/bin/bash

# Test database connection

echo "🔍 Testing database connection..."
echo ""

cd /home/khan/apps/depod-survey

php -r "
define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'classes/Database.php';

try {
    \$db = Database::getInstance();
    echo '✅ Database connection successful!' . PHP_EOL;
    echo '' . PHP_EOL;
    
    // Test query
    \$result = \$db->query('SELECT COUNT(*) as count FROM questions')->fetch();
    echo '📊 Questions in database: ' . \$result['count'] . PHP_EOL;
    
    \$result = \$db->query('SELECT COUNT(*) as count FROM products')->fetch();
    echo '🎧 Products in database: ' . \$result['count'] . PHP_EOL;
    
    \$result = \$db->query('SELECT COUNT(*) as count FROM admin_users')->fetch();
    echo '👤 Admin users: ' . \$result['count'] . PHP_EOL;
    
    echo '' . PHP_EOL;
    echo '✅ Everything is ready!' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo ""
echo "🚀 Start server with: ./start-server.sh"
echo ""
