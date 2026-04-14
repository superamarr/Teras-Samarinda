#!/usr/bin/env bash
# Deploy docker (Linux/macOS/Git Bash): SSH -> git pull -> docker compose up --build
#
#   chmod +x scripts/deploy-to-vps.sh
#   cp scripts/deploy.env.example scripts/deploy.env   # isi variabel
#   ./scripts/deploy-to-vps.sh

set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${SCRIPT_DIR}/deploy.env"

if [[ -f "$ENV_FILE" ]]; then
  set -a
  # shellcheck source=/dev/null
  source "$ENV_FILE"
  set +a
fi

: "${DEPLOY_HOST:?Set DEPLOY_HOST in scripts/deploy.env}"
: "${DEPLOY_USER:?Set DEPLOY_USER in scripts/deploy.env}"
: "${DEPLOY_REMOTE_PATH:?Set DEPLOY_REMOTE_PATH in scripts/deploy.env}"

DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
SSH_KEY="${DEPLOY_KEY:-}"

SSH_OPTS=(-o StrictHostKeyChecking=accept-new)
if [[ -n "$SSH_KEY" ]]; then
  SSH_OPTS+=(-i "$SSH_KEY")
fi

REMOTE="${DEPLOY_USER}@${DEPLOY_HOST}"
echo ">>> SSH: $REMOTE"
echo ">>> cd $DEPLOY_REMOTE_PATH && git pull && docker compose up -d --build"

ssh "${SSH_OPTS[@]}" "$REMOTE" "set -e && cd \"$DEPLOY_REMOTE_PATH\" && git fetch origin && git checkout \"$DEPLOY_BRANCH\" && git pull origin \"$DEPLOY_BRANCH\" && docker compose up -d --build"

echo "Selesai. Hard refresh browser (Ctrl+F5) jika perlu."
