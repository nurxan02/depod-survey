# DEPOD SURVEY - SÜRƏTLƏ QURAŞDIRMA

## 1-ci Üsul: Avtomatik Quraşdırma (Tövsiyə edilir)

Terminal-da aşağıdakı əmri icra edin:

```bash
cd /home/khan/apps/depod-survey
./install.sh
```

Skript sizə addım-addım yol göstərəcək.

---

## 2-ci Üsul: Əl ilə Quraşdırma

### Addım 1: MySQL Database Yaradın

```bash
mysql -u root -p
```

MySQL-də:

```sql
CREATE DATABASE depod_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### Addım 2: Schema-nı Import Edin

```bash
mysql -u root -p depod_survey < database/schema.sql
```

### Addım 3: config/config.php Faylını Redaktə Edin

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'depod_survey');
define('DB_USER', 'root');
define('DB_PASS', 'sizin_şifrəniz');
```

### Addım 4: Web Serveri Yenidən Başladın (əgər lazımdırsa)

Apache üçün:

```bash
sudo systemctl restart apache2
```

---

## Giriş Məlumatları

**Sorğu Səhifəsi:**

```
http://localhost/depod-survey/
```

**Admin Panel:**

```
http://localhost/depod-survey/admin/login.php
```

**Default Admin:**

- İstifadəçi adı: `admin`
- Şifrə: `admin123`

---

## Test

1. Brauzer-də açın: `http://localhost/depod-survey/`
2. Sorğunu doldurun
3. Admin panel-ə daxil olun və nəticələrə baxın

---

## Problemlərlə Qarşılaşdıqda

### PHP Extension yoxdursa:

```bash
sudo apt-get install php-mysql php-mbstring
sudo systemctl restart apache2
```

### .htaccess işləmirsə:

Apache2 konfiqurasiyasında `AllowOverride All` olduğundan əmin olun.

### Verilənlər bazasına bağlanmaq alınmırsa:

- MySQL işləyir? `sudo systemctl status mysql`
- İstifadəçi adı və şifrə düzdür?
- Database yaradılıb?

---

© 2025 Depod.az
