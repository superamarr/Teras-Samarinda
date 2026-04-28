@echo off
REM ============================================================
REM Backup Deployment Configuration
REM ============================================================
REM Menyalin konfigurasi deployment ke folder backups/deployment/
REM dengan timestamp agar tersimpan riwayat perubahan.
REM ============================================================

set BACKUP_DIR=backups\deployment
set TIMESTAMP=%date:~-4,4%-%date:~-7,2%-%date:~-10,2%_%time:~0,2%-%time:~3,2%-%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo ============================================================
echo  Backup Deployment Config - %TIMESTAMP%
echo ============================================================

if exist "deploy_config.bat" (
    copy "deploy_config.bat" "%BACKUP_DIR%\deploy_config_%TIMESTAMP%.bat" >nul
    echo  [OK] deploy_config.bat -^> %BACKUP_DIR%\deploy_config_%TIMESTAMP%.bat
) else (
    echo  [SKIP] deploy_config.bat tidak ditemukan
)

if exist "deploy_config.sh" (
    copy "deploy_config.sh" "%BACKUP_DIR%\deploy_config_%TIMESTAMP%.sh" >nul
    echo  [OK] deploy_config.sh -^> %BACKUP_DIR%\deploy_config_%TIMESTAMP%.sh
) else (
    echo  [SKIP] deploy_config.sh tidak ditemukan
)

if exist ".env" (
    copy ".env" "%BACKUP_DIR%\.env_%TIMESTAMP%" >nul
    echo  [OK] .env -^> %BACKUP_DIR%\.env_%TIMESTAMP%
) else (
    echo  [SKIP] .env tidak ditemukan
)

if exist "docker-compose.yml" (
    copy "docker-compose.yml" "%BACKUP_DIR%\docker-compose_%TIMESTAMP%.yml" >nul
    echo  [OK] docker-compose.yml -^> %BACKUP_DIR%\docker-compose_%TIMESTAMP%.yml
) else (
    echo  [SKIP] docker-compose.yml tidak ditemukan
)

echo.
echo  Backup selesai! File tersimpan di: %BACKUP_DIR%\
echo ============================================================
pause
