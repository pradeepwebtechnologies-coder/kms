<?php
/**
 * Plugin Name:       KMS Core — Krishna Music School
 * Plugin URI:        https://krishnamusicschool.com/
 * Description:       Classes, reviews, FAQs, enquiry form, structured data, llms.txt and the single facts registry for krishnamusicschool.com. Works with the "Krishna Music School" theme.
 * Version:           1.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Krishna Music School
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kms-core
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'KMS_CORE_VERSION', '1.1.0' );
define( 'KMS_CORE_FILE', __FILE__ );
define( 'KMS_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'KMS_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once KMS_CORE_DIR . 'includes/icons.php';
require_once KMS_CORE_DIR . 'includes/facts.php';
require_once KMS_CORE_DIR . 'includes/post-types.php';
require_once KMS_CORE_DIR . 'includes/meta-boxes.php';
require_once KMS_CORE_DIR . 'includes/pricing.php';
require_once KMS_CORE_DIR . 'includes/render.php';
require_once KMS_CORE_DIR . 'includes/shortcodes.php';
require_once KMS_CORE_DIR . 'includes/leads.php';
require_once KMS_CORE_DIR . 'includes/schema.php';
require_once KMS_CORE_DIR . 'includes/seo.php';
require_once KMS_CORE_DIR . 'includes/llms.php';
require_once KMS_CORE_DIR . 'includes/legacy-content.php';
require_once KMS_CORE_DIR . 'includes/health.php';
require_once KMS_CORE_DIR . 'includes/importer.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once KMS_CORE_DIR . 'includes/cli.php';
}

/**
 * Register content types and flush permalinks so /online-classes/ works immediately.
 */
function kms_core_activate() {
	kms_register_post_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'kms_core_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Front-end assets for interactive widgets (currency switcher, time-zone converter, enquiry form).
 */
function kms_core_register_assets() {
	// Small (≈3 KB gzipped) and needed on most pages: prices, time zones, forms, WhatsApp tracking.
	wp_enqueue_script( 'kms-core', KMS_CORE_URL . 'assets/kms.js', array(), KMS_CORE_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'kms-core',
		'kmsCore',
		array(
			'i18n' => array(
				'yourTime' => __( 'your time', 'kms-core' ),
				'nextDay'  => __( 'next day', 'kms-core' ),
				'prevDay'  => __( 'previous day', 'kms-core' ),
				'sending'  => __( 'Sending…', 'kms-core' ),
				'error'    => __( 'Sorry, that did not go through. Please WhatsApp us instead.', 'kms-core' ),
			),
		)
	);

	// Minimal fallback styles when the companion theme is not active.
	if ( ! current_theme_supports( 'kms-core' ) ) {
		wp_enqueue_style( 'kms-core-fallback', KMS_CORE_URL . 'assets/fallback.css', array(), KMS_CORE_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'kms_core_register_assets', 5 );
