# LARAS — Ladang Aksara Siliwangi

LARAS adalah aplikasi typing game berbasis Laravel 11 dan Vue 3. Backend menyediakan API Story Mode, progress pemain, leaderboard, achievement, realtime chat, dan challenge room. Frontend menggunakan Vue 3, Vite, Pinia, Vue Router, dan Axios.

## Stack

- Backend: Laravel 11, Laravel Sanctum, PHP 8.2+
- Frontend: Vue 3, Vite, Pinia, Vue Router, Axios
- Database: MySQL

## Requirement lokal

Pastikan ekstensi PHP berikut aktif sebelum menjalankan project:

- `mbstring`
- `xml` / `dom`
- `pdo_mysql`
- `openssl`
- `fileinfo`
- `ctype`
- `json`
- `tokenizer`

Catatan: project menyertakan fallback ringan untuk `mb_split()` agar Artisan tetap bisa berjalan pada environment minimal. Namun instalasi yang direkomendasikan tetap memakai ekstensi `mbstring` resmi.

## Setup dari awal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL bernama `laras`, lalu sesuaikan bagian berikut di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laras
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

## Menjalankan aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Buka aplikasi dari URL Laravel:

```text
http://127.0.0.1:8000
```

Jangan membuka aplikasi dari URL Vite sebagai entry utama. Vite hanya dipakai untuk asset development; halaman SPA tetap disajikan oleh Laravel.

## Build production

```bash
npm run build
```

## Akun dummy

Seeder membuat akun dummy berikut:

```text
Email    : demo@laras.test
Password : password
```

## Endpoint utama

Base URL API:

```text
/api/v1
```

Endpoint penting:

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/me`
- `GET /api/v1/chapters`
- `GET /api/v1/levels`
- `POST /api/v1/game/start`
- `POST /api/v1/game/submit`
- `GET /api/v1/progress`
- `GET /api/v1/leaderboard`
- `GET /api/v1/challenge/rooms`

## Catatan operasional

- File `.env`, `vendor`, `node_modules`, `.git`, dan log runtime tidak perlu dikirim ke deployment artifact.
- Jika `php artisan serve` menampilkan error `Class "DOMDocument" not found`, aktifkan ekstensi `php-xml` / `dom` pada instalasi PHP.
- Jika migration gagal dengan pesan `could not find driver`, aktifkan `pdo_mysql` dan pastikan service MySQL berjalan.
- Jika frontend tidak bisa memanggil API saat development, pastikan Laravel aktif di `http://127.0.0.1:8000` dan `VITE_API_BASE_URL=/api/v1` tetap ada di `.env`.
