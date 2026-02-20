#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

echo "==> Installing PHP 8.3 + required extensions"
sudo apt-get update
sudo apt-get install -y \
  ca-certificates \
  unzip \
  composer \
  php8.3-cli \
  php8.3-mongodb \
  php8.3-curl \
  php8.3-mbstring \
  php8.3-xml

if [ ! -x /usr/bin/php8.3 ]; then
  echo "ERROR: /usr/bin/php8.3 not found after install."
  exit 1
fi

COMPOSER_BIN="$(command -v composer || true)"
if [ -z "$COMPOSER_BIN" ]; then
  echo "ERROR: composer not found in PATH."
  exit 1
fi

echo "==> Switching default php to /usr/bin/php8.3"
if [ -x /usr/bin/php8.3 ]; then
  sudo update-alternatives --set php /usr/bin/php8.3 || true
fi
export PATH="/usr/bin:/bin:$PATH"
unalias php 2>/dev/null || true
hash -r

echo "==> Checking php mapping"
readlink -f /usr/bin/php || true
update-alternatives --display php || true

echo "==> Forcing php 8.3"
sudo update-alternatives --set php /usr/bin/php8.3
hash -r

echo "==> Verifying php and mongodb extension"
readlink -f /usr/bin/php
php -v
if ! php -m | grep -iq mongodb; then
  echo "ERROR: mongodb PHP extension is not loaded."
  exit 1
fi

echo "==> Preparing Composer project"
if [ ! -f composer.json ]; then
  /usr/bin/php8.3 "$COMPOSER_BIN" init -n
fi

echo "==> Installing MongoDB PHP library compatible with current extension"
/usr/bin/php8.3 "$COMPOSER_BIN" require mongodb/mongodb:^1.21

if [ ! -f .env.codespace ]; then
  cat > .env.codespace <<'EOF'
export MONGODB_URI='mongodb+srv://<username>:<password>@<cluster>.mongodb.net/?retryWrites=true&w=majority'
export MONGODB_DB='poll_system'
EOF
  echo "==> Created .env.codespace template"
fi

if ! grep -q 'export PATH="/usr/bin:/bin:$PATH"' "$HOME/.bashrc"; then
  cat >> "$HOME/.bashrc" <<'EOF'

# online_poll setup
export PATH="/usr/bin:/bin:$PATH"
alias php=/usr/bin/php8.3
EOF
fi

echo
echo "Setup complete."
echo "Next:"
echo "1) Edit .env.codespace with your MongoDB Atlas URI."
echo "2) Run: source .env.codespace"
echo "3) Run: php -S 0.0.0.0:8000 -t ."
