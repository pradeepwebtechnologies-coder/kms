#!/bin/sh
# Static preview of the new site for Netlify (for review only).
#
#   1. KMS_WP_DIR=.wp-preview KMS_WP_PORT=8091 tests/local-wp.sh   first run installs; leave it running
#   2. bin/preview/build.sh                                        in a second terminal
#
# Result: dist/preview/. Drag that folder onto https://app.netlify.com/drop
set -eu

ROOT=$(cd "$(dirname "$0")/../.." && pwd)
DIR=${KMS_WP_DIR:-"$ROOT/.wp-preview"}
PORT=${KMS_WP_PORT:-8091}
WP="php $DIR/wp-cli.phar --allow-root --path=$DIR/wordpress"

if ! curl -s --noproxy '*' -o /dev/null "http://127.0.0.1:$PORT/"; then
	echo "Start the preview site first: KMS_WP_DIR=$DIR KMS_WP_PORT=$PORT tests/local-wp.sh" >&2
	exit 1
fi

# Real retreat, performance, legal and blog pages from the live site, instead of the test placeholders.
LIVE=$(mktemp -d)
trap 'rm -rf "$LIVE"' EXIT
"$ROOT/bin/preview/fetch-live.sh" "$LIVE"
$WP eval-file "$ROOT/bin/preview/import-live.php" "$LIVE"

python3 "$ROOT/bin/preview/export-static.py" --base "http://127.0.0.1:$PORT" --out "$ROOT/dist/preview"
