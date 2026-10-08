#!/bin/sh
# Download the live pages listed in live-pages.txt into a new, empty folder.
#
#   bin/preview/fetch-live.sh <empty-folder>
set -eu

OUT=${1:?Usage: bin/preview/fetch-live.sh <empty-folder>}
LIST="$(cd "$(dirname "$0")" && pwd)/live-pages.txt"
UA="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0 Safari/537.36"

mkdir -p "$OUT"
grep -v '^[[:space:]]*\(#\|$\)' "$LIST" | while read -r slug; do
	if curl -sS --fail --max-time 60 -A "$UA" -o "$OUT/$slug.html" "https://krishnamusicschool.com/$slug/"; then
		echo "fetched $slug"
	else
		rm -f "$OUT/$slug.html"
		echo "skipped $slug (download failed)" >&2
	fi
	sleep 1
done
