<?php
/**
 * Styles, scripts, font preloading and the hero image preload.
 *
 * Budget: one stylesheet, one small deferred script, two self-hosted font files (Merriweather 900 and Inter).
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets.
 */
function km_enqueue_assets() {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style( 'km-main', $uri . '/assets/css/main.css', array(), KMS_THEME_VERSION . '-' . filemtime( $dir . '/assets/css/main.css' ) );
	wp_enqueue_script(
		'km-theme',
		$uri . '/assets/js/theme.js',
		array(),
		KMS_THEME_VERSION . '-' . filemtime( $dir . '/assets/js/theme.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'km_enqueue_assets' );

/**
 * Preload the fonts and the front-page hero image (the likely LCP element).
 */
function km_preload() {
	foreach ( array( 'merriweather-latin-900.woff2', 'inter-latin-wght.woff2' ) as $font ) {
		echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/' . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}

	if ( is_front_page() ) {
		$hero = km_hero_image_id();
		if ( $hero ) {
			$src    = wp_get_attachment_image_src( $hero, 'large' );
			$srcset = wp_get_attachment_image_srcset( $hero, 'large' );
			if ( $src ) {
				echo '<link rel="preload" as="image" href="' . esc_url( $src[0] ) . '"' . ( $srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="(min-width: 960px) 520px, 100vw"' : '' ) . ' fetchpriority="high">' . "\n";
			}
		}
	}
}
add_action( 'wp_head', 'km_preload', 1 );
