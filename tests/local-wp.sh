#!/bin/sh
# Local WordPress for testing the theme and plugin: SQLite (no MySQL), WP-CLI, PHP built-in server.
#
#   tests/local-wp.sh            install (first run) and serve on http://127.0.0.1:8080
#   KMS_WP_DIR=/tmp/kms-wp tests/local-wp.sh
#
# Media for the sample content: put the hero photo, founder photo and logo in tests/fixtures/media/
# (IMG_20230826_14451146.jpg, vinod-dewra.webp, Black-Logo.webp) — download them from the live site.
set -eu

ROOT=$(cd "$(dirname "$0")/.." && pwd)
DIR=${KMS_WP_DIR:-"$ROOT/.wp-local"}
PORT=${KMS_WP_PORT:-8080}
URL="http://127.0.0.1:$PORT"
WP="php $DIR/wp-cli.phar --allow-root --path=$DIR/wordpress"

if [ ! -f "$DIR/wordpress/wp-config.php" ]; then
	mkdir -p "$DIR" && cd "$DIR"
	curl -sSL -o wordpress.tar.gz https://wordpress.org/latest.tar.gz && tar -xzf wordpress.tar.gz && rm wordpress.tar.gz
	curl -sSL -o sqlite.zip https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip
	unzip -q sqlite.zip -d wordpress/wp-content/plugins/ && rm sqlite.zip
	curl -sSL -o wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

	cp wordpress/wp-content/plugins/sqlite-database-integration/db.copy wordpress/wp-content/db.php
	sed -i "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$DIR/wordpress/wp-content/plugins/sqlite-database-integration#; s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" wordpress/wp-content/db.php

	$WP config create --dbname=wp --dbuser=x --dbpass=x --dbhost=localhost --skip-check --force --extra-php <<'PHP'
define( 'DB_DIR', __DIR__ . '/wp-content/database/' );
define( 'DB_FILE', 'kms.sqlite' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISABLE_WP_CRON', true );
PHP
	$WP core install --url="$URL" --title="Krishna Music School" --admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
	$WP option update siteurl "$URL" && $WP option update home "$URL"
	$WP rewrite structure '/%postname%/'

	ln -sfn "$ROOT/wp-content/themes/kms-theme" wordpress/wp-content/themes/kms-theme
	ln -sfn "$ROOT/wp-content/plugins/kms-core" wordpress/wp-content/plugins/kms-core
	$WP plugin activate kms-core
	$WP theme activate kms-theme

	# Sample content mirroring the live site's structure.
	MEDIA="$ROOT/tests/fixtures/media"
	HERO_URL=""
	if [ -f "$MEDIA/IMG_20230826_14451146.jpg" ]; then
		HERO=$($WP media import "$MEDIA/IMG_20230826_14451146.jpg" --title="Vini Devda on a rooftop in Pushkar" --porcelain)
		HERO_URL=$($WP post get "$HERO" --field=guid)
		FACTS="{\"default_image\":\"$HERO_URL\""
		[ -f "$MEDIA/vinod-dewra.webp" ] && FOUNDER=$($WP media import "$MEDIA/vinod-dewra.webp" --porcelain) && FACTS="$FACTS,\"founder_image\":\"$($WP post get "$FOUNDER" --field=guid)\""
		[ -f "$MEDIA/Black-Logo.webp" ] && LOGO=$($WP media import "$MEDIA/Black-Logo.webp" --porcelain) && FACTS="$FACTS,\"logo\":\"$($WP post get "$LOGO" --field=guid)\"" && $WP theme mod set custom_logo "$LOGO"
		$WP option update kms_facts "$FACTS}" --format=json
	fi

	HOME_ID=$($WP post create --post_type=page --post_status=publish --post_title="Krishna Music School Pushkar" --porcelain)
	$WP option update show_on_front page && $WP option update page_on_front "$HOME_ID"
	for spec in "About Us|about-us" "Contact Us|contact-us" "Hear From Our Attendees|hear-from-our-attendees" "Terms and conditions|terms-and-conditions" "Summer Music Retreat Upper Bhagsu|summer-music-retreat-upper-bhagsu" "Chokhi Vini Project – Rajasthani Folk Music Band|folk-music-band-chokhi-vini-project-pushkar" "Multi-Instrument Training|multi-instrument-training"; do
		$WP post create --post_type=page --post_status=publish --post_title="${spec%%|*}" --post_name="${spec##*|}" --post_content="<p>Old content.</p>" --quiet
	done
	$WP post update "$($WP option get wp_page_for_privacy_policy)" --post_status=publish --post_name=privacy-policy --quiet
	sed "s#__HERO_URL__#$HERO_URL#" "$ROOT/tests/fixtures/divi-legacy-page.txt" > "$DIR/divi.txt"
	$WP post create "$DIR/divi.txt" --post_type=page --post_status=publish --post_title="8-Day Winter Music Retreat in Pushkar" --post_name=8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium --quiet
	CAT=$($WP term create category "Kirtan & Bhajan" --slug=kirtan-bhajan --porcelain)
	$WP post create "$ROOT/tests/fixtures/post-with-fake-rating.txt" --post_type=post --post_status=publish --post_title="How to Sing Bhajans with Harmonium: Complete 2026 Guide" --post_name=how-to-sing-bhajans-with-harmonium --post_category="$CAT" --quiet

	$WP kms import --publish --replace-pages
	for slug in pricing faq refund-policy; do
		ID=$($WP post list --post_type=page --name=$slug --post_status=draft --field=ID)
		[ -n "$ID" ] && $WP post update "$ID" --post_status=publish --quiet
	done
	$WP rewrite flush
fi

cat > "$DIR/router.php" <<'PHP'
<?php
$root = __DIR__ . '/wordpress';
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file = realpath( $root . $path );
if ( '/' !== $path && $file && is_file( $file ) && false === strpos( $file, '.php' ) ) {
	return false;
}
if ( $file && is_file( $file ) && '.php' === substr( $file, -4 ) ) {
	chdir( dirname( $file ) );
	require $file;
	return true;
}
chdir( $root );
require $root . '/index.php';
PHP

echo "Serving $URL (admin / admin). Ctrl+C to stop."
cd "$DIR/wordpress"
# Several workers: the plugin's site checks make requests back to the site itself.
PHP_CLI_SERVER_WORKERS=6 exec php -S "127.0.0.1:$PORT" -t "$DIR/wordpress" "$DIR/router.php"
