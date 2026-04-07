# Panduan Pengembangan (AGENTS.md)

Dokumen ini berisi informasi arsitektur, standar kode, dan alur kerja (workflow) untuk pengembangan project **TeraSamarinda**. Dokumen ini menjadi acuan utama bagi agent dan developer dalam menulis atau mengubah kode.

## 1. Project Overview

**TeraSamarinda** adalah sistem manajemen konten (CMS) Dashboard dan Landing Page interaktif. Sistem ini memisahkan secara jelas antara frontend interaktif dan backend pengelola data.

## 2. Tech Stack

- **Frontend:** Vue 3 (Composition API), Vite, Pinia (State Management), Vue Router, Bootstrap 5.
- **Backend:** PHP Native (tanpa framework besar).
- **Database:** MySQL.

## 3. Authentication

- Menggunakan **Session-based Authentication** via PHP.
- Session dikelola sepenuhnya di sisi server, client (Vue) akan melakukan validasi endpoint secara berkala atau ketika memuat halaman terproteksi.

## 4. API (RESTful)

- Backend harus menyediakan endpoint API yang bersifat **RESTful**.
- Semua response wajib menggunakan header `Content-Type: application/json`.
- Status HTTP (200, 201, 400, 401, 403, 404, 500) harus dikembalikan secara tepat sesuai konteks operasi.

## 5. Code Standards

### Vue 3 (Frontend)

- **Composition API:** Selalu gunakan `<script setup>` (hindari mode Options API).
- **Reusability:** Ekstrak bagian UI yang berulang menjadi komponen tersendiri di dalam folder `components/`.
- **State Management:** Simpan global state (misal: data user login) di **Pinia**.
- **Routing:** Atur routing di Vue Router, terapkan _route guard_ untuk rute yang membutuhkan autentikasi (dashboard).

### PHP Native (Backend)

- **Clean Architecture:** Jangan mencetak HTML (`echo "<html>"`) di dalam file API. Pastikan khusus mengelola payload data (JSON).
- **Database Access:** WAJIB menggunakan **Prepared Statements** (via PDO atau MySQLi). **DILARANG KERAS** menyisipkan variabel langsung ke dalam query SQL (contoh yang salah: `SELECT * FROM users WHERE email='$email'`).

## 6. Security Standards

- **SQL Injection:** Dicegah dengan penggunaan Prepared Statements yang ketat.
- **Cross-Site Scripting (XSS):** Semua data dari database yang sifatnya text murni harus diproses dengan `htmlspecialchars()` jika dirender langsung, namun di konteks Vue, Vue Template (`{{ }}`) secara otomatis sudah melakukan escaping.
- **Cross-Site Request Forgery (CSRF):** Tambahkan token CSRF untuk request API yang bermutasi state (POST, PUT, DELETE).
- **Session Security:** Lindungi session token, set instruksi `HttpOnly` pada cookie session, dan lakukan _Session ID Regeneration_ saat login/logout.

## 7. Project Structure

Standar struktur root direktori yang diinginkan:

```text
TeraSamarinda/
│
├── frontend/                 # Workspace Vue 3
│   ├── src/
│   │   ├── assets/           # Gambar, CSS/SCSS kustom
│   │   ├── components/       # Reusable components
│   │   ├── router/           # Konfigurasi Vue Router
│   │   ├── stores/           # Pinia stores
│   │   ├── views/            # Halaman utama (Dashboard, Login, dll)
│   │   ├── App.vue           # Root component
│   │   └── main.js           # Vue instance initialization
│   ├── public/               # File statis (favicon)
│   ├── index.html            # Vite template entry point
│   └── package.json          # Dependency Vue
│
├── backend/                  # Workspace PHP Native
│   ├── api/                  # Kumpulan endpoint (auth.php, users.php)
│   ├── config/               # database.php (PDO/MySQLi initialization)
│   ├── helpers/              # Fungsi-fungsi utility (sanitasi, validasi)
│   └── uploads/              # Penyimpanan file asset statis pengguna
│
└── AGENTS.md                 # Dokumentasi standar pengembangan
```

## 8. Development Workflow

1. **Pemisahan Environment:** Backend berjalan di server lokal (misalnya `http://localhost/backend`) via XAMPP/Laragon. Frontend berjalan di Vite Dev Server (`http://localhost:5173`).
2. **CORS:** Karena berjalan di port berbeda saat development, API PHP harus menangani header CORS dengan benar untuk memperbolehkan origin dari Vite.
3. **Pengerjaan Fitur:**
   - Desain skema database terlebih dahulu jika perlu.
   - Buat endpoint CRUD di `backend/api/`.
   - Lakukan pengetesan API terisolasi (misal via Thunder Client / Postman).
   - Buat/update file di `frontend/src/views/` dan sambungkan (fetch/axios) menuju endpoint PHP yang telah ada.
