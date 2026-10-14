# 📋 Rangkuman Kebutuhan Klien & Rekomendasi Sistem (MGISpec)

Dokumen ini merangkum poin-poin kesepakatan dari obrolan WhatsApp bersama klien (Bpk Bram Setiawan Liem - MGISpec / MGIClub Global), penyesuaian desain mockup, rekomendasi arsitektur teknologi di hosting Dewaweb, serta inventaris bahan proyek.

---

## 1. Rangkuman Analisis Chat Klien & Penyesuaian Mockup

Berdasarkan pesan dan gambar referensi yang dikirimkan klien pada 14 Oktober 2026:

### A. Ukuran & Resolusi Banner Slider
Klien meminta informasi ukuran pixel banner dan menginginkan tampilan banner yang besar / *full screen*:
*   **Desktop Slider:** `1920 × 900 px` (atau `1920 × 1080 px` untuk *full-bleed*) — Rasio ~16:9 / 21:9 agar tajam di laptop maupun layar monitor lebar.
*   **Mobile Slider:** `1080 × 1350 px` (atau `800 × 1000 px`) — Rasio 4:5 *portrait* agar tampil utuh di smartphone tanpa terpotong atau gepeng.

### B. Header & Navigasi
*   **Logo Resmi:** Menggunakan file logo transparan resolusi tinggi (`stuff/logo/MGISPEC LOGO PNG HD.png`).
*   **Pilihan Bahasa (Dual Language):** Dropdown/selector bendera **Indonesia (ID 🇮🇩)** dan **Philippines (PH 🇵🇭)**.
*   **Halaman Login:** Tombol tautan **Login** ke panel CMS / admin.
*   **Tanpa Shopping Cart:** Klien secara eksplisit menegaskan **TIDAK PERLU shopping cart** karena website ini difungsikan sebagai katalog profil & spesifikasi kacamata medis, bukan toko online transaksi mandiri.

### C. Hero Slider (Beranda)
*   Dibuat besar dengan tinggi layar penuh (*full screen viewport*, `h-[85vh]` s/d `h-[92vh]`) baik pada tampilan desktop maupun smartphone.
*   Dilengkapi slide interaktif (menampilkan konsep kacamata medis *"GET IT RIGHT"* dan seri IONSPEC), navigasi tombol panah kiri-kanan, indikator dot, dan tombol CTA langsung ke katalog produk.

### D. Section Antara Slider Besar & Footer (Promo & Manfaat)
*   Mengacu pada referensi `antara slider besar dengan slider kecil2 dibawah.jpeg`.
*   Berisi kartu-kartu program manfaat eksklusif bergradasi (*Oxygen Lenses, Anti Radiasi UV400, Free Hardcase & Microfiber Cloth, serta Sertifikasi Resmi MDA Malaysia*).

### E. Section Di Atas Footer (Katalog Produk Tanpa Harga)
*   Mengacu pada referensi `home_diatas_footer.jpeg`.
*   Klien menginstruksikan: *"di home diatas footer, kasi thumbnail kecil2 gini tapi tanpa harga... dan klo bsa semua item bsa masuk disitu"*.
*   Diwujudkan dalam bentuk *horizontal scrollable carousel / grid* berisi seluruh koleksi produk kacamata dengan foto bersih, label seri, dan tombol *Lihat Detail*, **100% tanpa menampilkan harga**. Klik pada item langsung membuka halaman detail spesifikasi produk.

### F. Footer & Hak Cipta
*   Memuat profil singkat, tautan navigasi, kontak layanan pelanggan, dan lisensi medis (*FDA Registered* & *MDA Malaysia License No: MDA-3521-WDP122*).
*   Mencantumkan teks hak cipta resmi persis sesuai instruksi klien:
    > **`© 2026 - MGISPEC @ MGIClub Global. All Rights Reserved`**

---

## 2. Rekomendasi Tech Stack untuk Hosting Dewaweb.com

Untuk kebutuhan hosting di **Dewaweb.com** (cPanel Cloud Hosting, LiteSpeed Web Server, PHP 8.2/8.3, MySQL/MariaDB), pilihan teknologi terbaik dan **paling cepat selesai** adalah:

> 🏆 **Laravel 11 + FilamentPHP v3 + MySQL + Tailwind CSS**

### Alasan Mengapa Paling Tepat & Cepat:
1. **CMS Selesai dalam Hitungan Jam (Zero Boilerplate):**
   Filament v3 menyediakan panel admin modern siap pakai lewat perintah artisan generator (`php artisan make:filament-resource Product`). CRUD produk, multi-upload gambar, manajemen slider, dan otentikasi login admin langsung berfungsi tanpa perlu *coding* HTML admin dari nol.
2. **100% Native & Stabil di Dewaweb cPanel:**
   Dewaweb menggunakan arsitektur CloudLinux + cPanel + LiteSpeed. Laravel dan Filament berjalan *native* via PHP-FPM, tidak memerlukan proses *daemon background* (seperti Node.js PM2 atau binary Go) yang sering terhenti mendadak pada *shared hosting*.
3. **Dukungan Dual Bahasa (ID & PH) Sangat Rapi:**
   Didukung oleh paket standar industri `spatie/laravel-translatable` dan `filament-spatie-translatable`. Konten multibahasa tersimpan rapi dalam kolom JSON di MySQL, dengan antarmuka tab input bahasa yang mudah digunakan oleh admin.
4. **Sangat Ramah AI Agent:**
   Model AI coding (Claude, Gemini, GPT) sangat fasih dengan arsitektur Laravel 11 & Filament v3. Tim developer dapat memberikan instruksi modul ke AI agent untuk menghasilkan kode bebas *error*.
5. **Integrasi Front-End Mulus:**
   Kode HTML mockup yang telah disusun rapi di folder `mockup/` dapat langsung disalin ke dalam template Blade Laravel (`home.blade.php` dan `product-details.blade.php`).

### Perbandingan dengan Opsi Lain:
*   **WordPress + ACF:** Cepat di awal, tetapi setup multibahasa (WPML/Polylang) sering memicu konflik dependensi, rentan terhadap masalah keamanan plugin, dan lebih lambat diakses.
*   **PocketBase (Go / SQLite):** Sangat ringan, namun tidak didukung secara *native* di cPanel shared hosting Dewaweb (membutuhkan VPS dengan akses root/daemon).

---

## 3. Inventaris Bahan & Asset di Folder `stuff/`

Seluruh bahan proyek telah dikumpulkan dan diorganisasi secara sistematis di dalam direktori `stuff/`:

```
stuff/
├── DEVELOPMENT_PLAN.md               # Panduan teknis lengkap & skema database
├── logo/
│   └── MGISPEC LOGO PNG HD.png       # File logo resmi resolusi tinggi & transparan
├── client_chat/
│   ├── Screenshot 2026-10-14 at 06.53.05.png  # Chat: banner slider, request home page
│   ├── Screenshot 2026-10-14 at 06.54.06.png  # Chat: dokumen logo HD & teks footer resmi
│   └── Screenshot 2026-10-14 at 06.54.18.png  # Chat: slider full screen desktop & mobile
├── design_references/
│   ├── contoh_slider_homepage_laptop.jpeg     # Referensi banner beranda desktop ("GET IT RIGHT")
│   ├── contoh_slider_homepage_mobile.jpeg     # Referensi banner beranda mobile portrait
│   ├── header jgn lupa pilihan bahasa...jpeg   # Catatan header: bahasa + login, tanpa cart
│   ├── antara slider besar dengan slider...   # Referensi section benefit/promo cards
│   └── home_diatas_footer.jpeg                # Referensi thumbnail produk tanpa harga
└── mockup/
    ├── index.html                    # Mockup beranda interaktif (sudah disesuaikan 100%)
    ├── product-details.html          # Mockup halaman detail katalog produk
    └── assets/images/                # Logo, banner slider, dan gambar referensi
```
