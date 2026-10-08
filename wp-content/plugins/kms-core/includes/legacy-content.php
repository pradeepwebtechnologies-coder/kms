<?php
/**
 * Render old Divi-built pages without the Divi builder.
 *
 * When the site switches away from Divi, every page built with it would show raw
 * [et_pb_section …] shortcodes. Most pages on krishnamusicschool.com keep their
 * real content in Divi "Code" and "Text" modules, so stripping the wrapper
 * shortcodes (and converting the few content modules) brings the content back.
 * Pages can then be rebuilt one by one in the block editor.
 *
 * Skipped automatically when the Divi theme or the Divi Builder plugin is active.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Divi itself can render builder content.
 *
 * @return bool
 */
function kms_divi_available() {
	// Divi or Extra (or a child theme of either) still active: Divi renders its own shortcodes.
	if ( in_array( strtolower( (string) get_template() ), array( 'divi', 'extra' ), true ) ) {
		return true;
	}
	return defined( 'ET_BUILDER_VERSION' ) || defined( 'ET_BUILDER_PLUGIN_VERSION' )
		|| class_exists( 'ET_Builder_Element' ) || class_exists( 'ET_Builder_Plugin' )
		|| function_exists( 'et_pb_is_pagebuilder_used' ) || function_exists( 'et_setup_builder' );
}

/**
 * Read one attribute from a shortcode attribute string.
 *
 * @param string $atts Raw attribute string.
 * @param string $name Attribute name.
 * @return string
 */
function kms_divi_attr( $atts, $name ) {
	$parsed = shortcode_parse_atts( $atts );
	return is_array( $parsed ) && isset( $parsed[ $name ] ) ? (string) $parsed[ $name ] : '';
}

/**
 * Convert Divi builder markup to plain HTML.
 *
 * @param string $content Raw post content.
 * @return string
 */
function kms_divi_to_html( $content ) {
	// Divi stores code-module line breaks as HTML comments.
	$content = str_replace( '<!-- [et_pb_line_break_holder] -->', "\n", $content );

	// Content modules that carry their content in attributes.
	$content = preg_replace_callback(
		'/\[et_pb_(image|fullwidth_image)\b([^\]]*)\]/',
		static function ( $m ) {
			$src = kms_divi_attr( $m[2], 'src' );
			if ( ! $src ) {
				return '';
			}
			$alt = kms_divi_attr( $m[2], 'alt' );
			$id  = attachment_url_to_postid( $src );
			if ( $id ) {
				// An empty Divi alt must not override the Media Library alt text.
				return '<figure class="km-legacy__image">' . wp_get_attachment_image( $id, 'large', false, '' !== $alt ? array( 'alt' => $alt ) : array() ) . '</figure>';
			}
			return '<figure class="km-legacy__image"><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async"></figure>';
		},
		$content
	);

	$content = preg_replace_callback(
		'/\[et_pb_button\b([^\]]*)\]/',
		static function ( $m ) {
			$url  = kms_divi_attr( $m[1], 'button_url' );
			$text = kms_divi_attr( $m[1], 'button_text' );
			return ( $url && $text ) ? '<p><a class="km-btn km-btn--primary" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></p>' : '';
		},
		$content
	);

	$content = preg_replace_callback(
		'/\[et_pb_(video|fullwidth_video)\b([^\]]*)\]/',
		static function ( $m ) {
			$src = kms_divi_attr( $m[2], 'src' );
			// A bare URL on its own line becomes an embed via WordPress auto-embeds.
			return $src ? "\n\n" . esc_url_raw( $src ) . "\n\n" : '';
		},
		$content
	);

	$content = preg_replace_callback(
		'/\[et_pb_(fullwidth_header|cta|blurb|testimonial)\b([^\]]*)\]/',
		static function ( $m ) {
			$html  = '';
			$title = kms_divi_attr( $m[2], 'title' );
			if ( $title ) {
				$html .= '<h2>' . esc_html( $title ) . '</h2>';
			}
			$sub = kms_divi_attr( $m[2], 'subhead' );
			if ( $sub ) {
				$html .= '<p class="km-legacy__subhead">' . esc_html( $sub ) . '</p>';
			}
			$author = kms_divi_attr( $m[2], 'author' );
			if ( $author ) {
				$html .= '<p class="km-legacy__author">— ' . esc_html( $author ) . '</p>';
			}
			foreach ( array( array( 'button_one_url', 'button_one_text' ), array( 'button_url', 'button_text' ) ) as $pair ) {
				$url  = kms_divi_attr( $m[2], $pair[0] );
				$text = kms_divi_attr( $m[2], $pair[1] );
				if ( $url && $text ) {
					$html .= '<p><a class="km-btn km-btn--primary" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></p>';
				}
			}
			return $html;
		},
		$content
	);

	$content = preg_replace( '/\[et_pb_divider\b[^\]]*\]/', '<hr>', $content );

	// Remove every remaining builder tag, opening and closing, keeping inner content.
	$content = preg_replace( '/\[\/?et_pb_[a-z0-9_]+\b[^\]]*\]/i', '', $content );

	return $content;
}

/**
 * the_content filter (runs before shortcodes and auto-embeds).
 *
 * @param string $content Post content.
 * @return string
 */
function kms_legacy_divi_filter( $content ) {
	if ( false === strpos( $content, '[et_pb_' ) || ! kms_fact_on( 'legacy_divi' ) || kms_divi_available() ) {
		return $content;
	}
	// Builder HTML must not get automatic <p>/<br> tags.
	remove_filter( 'the_content', 'wpautop' );
	add_filter( 'the_content', 'kms_legacy_restore_autop', PHP_INT_MAX );

	$html = kms_divi_to_html( $content );
	if ( ! kms_fact_on( 'legacy_styles' ) ) {
		$html = preg_replace( '#<style\b[^>]*>.*?</style>#is', '', $html );
	}
	return '<div class="km-legacy">' . $html . '</div>';
}
add_filter( 'the_content', 'kms_legacy_divi_filter', 5 );

/**
 * Re-enable wpautop for the next piece of content.
 *
 * @param string $content Content.
 * @return string
 */
function kms_legacy_restore_autop( $content ) {
	remove_filter( 'the_content', 'kms_legacy_restore_autop', PHP_INT_MAX );
	if ( ! has_filter( 'the_content', 'wpautop' ) ) {
		add_filter( 'the_content', 'wpautop' );
	}
	return $content;
}
