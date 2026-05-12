# Debug Report — LARAS Project

## Ringkasan masalah utama

Project awal sudah memiliki struktur Laravel 11 dan Vue 3 yang relatif lengkap, tetapi terdapat beberapa masalah yang membuat proses run/development tidak stabil:

1. Artisan gagal pada environment PHP minimal karena fungsi `mb_split()` tidak tersedia.
2. `.env.example` masih memakai konfigurasi skeleton Laravel/SQLite, tidak sinkron dengan target database MySQL.
3. Axios memakai base URL hardcoded sehingga kurang fleksibel untuk development/proxy.
4. Vite belum memiliki proxy `/api` untuk skenario development terpisah.
5. Audio store memanggil backsound pada saat module import sehingga berpotensi memicu autoplay block dan request audio yang tidak perlu.
6. Challenge timer belum aman untuk level tanpa batas waktu.
7. `welcome.blade.php` masih merujuk named route `login`/`register` yang tidak didefinisikan pada route Laravel.
8. `axios` berada di `devDependencies`, padahal dipakai sebagai runtime dependency frontend.
9. Terdapat file kosong tidak relevan bernama `value('target_text')`.

## Root cause

- Environment PHP lokal yang dipakai untuk verifikasi tidak memuat ekstensi standar yang umumnya dibutuhkan Laravel (`mbstring`, `xml/dom`, dan PDO driver). Ini menyebabkan error pada Artisan, serve command, dan migration verification.
- Konfigurasi project masih bercampur antara default Laravel skeleton dan kebutuhan aplikasi final.
- Sebagian logika frontend masih bersifat prototipe: audio dijalankan terlalu awal, konfigurasi API belum environment-based, dan timer challenge belum menangani no-time-limit case.

## Perbaikan yang dilakukan

- Menambahkan fallback `app/Support/polyfills.php` untuk `mb_split()` dan memuatnya dari `bootstrap/app.php`.
- Mengubah `.env.example` menjadi konfigurasi LARAS berbasis MySQL.
- Menambahkan `VITE_API_BASE_URL=/api/v1` pada `.env.example`.
- Mengubah `resources/js/services/api.js` agar base URL API membaca environment variable.
- Menambahkan proxy Vite untuk `/api` dan `/sanctum` menuju Laravel local server.
- Menghapus pemanggilan `soundService.playBGM()` pada module import agar audio hanya aktif dari mekanisme audio store/user action.
- Memperbaiki challenge timer agar aman jika `time_limit_seconds` kosong/null.
- Mengubah link `welcome.blade.php` dari named route yang tidak tersedia menjadi path `/login` dan `/register`.
- Memindahkan `axios` dari `devDependencies` ke `dependencies`.
- Menghapus file kosong `value('target_text')`.
- Mengganti README default Laravel dengan instruksi setup project LARAS.

## File yang diubah

- `.env.example`
- `README.md`
- `DEBUG_REPORT.md`
- `bootstrap/app.php`
- `app/Support/polyfills.php`
- `package.json`
- `package-lock.json`
- `vite.config.js`
- `resources/js/services/api.js`
- `resources/js/stores/audioStore.js`
- `resources/js/views/ChallengeGameView.vue`
- `resources/views/welcome.blade.php`
- `value('target_text')` dihapus

## Hasil verifikasi

Berhasil diverifikasi pada environment ini:

```bash
php artisan route:list --no-ansi
npm run build
php -S 127.0.0.1:8099 -t public
curl http://127.0.0.1:8099/api/v1/health
```

Hasil health endpoint:

```json
{"status":"ok","app":"LARAS API"}
```

Belum dapat diverifikasi penuh pada environment ini:

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Alasannya bukan karena kode project, tetapi karena container verifikasi tidak memiliki `composer`, `pdo_mysql`/`pdo_sqlite`, dan `xml/dom`. Pada mesin lokal normal, aktifkan requirement PHP yang tercantum di README.

## Cara menjalankan backend

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Cara menjalankan frontend

```bash
npm install
npm run dev
```

Buka aplikasi dari Laravel URL:

```text
http://127.0.0.1:8000
```

## Cara setup database

Buat database MySQL:

```sql
CREATE DATABASE laras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laras
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:

```bash
php artisan migrate --seed
```

## Akun dummy

```text
Email    : demo@laras.test
Password : password
```

## Catatan penting

- ZIP output tidak menyertakan `.env`, `.git`, `vendor`, `node_modules`, dan log runtime.
- Jika `php artisan serve` memunculkan `Class "DOMDocument" not found`, aktifkan ekstensi `php-xml` / `dom`.
- Jika migration memunculkan `could not find driver`, aktifkan `pdo_mysql` dan pastikan service MySQL berjalan.
