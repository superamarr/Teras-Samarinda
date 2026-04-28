# Dokumentasi Struktur Project — TeraSamarinda

## Gambaran Umum

**TeraSamarinda** adalah sistem manajemen konten (CMS) yang terdiri dari Dashboard admin dan Landing Page interaktif untuk Teras Samarinda. Project ini dibangun dengan pendekatan pemisahan tanggung jawab (separation of concerns) yang ketat — frontend bertanggung jawab penuh atas tampilan dan interaksi pengguna, sementara backend bertanggung jawab penuh atas pengelolaan data, validasi server-side, dan keamanan.

Komunikasi antara frontend dan backend dilakukan sepenuhnya melalui RESTful API dengan format JSON, sehingga kedua layer dapat dikembangkan dan di-deploy secara independen.

- **Frontend:** Vue 3 (Composition API + `<script setup>`), Vite, Pinia, Vue Router, Bootstrap 5
- **Backend:** PHP Native (RESTful API), MySQL
- **Deployment:** InfinityFree (shared hosting), build via Vite

---

## Struktur Root

Root project berisi file-file konfigurasi dan entry point yang mengatur keseluruhan proses development maupun production. File-file ini bersifat global — artinya pengaruhnya mencakup seluruh komponen frontend maupun backend.

| File | Fungsi |
|------|--------|
| `.env.example` | Template konfigurasi environment. Developer baru menyalin file ini menjadi `.env` lalu mengisi nilai sesuai environment lokal mereka. Berisi panduan untuk konfigurasi local development maupun production. |
| `.gitignore` | Mendefinisikan file dan folder mana yang tidak boleh di-track oleh Git. Mencegah kredensial sensitif (`.env`), build output (`dist/`), dan file development sementara masuk ke repository. |
| `.prettierrc.json` | Konfigurasi Prettier untuk menjaga konsistensi formatting kode di seluruh project. Memastikan setiap developer yang berkontribusi menghasilkan kode dengan gaya penulisan yang seragam. |
| `AGENTS.md` | Panduan pengembangan yang ditujukan untuk AI agent dan developer. Berisi arsitektur project, standar kode, alur kerja, dan aturan keamanan yang harus dipatuhi saat menulis atau mengubah kode. |
| `DOKUMENTASI.md` | Dokumentasi struktur project (file ini). Menjelaskan setiap folder, modul, dan alur kerja aplikasi secara naratif agar mudah dipahami. |
| `index.html` | Entry point HTML untuk Vite. File ini adalah satu-satunya file HTML yang ada di project — Vue akan me-mount aplikasi ke dalam `<div id="app">` yang ada di file ini. Vite menginject script dan style secara dinamis saat development maupun build. |
| `jsconfig.json` | Konfigurasi path alias untuk JavaScript dan IDE. Memungkinkan penggunaan import path seperti `@/components/...` alih-alih relative path yang panjang, serta mendukung auto-completion di VS Code. |
| `package.json` | Manifest project Node.js. Mendefinisikan dependensi (Vue, Pinia, Vue Router, Bootstrap, dll), script npm (`dev`, `build`, `preview`), dan metadata project. |
| `package-lock.json` | Lock file yang membekukan versi tepat dari setiap dependensi dan sub-dependensi. Memastikan bahwa `npm install` di mesin manapun menghasilkan versi dependensi yang identik, menghindari inkonsistensi. |
| `postcss.config.js` | Konfigurasi PostCSS yang memproses CSS sebelum di-output oleh Vite. Menjalankan Autoprefixer untuk menambahkan vendor prefix secara otomatis sehingga CSS kompatibel dengan berbagai browser. |
| `vite.config.js` | Konfigurasi utama Vite. Mengatur resolve alias (`@` → `src/`), dev server proxy ke backend PHP, dan environment variable loading melalui `loadEnv()` sehingga build production menggunakan API URL yang berbeda dari development. |

> **Catatan:** File `.env` dan `.env.production` ada di disk tetapi di-gitignore karena berisi kredensial sensitif (database password, API URL production, dll). File `.env` dipakai saat `npm run dev`, file `.env.production` dipakai saat `npm run build`.

---

## Frontend — `src/`

Folder `src/` adalah jantung dari seluruh aplikasi frontend. Setiap file, komponen, dan logika yang berjalan di browser pengguna berada di sini. Vite memproses seluruh isi folder ini saat development (hot-reload) maupun saat build production (optimasi, tree-shaking, code-splitting).

### File Root `src/`

| File | Fungsi |
|------|--------|
| `App.vue` | Root component Vue yang menjadi titik awal render seluruh aplikasi. Berisi `<RouterView>` sebagai penampung halaman aktif, serta komponen global seperti Navbar dan Footer yang tampil di seluruh halaman landing. Juga mengelola logic untuk menampilkan Navbar/Footer secara kondisional (tersembunyi di area admin). |
| `main.js` | Inisialisasi Vue instance. Meregistrasi semua plugin yang dibutuhkan (Pinia untuk state management, Vue Router untuk navigasi, Bootstrap untuk styling), serta mendefinisikan custom directive `v-entrance`. File ini adalah entry point yang di-reference oleh `index.html`. |

---

### `src/api/` — Lapisan Komunikasi API

Folder ini berfungsi sebagai jembatan komunikasi antara frontend Vue dan backend PHP. Setiap file di sini mewakili satu domain data dan mengekspor fungsi-fungsi yang sesuai dengan operasi CRUD — misalnya `events.js` menyediakan `getEvents()`, `createEvent()`, `updateEvent()`, dan `deleteEvent()`.

Semua request HTTP dilakukan melalui instance axios terpusat yang didefinisikan di `index.js`. Instance ini menangani base URL, header autentikasi (mengirim cookie session secara otomatis via `withCredentials`), dan interceptor error secara seragam. Dengan pemisahan ini, komponen Vue tidak perlu mengetahui detail teknis HTTP — cukup memanggil fungsi yang sudah disediakan, dan jika backend berubah URL atau menambah header, cukup diubah di satu tempat (`index.js`).

| File | Domain Data |
|------|-------------|
| `about.js` | Data halaman About (deskripsi, gambar) |
| `activities.js` | CRUD kegiatan/aktivitas Teras Samarinda |
| `activityLogs.js` | Log aktivitas pengguna (audit trail) |
| `analytics.js` | Data analitik dan page views (record view, update duration) |
| `auth.js` | Autentikasi (login, logout, session check, CSRF token) |
| `bookings.js` | CRUD reservasi/booking dari pengunjung |
| `contact.js` | Data kontak (alamat, telepon, email, maps) |
| `events.js` | CRUD event/acara yang diadakan Teras Samarinda |
| `facilities.js` | CRUD fasilitas yang tersedia |
| `gallery.js` | CRUD galeri foto |
| `hero.js` | CRUD hero section (gambar, video, teks utama) |
| `index.js` | Instance axios terpusat (base URL, withCredentials, interceptor) |
| `stats.js` | Statistik ringkasan dashboard (total user, booking, dll) |
| `system.js` | Pengaturan sistem (maintenance mode toggle) |
| `users.js` | CRUD pengguna/admin |

---

### `src/assets/` — Aset Statis Frontend

Folder ini menyimpan aset statis yang diimpor langsung oleh komponen Vue. Berbeda dengan file di `backend/public/uploads/` yang disajikan sebagai file statis oleh web server, aset di folder ini akan diproses oleh Vite — gambar akan dioptimasi dan diberi hash untuk cache-busting, CSS akan diproses oleh PostCSS, dan video akan di-copy ke build output.

#### `src/assets/icons/`

Menyimpan ikon dalam format SVG yang digunakan di seluruh aplikasi, terutama logo Teras Samarinda. Format SVG dipilih karena scalable tanpa kehilangan kualitas dan ukuran file yang kecil.

- `logo.svg` — Logo utama Teras Samarinda, digunakan di Navbar dan Sidebar

#### `src/assets/images/`

Gambar-gambar yang diimpor langsung oleh komponen Vue, terutama untuk section-section di Landing Page (hero background, about section, dll). Saat build, Vite akan mengoptimasi gambar ini (menambahkan hash ke filename untuk cache-busting) dan memindahkannya ke folder `dist/assets/`.

- 16 file gambar (format JPG, JFIF, PNG) untuk hero, about section, dan konten landing page lainnya

#### `src/assets/styles/`

File CSS kustom yang berisi variabel desain (design tokens). Pendekatan ini memastikan konsistensi visual di seluruh aplikasi — warna, spacing, font, dan border radius didefinisikan di satu tempat dan direference oleh komponen manapun.

- `design-tokens.css` — Variabel CSS root (warna primary/secondary, spacing unit, font family, border radius, dll)

#### `src/assets/videos/`

Video yang diimpor langsung oleh komponen Vue, digunakan sebagai background video di hero section. Format WebM dipilih untuk kompatibilitas web, sementara format MOV tersedia sebagai source asli.

- `pbw (1).MOV` — Video source asli
- `pbw (1).webm` — Video yang sudah dikonversi untuk web

---

### `src/components/` — Komponen Reusable

Komponen Vue yang dapat dipakai ulang (reusable) di berbagai halaman. Dibagi menjadi tiga kategori berdasarkan konteks penggunaannya: admin, landing, dan UI generik.

Pemisahan ini penting karena komponen admin memiliki kebutuhan yang berbeda (sidebar, stat card, data table) dari komponen landing (hero section, gallery carousel, event card). Sementara komponen UI generik (button, action button) bersifat netral dan bisa dipakai di keduanya.

#### `src/components/admin/` — Komponen Area Admin

Komponen-komponen yang hanya digunakan di dalam area Dashboard admin. Komponen-komponen ini membentuk kerangka layout admin (sidebar, navbar, content wrapper) serta menyediakan elemen visual khas admin seperti kartu statistik.

- `AdminContentWrapper.vue` — Wrapper yang membungkus konten setiap halaman admin. Menyediakan header judul halaman, padding, dan struktur yang konsisten sehingga setiap halaman admin memiliki tampilan yang seragam.
- `AdminNavbar.vue` — Navbar atas area admin yang menampilkan nama user yang sedang login dan tombol logout.
- `Sidebar.vue` — Sidebar navigasi admin dengan menu-menu yang dikelompokkan berdasarkan kategori (Dashboard, Konten, Sistem). Menandai halaman aktif secara visual.
- `StatCard.vue` — Kartu yang menampilkan satu angka statistik ringkasan (misalnya total user, total booking bulan ini). Digunakan di DashboardView untuk memberikan gambaran cepat.

#### `src/components/landing/` — Komponen Landing Page

Komponen-komponen yang menyusun Landing Page publik. Setiap file mewakili satu section/segmen dari halaman utama, dipanggil oleh `LandingView.vue` dan disusun secara berurutan dari atas ke bawah. Pendekatan satu-file-per-section memudahkan pengelolaan dan pengubahan tampilan satu bagian tanpa mempengaruhi bagian lain.

- `HeroSection.vue` — Section paling atas dengan background video/gambar dan teks utama. Memberikan kesan pertama kepada pengunjung.
- `AboutSection.vue` — Section yang memperkenalkan Teras Samarinda secara singkat.
- `FacilitiesSection.vue` — Menampilkan daftar fasilitas yang tersedia dalam format kartu.
- `ActivitiesSection.vue` — Menampilkan kegiatan-kegiatan terbaru dalam format daftar.
- `EventsSection.vue` — Menampilkan event/acara yang akan datang dalam format kartu.
- `GallerySection.vue` — Section galeri foto dengan grid layout.
- `GalleryEmblaCarousel.vue` — Carousel galeri menggunakan library Embla Carousel, menampilkan foto dalam format slideshow yang interaktif.
- `ContactSection.vue` — Section kontak dengan informasi alamat, telepon, email, dan embedded Google Maps.

#### `src/components/ui/` — Komponen UI Generik

Komponen UI yang bersifat generik dan dapat dipakai di mana saja — baik di landing page maupun di area admin. Komponen-komponen ini mengenkapsulasi pola UI yang berulang agar tidak perlu menulis ulang kode yang sama di banyak tempat.

- `BaseButton.vue` — Tombol dasar dengan variant (primary, secondary, danger, outline) dan ukuran (sm, md, lg). Semua tombol di aplikasi sebaiknya menggunakan komponen ini untuk menjaga konsistensi visual.
- `ActionButton.vue` — Tombol dengan ikon untuk aksi CRUD (edit, hapus, detail). Menggabungkan ikon dan teks dalam satu komponen.
- `EventCard.vue` — Kartu yang menampilkan informasi satu event (gambar, judul, tanggal, lokasi). Dipakai di Landing Page dan halaman Events.
- `FacilityCard.vue` — Kartu yang menampilkan informasi satu fasilitas (gambar, nama, deskripsi singkat).
- `ActivityListItem.vue` — Item daftar yang menampilkan informasi satu kegiatan (gambar, judul, deskripsi singkat).
- `ContactInfoItem.vue` — Item yang menampilkan satu informasi kontak (ikon, label, nilai).

#### File di `src/components/` (root level)

Komponen global yang muncul di seluruh Landing Page (bukan bagian dari section tertentu).

- `Navbar.vue` — Navbar Landing Page publik dengan navigasi ke halaman-halaman utama dan tombol login admin. Berubah secara visual saat user scroll (menjadi lebih kompak/transparan).
- `Footer.vue` — Footer Landing Page dengan informasi kontak, link navigasi, dan copyright.
- `ScrollReveal.vue` — Komponen wrapper yang menambahkan animasi masuk (fade-in, slide-up) ke elemen anak saat elemen tersebut masuk ke viewport saat user scroll. Menggunakan Intersection Observer API.

---

### `src/composables/` — Logika Reusable (Composition API)

Composable functions adalah fungsi-fungsi Vue 3 Composition API yang berisi logika yang dapat dipakai ulang di beberapa komponen. Berbeda dengan komponen yang mengenkapsulasi tampilan + logika, composable hanya mengandung logika — menjaga komponen tetap bersih dan fokus pada tampilan.

- `useSwal.js` — Wrapper SweetAlert2 yang menyediakan fungsi `confirm()` dan `alert()` dengan konfigurasi terpusat (tema, posisi, teks tombol). Memastikan setiap dialog konfirmasi dan notifikasi di seluruh aplikasi memiliki tampilan dan perilaku yang konsisten. Digunakan oleh hampir semua halaman admin untuk konfirmasi aksi destruktif (hapus, toggle maintenance mode, dll).
- `usePageTracking.js` — Composable yang melacak kunjungan halaman untuk keperluan analitik. Saat komponen di-mount, mengirim POST request ke backend untuk merekam view baru. Saat user meninggalkan halaman (visibility change atau beforeunload), mengirim PUT request untuk mengupdate durasi kunjungan menggunakan `sendBeacon` API sebagai metode utama dan XHR `beforeunload` sebagai fallback — memastikan data terkirim meskipun tab ditutup. Session ID disimpan di `localStorage` dengan masa berlaku 30 menit untuk mengidentifikasi pengunjung unik. Digunakan di semua halaman publik (Landing, Gallery, Events, EventDetail, AboutDetail).

---

### `src/directives/` — Custom Vue Directives

Custom directives adalah cara Vue untuk menambahkan perilaku langsung ke elemen DOM tanpa membungkusnya dalam komponen. Berguna untuk efek visual yang perlu diterapkan langsung ke elemen.

- `entrance.js` — Directive `v-entrance` yang menambahkan animasi masuk (fade-in + slide-up) ke elemen saat pertama kali masuk ke viewport. Menggunakan Intersection Observer API untuk mendeteksi visibilitas elemen. Diterapkan di seluruh Landing Page untuk memberikan efek scroll reveal yang halus dan meningkatkan pengalaman pengguna.

---

### `src/layouts/` — Layout Wrapper

Layout wrapper menyediakan struktur halaman yang konsisten untuk sekelompok halaman yang memiliki kerangka tampilan sama. Alih-alih mengulang struktur sidebar + navbar + content di setiap halaman admin, cukup didefinisikan sekali di layout.

- `AdminLayout.vue` — Layout utama area admin yang terdiri dari Sidebar (navigasi kiri), AdminNavbar (bar atas), dan slot konten di tengah. Semua halaman admin (`/admin/*`) di-render di dalam layout ini. Menggunakan Vue Router's nested route layout pattern.

---

### `src/router/` — Konfigurasi Navigasi

Vue Router mengatur navigasi antar halaman secara client-side — tanpa perlu memuat ulang seluruh halaman dari server. Semua definisi route dan logika penjaga (guard) berada di sini.

- `index.js` — Mendefinisikan seluruh route aplikasi dalam tiga kelompok: (1) halaman publik (Landing, Gallery, Events, About, Login, Maintenance), (2) halaman admin yang dilindungi oleh route guard autentikasi (Dashboard, Analytics, Content Management, System), dan (3) redirect untuk route yang tidak ditemukan. Route guard memeriksa status autentikasi dari Pinia auth store sebelum mengizinkan akses ke halaman admin — jika belum login, user diarahkan ke halaman Login.

---

### `src/stores/` — State Management Global

Pinia stores menyimpan state (data) yang perlu diakses dari banyak komponen secara bersamaan. Tanpa store, data harus di-pass secara manual melalui props dan events antar komponen, yang menjadi rumit untuk data global seperti status login.

- `auth.js` — Store autentikasi yang menyimpan data user yang sedang login (nama, username, role) dan status autentikasi (sudah login atau belum). Menyediakan fungsi `login()`, `logout()`, dan `checkAuth()` yang masing-masing berkomunikasi dengan backend API. Store ini di-watch oleh router guard untuk menentukan apakah user boleh mengakses halaman admin.

---

### `src/utils/` — Fungsi Utilitas

Fungsi-fungsi helper yang tidak terikat pada komponen tertentu dan bersifat murni (pure function) — menerima input, menghasilkan output, tanpa side effect. Dapat dipanggil dari mana saja tanpa perlu instantiasi.

- `media.js` — Helper untuk meresolusi URL media/gambar. Backend menyimpan path relatif di database (misalnya `uploads/hero/hero_123.jpg`), sementara frontend membutuhkan URL lengkap. Fungsi `resolveMediaUrl()` menggabungkan `VITE_API_BASE_URL` dari environment variable dengan path relatif untuk menghasilkan URL yang benar, baik saat development (localhost) maupun production.

---

### `src/views/` — Halaman-Halaman Aplikasi

Views adalah halaman-halaman utama yang di-mapping satu-ke-satu dengan route di Vue Router. Setiap file merepresentasikan satu halaman yang dapat diakses pengguna melalui URL tertentu. Views bertanggung jawab mengatur layout halaman, memanggil API, dan menyusun komponen-komponen yang dibutuhkan.

#### Halaman Publik (root level)

Halaman-halaman ini dapat diakses oleh siapa saja tanpa login. Mereka membentuk pengalaman pengunjung saat mengunjungi website Teras Samarinda.

- `LandingView.vue` — Halaman utama (Home) yang menyusun seluruh section Landing Page dari atas ke bawah: Hero, About, Facilities, Activities, Events, Gallery, Contact. Memanggil komponen section dari `src/components/landing/` dan mengirimkan data yang di-fetch dari API. Menginstal `usePageTracking()` untuk melacak kunjungan.
- `GalleryView.vue` — Halaman galeri foto terpisah yang menampilkan seluruh foto dalam format grid. Memiliki filter berdasarkan kategori dan lightbox untuk melihat foto dalam ukuran penuh. Menginstal `usePageTracking()`.
- `EventsView.vue` — Halaman daftar seluruh event/acara yang tersedia. Menampilkan event dalam format kartu dengan gambar, judul, tanggal, dan lokasi. Pengunjung dapat mengklik kartu untuk melihat detail. Menginstal `usePageTracking()`.
- `EventDetailView.vue` — Halaman detail satu event yang menampilkan informasi lengkap (deskripsi, gambar, tanggal, waktu, lokasi, kontak). Diakses dari EventsView melalui link. Menginstal `usePageTracking()`.
- `AboutDetailView.vue` — Halaman "Tentang Kami" yang menampilkan informasi detail tentang Teras Samarinda (sejarah, visi misi, gambar-gambar). Menginstal `usePageTracking()`.
- `LoginView.vue` — Halaman login admin dengan form username dan password. Hanya admin yang dapat login — pengunjung biasa tidak memiliki akun. Jika login berhasil, diarahkan ke Dashboard.
- `MaintenanceView.vue` — Halaman yang ditampilkan ketika sistem dalam mode maintenance. Menampilkan pesan bahwa website sedang dalam perbaikan. Mode maintenance diaktifkan/dinonaktifkan oleh admin melalui SystemSettingsView.

#### `src/views/admin/` — Halaman Dashboard Admin

Halaman-halaman yang hanya dapat diakses oleh admin yang sudah login. Dilindungi oleh route guard yang memeriksa status autentikasi. Semua halaman admin di-render di dalam `AdminLayout.vue`.

- `DashboardView.vue` — Dashboard utama yang menampilkan ringkasan statistik (total pengguna, booking bulan ini, event aktif, dll) menggunakan StatCard. Juga menampilkan grafik tren analitik. Data di-refresh otomatis setiap 60 detik dengan indikator "terakhir diperbarui" agar admin selalu melihat data terkini tanpa perlu refresh manual.
- `AnalyticsView.vue` — Halaman analitik yang menampilkan data kunjungan halaman secara detail: grafik tren harian/mingguan, halaman paling populer, durasi rata-rata kunjungan, dan sumber referral. Data di-refresh otomatis setiap 60 detik.
- `BookingView.vue` — Halaman manajemen reservasi yang menampilkan daftar booking dalam format tabel. Admin dapat melihat detail booking, mengubah status (pending → confirmed → completed), dan menghapus booking.

#### `src/views/admin/content/` — Manajemen Konten Landing Page

Halaman-halaman untuk mengelola konten yang ditampilkan di Landing Page publik. Setiap halaman biasanya menampilkan daftar item dalam format tabel dan menyediakan tombol untuk menambah, mengedit, dan menghapus. Perubahan di sini langsung terlihat oleh pengunjung di Landing Page.

- `ContentHeroView.vue` — Mengelola hero section: mengubah background (gambar/video), teks judul, dan teks sub-judul. Mendukung upload gambar dan konversi otomatis ke format WebP untuk performa.
- `ContentAboutView.vue` — Mengelola konten halaman About: deskripsi, gambar-gambar, dan informasi lainnya.
- `ContentGalleryView.vue` — Mengelola galeri foto: menambah foto baru, mengedit keterangan, menghapus foto, dan mengatur urutan tampilan.
- `ContentContactView.vue` — Mengelola informasi kontak: alamat, nomor telepon, alamat email, dan embedded Google Maps.
- `ActivitiesListView.vue` — Menampilkan daftar seluruh kegiatan dalam format tabel. Menyediakan navigasi ke halaman detail untuk menambah atau mengedit.
- `ActivityDetailView.vue` — Form untuk menambah atau mengedit satu kegiatan. Menangani input teks dan upload gambar.
- `EventsListView.vue` — Menampilkan daftar seluruh event dalam format tabel. Menyediakan navigasi ke halaman detail.
- `EventDetailView.vue` — Form untuk menambah atau mengedit satu event. Menangani input teks, tanggal, lokasi, dan upload gambar.
- `FacilitiesListView.vue` — Menampilkan daftar seluruh fasilitas dalam format tabel.
- `FacilityDetailView.vue` — Form untuk menambah atau mengedit satu fasilitas. Menangani input teks dan upload gambar.

#### `src/views/admin/system/` — Pengaturan Sistem

Halaman-halaman untuk mengelola aspek teknis sistem, bukan konten. Perubahan di sini mempengaruhi perilaku keseluruhan website.

- `SystemSettingsView.vue` — Pengaturan sistem termasuk toggle maintenance mode. Ketika admin mengaktifkan maintenance mode, seluruh halaman publik akan menampilkan MaintenanceView dan tidak dapat diakses sampai mode maintenance dinonaktifkan. Menggunakan SweetAlert2 untuk konfirmasi sebelum perubahan diterapkan — jika admin membatalkan, tidak ada perubahan state yang terjadi.
- `UserManagementView.vue` — Manajemen akun pengguna admin. Menampilkan daftar user dalam format tabel. Admin dapat menambah user baru, mengedit (mengubah username, role, password), dan menghapus user. Password di-hash menggunakan bcrypt sebelum disimpan ke database.

---

## Backend — `backend/`

Folder `backend/` berisi seluruh source code PHP Native yang berjalan di server. Backend ini berfungsi sebagai penyedia RESTful API — menerima request HTTP dari frontend, memproses data, dan mengembalikan response dalam format JSON. Backend tidak pernah mencetak HTML — pemisahan ini memungkinkan frontend dan backend dikembangkan secara independen.

Struktur backend mengikuti pola MVC sederhana: Controller menangani request dan response, Model menangani akses database, dan Helper menyediakan fungsi-fungsi pendukung.

### `backend/app/Controllers/` — Pengendali Request API

Controller adalah titik masuk logika bisnis. Setiap request HTTP yang masuk ke `index.php` akan diarahkan (routing) ke controller dan method yang sesuai. Controller bertanggung jawab untuk: (1) menerima dan memvalidasi input, (2) memanggil Model untuk operasi database, (3) mengembalikan response JSON dengan HTTP status code yang tepat.

Validasi input dan pengecekan autentikasi dilakukan di level controller sebelum operasi database dijalankan, memastikan bahwa data yang tidak valid atau request yang tidak terotorisasi ditolak sejak awal.

- `AboutController.php` — Menangani operasi CRUD data halaman About (mengambil, memperbarui deskripsi dan gambar).
- `ActivityController.php` — Menangani operasi CRUD kegiatan/aktivitas (daftar, detail, tambah, edit, hapus).
- `ActivityLogController.php` — Menangani pengambilan log aktivitas pengguna (read-only, tidak ada operasi write langsung — log ditulis oleh helper `ActivityLogger`).
- `AnalyticsController.php` — Menangani dua operasi: (1) mengambil data analitik untuk dashboard (statistik kunjungan, tren, halaman populer), dan (2) merekam kunjungan halaman baru (POST) serta memperbarui durasi kunjungan (PUT) untuk tracking real-time.
- `AuthController.php` — Menangani autentikasi: login (verifikasi kredensial, pembuatan session), logout (penghancuran session), dan session check (memvalidasi apakah session masih aktif). Juga menangani CSRF token generation.
- `BookingController.php` — Menangani operasi CRUD reservasi/booking (daftar, detail, tambah, ubah status, hapus).
- `ContactController.php` — Menangani operasi CRUD informasi kontak (mengambil, memperbarui alamat/telepon/email/maps).
- `EventController.php` — Menangani operasi CRUD event/acara (daftar, detail, tambah, edit, hapus).
- `FacilityController.php` — Menangani operasi CRUD fasilitas (daftar, detail, tambah, edit, hapus).
- `GalleryController.php` — Menangani operasi CRUD galeri foto (daftar, detail, tambah, edit, hapus, upload gambar).
- `HeroController.php` — Menangani operasi CRUD hero section (mengambil, memperbarui gambar/video/teks, upload media).
- `SystemController.php` — Menangani pengaturan sistem (mengambil dan mengubah maintenance mode, konfigurasi lainnya).
- `UserController.php` — Menangani operasi CRUD pengguna/admin (daftar, detail, tambah, edit, hapus, ubah password).

---

### `backend/app/Models/` — Lapisan Akses Database

Model adalah satu-satunya bagian dalam aplikasi yang berinteraksi langsung dengan database MySQL. Setiap Model mewakili satu tabel dan menyediakan metode-metode untuk operasi CRUD. **Semua query menggunakan Prepared Statements** (PDO parameter binding) untuk mencegah SQL Injection — variabel tidak pernah disisipkan langsung ke dalam string query.

Pemisahan Model dari Controller memastikan bahwa logika query database terkonsentrasi di satu tempat. Jika struktur tabel berubah, hanya Model yang perlu diubah — Controller tetap memanggil method yang sama.

- `AboutModel.php` — Operasi pada tabel `about` (mengambil data, memperbarui deskripsi dan path gambar).
- `ActivityModel.php` — Operasi pada tabel `activities` (CRUD kegiatan, termasuk upload gambar).
- `ActivityLogModel.php` — Operasi pada tabel `activity_logs` (mencatat dan mengambil log aktivitas pengguna).
- `BookingModel.php` — Operasi pada tabel `bookings` (CRUD reservasi, termasuk perubahan status).
- `ContactModel.php` — Operasi pada tabel `contacts` (mengambil dan memperbarui informasi kontak).
- `EventModel.php` — Operasi pada tabel `events` (CRUD event, termasuk upload gambar).
- `FacilityModel.php` — Operasi pada tabel `facilities` (CRUD fasilitas, termasuk upload gambar).
- `GalleryModel.php` — Operasi pada tabel `gallery` (CRUD galeri, termasuk upload dan penghapusan gambar).
- `HeroModel.php` — Operasi pada tabel `hero` (mengambil dan memperbarui hero section, termasuk upload media).
- `PageViewModel.php` — Operasi pada tabel `page_views` (mencatat kunjungan baru dengan INSERT, memperbarui durasi kunjungan dengan UPDATE berdasarkan session_id dan page_url).
- `SectionSettingsModel.php` — Operasi pada tabel `section_settings` (mengatur pengaturan per-section seperti visibilitas dan urutan).
- `SystemModel.php` — Operasi pada tabel `system_settings` (mengambil dan mengubah konfigurasi sistem seperti maintenance mode).
- `UserModel.php` — Operasi pada tabel `users` (CRUD pengguna, verifikasi password bcrypt, pembuatan user baru).

---

### `backend/config/` — Konfigurasi Koneksi Database

Folder ini berisi satu file yang sangat krusial — koneksi ke database. Seluruh operasi database bergantung pada koneksi yang dibangun di sini.

- `connection.php` — Membangun koneksi ke database MySQL menggunakan PDO. Mengimplementasikan pola Singleton (`Database::getInstance()`) sehingga hanya ada satu instance koneksi sepanjang lifecycle request — menghindari pemborosan resource. Mengatur PDO error mode ke Exception dan men-disable emulated prepares untuk keamanan Prepared Statements yang sesungguhnya. Kredensial database dibaca dari environment (host, nama database, username, password).

---

### `backend/helpers/` — Fungsi Pendukung (Utility)

Helper adalah kelas-kelas statis yang menyediakan fungsi-fungsi pendukung yang dipanggil oleh Controller dan Model. Fungsi-fungsi ini bersifat teknis dan lintas-domain — tidak terikat pada satu domain data tertentu, melainkan melayani kebutuhan umum yang dipakai di banyak tempat.

- `ActivityLogger.php` — Mencatat log aktivitas pengguna ke database secara otomatis. Dipanggil oleh Controller setiap kali operasi CRUD berhasil (misalnya "Admin X menambah event Y"). Menyimpan informasi user, aksi, target, dan timestamp.
- `Auth.php` — Helper autentikasi yang melakukan dua fungsi utama: (1) `Auth::check()` memvalidasi apakah request yang masuk memiliki session yang valid — jika tidak, mengembalikan response 401 Unauthorized, dan (2) `Auth::csrf()` menangani pembuatan dan verifikasi CSRF token untuk melindungi request bermutasi (POST, PUT, DELETE) dari serangan Cross-Site Request Forgery.
- `Response.php` — Helper yang menyederhanakan pengembalian response JSON. Menyediakan method seperti `Response::success()`, `Response::error()`, dan `Response::notFound()` yang otomatis mengatur HTTP status code dan header `Content-Type: application/json`. Memastikan setiap endpoint API mengembalikan response dalam format yang konsisten.
- `Sanitize.php` — Helper sanitasi input yang membersihkan data sebelum diproses atau disimpan. Menggunakan `htmlspecialchars()` untuk mencegah XSS, `trim()` untuk menghapus spasi berlebih, dan fungsi validasi untuk format data tertentu (email, angka, dll). Meskipun Vue Template sudah melakukan auto-escaping, sanitasi di backend tetap diperlukan sebagai lapisan pertahanan kedua.
- `Uploader.php` — Helper yang menangani seluruh proses upload file: memvalidasi tipe file (hanya gambar/video yang diizinkan), memvalidasi ukuran (batas maksimum), men-generate nama file unik dengan timestamp, memindahkan file ke folder `backend/public/uploads/`, dan mengembalikan path relatif untuk disimpan di database. Juga menangani penghapusan file lama saat data diperbarui.

---

### `backend/public/` — Web Root (Document Root)

**Ini adalah folder terpenting di backend dari perspektif server.** Folder ini adalah document root yang dikonfigurasi di Apache/Nginx — artinya hanya file di dalam folder ini yang dapat diakses langsung oleh browser. File-file PHP di luar folder ini (Controllers, Models, Helpers, Config) tidak dapat diakses langsung, sehingga kode bisnis dan kredensial database tetap aman.

- `.htaccess` — Konfigurasi Apache yang melakukan dua hal: (1) men-rewrite seluruh request (kecuali file statis seperti gambar) ke `index.php`, mengimplementasikan pola front controller, dan (2) menambahkan header CORS (`Access-Control-Allow-Origin`, `Access-Control-Allow-Headers`, dll) agar frontend di port/origin berbeda dapat mengakses API saat development.
- `index.php` — Front controller dan router. Menerima seluruh request, mem-parse URL untuk menentukan controller dan method mana yang harus dipanggil, melakukan autoloading class, menangani preflight OPTIONS request (CORS), dan mengirimkan response. Ini adalah satu-satunya file PHP yang diakses langsung oleh browser — semua request API melewati file ini.

#### `backend/public/uploads/` — Penyimpanan File Upload

Folder ini menyimpan semua file (gambar dan video) yang di-upload oleh admin melalui CMS. File-file ini disajikan langsung oleh web server sebagai file statis — tidak melalui PHP processing, sehingga akses sangat cepat. Struktur subfolder mengikuti domain data masing-masing.

- `about/` — Gambar-gambar untuk halaman About (foto gedung, suasana, dll)
- `activities/` — Gambar-gambar kegiatan/aktivitas Teras Samarinda
- `events/` — Gambar-gambar poster dan dokumentasi event
- `facilities/` — Gambar-gambar fasilitas (ruangan, peralatan, dll)
- `gallery/` — Gambar-gambar galeri foto
- `hero/` — Gambar dan video background hero section (termasuk subfolder `videos/` untuk file video .webm)

---

## Lainnya

### `public/` — Aset Statis Vite

Folder `public/` Vite berisi aset yang tidak diproses/transformasi oleh Vite — file di sini di-copy langsung ke build output (`dist/`) apa adanya. Cocok untuk file yang tidak perlu hashing atau optimasi.

- `favicon.ico` — Ikon kecil yang muncul di tab browser. Di-copy langsung ke root build output.

### `backups/` — Backup Konfigurasi Deployment

Folder untuk menyimpan backup konfigurasi deployment. Berguna ketika perlu mengembalikan konfigurasi yang sudah pernah dipakai, atau ketika berpindah mesin development.

- `backup-deploy.bat` — Script Windows untuk membuat backup bertimestamp dari file konfigurasi deployment (.env, .htaccess, dll).
- `backup-deploy.sh` — Script Linux/Mac dengan fungsi yang sama.
- `deployment/` — Folder penyimpanan backup. Isinya di-gitignore karena berisi konfigurasi sensitif, hanya `.gitkeep` yang ter-track untuk menjaga folder ada di repository.

### `dist/` — Output Build Production

Folder `dist/` adalah hasil dari perintah `npm run build`. Berisi file-file yang sudah dioptimasi dan siap di-deploy ke hosting: HTML, JavaScript (di-minify dan di-code-split per route), CSS (di-minify dan di-extract), serta aset (gambar di-hash untuk cache-busting).

Folder ini **tidak di-commit ke Git** (ada di `.gitignore`) karena dapat dibangun kembali kapan saja dari source code. Di deployment InfinityFree, isi folder `dist/` di-upload ke public_html.

---

## Arsitektur Alur Data

```
Browser (Vue 3)
    │
    ├── npm run dev ──→ Vite Dev Server (:5173)
    │                     │
    │                     └── Proxy / API request ──→
    │
    └── npm run build ──→ dist/ (HTML + JS + CSS)
                          │
                          └── Deploy ke hosting ──→
                                                      │
                                          Apache/Nginx (InfinityFree)
                                                      │
                                          backend/public/index.php
                                          (Front Controller + Router)
                                                      │
                                          backend/app/Controllers/
                                          (Validasi input, logika bisnis)
                                                      │
                                          backend/app/Models/
                                          (Query database, Prepared Statements)
                                                      │
                                          MySQL Database (terassamarinda)
```

### Alur Request API

1. Browser mengirim HTTP request (fetch/axios) ke `backend/public/` dengan endpoint tertentu
2. `.htaccess` me-rewrite URL ke `index.php` (kecuali request untuk file statis di `uploads/`)
3. `index.php` mem-parse URL, menentukan controller dan method yang sesuai, serta memvalidasi HTTP method
4. Controller memvalidasi input menggunakan `Sanitize.php`, mengecek autentikasi via `Auth::check()` untuk route terproteksi
5. Controller memanggil Model untuk operasi database, Model mengeksekusi query menggunakan Prepared Statements
6. Model mengembalikan data ke Controller
7. Controller mengembalikan response JSON via `Response.php` helper dengan HTTP status code yang tepat (200, 201, 400, 401, 403, 404, 500)
8. Browser menerima JSON, Vue mengupdate state/UI secara reaktif

### Alur Autentikasi

1. User submit form login → `AuthController::login()` dipanggil
2. Controller mencari user di database via `UserModel`, memverifikasi password menggunakan `password_verify()` (bcrypt)
3. Jika valid, PHP session dibuat dan Session ID disimpan di cookie dengan flag `HttpOnly` dan `Secure` (di production HTTPS)
4. Session ID di-regenerate setelah login berhasil untuk mencegah session fixation attack
5. Setiap request ke route terproteksi → `Auth::check()` memvalidasi session sebelum Controller mengeksekusi logika bisnis
6. Jika session tidak valid → response 401 Unauthorized, frontend mengarahkan ke LoginView
7. Logout → session dihancurkan dan Session ID di-regenerate lagi

### Alur Page Tracking

1. Pengunjung membuka halaman publik (Landing, Gallery, dll) → `usePageTracking()` aktif saat `onMounted`
2. Composable men-generate/mengambil `session_id` dari `localStorage` (berlaku 30 menit, untuk mengidentifikasi pengunjung unik)
3. POST request dikirim ke `/page-views` dengan data: `page_url`, `session_id`, `referrer` → `PageViewModel::recordView()` melakukan INSERT ke tabel `page_views`
4. Saat user meninggalkan halaman (visibility change atau beforeunload) → PUT request dikirim ke `/page-views` dengan `session_id` dan `page_url` → `PageViewModel::updateDuration()` menghitung dan mengupdate durasi kunjungan
5. Pengiriman data saat leave menggunakan `navigator.sendBeacon()` sebagai metode utama (tetap terkirim meskipun tab ditutup), dengan XHR `beforeunload` sebagai fallback
6. Dashboard dan Analytics menampilkan data tracking yang di-refresh setiap 60 detik
