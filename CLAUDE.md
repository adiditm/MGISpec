# MGISpec Project Handover & Context Guide for Claude Code

Dokumen ini berisi rangkuman arsitektur, histori pekerjaan, struktur file, dan petunjuk operasional untuk melanjutkan pengembangan website **MGISpec** menggunakan Claude Code.

---

## 1. Ringkasan Eksekutif & Status Terkini

- **URL Publik (Dev/Staging)**: [https://dev.mgispecofficial.com](https://dev.mgispecofficial.com) (Status: `HTTP 200 OK`, Live).
- **Domain Utama**: `mgispecofficial.com` (Saat ini halaman coming soon).
- **Branch Git Utama**: `main`.
- **Framework**: Laravel 11.x + Filament Admin v3 + TailwindCSS + Alpine.js.
- **Database**: MySQL (`mgiclubc_mgispecofficial`).
- **Brand Status**: **100% MGISpec**. Kerjasama dengan brand lama (IONSPEC) telah berakhir sepenuhnya. **TIDAK BOLEH ADA** teks, gambar, atau residu yang menyebut "IONSPEC".

---

## 2. Pekerjaan yang Telah Diselesaikan

### A. Rebranding & Sanitasi Brand Total
1. Menghapus seluruh nama, kode model, teks, dan gambar yang mengandung unsur "IONSPEC".
2. Mengganti identitas visual dengan logo resmi resolusi tinggi: `public/images/logo.png` dan `public/images/logo-sm.png`.
3. Mengubah positioning & slogan menjadi: **MGISpec — Bio-Tech Oxygen Glasses**.

### B. Navigasi & Sistem Multibahasa (ID / EN / CN)
1. **Header Navigation**:
   - `Home / Beranda` (`/`)
   - `MGISPEC Catalog / Katalog MGISPEC` (`/katalog`)
   - `MGISPEC Profile / Profil MGISPEC` (`/#tentang`)
   - `MGISPEC Technologies / Teknologi MGISPEC` (`/#teknologi`)
   - `MGISPEC Certifications / Sertifikasi MGISPEC` (`/#sertifikasi`)
   - `MGISPEC Contact / Kontak MGISPEC` (`/kontak`)
   - **Tombol Login Member**: Mengarah ke portal resmi `https://member.mgiclub.com/member/login.jsp` (terbuka di tab baru).
2. **Halaman Kontak Internasional** (`/id/kontak` & `/en/kontak`):
   - Controller: `app/Http/Controllers/ContactController.php`
   - View: `resources/views/contact.blade.php`
   - Berisi kartu kantor cabang: Indonesia (Jakarta HQ), Filipina (Ortigas Manila), Malaysia (Kuala Lumpur - MDA Certified), dan Global Member Portal.

### C. Sistem Slider Hero Kontekstual & Multibahasa
1. **Skema Database & Model**:
   - Migration `2026_10_17_063000_update_sliders_table_for_multilingual_images.php` menambahkan field `image_path_id`, `image_mobile_path_id`, `image_path_en`, `image_mobile_path_en`, `image_path_cn`, `image_mobile_path_cn`.
   - Model `app/Models/Slider.php` memiliki helper method `getDesktopImageForLocale($locale)` dan `getMobileImageForLocale($locale)`.
2. **CMS / Filament Admin**:
   - `app/Filament/Resources/SliderResource.php` mendukung upload banner responsif terpisah untuk Desktop (rekomendasi `1920x800`) dan Mobile (rekomendasi `750x1000`) per bahasa.
3. **Aset Slider**:
   - 5 slider resmi dari materi Bram telah dikonversi ke format WebP HD teroptimasi di `storage/app/public/sliders/`.

### D. 10 Elemen Material Canggih MGISpec & Infografis
1. Mengganti bagian "kotak-kotak pink" di beranda dengan 10 material resmi:
   1. *Dioksida Silikon (SiO₂)*
   2. *Dioksida Titanium (TiO₂)*
   3. *Dioksida Zirkonium (ZrO₂)*
   4. *Grafena (Graphene)*
   5. *Nano Perak (Nano Silver)*
   6. *Germanium Organik (Organic Germanium)*
   7. *Arang Bambu Aktif (Bamboo Charcoal)*
   8. *Mineral FIR (Far Infrared)*
   9. *Mineral Ion Negatif (Negative Ion)*
   10. *Oksida Seng (ZnO)*
2. Data tersimpan di tabel `promo_banners` dengan terjemahan bilingual ID & EN.
3. Ditambahkan modal popup diagram infografis resolusi tinggi:
   - `public/images/infografis-material-id.png`
   - `public/images/infografis-material-en.png`

### E. Katalog Produk Lengkap (46 Model Kacamata & Galeri 8 Sudut)
1. **Pipeline Konversi Otomatis**:
   - Script `process_catalog.py` memproses batch foto dari folder `MGISPEC EYEWEAR PICTURE (1)` dan `(2)`.
   - Mengonversi foto berukuran besar menjadi WebP HD terkompresi (~30KB-80KB per foto):
     - `thumbnail.webp` (800x600 px)
     - `gallery-01.webp` s/d `gallery-08.webp` (1200x900 px)
2. **Data Produk**:
   - 46 model kacamata dengan kategori: *Optical/Daily*, *Sunglasses*, *Ultem*, dan *Kids*.
   - Seeder: `database/seeders/CatalogProductSeeder.php` membaca JSON metadata `database/seeders/products_catalog_data.json`.
   - Gambar publik tersimpan di `storage/app/public/products/<product-slug>/`.

---

## 3. Struktur Direktori Proyek

```
MGISpec/
├── CLAUDE.md                             # Dokumen konteks ini (dibaca otomatis oleh Claude Code)
├── ftp_tool.py                           # CLI tool sinkronisasi FTP cPanel/Hosting
├── process_catalog.py                    # Script konversi raw images -> WebP catalog & seeding JSON
├── database/                             # Ekspor / dump data (jika ada)
│
├── Slider/                               # Folder bahan mentah gambar slider dari Bram
├── MGISPEC EYEWEAR PICTURE (1)/          # Folder bahan mentah foto kacamata bagian 1
├── MGISPEC EYEWEAR PICTURE (2)/          # Folder bahan mentah foto kacamata bagian 2
│
└── kacamata/mgispec/                     # Source Code Utama Laravel
    ├── app/
    │   ├── Filament/Resources/           # CMS Admin (Product, Slider, PromoBanner, Category)
    │   ├── Http/Controllers/            # HomeController, CatalogController, ContactController
    │   └── Models/                       # Product, Slider, PromoBanner, Category, Setting
    ├── config/                           # Konfigurasi Laravel
    ├── database/
    │   ├── migrations/                   # Skema tabel
    │   └── seeders/                      # CatalogProductSeeder, DatabaseSeeder
    ├── lang/                             # Terjemahan JSON (id.json, en.json, cn.json)
    ├── public/
    │   ├── images/                       # Logo, infografis material
    │   └── storage -> ../storage/app/public
    ├── resources/views/                  # Blade templates (catalog/, partials/, home, contact)
    ├── routes/web.php                    # Routing aplikasi
    └── storage/app/public/               # Asset media publik terkomit (products, sliders, images)
```

---

## 4. Setup Lingkungan Lokal

Jika anggota tim melakukan `git clone`:

```bash
# 1. Masuk ke direktori aplikasi Laravel
cd kacamata/mgispec

# 2. Salin environment & install dependensi
cp .env.example .env
composer install
npm install
npm run build

# 3. Generate App Key & Hubungkan Storage Symlink
php artisan key:generate
php artisan storage:link

# 4. Konfigurasi Database Lokal di .env
# DB_CONNECTION=mysql
# DB_DATABASE=mgispec
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan Migration & Seeder
php artisan migrate --seed

# 6. Jalankan Server Lokal
php artisan serve
```

---

## 5. Deployment ke Remote Server (FTP)

Deployment ke server cPanel dilakukan melalui `ftp_tool.py`:

```bash
# Test koneksi ke server dev
python3 ftp_tool.py --profile dev test

# Melihat isi direktori remote
python3 ftp_tool.py --profile dev ls public_html

# Upload file spesifik
python3 ftp_tool.py --profile dev upload <local_file> <remote_path>

# Upload satu folder rekursif
python3 ftp_tool.py --profile dev upload_dir <local_dir> <remote_dir>
```

> **Catatan Kredensial**: File `.ftp_credentials.json` dan `.env` berstatus `ignored` di git untuk menjaga keamanan kredensial hosting dan database. Pastikan meminta file kredensial secara privat dari lead developer jika perlu akses deploy langsung.

---

## 6. Pedoman Kode & Konvensi Brand

1. **Brand Identity**:
   - Selalu gunakan penulisan `MGISpec` (bukan IonSpec / Ionspec).
   - Seluruh nama model kacamata diawali dengan prefix `MGISPEC` (contoh: `MGISPEC MS131`, `MGISPEC SG01`, `MGISPEC KID13`).
2. **Optimasi Gambar**:
   - Gambar produk dan banner harus selalu dikonversi ke format **WebP** dengan kualitas `quality=82-85` sebelum ditayangkan agar kecepatan muat halaman tetap di bawah 1.5 detik.
3. **Multibahasa**:
   - Seluruh teks yang tampil di view wajib menggunakan helper localization Laravel: `__('Kunci Teks')` dengan entri padanan di `lang/id.json` dan `lang/en.json`.
