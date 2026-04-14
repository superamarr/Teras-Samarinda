@echo off
REM ============================================================
REM TeraSamarinda Deployment Script (Windows)
REM ============================================================
REM Cara pakai: deploy.bat
REM ============================================================

setlocal enabledelayedexpansion

echo ============================================
echo   TeraSamarinda Deployment Script
echo ============================================
echo.

REM Load konfigurasi
if not exist "deploy_config.bat" (
    echo [ERROR] deploy_config.bat not found!
    echo Please copy deploy_config.example.bat to deploy_config.bat
    exit /b 1
)

call deploy_config.bat

if "%SERVER_HOST%"=="" (
    echo [ERROR] Missing SERVER_HOST in deploy_config.bat
    exit /b 1
)

echo [Step 1] Building project...
call npm run build

if not exist "dist\index.html" (
    echo [ERROR] Build failed - dist folder not found
    exit /b 1
)

echo [Step 2] Creating deployment package...
if exist dist.zip del /f dist.zip
powershell -Command "Compress-Archive -Path 'dist' -DestinationPath 'dist.zip' -Force"

echo [Step 3] Uploading to VPS...
echo Server: %SERVER_USER%@%SERVER_HOST%
echo Path: %SERVER_PATH%

scp -P %SERVER_PORT% dist.zip %SERVER_USER%@%SERVER_HOST%:/tmp/
if errorlevel 1 (
    echo [ERROR] Upload failed!
    exit /b 1
)

echo [Step 4] Extracting on server...

REM SSH command - Linux server (unzip command)
ssh -p %SERVER_PORT% %SERVER_USER%@%SERVER_HOST% "if [ -d '%SERVER_PATH%/dist' ]; then rm -rf '%SERVER_PATH%/dist_old'; mv '%SERVER_PATH%/dist' '%SERVER_PATH%/dist_old'; fi"

ssh -p %SERVER_PORT% %SERVER_USER%@%SERVER_HOST% "unzip -o /tmp/dist.zip -d %SERVER_PATH%/"

ssh -p %SERVER_PORT% %SERVER_USER%@%SERVER_HOST% "rm -f /tmp/dist.zip"

echo.
echo ============================================
echo   Deployment Successful!
echo ============================================
echo.
echo Frontend deployed to: https://%SERVER_HOST%
echo.

endlocal
