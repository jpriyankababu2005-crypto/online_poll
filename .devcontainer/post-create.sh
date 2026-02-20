#!/usr/bin/env bash
set -euo pipefail

echo "==> PHP version"
php -v

echo "==> Installing build tools for PHP extensions"
sudo apt-get update
sudo apt-get install -y php-pear php-dev pkg-config libssl-dev gcc g++ make autoconf

EXT_VERSION="$(php -r 'echo phpversion("mongodb") ?: "0.0.0";')"
if php -r 'exit(version_compare(phpversion("mongodb") ?: "0.0.0", "2.2.0", ">=") ? 0 : 1);'; then
  echo "MongoDB extension version ${EXT_VERSION} is already >= 2.2.0"
else
  echo "==> Installing/Upgrading MongoDB PHP extension to >= 2.2.0"
  yes '' | sudo pecl install -f mongodb

  INI_SCAN_DIR="$(php --ini | awk -F': ' '/Scan for additional .ini files in/{print $2}')"
  if [ -z "${INI_SCAN_DIR}" ] || [ "${INI_SCAN_DIR}" = "(none)" ]; then
    echo "Could not detect PHP additional ini directory."
    exit 1
  fi

  echo "extension=mongodb.so" | sudo tee "${INI_SCAN_DIR}/30-mongodb.ini" >/dev/null
fi

echo "==> Verifying MongoDB extension"
php -m | grep -i mongodb
php -r 'echo "ext-mongodb version: " . (phpversion("mongodb") ?: "none") . PHP_EOL;'

if ! php -r 'exit(version_compare(phpversion("mongodb") ?: "0.0.0", "2.2.0", ">=") ? 0 : 1);'; then
  echo "ERROR: ext-mongodb must be >= 2.2.0 for plain 'composer require mongodb/mongodb'."
  exit 1
fi

if [ -f composer.json ]; then
  if ! composer show mongodb/mongodb >/dev/null 2>&1; then
    composer require mongodb/mongodb --no-interaction
    echo "Installed mongodb/mongodb"
  fi
fi

echo "==> Post-create setup complete"
