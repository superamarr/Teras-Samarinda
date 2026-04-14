# Panduan deploy TeraSamarinda ke VPS (dari nol)

Dokumen ini menjelaskan **menyambung ke VPS lewat SSH**, **mengunggah kode ke server**, lalu **menjalankan aplikasi dengan Docker**. Stack di server: **Nginx** (frontend Vue) + **PHP 8.2 FPM** + **MySQL 8**.

---

## Bagian 1 — Apa yang Anda butuhkan

| Hal | Keterangan |
|-----|------------|
| **VPS** | Ubuntu/Debian umum dipakai; akses root atau user dengan `sudo`. |
| **IP publik VPS** | Contoh: `203.0.113.50` (ganti dengan IP Anda). |
| **User SSH** | Biasanya `root` atau user yang dibuat provider (contoh: `ubuntu`). |
| **Autentikasi** | Password **atau** kunci SSH (disarankan untuk keamanan). |
| **Komputer lokal** | Windows, macOS, atau Linux. |

---

## Bagian 2 — Menyambung ke VPS dengan SSH

SSH adalah cara standar masuk ke server lewat terminal: perintah dijalankan **di komputer Anda**, yang dieksekusi **di VPS**.

### 2.1 Windows (PowerShell atau Terminal)

Windows 10/11 biasanya sudah punya klien SSH bawaan.

1. Buka **PowerShell** atau **Terminal** (Win + X → Windows Terminal).
2. Sambung dengan pola:

   ```bash
   ssh USER@IP_VPS
   ```

   Contoh user `root`:

   ```bash
   ssh root@203.0.113.50
   ```

   Contoh user `ubuntu`:

   ```bash
   ssh ubuntu@203.0.113.50
   ```

3. Pertama kali, Anda akan ditanya *“Are you sure you want to continue connecting?”* — ketik `yes` lalu Enter.
4. Masukkan **password** user (karakter tidak tampil saat mengetik — itu normal), lalu Enter.

Jika berhasil, prompt berubah seperti `root@vps-name:~#` atau `ubuntu@vps-name:~$` — artinya Anda sudah **di dalam server**.

**Keluar dari SSH** tanpa mematikan server:

```bash
exit
```

### 2.2 Menggunakan kunci SSH (disarankan)

Lebih aman daripada password saja.

**Di komputer Anda (PowerShell / bash):**

```bash
ssh-keygen -t ed25519 -C "email-anda@contoh.com"
```

Tekan Enter untuk lokasi default; passphrase opsional.

**Salin kunci publik ke VPS** (ganti user & IP):

```bash
type $env:USERPROFILE\.ssh\id_ed25519.pub
```

Salin teks yang muncul. Di VPS (setelah login password), tambahkan ke `~/.ssh/authorized_keys`:

```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
```

Tempel baris kunci publik, simpan (Ctrl+O, Enter, Ctrl+X). Lalu:

```bash
chmod 600 ~/.ssh/authorized_keys
```

Selanjutnya `ssh user@IP` bisa tanpa password (jika tidak pakai passphrase, atau pakai ssh-agent).

### 2.3 macOS & Linux

Sama seperti di atas:

```bash
ssh user@IP_VPS
```

Generate kunci:

```bash
ssh-keygen -t ed25519 -C "email@contoh.com"
ssh-copy-id user@IP_VPS
```

(`ssh-copy-id` menyalin kunci publik ke server secara otomatis.)

### 2.4 Port SSH bukan 22

Jika provider memakai port lain (misal `2222`):

```bash
ssh -p 2222 user@IP_VPS
```

### 2.5 Masalah umum SSH

| Gejala | Yang bisa dicoba |
|--------|-------------------|
| `Connection timed out` | Firewall VPS / cloud (buka port 22), IP salah, VPS mati. |
| `Permission denied` | User/password salah, atau SSH key tidak cocok. |
| `Host key verification failed` | Hapus baris host lama: `ssh-keygen -R IP_VPS` lalu sambung lagi. |

---

## Bagian 3 — Mengunggah project ke VPS

Anda perlu **isi folder project** (kode TeraSamarinda) ada di VPS, misalnya di `/var/www/teras-samarinda` atau `~/teras-samarinda`.

### Opsi A — Git clone (paling rapi, disarankan)

**Syarat:** kode sudah di **GitHub / GitLab / Gitea** (repo publik atau private).

Di VPS (setelah SSH):

```bash
sudo apt update
sudo apt install -y git
cd ~
git clone https://github.com/USERNAME/REPO-Teras-Samarinda.git teras-samarinda
cd teras-samarinda
```

Untuk repo **private**, gunakan HTTPS + token, atau SSH deploy key. Contoh HTTPS:

```bash
git clone https://USERNAME:TOKEN@github.com/USERNAME/REPO.git teras-samarinda
```

### Opsi B — SCP (salin folder dari komputer ke VPS)

Jalankan **di komputer Anda** (bukan di dalam SSH), dari folder **induk** project (bukan harus di dalam folder, tapi path-nya benar).

**PowerShell (Windows)** — salin seluruh folder project:

```powershell
scp -r C:\laragon\www\pa\Teras-Samarinda user@IP_VPS:~/teras-samarinda
```

**macOS / Linux:**

```bash
scp -r /path/ke/Teras-Samarinda user@IP_VPS:~/teras-samarinda
```

`-r` artinya rekursif (folder beserta isinya). Pertama kali akan minta password SSH.

**Catatan:** folder `node_modules` besar; lebih baik **jangan** ikut (pakai `.dockerignore` / hapus dulu), atau clone lewat Git supaya bersih.

### Opsi C — SFTP dengan WinSCP / FileZilla (GUI)

1. Install **WinSCP** (Windows) atau **FileZilla** (semua OS).
2. Buat koneksi baru: **Host** = IP VPS, **Port** = 22 (atau port SSH Anda), **User** + **Password** atau kunci.
3. Sambung, lalu **seret-drop** folder project ke folder di server (misalnya `/home/ubuntu/teras-samarinda`).

### Opsi D — Arsip zip, lalu unzip di VPS

**Di komputer:** zip folder project (tanpa `node_modules` jika bisa).

**Unggah:**

```bash
scp Teras-Samarinda.zip user@IP_VPS:~/
```

**Di VPS:**

```bash
sudo apt install -y unzip
unzip Teras-Samarinda.zip -d ~/teras-samarinda
```

---

## Bagian 4 — Siapkan Docker di VPS

Sambung SSH ke VPS, lalu:

### Ubuntu/Debian

```bash
sudo apt update
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

Cek:

```bash
docker --version
docker compose version
```

Distribusi selain Ubuntu (misalnya Debian, CentOS): ikuti panduan resmi [Install Docker Engine](https://docs.docker.com/engine/install/) untuk OS Anda; yang penting terpasang **Docker Engine** dan plugin **docker compose**.

Agar user non-root bisa menjalankan Docker (opsional):

```bash
sudo usermod -aG docker $USER
```

Lalu **logout SSH dan login lagi**, atau `newgrp docker`.

---

## Bagian 5 — Jalankan TeraSamarinda dengan Docker

Asumsi folder project ada di `~/teras-samarinda` (sesuaikan path Anda).

```bash
cd ~/teras-samarinda
cp .env.example .env
nano .env
```

Isi minimal:

- `DB_PASSWORD` — password kuat untuk MySQL.
- `DB_HOST`, `DB_NAME`, `DB_USER` — biasanya `localhost` / `terassamarinda` / `root` di lokal; di VPS samakan `DB_NAME` & password dengan impor SQL.
- `HTTP_PORT=80` — atau port lain jika 80 sudah dipakai.

**Hubungan `.env` dengan `backend/config/connection.php`:** PHP tidak membaca `.env` secara ajaib; file `connection.php` memuat `.env` di **root project** (lalu cadangan `backend/.env`) dan mengisi `getenv()`. Nilai yang sudah diset lingkungan (misalnya oleh Docker) **tidak ditimpa**. Di **Docker Compose**, service `php` memakai `env_file: .env` untuk `DB_NAME` / `DB_USER` / `DB_PASSWORD`, sementara **`DB_HOST` dipaksa jadi `db`** (nama service MySQL di jaringan Docker), sehingga isi `DB_HOST=localhost` di `.env` Anda tetap aman untuk Laragon dan tidak mengganggu kontainer.

Simpan file (di nano: Ctrl+O, Enter, Ctrl+X).

**Build dan jalankan:**

```bash
docker compose build
docker compose up -d
```

**Impor database sekali:**

```bash
docker compose exec -T db mysql -uroot -p"PASSWORD_DARI_ENV" < database.sql
```

Ganti `PASSWORD_DARI_ENV` dengan nilai `DB_PASSWORD` di `.env` Anda.

**Cek kontainer:**

```bash
docker compose ps
docker compose logs -f --tail=50
```

Buka browser: `http://IP_VPS` (atau `http://domain-anda` jika DNS sudah mengarah ke IP ini).

---

## Bagian 6 — Firewall (penting di VPS)

Jika pakai **UFW** (Ubuntu):

```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status
```

Di **panel cloud** (AWS, DigitalOcean, dll.), pastikan **Security Group / Firewall** mengizinkan port yang sama.

---

## Bagian 7 — HTTPS (ringkas)

Letakkan reverse proxy (Nginx/Caddy) di **host** dengan Certbot, atau gunakan Traefik/Caddy di depan kontainer. Proxy ke `127.0.0.1:80` jika `HTTP_PORT=80` dipetakan ke host.

Header yang berguna di proxy:

- `X-Forwarded-Proto`
- `X-Forwarded-For`
- `Host`

---

## Bagian 8 — Variabel build frontend

| Variabel | Keterangan |
|----------|------------|
| `VITE_API_BASE_URL` | Untuk Docker bawaan: **kosongkan** agar API memakai path relatif (`/hero`, `/auth`, …) di domain yang sama. |

Ubah build:

```bash
docker compose build --build-arg VITE_API_BASE_URL= nginx
docker compose up -d
```

---

## Bagian 9 — Perintah berguna

```bash
docker compose logs -f php nginx db
docker compose exec php sh
docker compose down
docker compose up -d --build
```

---

## Bagian 10 — Pemecahan masalah

| Masalah | Langkah cek |
|---------|-------------|
| Tidak bisa SSH | Ping IP, cek firewall, port SSH, kredensial. |
| SCP gagal | Path lokal benar, user@IP benar, ruang disk VPS cukup. |
| `docker: command not found` | Install Docker + plugin compose (Bagian 4). |
| 502 / API error | `docker compose ps`, log `nginx` & `php`, pastikan route ke `php:9000`. |
| DB error | Password `.env` = MySQL, tunggu MySQL healthy, impor `database.sql`. |
| Upload gagal | Volume `uploads_data`, batas di `docker/php/uploads.ini`. |

---

## Bagian 11 — Deploy cepat: kode lokal → VPS (tanpa upload manual tiap file)

**Konsep:** di lokal Anda **commit & push** ke Git (GitHub/GitLab). Di VPS project sudah **`git clone`** sekali. Setelah itu, setiap kali mau “refresh” server dengan kode terbaru, cukup **pull di VPS + rebuild Docker** — bisa otomatis lewat skrip dari PC Anda.

Ini **bukan** live-reload seperti `npm run dev` (yang langsung menyamakan setiap ketikan). Alurnya: **push → pull di server → build ulang image**.

### Satu kali di VPS

1. Clone repo ke path tetap, misalnya `/home/taufik/teras-samarinda`.
2. Isi `.env` di server, jalankan `docker compose up -d`, impor DB (seperti bagian sebelumnya).
3. Pastikan remote Git sama dengan yang Anda pakai di lokal (`git remote -v`).

### Setiap kali ada perubahan dari lokal

1. Di komputer lokal:

   ```bash
   git add .
   git commit -m "pesan commit"
   git push origin main
   ```

   (Ganti `main` dengan nama branch Anda.)

2. **Opsi A — Skrip deploy dari PC (disarankan)**  
   - Salin `scripts/deploy.env.example` → `scripts/deploy.env`, isi `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_REMOTE_PATH`, opsional `DEPLOY_KEY` & `DEPLOY_BRANCH`.  
   - `scripts/deploy.env` **sudah di-ignore Git** agar IP/path/kunci tidak ter-commit.  
   - **Windows (PowerShell), dari root project:**

     ```powershell
     .\scripts\deploy-to-vps.ps1
     ```

   - **Linux / macOS / Git Bash:**

     ```bash
     chmod +x scripts/deploy-to-vps.sh
     ./scripts/deploy-to-vps.sh
     ```

   Skrip akan SSH ke VPS, menjalankan `git pull` lalu `docker compose up -d --build`.

3. **Opsi B — Manual**  
   SSH ke VPS, lalu:

   ```bash
   cd /home/taufik/teras-samarinda
   git pull origin main
   docker compose up -d --build
   ```

### Catatan

- **Branch:** `DEPLOY_BRANCH` di `deploy.env` harus sama dengan branch yang Anda push (mis. `main`).
- **Kunci SSH:** `-i` / `DEPLOY_KEY` mengarah ke path **private key** `.pem` atau `id_ed25519` di PC Anda.
- **Tanpa Git di VPS:** Anda bisa pakai `rsync`/SCP dari PC (lebih ribet); Git + pull tetap yang paling mudah untuk sinkron.
- **CI/CD lanjutan:** bisa ditambah **GitHub Actions** agar setiap `git push` otomatis SSH ke VPS dan menjalankan perintah yang sama — pola sama dengan skrip, hanya dipicu oleh GitHub.

---

## Ringkasan alur

1. `ssh user@IP_VPS` → masuk server.  
2. Pasang Git / terima file lewat SCP / WinSCP / zip.  
3. Pasang Docker + Docker Compose.  
4. `cd` ke folder project → `.env` → `docker compose build && docker compose up -d`.  
5. Impor `database.sql`.  
6. Buka `http://IP` dan uji; setelah itu pertimbangkan HTTPS dan cadangan database.

Jika Anda baru pertama kali deploy, lakukan langkah **SSH → clone/upload → Docker → impor DB** secara berurutan; jangan lupa **ganti password default** di `.env` sebelum dipakai produksi.

**Untuk update berikutnya:** push dari lokal → jalankan `scripts/deploy-to-vps.ps1` atau `deploy-to-vps.sh` (Bagian 11).
