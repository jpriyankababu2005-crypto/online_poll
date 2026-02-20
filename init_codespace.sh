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

echo "==> Switching default php to /usr/bin/php8.3"
if [ -x /usr/bin/php8.3 ]; then
  sudo update-alternatives --set php /usr/bin/php8.3 || true
fi
export PATH="/usr/bin:$PATH"
hash -r

echo "==> PHP and MongoDB extension check"
php -v
php -m | grep -i mongodb

echo "==> Preparing Composer project"
if [ ! -f composer.json ]; then
  composer init -n
fi

EXT_VERSION="$(php -r 'echo phpversion("mongodb") ?: "";')"
if php -r 'exit(version_compare(phpversion("mongodb") ?: "0.0.0", "2.2.0", ">=") ? 0 : 1);'; then
  echo "==> Installing mongodb/mongodb:^2.2 (ext-mongodb ${EXT_VERSION})"
  composer require mongodb/mongodb:^2.2
else
  echo "==> Installing mongodb/mongodb:^1.21 (ext-mongodb ${EXT_VERSION})"
  composer require mongodb/mongodb:^1.21
fi

if [ ! -f .env.codespace ]; then
  cat > .env.codespace <<'EOF'
export MONGODB_URI='mongodb+srv://<username>:<password>@<cluster>.mongodb.net/?retryWrites=true&w=majority'
export MONGODB_DB='poll_system'
EOF
  echo "==> Created .env.codespace template"
fi

if ! grep -q 'export PATH="/usr/bin:$PATH"' "$HOME/.bashrc"; then
  cat >> "$HOME/.bashrc" <<'EOF'

# online_poll setup
export PATH="/usr/bin:$PATH"
EOF
fi

echo
echo "Setup complete."
echo "Next:"
echo "1) Edit .env.codespace with your MongoDB Atlas URI."
echo "2) Run: source .env.codespace"
echo "3) Run: php -S 0.0.0.0:8000 -t ."
