#!/bin/sh
# Build upload-ready zips for WordPress (Appearance → Themes → Upload, Plugins → Upload).
set -eu
ROOT=$(cd "$(dirname "$0")/.." && pwd)
mkdir -p "$ROOT/dist"
rm -f "$ROOT/dist/kms-theme.zip" "$ROOT/dist/kms-core.zip"

# Lint first: a PHP syntax error in a theme can take the whole site down.
find "$ROOT/wp-content" -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null

cd "$ROOT/wp-content/themes" && zip -qr "$ROOT/dist/kms-theme.zip" kms-theme -x '*.DS_Store'
cd "$ROOT/wp-content/plugins" && zip -qr "$ROOT/dist/kms-core.zip" kms-core -x '*.DS_Store'
ls -la "$ROOT/dist"
