<#
  Deploy dari PC Windows ke VPS: SSH → git pull → docker compose up --build

  Persiapan:
    1. cp scripts/deploy.env.example scripts/deploy.env  (isi DEPLOY_*)
    2. Pastikan repo di VPS sudah git clone & remote sama dengan yang Anda push dari lokal
    3. Jalankan dari root project:  .\scripts\deploy-to-vps.ps1

  Tanpa deploy.env:  .\scripts\deploy-to-vps.ps1 -DeployHost "IP" -DeployUser "user" -RemotePath "/home/user/app"
#>

param(
  [string]$DeployHost = "",
  [string]$DeployUser = "",
  [string]$RemotePath = "",
  [string]$DeployKey = "",
  [string]$Branch = "",
  [string]$ConfigFile = ""
)

$ErrorActionPreference = "Stop"
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$envPath = if ($ConfigFile) { $ConfigFile } else { Join-Path $scriptDir "deploy.env" }

if (Test-Path $envPath) {
  Get-Content $envPath -Encoding UTF8 | ForEach-Object {
    $line = $_.Trim()
    if ($line -eq "" -or $line.StartsWith("#")) { return }
    $eq = $line.IndexOf("=")
    if ($eq -lt 1) { return }
    $k = $line.Substring(0, $eq).Trim()
    $v = $line.Substring($eq + 1).Trim()
    [System.Environment]::SetEnvironmentVariable($k, $v, "Process")
  }
}

if (-not $DeployHost) { $DeployHost = [Environment]::GetEnvironmentVariable("DEPLOY_HOST", "Process") }
if (-not $DeployUser) { $DeployUser = [Environment]::GetEnvironmentVariable("DEPLOY_USER", "Process") }
if (-not $RemotePath) { $RemotePath = [Environment]::GetEnvironmentVariable("DEPLOY_REMOTE_PATH", "Process") }
if (-not $DeployKey) { $DeployKey = [Environment]::GetEnvironmentVariable("DEPLOY_KEY", "Process") }
if (-not $Branch) {
  $Branch = [Environment]::GetEnvironmentVariable("DEPLOY_BRANCH", "Process")
}
if (-not $Branch) { $Branch = "main" }

if (-not $DeployHost -or -not $DeployUser -or -not $RemotePath) {
  Write-Host "Kurang DEPLOY_HOST / DEPLOY_USER / DEPLOY_REMOTE_PATH." -ForegroundColor Red
  Write-Host "Isi scripts/deploy.env (dari deploy.env.example) atau beri parameter -DeployHost -DeployUser -RemotePath"
  exit 1
}

$sshTarget = "${DeployUser}@${DeployHost}"
$remoteCmd = "set -e && cd `"$RemotePath`" && git fetch origin && git checkout $Branch && git pull origin $Branch && docker compose up -d --build"

Write-Host ">>> SSH: $sshTarget" -ForegroundColor Cyan
Write-Host ">>> $remoteCmd" -ForegroundColor DarkGray

$sshArgs = @()
if ($DeployKey) {
  $sshArgs += "-i", $DeployKey
}
$sshArgs += $sshTarget, $remoteCmd

& ssh @sshArgs
if ($LASTEXITCODE -ne 0) {
  Write-Host "Deploy gagal (exit $LASTEXITCODE)." -ForegroundColor Red
  exit $LASTEXITCODE
}
Write-Host "Selesai. Buka situs di browser (hard refresh: Ctrl+F5)." -ForegroundColor Green
