<?php
/**
 * Copy pages and posts downloaded from the live site into a local preview site.
 *
 * Most pages on krishnamusicschool.com are made of Divi "Code" modules, and Divi prints
 * a Code module's content unchanged. Each module is stored back as a Divi shortcode, so
 * the KMS Core legacy fallback renders the page the way it will render the real
 * content once Divi is switched off. Pages not built with Divi are stored as one HTML block.
 *
 *   wp eval-file bin/preview/import-live.php <folder-from-fetch-live.sh>
 *
 * For local preview sites only: it refuses to run on krishnamusicschool.com.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

$kms_dir = isset( $args[0] ) ? rtrim( (string) $args[0], '/' ) : '';
if ( '' === $kms_dir || ! is_dir( $kms_dir ) ) {
	WP_CLI::error( 'Usage: wp eval-file bin/preview/import-live.php <folder with the downloaded HTML files>' );
}
if ( false !== strpos( (string) wp_parse_url( home_url(), PHP_URL_HOST ), 'krishnamusicschool.com' ) ) {
	WP_CLI::error( 'This is the live site. The importer is for local preview sites only.' );
}

// Keep <style> and <script> blocks exactly as they are on the live site.
kses_remove_filters();

/**
 * Inner HTML of every <div> that starts with $open, in document order.
 *
 * @param string $html HTML.
 * @param string $open Exact opening tag, e.g. '<div class="et_pb_code_inner">'.
 * @return string[]
 */
function kms_preview_inner_divs( $html, $open ) {
	$found = array();
	$pos   = 0;
	while ( false !== ( $start = strpos( $html, $open, $pos ) ) ) {
		$from  = $start + strlen( $open );
		$depth = 1;
		$i     = $from;
		while ( $depth > 0 && preg_match( '#<div\b|</div>#i', $html, $m, PREG_OFFSET_CAPTURE, $i ) ) {
			$depth += ( '</div>' === strtolower( $m[0][0] ) ) ? -1 : 1;
			$i      = $m[0][1] + strlen( $m[0][0] );
		}
		if ( $depth > 0 ) {
			break;
		}
		$found[] = substr( $html, $from, $i - $from - strlen( '</div>' ) );
		$pos     = $i;
	}
	return $found;
}

/**
 * Content of a <meta> tag.
 *
 * @param string $html HTML.
 * @param string $attr "name" or "property".
 * @param string $key  Meta name.
 * @return string
 */
function kms_preview_meta( $html, $attr, $key ) {
	if ( preg_match( '#<meta\s+' . $attr . '="' . preg_quote( $key, '#' ) . '"\s+content="([^"]*)"#i', $html, $m ) ) {
		return html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
	return '';
}

/**
 * Undo the live site's image optimiser. It swaps every image for an SVG placeholder that
 * carries the real address and loads it with a script the new theme does not have.
 *
 * @param string $html Content.
 * @return string
 */
function kms_preview_restore_images( $html ) {
	return (string) preg_replace_callback(
		'#src="data:image/svg\+xml;base64,([A-Za-z0-9+/=]+)"#',
		static function ( $m ) {
			$svg = (string) base64_decode( $m[1], true );
			if ( ! preg_match( '#bv-img-url="([^"]+)"#', $svg, $url ) ) {
				return $m[0];
			}
			// .../uploads/al_opt_content/IMAGE/<host>/wp-content/uploads/2025/10/a.jpg.bv.webp → .../uploads/2025/10/a.jpg
			$real = preg_replace( '#^(https?://[^/]+)/wp-content/uploads/al_opt_content/IMAGE/[^/]+(/wp-content/uploads/.+?)\.bv\.webp$#', '$1$2', $url[1] );
			return 'src="' . esc_url( $real ) . '"';
		},
		$html
	);
}

$kms_count = 0;
foreach ( glob( $kms_dir . '/*.html' ) as $kms_file ) {
	$slug = sanitize_title( basename( $kms_file, '.html' ) );
	$html = (string) file_get_contents( $kms_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	$body = kms_preview_inner_divs( $html, '<div class="entry-content">' );
	if ( ! $body ) {
		WP_CLI::warning( "$slug: no post content found, skipped." );
		continue;
	}
	$code = kms_preview_inner_divs( $body[0], '<div class="et_pb_code_inner">' );
	if ( $code ) {
		$content = '[et_pb_section fb_built="1" fullwidth="on"]';
		foreach ( $code as $module ) {
			$content .= '[et_pb_fullwidth_code]' . trim( $module ) . '[/et_pb_fullwidth_code]';
		}
		$content .= '[/et_pb_section]';
	} else {
		// Not built with Divi: the content as WordPress printed it, unchanged.
		$content = "<!-- wp:html -->\n" . trim( $body[0] ) . "\n<!-- /wp:html -->";
	}
	$content = kms_preview_restore_images( $content );
	// Divi loads jQuery and the new theme does not, so page scripts that need it stop working.
	if ( preg_match_all( '#<script(?![^>]*ld\+json)[^>]*>(.*?)</script>#is', $content, $scripts ) && preg_match( '#\bjQuery\b|\$\(#', implode( "\n", $scripts[1] ) ) ) {
		WP_CLI::warning( "$slug: a script on this page uses jQuery, which the new theme does not load." );
	}

	$is_post = (bool) preg_match( '#<body[^>]*class="[^"]*\bsingle-post\b#', $html );
	$title   = preg_match( '#<h1 class="entry-title[^"]*"[^>]*>(.*?)</h1>#s', $html, $m ) ? wp_strip_all_tags( $m[1] ) : '';
	if ( '' === $title ) {
		$title = kms_preview_meta( $html, 'property', 'og:title' );
	}
	$title = trim( (string) preg_replace( '#\s+[-|–]\s+Krishna Music School.*$#u', '', html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );

	$existing = get_page_by_path( $slug, OBJECT, array( 'page', 'post' ) );
	$postarr  = array(
		'post_type'    => $existing ? $existing->post_type : ( $is_post ? 'post' : 'page' ),
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title ? $title : $slug,
		'post_content' => $content,
		'post_excerpt' => '', // The live meta description is not an excerpt; cards fall back to the content, as they will in production.
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
	}

	$published = kms_preview_meta( $html, 'property', 'article:published_time' );
	if ( '' === $published && preg_match( '#"datePublished"\s*:\s*"([^"]+)"#', $html, $m ) ) {
		$published = $m[1];
	}
	if ( $published && strtotime( $published ) ) {
		$postarr['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', strtotime( $published ) );
		$postarr['post_date']     = get_date_from_gmt( $postarr['post_date_gmt'] );
	}

	$id = wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$slug: " . $id->get_error_message() );
		continue;
	}

	$modified = kms_preview_meta( $html, 'property', 'article:modified_time' );
	if ( '' === $modified && preg_match( '#"dateModified"\s*:\s*"([^"]+)"#', $html, $m ) ) {
		$modified = $m[1];
	}
	if ( $modified && strtotime( $modified ) ) {
		global $wpdb;
		$gmt = gmdate( 'Y-m-d H:i:s', strtotime( $modified ) );
		$wpdb->update( $wpdb->posts, array( 'post_modified_gmt' => $gmt, 'post_modified' => get_date_from_gmt( $gmt ) ), array( 'ID' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		clean_post_cache( $id );
	}

	$section = kms_preview_meta( $html, 'property', 'article:section' );
	if ( 'post' === get_post_type( $id ) && '' !== $section ) {
		$term = term_exists( $section, 'category' );
		$term = $term ? $term : wp_insert_term( $section, 'category' );
		if ( ! is_wp_error( $term ) ) {
			wp_set_post_categories( $id, array( (int) $term['term_id'] ) );
		}
	}

	WP_CLI::log( ( $existing ? 'updated ' : 'created ' ) . get_post_type( $id ) . " $slug" );
	++$kms_count;
}

WP_CLI::success( "$kms_count pages and posts imported." );
