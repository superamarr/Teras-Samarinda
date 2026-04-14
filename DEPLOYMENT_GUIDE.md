# ============================================================

# Deployment Guide - TeraSamarinda

# ============================================================

## Prasyarat

Pastikan komputer Anda sudah install:

- Node.js & npm
- zip (untuk Windows, sudah ada di OS)
- SSH client (untuk Windows, sudah ada via Git Bash atau Laragon)

## Cara Pakai

### 1. Setup Konfigurasi

Edit file `deploy_config.sh` dengan data server Anda:

```bash
SERVER_HOST="taufikramadhani.web.id"
SERVER_USER="root"
SERVER_PORT="22"
SERVER_PATH="/var/www/html"
```

### 2. Jalankan Deployment

#### Linux/Mac/Git Bash:

```bash
chmod +x deploy.sh
./deploy.sh
```

#### Windows (Command Prompt):

```cmd
deploy.bat
```

## Struktur Folder Setelah Deploy

```
/var/www/html/
├── index.html          <- dari dist/index.html
├── assets/             <- dari dist/assets/
│   ├── index-xxx.js
│   └── index-xxx.css
└── ...file lain
```

## Jika Menggunakan Backend Juga

Edit `deploy_config.sh` dan tambahkan:

```bash
BACKEND_PATH="/var/www/html/backend"
```

Lalu script akan otomatis upload folder `backend/` juga.

## Troubleshooting

### SSH Connection Failed

- Cek SERVER_HOST, SERVER_USER, SERVER_PORT
- Pastikan SSH credentials benar
- Coba test koneksi: `ssh -P 22 user@host`

### Upload Failed

- Pastikan folder di server ada: `ssh user@host "ls -la /var/www/html"`
- Cek permission menulis ke folder

### Build Failed

- Pastikan Node.js terinstall: `node --version`
- Install ulang dependencies: `npm install`

## Catatan

- Script ini hanya mengupload folder `dist/` (frontend yang sudah di-build)
- Folder `backend/` harus di-upload manual atau configure terpisah
- File-file statis (gambar, video) ada di `backend/public/uploads/`
