#!/bin/bash
# ============================================================
# Backup Deployment Configuration
# ============================================================
# Menyalin konfigurasi deployment ke folder backups/deployment/
# dengan timestamp agar tersimpan riwayat perubahan.
# ============================================================

BACKUP_DIR="backups/deployment"
TIMESTAMP=$(date +%Y-%m-%d_%H-%M-%S)

mkdir -p "$BACKUP_DIR"

echo "============================================================"
echo " Backup Deployment Config - $TIMESTAMP"
echo "============================================================"

if [ -f "deploy_config.sh" ]; then
    cp "deploy_config.sh" "$BACKUP_DIR/deploy_config_$TIMESTAMP.sh"
    echo " [OK] deploy_config.sh -> $BACKUP_DIR/deploy_config_$TIMESTAMP.sh"
else
    echo " [SKIP] deploy_config.sh tidak ditemukan"
fi

if [ -f "deploy_config.bat" ]; then
    cp "deploy_config.bat" "$BACKUP_DIR/deploy_config_$TIMESTAMP.bat"
    echo " [OK] deploy_config.bat -> $BACKUP_DIR/deploy_config_$TIMESTAMP.bat"
else
    echo " [SKIP] deploy_config.bat tidak ditemukan"
fi

if [ -f ".env" ]; then
    cp ".env" "$BACKUP_DIR/.env_$TIMESTAMP"
    echo " [OK] .env -> $BACKUP_DIR/.env_$TIMESTAMP"
else
    echo " [SKIP] .env tidak ditemukan"
fi

if [ -f "docker-compose.yml" ]; then
    cp "docker-compose.yml" "$BACKUP_DIR/docker-compose_$TIMESTAMP.yml"
    echo " [OK] docker-compose.yml -> $BACKUP_DIR/docker-compose_$TIMESTAMP.yml"
else
    echo " [SKIP] docker-compose.yml tidak ditemukan"
fi

echo ""
echo " Backup selesai! File tersimpan di: $BACKUP_DIR/"
echo "============================================================"
