<?php
/**
 * Theme supports, menus and image sizes.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 */
function km_setup() {
	load_theme_textdomain( 'kms-theme', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 141,
			'width'       => 500,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	// Tells KMS Core that this theme styles its widgets (no fallback CSS needed).
	add_theme_support( 'kms-core' );

	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Main menu', 'kms-theme' ),
			'footer'  => __( 'Footer legal links', 'kms-theme' ),
		)
	);

	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'km-card', 640, 400, true );
}
add_action( 'after_setup_theme', 'km_setup' );

/**
 * Content width for embeds.
 */
function km_content_width() {
	$GLOBALS['content_width'] = 760; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'km_content_width', 0 );

/**
 * Body classes for layout hooks.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function km_body_class( $classes ) {
	if ( is_singular() && false !== strpos( (string) get_post_field( 'post_content', get_queried_object_id() ), '[et_pb_' ) ) {
		$classes[] = 'km-has-legacy';
	}
	if ( km_has_kms() ) {
		$classes[] = 'km-has-core';
	}
	return $classes;
}
add_filter( 'body_class', 'km_body_class' );

/**
 * Whether the companion plugin is active.
 *
 * @return bool
 */
function km_has_kms() {
	return function_exists( 'kms_fact' );
}
