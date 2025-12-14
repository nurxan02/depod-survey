# DEPOD SURVEY - TƏFƏRRÜATLı SƏHIFƏLƏR TƏSVIRI

## 📄 İSTIFADƏÇİ TƏRƏFİ SƏHIFƏLƏR

### 1. index.php - Ana Səhifə / Sorğu (Steps 1-7)

**Funksiyalar:**

- 7 addımlı interaktiv sorğu interfeysi
- Hər addımda 2 cavab variantı (seçim kartları)
- Real-vaxt qiymət hesablama və göstərilməsi
- Vizual progress bar (0%-100%)
- Addım göstəricisi (Addım X / 7)
- Dinamik səhifə keçidləri (fade animasiyası)
- "Geri" və "Növbəti" düymələri
- Responsive dizayn (mobil, tablet, desktop)
- Seçimlər sessionStorage-də saxlanılır

**Texnologiyalar:**

- PHP (sorğu məlumatlarının yüklənməsi)
- JavaScript (interaktivlik)
- Tailwind CSS (dizayn)

---

### 2. result.php - Nəticə Səhifəsi (Step 8)

**Funksiyalar:**

- Final qiymətin göstərilməsi
- AI-powered məhsul tövsiyəsi
- Tövsiyə edilən məhsul məlumatları:
  - Məhsul adı
  - Təsvir
  - Qiymət
- **Əlaqə Formu** (məcburi deyil):
  - Ad
  - Soyad
  - Mobil nömrə
- CSRF qoruması
- Nəticə verilənlər bazasına saxlanılır
- Uğur mesajı və yenidən başlamaq linki

**API Requests:**

- `/api/get_recommendation.php` - məhsul tövsiyəsi

---

## 🔐 ADMIN PANEL SƏHIFƏLƏR

### 3. admin/login.php - Admin Girişi

**Funksiyalar:**

- Təhlükəsiz giriş formu
- İstifadəçi adı və şifrə doğrulaması
- Password hash yoxlaması
- Session yaradılması
- CSRF token qoruması
- Xəta mesajları
- "Sayta qayıt" linki

**Default Giriş:**

- Username: admin
- Password: admin123

---

### 4. admin/dashboard.php - Dashboard

**Funksiyalar:**

- **Statistika Kartları:**
  - Ümumi göndərişlər sayı
  - Orta qiymət
  - Ən çox tövsiyə edilən məhsul
  - Son 7 gündəki göndərişlər
- **Son Nəticələr Cədvəli:**
  - 10 ən son nəticə
  - ID, ad/soyad, telefon, qiymət, məhsul, tarix
  - "Hamısını gör" linki
- Naviqasiya menyusu

---

### 5. admin/results.php - Nəticələr İdarəetməsi

**Funksiyalar:**

- Bütün sorğu nəticələrinin cədvəl görünüşü
- Hər nəticə üçün:
  - İstifadəçi məlumatları
  - Hesablanmış qiymət
  - Tövsiyə edilən məhsul
  - Tarix
- **Əməliyyatlar:**
  - "Bax" - təfərrüatlı məlumat modal-da
  - "Sil" - nəticəni sil (təsdiq tələb olunur)
- Modal pəncərədə tam məlumat:
  - İstifadəçi məlumatları
  - Qiymət və məhsul
  - 7 sualın cavabları (sual-cavab-qiymət)
- AJAX ilə dinamik yükləmə
- CSRF qorumalı silmə

**API Requests:**

- `/admin/api/get_result_details.php` - nəticə detalları

---

### 6. admin/questions.php - Sualların İdarəetməsi (CRUD)

**Funksiyalar:**

- Bütün sualların siyahısı
- Hər sual üçün:
  - **Sual redaktəsi:**
    - Sual mətni
    - Sıra nömrəsi
  - **Cavablar redaktəsi:**
    - Cavab mətni
    - Qiymət dəyəri (+X ₼)
    - Premium checkbox
- İki görünüş rejimi: Baxış və Redaktə
- "Redaktə Et" / "Yadda Saxla" / "Ləğv Et" düymələri
- Real-vaxt yeniləmələr
- CSRF qoruması

---

### 7. admin/products.php - Məhsulların İdarəetməsi (CRUD)

**Funksiyalar:**

- Bütün məhsulların grid görünüşü
- Hər məhsul üçün:
  - **Əsas Məlumatlar:**
    - Məhsul adı
    - Baza qiyməti
    - Təsvir
    - Şəkil URL
    - Aktiv/Deaktiv status
  - **Tövsiyə Parametrləri (JSON):**
    - min_price - minimum qiymət
    - max_price - maksimum qiymət
    - anc_required - ANC tələbi (true/false)
    - premium - premium statusu
- İki görünüş rejimi
- JSON validator ilə konfiqurasiya
- Kömək məlumatları (JSON formatı)
- CSRF qoruması

---

### 8. admin/logout.php - Çıxış

**Funksiyalar:**

- Session məhv edilməsi
- Login səhifəsinə yönləndirmə

---

## 🔧 API ENDPOINTS

### 9. api/get_recommendation.php

**Metod:** POST  
**Input:**

```json
{
  "selections": {"1": 5, "2": 7, ...},
  "total_price": 120
}
```

**Output:**

```json
{
  "success": true,
  "product": {...},
  "calculated_price": 120
}
```

**Funksiya:** Seçimlərə əsasən ən uyğun məhsulu tövsiyə edir

---

### 10. admin/api/get_result_details.php

**Metod:** GET  
**Parameters:** `?id=123`  
**Output:**

```json
{
  "success": true,
  "result": {
    "user_name": "...",
    "selections": [...]
  }
}
```

**Funksiya:** Nəticənin tam təfərrüatlarını qaytarır

---

## 🗄️ VERILƏNLƏR BAZASI

### 11. database/schema.sql

**Tables:**

1. **questions** - Sorğu sualları
2. **options** - Sual cavabları
3. **products** - Məhsullar kataloqu
4. **results** - İstifadəçi cavabları
5. **admin_users** - Admin istifadəçilər

**Sample Data:**

- 7 sual və hər birinin 2 cavabı
- 4 məhsul (Basic, Pro, Pro2 ANC, PEAK)
- 1 admin istifadəçi

---

## 📚 PHP CLASSES (Backend Logic)

### 12. classes/Database.php

- PDO bağlantısı (Singleton pattern)
- Prepared statements
- SQL injection qoruması

### 13. classes/Security.php

- Input sanitization
- CSRF token yaradma/yoxlama
- Password hashing
- Admin authentication yoxlaması
- Security headers

### 14. classes/Question.php

- Sualların CRUD əməliyyatları
- Sual + cavablar birlikdə yükləmə

### 15. classes/Option.php

- Cavabların CRUD əməliyyatları
- Qiymət məlumatlarının idarəsi

### 16. classes/Product.php

- Məhsulların CRUD əməliyyatları
- **recommendProduct()** - Tövsiyə alqoritmi:
  - Qiymət diapazonu üzrə
  - ANC tələbi üzrə
  - Premium status üzrə

### 17. classes/Result.php

- Nəticələrin saxlanması
- Statistika hesablamaları
- Axtarış funksiyası

### 18. classes/Admin.php

- Admin authentication
- Password verification
- Session idarəetməsi

---

## ⚙️ KONFİQURASİYA

### 19. config/config.php

**Parametrlər:**

- Database credentials
- Application settings
- Security settings
- Session configuration
- Timezone

### 20. includes/helpers.php

**Utility Functions:**

- redirect()
- formatDate()
- timeAgo()
- formatPrice()
- jsonResponse()
- getClientIP()
- arrayToCsv()

---

## 🔒 TƏHLÜKƏSİZLİK

### 21. .htaccess

- Directory listing qadağası
- Həssas faylların qorunması
- GZIP sıxılma
- Security headers
- Browser caching

---

## 📦 QURAŞDIRMA

### 22. install.sh

- Avtomatik quraşdırma skripti
- Database yaradır
- Schema import edir
- Config faylını yeniləyir

### 23. README.md

- Tam quraşdırma təlimatı
- Xüsusiyyətlərin siyahısı
- Təhlükəsizlik qeydləri

### 24. INSTALL.md

- Sürətli quraşdırma təlimatı
- Problem həll yolları

---

## 🎨 DİZAYN KONSEPSİYASI

**Rəng Palitri:**

- White background (#FFFFFF)
- Dark text (#1F2937)
- Red accent (#E53E3E)
- Orange accent (#F56565)
- Gray tones (50, 100, 200, 300, etc.)

**Komponentlər:**

- Rounded corners (rounded-xl, rounded-2xl)
- Subtle shadows
- Gradient buttons (red to orange)
- Hover effects
- Smooth transitions
- Responsive grid layouts

**Animasiyalar:**

- Fade in (səhifə yükləmə)
- Scale in (modal)
- Slide transitions
- Progress bar fill

---

## 📊 İŞLƏYİŞ AXINI

1. İstifadəçi **index.php**-ə gəlir
2. 7 sualı cavablandırır (hər cavab qiymət əlavə edir)
3. **result.php**-ə yönləndirilir
4. API məhsul tövsiyəsi verir
5. İstifadəçi (istəsə) əlaqə məlumatlarını daxil edir
6. Məlumatlar **results** cədvəlinə saxlanılır
7. Admin **admin/results.php**-dən baxır

---

## 🚀 XÜSUSİYYƏTLƏR

✅ Full CRUD (Create, Read, Update, Delete)  
✅ Responsive Design (mobil + desktop)  
✅ Real-time Price Calculation  
✅ Smart Product Recommendation  
✅ Secure Authentication  
✅ SQL Injection Protection  
✅ CSRF Protection  
✅ XSS Protection  
✅ Password Hashing  
✅ Session Management  
✅ AJAX Requests  
✅ Modal Windows  
✅ Statistics Dashboard  
✅ Search & Filter  
✅ Azerbaijani Language  
✅ Clean Code Architecture

---

© 2025 Depod.az - Professional Survey System
