# Deploy Docker ke VPS

Stack: `nginx` + `php-fpm` + `mysql`.

## 1) Persiapan server

```bash
sudo apt update
sudo apt install -y docker.io docker-compose-plugin
sudo usermod -aG docker $USER
```

Logout/login lagi.

## 2) Siapkan env

```bash
cd /path/ke/Teras-Samarinda
cp .env.docker.example .env
nano .env
```

Wajib cek:
- `DB_PASSWORD`
- `CORS_ALLOWED_ORIGINS`
- `SESSION_DOMAIN=taufikramadhani.web.id`

## 3) Jalankan container

```bash
docker compose build
docker compose up -d
docker compose ps
```

## 4) Import database awal

```bash
docker compose exec -T db mysql -uroot -p"$DB_PASSWORD" "$DB_NAME" < database.sql
```

## 5) DNS domain

Di panel domain `taufikramadhani.web.id`:
- A record `@` -> IP VPS
- A record `www` -> IP VPS

## 6) SSL (disarankan via reverse proxy host)

Gunakan Nginx host + Certbot, proxy ke `127.0.0.1:80` (container nginx).
