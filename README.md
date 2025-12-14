# Depod Survey Application

Çox mərhələli sorğu və admin paneli ilə dinamik veb tətbiqi.

## Xüsusiyyətlər

### İstifadəçi Tərəfi

- 7 addımlı interaktiv sorğu
- Dinamik qiymət hesablama
- Real-vaxt progress izləmə
- Zərif və responsive dizayn (Tailwind CSS)
- Ağıllı məhsul tövsiyəsi
- Əlaqə formu (məcburi deyil)

### Admin Paneli

- Təhlükəsiz giriş sistemi
- Dashboard və statistika
- Sorğu nəticələrinin idarə edilməsi
- Suallar və cavabların redaktəsi (CRUD)
- Məhsul məlumatlarının redaktəsi (CRUD)
- Tövsiyə alqoritminin tənzimlənməsi

## Təhlükəsizlik Xüsusiyyətləri

✅ PDO və Prepared Statements (SQL Injection qoruması)
✅ CSRF Token doğrulaması
✅ Input sanitizasiyası və validasiyası
✅ Şifrələrin hash-lənməsi (password_hash)
✅ Session idarəetməsi
✅ XSS qoruması
✅ Security Headers

## Quraşdırma Addımları

### 1. Tələblər

- PHP 7.4 və ya daha yüksək
- MySQL 5.7 və ya daha yüksək
- Apache veb server (mod_rewrite aktiv)
- PDO MySQL extension

### 2. Faylların Kopyalanması

Layihə fayllarını veb server qovluğuna kopyalayın:

```bash
/var/www/html/depod-survey/
```

### 3. Verilənlər Bazasının Yaradılması

**Addım 1:** MySQL-ə daxil olun:

```bash
mysql -u root -p
```

**Addım 2:** Verilənlər bazası yaradın:

```sql
CREATE DATABASE depod_survey CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Addım 3:** İstifadəçi yaradın və icazələr verin:

```sql
CREATE USER 'depod_user'@'localhost' IDENTIFIED BY 'güclü_şifrə_buraya';
GRANT ALL PRIVILEGES ON depod_survey.* TO 'depod_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Addım 4:** Schema faylını import edin:

```bash
mysql -u depod_user -p depod_survey < database/schema.sql
```

### 4. Konfiqurasiya

`config/config.php` faylını açın və verilənlər bazası məlumatlarını yeniləyin:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'depod_survey');
define('DB_USER', 'depod_user');
define('DB_PASS', 'sizin_şifrəniz');
```

### 5. Qovluq İcazələri

Aşağıdakı qovluqların yazma icazəsi olduğundan əmin olun:

```bash
chmod 755 /path/to/depod-survey/
chmod 644 /path/to/depod-survey/config/config.php
```

### 6. Apache Konfiqurasiyası

`.htaccess` faylının düzgün işlədiyindən əmin olun. Apache-də `mod_rewrite` aktivləşdirin:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## İstifadə

### İstifadəçi Tərəfi

Brauzerinizə daxil olun:

```
http://localhost/depod-survey/
```

və ya

```
http://yourdomain.com/
```

### Admin Paneli

Admin panelinə daxil olun:

```
http://localhost/depod-survey/admin/login.php
```

**Default Admin Məlumatları:**

- İstifadəçi adı: `admin`
- Şifrə: `admin123`

⚠️ **ÖNƏMLİ:** İlk girişdən sonra mütləq admin şifrəsini dəyişdirin!

## Faylların Strukturu

```
depod-survey/
├── config/
│   └── config.php              # Əsas konfiqurasiya
├── classes/
│   ├── Database.php            # Verilənlər bazası sinfi
│   ├── Security.php            # Təhlükəsizlik funksiyaları
│   ├── Admin.php               # Admin idarəetməsi
│   ├── Question.php            # Sual modeli
│   ├── Option.php              # Cavab modeli
│   ├── Product.php             # Məhsul modeli
│   └── Result.php              # Nəticə modeli
├── database/
│   └── schema.sql              # Verilənlər bazası sxemi
├── api/
│   └── get_recommendation.php  # Tövsiyə API
├── admin/
│   ├── login.php               # Admin girişi
│   ├── logout.php              # Admin çıxışı
│   ├── dashboard.php           # Dashboard
│   ├── results.php             # Nəticələr səhifəsi
│   ├── questions.php           # Suallar idarəetməsi
│   ├── products.php            # Məhsullar idarəetməsi
│   ├── includes/
│   │   └── nav.php             # Admin naviqasiyası
│   └── api/
│       └── get_result_details.php
├── index.php                   # Ana səhifə (sorğu)
├── result.php                  # Nəticə səhifəsi
├── .htaccess                   # Apache konfiqurasiyası
└── README.md                   # Bu fayl
```

## Xüsusiyyətlərin Şərhi

### Tövsiyə Alqoritmi

Məhsul tövsiyəsi aşağıdakı parametrlərə əsaslanır:

- Ümumi hesablanmış qiymət
- ANC seçimi
- Premium cavablar
- Məhsulun `optimal_fit_score` JSON parametrləri

### Qiymət Hesablama

Hər sualda seçilən cavabın qiymət dəyəri toplanır və real-vaxt olaraq göstərilir.

## Təhlükəsizlik Qeydləri

1. **Production mühitində:**

   - `config/config.php`-də error reporting-i söndürün
   - Güclü verilənlər bazası şifrəsi istifadə edin
   - HTTPS aktivləşdirin və `session.cookie_secure = 1` təyin edin
   - Admin şifrəsini dəyişdirin

2. **Fayl icazələri:**

   - Konfiqurasiya faylları: 644
   - PHP faylları: 644
   - Qovluqlar: 755

3. **Backup:**
   - Mütəmadi olaraq verilənlər bazasının ehtiyat nüsxəsini alın:
   ```bash
   mysqldump -u depod_user -p depod_survey > backup_$(date +%Y%m%d).sql
   ```

## Dəstək

Suallar və problemlər üçün:

- Email: admin@depod.az
- Website: https://depod.az

## Lisenziya

© 2025 Depod.az - Bütün hüquqlar qorunur
