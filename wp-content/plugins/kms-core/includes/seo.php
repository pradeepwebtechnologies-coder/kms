<?php
/**
 * SEO helpers that complement (never fight) Rank Math / Yoast.
 *
 * - With an SEO plugin active: only fills its missing share image and leaves
 *   titles/descriptions to it.
 * - Without one: prints a meta description, Open Graph and Twitter tags.
 * - robots.txt: explicit Allow groups for AI search/assistant crawlers.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin handles meta tags.
 *
 * @return bool
 */
function kms_has_seo_plugin() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\\Load' );
}

/**
 * Meta description for the current request (fallback mode only).
 *
 * @return string
 */
function kms_meta_description() {
	$text = '';
	if ( is_front_page() ) {
		$text = kms_fact_text( 'description' );
	} elseif ( is_singular( 'kms_course' ) ) {
		$text = kms_course_answer( get_queried_object_id() );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
	} elseif ( is_post_type_archive( 'kms_course' ) ) {
		$text = kms_fill( __( 'Live one-to-one online classes in harmonium, Indian singing, bhajan and kirtan, Hindustani classical vocal and tabla with {founder}. Beginners welcome; classes in your time zone.', 'kms-core' ) );
	} elseif ( is_category() || is_tag() ) {
		$text = wp_strip_all_tags( term_description() );
	}
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
	return mb_strlen( $text ) > 158 ? rtrim( mb_substr( $text, 0, 155 ) ) . '…' : $text;
}

/**
 * Fallback meta tags when no SEO plugin is active.
 */
function kms_print_fallback_meta() {
	if ( kms_has_seo_plugin() || is_admin() ) {
		return;
	}
	$description = kms_meta_description();
	$url         = kms_current_url();
	$image       = is_singular() ? kms_post_image_url( get_queried_object_id() ) : kms_fact( 'default_image' );
	$title       = wp_get_document_title();

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="' . esc_attr( kms_fact( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
	if ( is_front_page() && ! is_singular() && ! is_paged() ) {
		// WordPress prints rel=canonical for singular views only (a static front page is singular).
		echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'kms_print_fallback_meta', 2 );

/**
 * Keep search results and the class archive's paged copies out of the index (fallback mode).
 *
 * @param array $robots Robots directives.
 * @return array
 */
function kms_fallback_robots( $robots ) {
	if ( ! kms_has_seo_plugin() && ( is_search() || is_404() ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'kms_fallback_robots' );

/**
 * Rank Math: use the default share image when a page has none (92 of 113 pages had none).
 *
 * @param string $image Image URL chosen by Rank Math.
 * @return string
 */
function kms_rank_math_default_image( $image ) {
	return $image ? $image : kms_fact( 'default_image' );
}
add_filter( 'rank_math/opengraph/facebook/image', 'kms_rank_math_default_image' );
add_filter( 'rank_math/opengraph/twitter/image', 'kms_rank_math_default_image' );

/**
 * Crawlers used by AI search engines and assistants.
 *
 * @return string[]
 */
function kms_ai_crawlers() {
	return array(
		'OAI-SearchBot',     // ChatGPT search.
		'ChatGPT-User',      // ChatGPT browsing on a user's request.
		'GPTBot',            // OpenAI model training.
		'ClaudeBot',         // Anthropic.
		'Claude-SearchBot',
		'Claude-User',
		'PerplexityBot',
		'Perplexity-User',
		'Google-Extended',   // Gemini / Google AI.
		'Applebot-Extended', // Apple Intelligence.
		'Meta-ExternalAgent',
		'Amazonbot',
		'DuckAssistBot',
		'MistralAI-User',
		'CCBot',             // Common Crawl, used by many open models.
	);
}

/**
 * Add explicit Allow groups for AI crawlers and point them at llms.txt.
 *
 * A crawler with its own group ignores the "*" group, so the WordPress
 * admin rules are repeated in each group.
 *
 * @param string $output Robots.txt content.
 * @param bool   $public Whether the site is public.
 * @return string
 */
function kms_robots_txt( $output, $public ) {
	if ( ! $public || ! kms_fact_on( 'allow_ai_crawlers' ) ) {
		return $output;
	}
	$path  = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$path  = $path ? trailingslashit( $path ) : '/';
	$rules = "Allow: {$path}\nDisallow: {$path}wp-admin/\nAllow: {$path}wp-admin/admin-ajax.php\nDisallow: {$path}?s=\n";

	$block = "\n# AI search and assistant crawlers are welcome. Summary for language models: " . home_url( '/llms.txt' ) . "\n";
	foreach ( kms_ai_crawlers() as $bot ) {
		$block .= "User-agent: {$bot}\n";
	}
	$block .= $rules;

	return rtrim( $output ) . "\n" . $block;
}
add_filter( 'robots_txt', 'kms_robots_txt', 100, 2 );
