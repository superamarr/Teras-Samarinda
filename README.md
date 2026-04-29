<p align="center">
  <img src="src/assets/icons/logo.svg" alt="TeraSamarinda Logo" width="120" />
</p>

<h1 align="center">TeraSamarinda</h1>

<p align="center">
  <strong>Sistem Manajemen Konten (CMS) & Landing Page Interaktif</strong><br>
  Teras Samarinda — Pusat Kegiatan dan Fasilitas Masyarakat
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Vue-3-42b883?logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Vite-8-646cff?logo=vite&logoColor=white" alt="Vite" />
  <img src="https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white" alt="PHP Native" />
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5" />
</p>

---

## Deskripsi Website

**TeraSamarinda** adalah website CMS (Content Management System) yang dirancang untuk **Teras Samarinda**, sebuah pusat kegiatan dan fasilitas masyarakat di Kota Samarinda, Kalimantan Timur. Website ini memiliki dua sisi utama yang bekerja secara terpisah namun terintegrasi:

1. **Landing Page Publik** — Halaman depan yang dilihat oleh pengunjung umum. Menampilkan informasi tentang Teras Samarinda, fasilitas yang tersedia, kegiatan dan event yang diadakan, galeri foto, serta informasi kontak. Desain responsif dan interaktif dengan animasi scroll reveal dan carousel galeri.

2. **Dashboard Admin** — Panel pengelolaan konten yang hanya dapat diakses oleh administrator. Menyediakan fitur CRUD (Create, Read, Update, Delete) untuk seluruh konten yang tampil di Landing Page, manajemen booking/reservasi, analitik kunjungan, serta pengaturan sistem.

Komunikasi antara frontend dan backend dilakukan sepenuhnya melalui **RESTful API** dengan format JSON, sehingga kedua layer dapat dikembangkan dan di-deploy secara independen.

---

## Teknologi

| Kategori | Teknologi | Keterangan |
|----------|-----------|------------|
| **Frontend Framework** | Vue 3 (Composition API) | Antarmuka pengguna reaktif dengan `<script setup>` |
| **Build Tool** | Vite 8 | Development server cepat + optimasi build production |
| **State Management** | Pinia | Store global untuk autentikasi dan data bersama |
| **Routing** | Vue Router | Navigasi SPA dengan route guard autentikasi |
| **UI Framework** | Bootstrap 5 | Sistem grid, komponen, dan responsivitas |
| **Notifikasi** | SweetAlert2 | Dialog konfirmasi dan notifikasi yang konsisten |
| **Grafik** | ApexCharts | Visualisasi data analitik di dashboard |
| **Carousel** | Embla Carousel | Galeri foto interaktif di landing page |
| **Backend** | PHP Native | RESTful API tanpa framework, arsitektur MVC sederhana |
| **Database** | MySQL | Penyimpanan data relasional |
| **Koneksi DB** | PDO (Singleton) | Prepared Statements untuk keamanan SQL Injection |
| **Deployment** | InfinityFree | Shared hosting, build via Vite |

---

## Fitur Website

### Halaman Publik

| No | Fitur | Deskripsi | Screenshot |
|----|-------|-----------|------------|
| 1 | **Landing Page (Home)** | Halaman utama yang menyusun seluruh section dari atas ke bawah: Hero section dengan background video, About, Fasilitas, Kegiatan, Event, Galeri, dan Kontak. Animasi scroll reveal untuk setiap section. | <img width="1895" height="855" alt="image" src="https://github.com/user-attachments/assets/97154f86-5cc0-403d-8c5b-7414367808b7" /> |
| 2 | **Hero Section** | Background video/gambar dengan teks judul dan sub-judul yang dinamis. Konten dapat diubah oleh admin melalui dashboard. | <img width="1897" height="858" alt="image" src="https://github.com/user-attachments/assets/eda665c9-08d9-4aa0-8e05-fe7a6a26d11f" /> |
| 3 | **Halaman Galeri** | Galeri foto terpisah dengan grid layout responsif. Dilengkapi carousel Embla untuk tampilan slideshow dan lightbox untuk melihat foto ukuran penuh. | <img width="1889" height="848" alt="image" src="https://github.com/user-attachments/assets/1e41e495-d548-4e7f-ad7f-6d2d4223bae4" /> |
| 4 | **Halaman Event** | Daftar seluruh event/acara yang tersedia dalam format kartu. Menampilkan gambar, judul, tanggal, dan lokasi. Klik kartu untuk detail lengkap. | <img width="1895" height="855" alt="image" src="https://github.com/user-attachments/assets/131edd56-3fff-4cd1-aad0-acb9b89a420e" /> |
| 5 | **Detail Event** | Halaman detail satu event dengan informasi lengkap: deskripsi, gambar, tanggal & waktu, lokasi, dan kontak penyelenggara. | <img width="1896" height="870" alt="image" src="https://github.com/user-attachments/assets/fe499a9c-f2f7-47d1-b39b-d77e5c2356a0" /> |
| 6 | **Halaman About** | Halaman "Tentang Kami" dengan informasi detail tentang Teras Samarinda: sejarah, visi misi, dan gambar-gambar terkait. | <img width="1896" height="860" alt="image" src="https://github.com/user-attachments/assets/3d7b41b1-0d2b-447e-9fb1-1f2fab43ba52" /> |
| 7 | **Informasi Kontak** | Section kontak di landing page dengan alamat, telepon, email, dan embedded Google Maps untuk lokasi. | <img width="1896" height="766" alt="image" src="https://github.com/user-attachments/assets/2be52448-b5c7-4ab0-a83f-c127402fbbdb" /> |
| 8 | **Mode Maintenance** | Halaman pengganti yang ditampilkan ketika Superadmin mengaktifkan mode maintenance. Pengunjung tidak dapat mengakses konten apapun. | <img width="1913" height="859" alt="image" src="https://github.com/user-attachments/assets/3228bd27-ca74-4ea2-b9b4-875489df562a" /> |

### Dashboard Admin

| No | Fitur | Deskripsi | Screenshot |
|----|-------|-----------|------------|
| 9 | **Login Admin** | Halaman login dengan form username dan password. Hanya admin yang dapat mengakses dashboard. Password di-hash dengan bcrypt. | <img width="1919" height="875" alt="image" src="https://github.com/user-attachments/assets/5861ad24-cb0d-4968-9763-8e1f19682b88" /> |
| 10 | **Dashboard** | Ringkasan statistik (total pengguna, booking, event aktif) dalam kartu angka. Grafik tren analitik. Auto-refresh setiap 60 detik. | <img width="1919" height="863" alt="image" src="https://github.com/user-attachments/assets/e95a1b27-1c5a-4a7a-a683-70ecd98d14ea" /> |
| 11 | **Analitik & Page Views** | Data kunjungan halaman detail: grafik tren harian/mingguan, halaman paling populer, durasi rata-rata kunjungan, dan sumber referral. Tracking real-time menggunakan sendBeacon API. | <img width="1919" height="864" alt="image" src="https://github.com/user-attachments/assets/e4824493-2340-4837-b906-94fec3ad833a" /> |
| 12 | **Manajemen Hero** | Kelola hero section: ubah background (gambar/video), teks judul, dan sub-judul. Upload gambar dengan konversi otomatis ke WebP. | <img width="1919" height="869" alt="image" src="https://github.com/user-attachments/assets/f78f7d54-55c0-4596-9e93-24a17885cdf6" /> |
| 13 | **Manajemen About** | Kelola konten halaman About: deskripsi, gambar-gambar, dan informasi lainnya. | <img width="1896" height="870" alt="image" src="https://github.com/user-attachments/assets/aa64d881-b884-42d7-a124-1fb458656c5e" /> |
| 14 | **Manajemen Galeri** | Tambah, edit, hapus foto galeri. Atur keterangan dan urutan tampilan. | <img width="1900" height="855" alt="image" src="https://github.com/user-attachments/assets/fcca0162-f087-49fd-b6aa-27cd089ba449" /> |
| 15 | **Manajemen Kontak** | Kelola informasi kontak: alamat, nomor telepon, email, dan link Google Maps. | <img width="1898" height="865" alt="image" src="https://github.com/user-attachments/assets/f5ebe931-eae5-4bc6-8bf3-5c350d5bc193" /> |
| 16 | **Manajemen Kegiatan** | CRUD kegiatan/aktivitas Teras Samarinda. Daftar dalam format tabel, form detail untuk tambah/edit dengan upload gambar. | <img width="1899" height="792" alt="image" src="https://github.com/user-attachments/assets/e38e222e-60e2-4ee3-910d-b7bcf8754ade" /> |
| 17 | **Manajemen Event** | CRUD event/acara. Daftar dalam format tabel, form detail untuk tambah/edit dengan input tanggal, lokasi, dan upload gambar. | <img width="1894" height="830" alt="image" src="https://github.com/user-attachments/assets/3d609aa9-5531-42a0-9146-1c20b5115713" /> |
| 18 | **Manajemen Fasilitas** | CRUD fasilitas yang tersedia. Daftar dalam format tabel, form detail untuk tambah/edit dengan upload gambar. | <img width="1893" height="835" alt="image" src="https://github.com/user-attachments/assets/73f27012-a2f5-440e-bef6-be2315a4b7f1" /> |
| 19 | **Manajemen Booking** | Daftar reservasi/booking dalam format tabel. Admin dapat melihat detail dan mengubah status (pending → confirmed → completed). | <img width="1897" height="865" alt="image" src="https://github.com/user-attachments/assets/05f58677-e766-4847-bacc-ed5e24b28add" /> |
| 20 | **Pengaturan Sistem** | Konfigurasi sistem termasuk toggle maintenance mode. Konfirmasi SweetAlert2 sebelum perubahan diterapkan. | <img width="1891" height="786" alt="image" src="https://github.com/user-attachments/assets/22b11201-e948-483a-9b6d-487db63f4499" /> |
| 21 | **Manajemen Pengguna** | CRUD akun admin. Tambah, edit (username, role, password), dan hapus user. Password di-hash bcrypt sebelum disimpan. | <img width="1903" height="741" alt="image" src="https://github.com/user-attachments/assets/01c5e9bf-6ee2-4ac8-aaed-80bc87ef1526" /> |

---

## Struktur Project

```
TeraSamarinda/
│
├── src/                          # Frontend Vue 3
│   ├── api/                      # Modul komunikasi HTTP ke backend (axios)
│   ├── assets/                   # Ikon, gambar, CSS, video (diproses Vite)
│   │   ├── icons/
│   │   ├── images/
│   │   ├── styles/
│   │   └── videos/
│   ├── components/               # Komponen reusable
│   │   ├── admin/                # Komponen dashboard admin
│   │   ├── landing/              # Komponen section landing page
│   │   └── ui/                   # Komponen UI generik
│   ├── composables/              # Logika reusable (usePageTracking, useSwal)
│   ├── directives/               # Custom directive (v-entrance)
│   ├── layouts/                  # Layout wrapper (AdminLayout)
│   ├── router/                   # Konfigurasi Vue Router + guards
│   ├── stores/                   # Pinia stores (auth)
│   ├── utils/                    # Helper (media URL resolver)
│   ├── views/                    # Halaman utama
│   │   ├── admin/                # Halaman dashboard
│   │   │   ├── content/          # Manajemen konten landing
│   │   │   └── system/          # Pengaturan sistem
│   │   ├── LandingView.vue
│   │   ├── GalleryView.vue
│   │   └── ...                   # Halaman publik lainnya
│   ├── App.vue
│   └── main.js
│
├── backend/                      # Backend PHP Native
│   ├── app/
│   │   ├── Controllers/          # Handler request API (13 controller)
│   │   └── Models/               # Akses database PDO (13 model)
│   ├── config/                   # Koneksi database (connection.php)
│   ├── helpers/                  # Utility (Auth, Response, Sanitize, dll)
│   └── public/                   # Web root (document root Apache)
│       ├── .htaccess             # URL rewriting + CORS
│       ├── index.php             # Front controller + router
│       └── uploads/              # File upload (gambar, video)
│           ├── about/
│           ├── activities/
│           ├── events/
│           ├── facilities/
│           ├── gallery/
│           └── hero/
│
├── public/                       # Aset statis Vite (favicon)
├── backups/                      # Backup konfigurasi deployment
├── screenshots/                  # Screenshot untuk dokumentasi
│
├── .env.example                  # Template environment
├── .gitignore
├── index.html                    # Entry point HTML Vite
├── package.json                  # Dependensi npm
├── vite.config.js                # Konfigurasi Vite + proxy
├── DOKUMENTASI.md                # Dokumentasi detail struktur project
└── README.md                     # File ini
```

> Untuk penjelasan detail setiap folder dan file, lihat [DOKUMENTASI.md](./DOKUMENTASI.md).

---

## Keamanan

| Aspek | Implementasi |
|-------|--------------|
| **SQL Injection** | Semua query database menggunakan PDO Prepared Statements — variabel tidak pernah disisipkan langsung ke string query |
| **XSS** | Vue Template (`{{ }}`) melakukan auto-escaping. Backend juga menerapkan `htmlspecialchars()` via `Sanitize.php` |
| **CSRF** | Token CSRF diterapkan untuk semua request bermutasi (POST, PUT, DELETE) melalui `Auth.php` helper |
| **Session Security** | Cookie session menggunakan flag `HttpOnly`. Di production (HTTPS), flag `Secure=true` dan `SameSite=None`. Session ID di-regenerate saat login/logout |
| **Password Hashing** | Password di-hash menggunakan `password_hash()` dengan algoritma bcrypt (`PASSWORD_DEFAULT`) |
| **File Upload** | Validasi tipe file (hanya gambar/video), batas ukuran, dan penamaan unik via `Uploader.php` |
| **Input Sanitasi** | Semua input dari request divalidasi dan disanitasi via `Sanitize.php` sebelum diproses |

---

## API Endpoints

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| POST | `/auth/login` | Login admin | ❌ |
| POST | `/auth/logout` | Logout admin | ✅ |
| GET | `/auth/check` | Cek session aktif | ✅ |
| GET | `/hero` | Ambil data hero | ❌ |
| PUT | `/hero` | Update hero | ✅ |
| GET | `/about` | Ambil data about | ❌ |
| PUT | `/about` | Update about | ✅ |
| GET | `/gallery` | Daftar galeri | ❌ |
| POST | `/gallery` | Tambah galeri | ✅ |
| PUT | `/gallery/{id}` | Edit galeri | ✅ |
| DELETE | `/gallery/{id}` | Hapus galeri | ✅ |
| GET | `/events` | Daftar event | ❌ |
| GET | `/events/{id}` | Detail event | ❌ |
| POST | `/events` | Tambah event | ✅ |
| PUT | `/events/{id}` | Edit event | ✅ |
| DELETE | `/events/{id}` | Hapus event | ✅ |
| GET | `/activities` | Daftar kegiatan | ❌ |
| GET | `/activities/{id}` | Detail kegiatan | ❌ |
| POST | `/activities` | Tambah kegiatan | ✅ |
| PUT | `/activities/{id}` | Edit kegiatan | ✅ |
| DELETE | `/activities/{id}` | Hapus kegiatan | ✅ |
| GET | `/facilities` | Daftar fasilitas | ❌ |
| GET | `/facilities/{id}` | Detail fasilitas | ❌ |
| POST | `/facilities` | Tambah fasilitas | ✅ |
| PUT | `/facilities/{id}` | Edit fasilitas | ✅ |
| DELETE | `/facilities/{id}` | Hapus fasilitas | ✅ |
| GET | `/bookings` | Daftar booking | ✅ |
| POST | `/bookings` | Tambah booking | ❌ |
| PUT | `/bookings/{id}` | Ubah status booking | ✅ |
| DELETE | `/bookings/{id}` | Hapus booking | ✅ |
| GET | `/contact` | Ambil data kontak | ❌ |
| PUT | `/contact` | Update kontak | ✅ |
| GET | `/analytics` | Data analitik | ✅ |
| POST | `/analytics` | Data analitik (filtered) | ✅ |
| POST | `/page-views` | Rekam kunjungan halaman | ❌ |
| PUT | `/page-views` | Update durasi kunjungan | ❌ |
| GET | `/stats` | Statistik ringkasan | ✅ |
| GET | `/system` | Pengaturan sistem | ✅ |
| PUT | `/system` | Update pengaturan | ✅ |
| GET | `/users` | Daftar pengguna | ✅ |
| POST | `/users` | Tambah pengguna | ✅ |
| PUT | `/users/{id}` | Edit pengguna | ✅ |
| DELETE | `/users/{id}` | Hapus pengguna | ✅ |
| GET | `/activity-logs` | Log aktivitas | ✅ |

---

## Lisensi

Project ini dikembangkan untuk kebutuhan Teras Samarinda. Hak cipta dilindungi.
