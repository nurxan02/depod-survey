# 🎉 DEPOD SURVEY - LAYIHƏ HAZIRDIR!

## ✅ TƏKMİLLƏŞDİRİLMİŞ SƏHIFƏLƏR

### 📱 İstifadəçi Tərəfi

1. **index.php** - 7 addımlı interaktiv sorğu

   - Real-vaxt qiymət hesablama
   - Progress bar
   - Smooth animasiyalar
   - Responsive dizayn

2. **result.php** - Nəticə və təkliflər
   - AI-powered məhsul tövsiyəsi
   - Əlaqə formu (optional)
   - Uğur mesajı

### 🔐 Admin Panel

3. **admin/login.php** - Təhlükəsiz giriş
4. **admin/dashboard.php** - Statistika və overview
5. **admin/results.php** - Nəticələrin idarəsi (CRUD)
6. **admin/questions.php** - Sualların redaktəsi (CRUD)
7. **admin/products.php** - Məhsulların redaktəsi (CRUD)
8. **admin/logout.php** - Çıxış

### 🔌 API Endpoints

9. **api/get_recommendation.php** - Məhsul tövsiyəsi
10. **admin/api/get_result_details.php** - Nəticə detalları

## 🏗️ BACKEND ARXITEKTURA

### Classes (OOP)

- **Database.php** - PDO + Prepared Statements
- **Security.php** - CSRF, Sanitization, Authentication
- **Question.php** - Suallar modeli
- **Option.php** - Cavablar modeli
- **Product.php** - Məhsullar + Recommendation Logic
- **Result.php** - Nəticələr + Statistics
- **Admin.php** - Admin idarəetməsi

### Helpers

- **helpers.php** - Utility functions

### Configuration

- **config/config.php** - Mərkəzi konfiqurasiya

## 🗄️ DATABASE

### Tables

1. **questions** - 7 sual
2. **options** - Hər sual üçün 2 cavab
3. **products** - 4 məhsul (Basic, Pro, Pro2 ANC, PEAK)
4. **results** - İstifadəçi cavabları
5. **admin_users** - Admin istifadəçilər

### Sample Data

✅ 7 sual və 14 cavab (doldurulub)
✅ 4 məhsul (doldurulub)
✅ 1 admin (username: admin, password: admin123)

## 🔒 TƏHLÜKƏSİZLİK XÜSUSİYYƏTLƏRİ

✅ **SQL Injection Prevention** - PDO Prepared Statements
✅ **CSRF Protection** - Token validation
✅ **XSS Prevention** - Input sanitization
✅ **Password Security** - password_hash() və password_verify()
✅ **Session Security** - HttpOnly, Secure flags
✅ **Security Headers** - X-Frame-Options, X-XSS-Protection
✅ **Input Validation** - Phone, Email, Integer validation
✅ **Error Handling** - Secure error messages

## 🎨 DİZAYN

### Framework

- **Tailwind CSS** (CDN)

### Colors

- White background (#FFFFFF)
- Dark text (#1F2937)
- Red accent (#E53E3E)
- Orange accent (#F56565)

### Features

- Responsive (mobil, tablet, desktop)
- Smooth animations (fade, scale, slide)
- Modern UI components
- Professional layout

## 📁 FAYIL STRUKTURU

```
depod-survey/
├── admin/
│   ├── api/
│   │   └── get_result_details.php
│   ├── includes/
│   │   └── nav.php
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── products.php
│   ├── questions.php
│   └── results.php
├── api/
│   └── get_recommendation.php
├── classes/
│   ├── Admin.php
│   ├── Database.php
│   ├── Option.php
│   ├── Product.php
│   ├── Question.php
│   ├── Result.php
│   └── Security.php
├── config/
│   └── config.php
├── database/
│   └── schema.sql
├── includes/
│   └── helpers.php
├── .gitignore
├── .htaccess
├── DOCUMENTATION.md
├── INSTALL.md
├── README.md
├── index.php
├── install.sh
└── result.php
```

## 🚀 QURAŞDIRMA

### Avtomatik (Tövsiyə):

```bash
cd /home/khan/apps/depod-survey
./install.sh
```

### Əl ilə:

1. MySQL database yaradın: `depod_survey`
2. Schema import edin: `mysql -u root -p depod_survey < database/schema.sql`
3. `config/config.php`-də DB məlumatlarını yeniləyin
4. Brauzer-də açın: `http://localhost/depod-survey/`

## 🔑 GİRİŞ MƏLUMATLARI

**Admin Panel:** `http://localhost/depod-survey/admin/login.php`

- Username: `admin`
- Password: `admin123`

⚠️ **İlk girişdən sonra şifrəni dəyişdirin!**

## ✨ XÜSUSİYYƏTLƏR

### İstifadəçi Tərəfi:

✅ 7 addımlı interaktiv sorğu
✅ Real-vaxt qiymət hesablama
✅ Progress bar (0-100%)
✅ Vizual feedback
✅ Smooth transitions
✅ AI məhsul tövsiyəsi
✅ Optional əlaqə formu
✅ Responsive dizayn

### Admin Panel:

✅ Secure login system
✅ Dashboard statistikası
✅ Nəticələrin idarəsi
✅ Sualların CRUD
✅ Məhsulların CRUD
✅ Real-time redaktə
✅ Modal windows
✅ AJAX requests
✅ Export capability

### Backend:

✅ OOP architecture
✅ MVC pattern
✅ Singleton Database
✅ Prepared Statements
✅ CSRF protection
✅ Password hashing
✅ Input validation
✅ Error handling
✅ Session management

## 📊 TÖVSIYƏ ALQORİTMİ

Məhsul tövsiyəsi aşağıdakı parametrlərə əsasən işləyir:

1. **Qiymət Diapazonu:**

   - Basic: 0-70 ₼
   - Pro: 71-110 ₼
   - Pro2 ANC: 111-160 ₼
   - PEAK: 161+ ₼

2. **ANC Tələbi:**

   - Əgər ANC seçilibsə, ANC-li məhsul tövsiyə edilir

3. **Premium Status:**
   - Premium cavablar daha yüksək qiymətli məhsullərə yönləndirir

## 📖 SƏNƏDLƏR

- **README.md** - Tam quraşdırma təlimatı
- **INSTALL.md** - Sürətli başlanğıc
- **DOCUMENTATION.md** - Səhifələrin təfərrüatlı təsviri

## 🧪 TEST

### 1. İstifadəçi Axını Test Edin:

1. `http://localhost/depod-survey/` açın
2. 7 sualı cavablandırın
3. Qiymətin real-vaxt yenilənməsinə baxın
4. Nəticə səhifəsində məhsul tövsiyəsini görün
5. Əlaqə formu göndərin (və ya keçin)

### 2. Admin Panel Test Edin:

1. `http://localhost/depod-survey/admin/login.php` açın
2. Login edin (admin/admin123)
3. Dashboard statistikasına baxın
4. Nəticələri açın və detallarına baxın
5. Sualları redaktə edin
6. Məhsulları redaktə edin

## 🐛 PROBLEM HƏLL

### PHP Extension xətası:

```bash
sudo apt-get install php-mysql php-mbstring
sudo systemctl restart apache2
```

### Database bağlantı xətası:

- MySQL işləyir? `sudo systemctl status mysql`
- DB yaradılıb? `SHOW DATABASES;`
- İstifadəçi/şifrə düzdür?

### .htaccess işləmir:

- Apache-də `mod_rewrite` aktiv?
- `AllowOverride All` təyin olunub?

## 📞 DƏSTƏK

Suallar və problemlər üçün:

- Email: admin@depod.az
- Website: https://depod.az

## 📝 LİSENZİYA

© 2025 Depod.az - Bütün hüquqlar qorunur

---

## 🎯 LAYİHƏ STATUS: ✅ HAZIR!

Bütün xüsusiyyətlər tətbiq edilib və test edilməyə hazırdır!

**Növbəti Addımlar:**

1. ✅ Quraşdırma (`./install.sh`)
2. ✅ Test (istifadəçi və admin)
3. ✅ Admin şifrəsini dəyişdirin
4. ✅ Production-a deploy edin (HTTPS + təhlükəsizlik)

---

**Uğurlar! 🚀**
