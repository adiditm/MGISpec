# 🚀 Web Development Plan: MGISpec Eyewear Catalog & CMS

**Project Name:** MGISpec Catalog Website  
**Client:** Bpk Bram Setiawan Liem (MGISpec / MGIClub Global)  
**Agreed Proposal:** [Penawaran_Katalog_Kacamata_Rev4.pdf](stuff/proposals/Penawaran_Katalog_Kacamata_Rev4.pdf) (Rp 5.000.000 / Web + CMS Scope)  
**Hosting Target:** Dewaweb.com (cPanel Cloud Hosting, PHP 8.2/8.3, MySQL/MariaDB, LiteSpeed)  
**Language Support:** Dual Language (🇮🇩 Bahasa Indonesia `id` & 🇵🇭 Philippines `ph`)  

---

## 1. Executive Summary & Core Requirements

Berdasarkan kesepakatan penawaran dan instruksi terbaru dari klien via WhatsApp (tertanggal 14 Oktober 2026):

1. **Lingkup Halaman Publik (2 Halaman Utama):**
   - **Halaman 1 (Home Page):**
     - **Header:** Logo HD resmi (`stuff/logo/MGISPEC LOGO PNG HD.png`), menu navigasi, tombol pilih bahasa (ID/PH), tombol link Login (Admin/CMS). **Tidak ada fitur/ikon keranjang belanja (No Shopping Cart).**
     - **Hero Slider:** Full-screen viewport baik di tampilan Desktop maupun Mobile. Banner interaktif dengan tombol CTA ke katalog.
     - **Section Antara Slider Besar & Footer:** Banner/kartu program manfaat, keunggulan medis, sertifikasi (mengacu pada referensi `antara slider besar dengan slider kecil2 dibawah.jpeg`).
     - **Section Di Atas Footer (Thumbnail Katalog):** Carousel/grid horizontal memuat seluruh item kacamata dengan foto bersih dan nama produk, **TANPA HARGA**, yang jika diklik langsung mengarah ke halaman detail spesifikasi.
     - **Footer:** Memuat identitas perusahaan, sertifikasi medis (FDA & MDA Malaysia No. MDA-3521-WDP122), media sosial Facebook, dan teks hak cipta resmi yang diminta klien:
       > `© 2026 - MGISPEC @ MGIClub Global. All Rights Reserved`
   - **Halaman 2 (Product Details / Katalog Spesifikasi):**
     - Menampilkan galeri foto produk (4 thumbnail + 1 preview utama), informasi seri frame, keunggulan lensa medis (Oxygen / Ion / Anti Blue Light / UV400), spesifikasi dimensi frame (bridge, temple length, weight), serta tombol CTA konsultasi/pemesanan langsung via WhatsApp (menggantikan tombol checkout/cart).

2. **Lingkup CMS (Admin Panel Backend):**
     - Autentikasi Admin login yang aman.
     - Manajemen Slider Beranda (Upload gambar terpisah untuk desktop & mobile, judul, link CTA, urutan).
     - Manajemen Promo Cards (Judul bilingual, deskripsi, badge).
     - Manajemen Katalog Produk (Nama bilingual, foto utama, galeri multi-foto, kategori, spesifikasi, visibilitas).
     - Manajemen Pengaturan Website (Kontak WA, logo, email, Facebook, teks copyright).

---

## 2. Analisis & Rekomendasi Tech Stack untuk Dewaweb.com

### Rekomendasi Utama (Terbaik & Tercepat): **Laravel 11 + FilamentPHP v3 + MySQL**

Mengapa ini adalah opsi tercepat dan paling andal untuk tim dan AI Agent di hosting Dewaweb:

| Kriteria | Laravel 11 + Filament v3 | Alternatif: WordPress + ACF | Alternatif: PocketBase + Frontend JS |
| :--- | :--- | :--- | :--- |
| **Kecepatan Bikin CMS** | ⚡ **Sangat Cepat (Hitungan Jam)** via artisan resource generator. | Cepat untuk awal, tapi setup bilingual (WPML/Polylang) sering konflik. | Sangat cepat, namun perlu build front-end API terpisah. |
| **Kesesuaian Dewaweb (cPanel)** | 🟢 **100% Native & Stabil.** Menggunakan PHP 8.2/8.3 + MySQL + LiteSpeed tanpa daemon tambahan. | 🟢 Native cPanel, namun rawan lambat dan rawan malware/update plugin. | 🔴 Kurang cocok di shared hosting Dewaweb karena butuh daemon process Go/Node yang persisten. |
| **Dukungan Dual Bahasa** | 🟢 Sangat rapi menggunakan paket resmi `spatie/laravel-translatable` & `filament-spatie-translatable`. | 🟡 Memerlukan plugin pihak ketiga berbayar/rumit (WPML/TranslatePress). | 🟡 Harus dirancang manual di skema database. |
| **Kemudahan bagi AI Agent** | 🟢 **Sangat Tinggi.** Syntax Laravel & Filament sangat standar, mudah diprogram oleh AI agent. | 🟡 Sulit bagi AI agent untuk mengedit database WordPress secara programmatic. | 🟡 Cukup tinggi. |
| **Front-End Integration** | 🟢 Langsung menggunakan file HTML/Tailwind mockup kita ke dalam Blade view (`home.blade.php`). | 🔴 Harus mengubah total struktur HTML menjadi theme PHP WordPress loop. | 🟡 Harus konversi ke Astro/React komponen. |

> **Kesimpulan:** Gunakan **Laravel 11 + FilamentPHP v3** sebagai fondasi backend/CMS dan **Tailwind CSS + Blade** untuk antarmuka publik. Mockup HTML yang telah kita buat di folder `mockup/` siap 100% dipindahkan ke Blade templates.

---

## 3. Spesifikasi Banner & Slider untuk Klien

Klien menanyakan ukuran pixel untuk banner slider di chat WhatsApp. Berikut panduan ukuran resolusi standar yang responsif dan tajam:

| Tipe Banner | Rekomendasi Resolusi (Px) | Rasio Aspek | Keterangan |
| :--- | :--- | :--- | :--- |
| **Desktop Slider** | **1920 × 900 px** *(atau 1920 × 1080 px)* | ~16:9 / 21:9 | Tampilan *full width bleed* tajam di layar laptop & monitor besar. |
| **Mobile Slider** | **1080 × 1350 px** *(atau 800 × 1000 px)* | 4:5 Portrait | Tampilan *full height portrait* di smartphone tanpa terpotong. |
| **Promo/Benefit Cards**| **600 × 600 px** (Square) | 1:1 Square | Format kartu grid di bawah slider besar. |
| **Thumbnail Produk** | **800 × 800 px** (Square PNG/JPG) | 1:1 Square | Foto produk kacamata dengan latar transparan atau putih bersih. |

---

## 4. Arsitektur Database (MySQL)

```sql
-- 1. Sliders Table
CREATE TABLE sliders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title JSON NULL,             -- {"id": "GET IT RIGHT", "ph": "GET IT RIGHT"}
    subtitle JSON NULL,          -- {"id": "Perlindungan mata revolusioner...", "ph": "Rebolusyonaryong proteksyon..."}
    desktop_image VARCHAR(255) NOT NULL,
    mobile_image VARCHAR(255) NULL,
    cta_text JSON NULL,          -- {"id": "Jelajahi Katalog", "ph": "Tingnan ang Katalogo"}
    cta_url VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. Promo / Benefit Cards Table
CREATE TABLE promo_banners (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    badge_text VARCHAR(50) NULL, -- 'CERTIFIED', 'BONUS', dll
    title JSON NOT NULL,         -- {"id": "OXYGEN LENSES", "ph": "OXYGEN LENSES"}
    description JSON NOT NULL,   -- {"id": "Teknologi pelepasan ion...", "ph": "Teknolohiya ng negatibong ion..."}
    icon VARCHAR(100) NULL,      -- FontAwesome class or image path
    link_url VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 3. Products Table
CREATE TABLE products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    name JSON NOT NULL,          -- {"id": "MGI IONSPEC Hospital Grade M40", "ph": "MGI IONSPEC Hospital Grade M40"}
    category VARCHAR(100) NULL,  -- Medical Series, Optical Frame, Kids Series, etc.
    description JSON NULL,
    specs JSON NULL,             -- {"bridge": "18mm", "temple": "140mm", "material": "Titanium", "weight": "14g"}
    thumbnail VARCHAR(255) NOT NULL,
    is_featured BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 4. Product Gallery Images
CREATE TABLE product_images (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 5. Site Settings
CREATE TABLE site_settings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 5. Strategi Implementasi Dual Bahasa (ID & PH)

1. **Routing & Middleware:**
   - URL Route: `/{locale}` di mana `{locale}` adalah `id` atau `ph`.
   - Contoh:
     - Beranda ID: `https://mgispec.com/id`
     - Beranda PH: `https://mgispec.com/ph`
     - Default redirect ke `/id` jika tidak disertakan.
   - Session/Cookie `app_locale` menyimpan preferensi pengunjung.
2. **Static UI Translation:**
   - Dikelola di `lang/id.json` dan `lang/ph.json` untuk teks navigasi, tombol, footer, dll.
3. **Dynamic Content Translation (CMS):**
   - Menggunakan `spatie/laravel-translatable` di Model Eloquent (`Product`, `Slider`, `PromoBanner`).
   - Di Filament CMS, form input menyediakan tab atau field terpisah untuk Bahasa Indonesia dan Philippines secara berdampingan.

---

## 6. Pembagian Tugas Tim & AI Agent (Sprint Roadmap)

### Sprint 1: Setup Proyek & Filament CMS (Hari 1 - 2)
*   **Task 1.1:** Inisialisasi proyek Laravel 11 (`composer create-project laravel/laravel mgispec`).
*   **Task 1.2:** Install Filament v3 (`composer require filament/filament`).
*   **Task 1.3:** Setup database migrations sesuai skema di atas.
*   **Task 1.4:** Buat Filament Resources:
    - `SliderResource` (upload foto desktop/mobile, judul bilingual, status).
    - `PromoBannerResource` (kartu manfaat / promo).
    - `ProductResource` (nama bilingual, upload multi-gambar, spesifikasi).
    - `SettingResource` (kontak WA, link medsos, footer copyright).
*   *Prompt AI Agent siap pakai:*
    > "Generate Laravel 11 migration, Eloquent model with spatie/laravel-translatable, and Filament v3 Resource for Product with fields: sku, slug, name (bilingual), category, specs (json), thumbnail, is_featured, is_active, and product_images relation manager."

### Sprint 2: Front-End Blade & Mockup Migration (Hari 3 - 4)
*   **Task 2.1:** Setup Tailwind CSS di Laravel Vite.
*   **Task 2.2:** Pindahkan header, navigasi, dan footer dari `mockup/index.html` ke `layouts/app.blade.php`.
*   **Task 2.3:** Integrasikan slider dinamis dari tabel `sliders` di `home.blade.php`.
*   **Task 2.4:** Integrasikan Promo Cards dari `promo_banners` di `home.blade.php`.
*   **Task 2.5:** Integrasikan Carousel Thumbnail Produk dari tabel `products` (tanpa harga) di `home.blade.php`.
*   **Task 2.6:** Integrasikan halaman detail produk di `product/show.blade.php` lengkap dengan galeri foto interaktif dan tombol WhatsApp order.

### Sprint 3: Language Switching & QA Testing (Hari 5)
*   **Task 3.1:** Implementasi Middleware `SetLocale` untuk mendeteksi prefix `/id` atau `/ph`.
*   **Task 3.2:** Uji coba pergantian bahasa di frontend (teks statis dan konten dinamis).
*   **Task 3.3:** Uji coba responsivitas tampilan (Laptop, Tablet, Mobile iPhone/Android).
*   **Task 3.4:** Validasi seluruh poin chat klien:
    - [x] Fullscreen slider desktop & mobile
    - [x] Header: Logo HD, Pilihan Bahasa (ID/PH), Tombol Login, **TANPA Shopping Cart**
    - [x] Section manfaat di antara slider besar dan thumbnail
    - [x] Thumbnail produk katalog di atas footer **TANPA HARGA**
    - [x] Copyright footer: `© 2026 - MGISPEC @ MGIClub Global. All Rights Reserved`

### Sprint 4: Deployment ke Dewaweb cPanel (Hari 6 - 7)
*   **Task 4.1:** Buat database MySQL & user di cPanel Dewaweb.
*   **Task 4.2:** Upload source code via Git Version Control di cPanel atau SSH.
*   **Task 4.3:** Setup `.env` production (DB connection, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`).
*   **Task 4.4:** Konfigurasi symlink storage `php artisan storage:link`.
*   **Task 4.5:** Atur Document Root domain utama mengarah ke folder `/public` (atau konfigurasi `.htaccess` LiteSpeed redirect).
*   **Task 4.6:** Pasang SSL gratis (Let's Encrypt / cPanel AutoSSL).

---

## 7. Inventaris Bahan & Asset di Folder `stuff/`

Seluruh bahan proyek telah diklasifikasikan dan disimpan di direktori `stuff/` agar tim developer dapat langsung menggunakannya:

1. **`stuff/logo/`**
   - `MGISPEC LOGO PNG HD.png` : File logo transparan resmi resolusi tinggi.
2. **`stuff/client_chat/`**
   - Tangkapan layar chat WhatsApp klien berisi permintaan revisi slider, menu, dan footer.
3. **`stuff/design_references/`**
   - `contoh_slider_homepage_laptop.jpeg` : Tampilan slider beranda desktop referensi klien.
   - `contoh_slider_homepage_mobile.jpeg` : Tampilan slider beranda mobile referensi klien.
   - `header jgn lupa pilihan bahasa dan halaman login tapi gk perlu shopping cart.jpeg` : Catatan header klien.
   - `antara slider besar dengan slider kecil2 dibawah.jpeg` : Referensi section kartu manfaat / promo.
   - `home_diatas_footer.jpeg` : Referensi section thumbnail produk tanpa harga di atas footer.
4. **`stuff/proposals/`**
   - `Penawaran_Katalog_Kacamata_Rev4.pdf` & `.docx` : Dokumen penawaran resmi yang telah disepakati (deal).
5. **`stuff/mockup/`**
   - `index.html` : Mockup beranda interaktif yang telah disesuaikan 100% dengan permintaan klien.
   - `product-details.html` : Mockup halaman detail katalog produk.
   - `assets/` : Gambar, logo, dan stylesheet pendukung mockup.
