<?php
/**
 * Front-end weight reduction.
 *
 * Removes assets this site does not use. Each item was found loading on the
 * live site during the October 2026 audit.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

// Emoji detection script and styles (browsers render emoji natively).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Header clutter.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * WooCommerce: nothing is sold, so its assets are removed everywhere except
 * its own pages (in case it is still active during migration).
 */
function km_dequeue_unused() {
	if ( class_exists( 'WooCommerce' ) && function_exists( 'is_woocommerce' ) && ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		foreach ( array( 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general', 'wc-blocks-style', 'wc-blocks-vendors-style', 'woocommerce-inline' ) as $style ) {
			wp_dequeue_style( $style );
		}
		foreach ( array( 'wc-cart-fragments', 'woocommerce', 'wc-add-to-cart', 'sourcebuster-js', 'wc-order-attribution' ) as $script ) {
			wp_dequeue_script( $script );
		}
	}
	// Classic block library CSS is still needed for core blocks in content; keep it.
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'km_dequeue_unused', 100 );

/**
 * Hide the REST API user list from visitors (the audit found the admin login exposed).
 *
 * @param array $endpoints REST endpoints.
 * @return array
 */
function km_hide_user_endpoints( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'km_hide_user_endpoints' );

/**
 * Stop ?author=N scans from revealing user names.
 */
function km_block_author_scan() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check.
	if ( ! is_admin() && isset( $_GET['author'] ) && ! is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'km_block_author_scan', 1 );
