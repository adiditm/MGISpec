# MGISpec — Katalog Kacamata

Laravel 12 · Filament 3.3 · MySQL/MariaDB · Tailwind CSS 4 · Swiper.

- Website: `/id` (Indonesia) dan `/ph` (Philippines). Halaman `/` mengarahkan ke bahasa terakhir yang dipakai.
- Halaman aktif: Home (`/{locale}`), Katalog (`/{locale}/katalog`, filter `?kategori=slug`), Detail (`/{locale}/katalog/{slug}`).
- CMS: `/admin` → Hero Slider, Kartu Promo/Manfaat, Produk, Kategori, Pengaturan Website (WhatsApp, sosmed, footer, efek & durasi slider).

## Setup lokal

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate   # isi DB_*
php artisan migrate --seed                          # data contoh + akun admin
php artisan storage:link
php artisan serve
```

Akun admin dibuat oleh seeder dari `ADMIN_EMAIL` / `ADMIN_PASSWORD` di `.env`. Isi keduanya **sebelum** `--seed` di server produksi; nilai default di seeder hanya untuk lokal.

## Ukuran gambar

| Bagian | Ukuran |
| --- | --- |
| Hero slider desktop | 1920 × 900 px (landscape) |
| Hero slider mobile | 1080 × 1350 px (portrait 4:5) |
| Kartu promo (opsional) | 600 × 750 px (4:5) |
| Foto produk | 1000 × 1000 px (persegi) |

Jika banner sudah berisi teks, matikan "Tampilkan teks" di slide tersebut.

## Deploy ke shared hosting (cPanel / FTP)

1. Build aset di lokal: `npm run build` (hosting tidak butuh Node). Folder `public/build` ikut di-upload.
2. Install vendor di lokal dengan `composer install --no-dev --optimize-autoloader`. Platform composer dikunci ke PHP 8.2.
3. Upload seluruh project, misalnya ke `~/mgispec`. Arahkan document root domain ke `~/mgispec/public`. Kalau document root tidak bisa diubah, isi `public_html` dengan isi folder `public` lalu sesuaikan path di `index.php`.
4. Siapkan `.env` produksi: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain`, DB_*, ADMIN_*.
5. Jalankan via Terminal cPanel: `php artisan migrate --force`, `php artisan storage:link`, `php artisan optimize`.
6. Data contoh (`--seed`) memakai foto placeholder. Untuk produksi, cukup buat admin lalu isi konten lewat CMS.
