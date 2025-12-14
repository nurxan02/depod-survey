# Docker Container Setup - Depod Survey

## 🐳 Container Arxitekturası

Tətbiq 2 ayrı Docker container-də işləyir:

1. **depod-mysql** - MySQL 8.0 verilənlər bazası
2. **depod-survey-app** - PHP 8.4 + Apache web server

## 🚀 Quraşdırma və İşə Salma

### 1. Container-ləri Build və Start Et

```bash
cd /home/khan/apps/depod-survey
docker compose up -d --build
```

### 2. Verilənlər Bazasını Yarat və İmport Et

```bash
# Database yarat
docker exec -i depod-mysql mysql -uroot -pdepod_root_2025 -e "CREATE DATABASE IF NOT EXISTS depod_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Schema.sql-i import et
docker exec -i depod-mysql mysql -uroot -pdepod_root_2025 depod_survey < database/schema.sql
```

### 3. Bağlantını Test Et

```bash
# DB bağlantı testi
docker exec depod-survey-app php -r "try { \$pdo = new PDO('mysql:host=depod-mysql;dbname=depod_survey', 'depod_user', 'depod_pass_2025'); echo 'DB Connection: SUCCESS\n'; } catch(Exception \$e) { echo 'DB Connection: FAILED - ' . \$e->getMessage() . '\n'; }"
```

### 4. Brauzerə Daxil Ol

- **Frontend Survey:** http://localhost:3169
- **Admin Panel:** http://localhost:3169/admin/login.php

## 🔧 Container İdarəetməsi

### Container Statusunu Yoxla

```bash
docker ps | grep depod
```

### Log-ları Bax

```bash
# App logs
docker logs depod-survey-app --tail 50

# MySQL logs
docker logs depod-mysql --tail 50
```

### Container-ləri Dayandır

```bash
docker compose down
```

### Container-ləri Sil (data ilə)

```bash
docker compose down -v
```

### Yenidən Başlat

```bash
docker compose restart
```

## 📊 Verilənlər Bazası İdarəetməsi

### MySQL Container-ə Daxil Ol

```bash
docker exec -it depod-mysql mysql -uroot -pdepod_root_2025 depod_survey
```

### Backup Al

```bash
docker exec depod-mysql mysqldump -uroot -pdepod_root_2025 depod_survey > backup_$(date +%Y%m%d).sql
```

### Backup-dan Restore Et

```bash
docker exec -i depod-mysql mysql -uroot -pdepod_root_2025 depod_survey < backup_20241214.sql
```

## 🌐 Cloudflared Tunnel ilə Xarici Giriş

Cloudflared tunnel-də 3169 portunu expose et:

```bash
cloudflared tunnel --url http://localhost:3169
```

## 📝 Environment Variables

Container-lər `docker-compose.yml` faylında təyin edilmiş aşağıdakı environment variable-ları istifadə edir:

```yaml
DB_HOST=depod-mysql
DB_NAME=depod_survey
DB_USER=depod_user
DB_PASS=depod_pass_2025
MYSQL_ROOT_PASSWORD=depod_root_2025
MYSQL_DATABASE=depod_survey
MYSQL_USER=depod_user
MYSQL_PASSWORD=depod_pass_2025
```

## 🎨 Dizayn Xüsusiyyətləri

### Rəng Paletri

- **Ağ (White):** #FFFFFF - Fon və card-lar
- **Qara (Dark):** #1a1a1a - Header, button-lar, mətinlər
- **Boz (Gray):** #2d2d2d - Hover state-lər
- **Açıq Boz (Light Gray):** #f5f5f5 - Selected state-lər

### Xüsusiyyətlər

- ✅ Heç bir gradient istifadə edilməyib
- ✅ Minimal və modern dizayn
- ✅ Price badge artıq overlay problemi yoxdur
- ✅ Tam responsive (mobile, tablet, desktop)

## 📦 Port Konfiqurasiyası

- **App Container:** Port 3169 (host) → Port 80 (container)
- **MySQL Container:** Port 3306 (yalnız internal network)

## 🔒 Admin Girişi

**İstifadəçi adı:** admin  
**Şifrə:** admin123

**⚠️ Production-da mütləq şifrəni dəyişdirin!**

## 🧹 Təmizlik (Clean Up)

Lokal MySQL sistemdən silindi və yalnız Docker container-lər istifadə edilir:

```bash
# Lokal MySQL yoxdur
systemctl status mysql  # inactive/not found
```

## 🎯 Nəticə

✅ Tətbiq tam olaraq Docker container-lərində işləyir  
✅ Lokal MySQL asılılığı silindi  
✅ Dizayn monoxrom (ağ/qara/boz) palitrasına keçirildi  
✅ Gradient-lər tamamilə silindi  
✅ Price badge overlay problemi həll edildi  
✅ Cloudflared tunnel hazırdır (port 3169)
