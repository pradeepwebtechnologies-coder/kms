<?php
/**
 * Site checks shown on Settings → KMS Facts ("Run checks" button).
 *
 * Catches the problems found in the October 2026 audit if they come back:
 * missing pages redirecting to the homepage, llms.txt not served, AI crawlers
 * not allowed, wrong postcode, unused heavy plugins.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Run all checks.
 *
 * @return array<int, array{status:string,label:string,detail:string}>
 */
function kms_health_checks() {
	$results = array();
	$add     = static function ( $status, $label, $detail = '' ) use ( &$results ) {
		$results[] = compact( 'status', 'label', 'detail' );
	};

	// 1. Missing pages must return 404, not redirect to the homepage.
	$probe    = home_url( '/kms-health-check-' . strtolower( wp_generate_password( 8, false ) ) . '/' );
	$response = wp_remote_get(
		$probe,
		array(
			'redirection' => 0,
			'timeout'     => 10,
			'sslverify'   => false,
		)
	);
	if ( is_wp_error( $response ) ) {
		$add( 'info', __( 'Could not test how missing pages respond (the server blocked a request to itself).', 'kms-core' ), $response->get_error_message() );
	} else {
		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 404 === $code ) {
			$add( 'good', __( 'Missing pages return a real 404.', 'kms-core' ) );
		} elseif ( in_array( $code, array( 301, 302, 307, 308 ), true ) ) {
			$add( 'bad', __( 'Missing pages redirect instead of returning 404 (Google reports these as soft 404s).', 'kms-core' ), sprintf( /* translators: 1: code, 2: location */ __( 'Got %1$d → %2$s. Turn off any "redirect 404 to homepage" plugin or Rank Math setting.', 'kms-core' ), $code, wp_remote_retrieve_header( $response, 'location' ) ) );
		} else {
			/* translators: %d: HTTP status */
			$add( 'warn', sprintf( __( 'Missing pages return HTTP %d instead of 404.', 'kms-core' ), $code ) );
		}
	}

	// 2. llms.txt.
	if ( kms_fact_on( 'llms_enabled' ) ) {
		$response = wp_remote_get(
			home_url( '/llms.txt' ),
			array(
				'redirection' => 0,
				'timeout'     => 10,
				'sslverify'   => false,
			)
		);
		if ( is_wp_error( $response ) ) {
			$add( 'info', __( 'Could not fetch /llms.txt from the server.', 'kms-core' ), $response->get_error_message() );
		} elseif ( 200 === (int) wp_remote_retrieve_response_code( $response ) && false !== strpos( wp_remote_retrieve_body( $response ), '# ' ) ) {
			$add( 'good', __( '/llms.txt is served.', 'kms-core' ) );
		} else {
			/* translators: %d: HTTP status */
			$add( 'bad', sprintf( __( '/llms.txt is not served (HTTP %d). Check for a physical llms.txt file or a redirect rule.', 'kms-core' ), (int) wp_remote_retrieve_response_code( $response ) ) );
		}
	}

	// 3. robots.txt.
	if ( kms_fact_on( 'allow_ai_crawlers' ) ) {
		$response = wp_remote_get(
			home_url( '/robots.txt' ),
			array(
				'timeout'   => 10,
				'sslverify' => false,
			)
		);
		if ( ! is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			if ( false !== stripos( $body, 'OAI-SearchBot' ) ) {
				$add( 'good', __( 'robots.txt welcomes AI search crawlers.', 'kms-core' ) );
			} else {
				$add( 'warn', __( 'robots.txt does not include the AI crawler rules — a physical robots.txt file probably overrides WordPress. Delete it or copy the rules in.', 'kms-core' ) );
			}
			if ( preg_match( '/User-agent:\s*GPTBot\s*\R+\s*Disallow:\s*\/\s*$/mi', $body ) ) {
				$add( 'bad', __( 'robots.txt blocks GPTBot.', 'kms-core' ) );
			}
		}
	}
	$add( 'info', __( 'The Hostinger CDN answered GPTBot with HTTP 429 during the audit. Robots.txt cannot fix that: allow AI crawlers in hPanel (see the install guide).', 'kms-core' ) );

	// 4. Facts.
	if ( '305001' === kms_fact( 'postal_code' ) ) {
		$add( 'bad', __( 'PIN code 305001 is Ajmer city. Pushkar is 305022.', 'kms-core' ) );
	}
	if ( ! kms_fact( 'tripadvisor_url' ) ) {
		$add( 'warn', __( 'Add the TripAdvisor listing URL in KMS Facts → Proof (used for links and schema sameAs).', 'kms-core' ) );
	}
	$checked = kms_fact( 'ratings_checked' );
	if ( $checked && strtotime( $checked . '-01' ) < strtotime( '-6 months' ) ) {
		$add( 'warn', __( 'Ratings were last checked more than 6 months ago. Update the numbers and the "last checked" month.', 'kms-core' ) );
	}

	// 5. Content.
	$courses = wp_count_posts( 'kms_course' );
	if ( empty( $courses->publish ) ) {
		$add( 'bad', __( 'No classes are published. Import the starter content (Tools → KMS Starter Content) or add classes.', 'kms-core' ) );
	} else {
		/* translators: %d: number */
		$add( 'good', sprintf( _n( '%d class published.', '%d classes published.', (int) $courses->publish, 'kms-core' ), (int) $courses->publish ) );
	}
	$unverified = get_posts(
		array(
			'post_type'      => 'kms_review',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => 'kms_verified',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'   => 'kms_verified',
					'value' => '',
				),
			),
		)
	);
	if ( $unverified ) {
		/* translators: %d: number */
		$add( 'warn', sprintf( _n( '%d published review is not marked as verified yet.', '%d published reviews are not marked as verified yet.', count( $unverified ), 'kms-core' ), count( $unverified ) ) );
	}

	// 6. Plugins that the audit found loaded but unused.
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'elementor/elementor.php' ) ) {
		$add( 'warn', __( 'Elementor is active. The audit found no page using it — deactivate it to remove its CSS/JS.', 'kms-core' ) );
	}
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$add( 'warn', __( 'WooCommerce is active but nothing is sold. Deactivate it and delete the Shop, Cart, Checkout and My account pages.', 'kms-core' ) );
	}
	if ( ! kms_schema_enabled() && defined( 'RANK_MATH_VERSION' ) ) {
		$titles = get_option( 'rank-math-options-titles', array() );
		if ( isset( $titles['knowledgegraph_type'] ) && 'person' === $titles['knowledgegraph_type'] ) {
			$add( 'bad', __( 'Rank Math describes the school as a Person. Set Rank Math → Titles & Meta → Local SEO → "Organization", or enable the KMS graph.', 'kms-core' ) );
		}
	}

	return $results;
}

/**
 * Panel with a "Run checks" button.
 */
function kms_health_render_panel() {
	$run_url = wp_nonce_url( admin_url( 'options-general.php?page=kms-facts&kms_health=1' ), 'kms_health' );
	echo '<div class="card" style="max-width:780px;margin:16px 0;padding:12px 16px">';
	echo '<h2 style="margin-top:0">' . esc_html__( 'Site checks', 'kms-core' ) . '</h2>';
	if ( isset( $_GET['kms_health'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'kms_health' ) ) {
		$icons = array(
			'good' => '✅',
			'warn' => '⚠️',
			'bad'  => '❌',
			'info' => 'ℹ️',
		);
		echo '<ul>';
		foreach ( kms_health_checks() as $check ) {
			echo '<li><span aria-hidden="true">' . esc_html( $icons[ $check['status'] ] ) . '</span> <strong>' . esc_html( $check['label'] ) . '</strong>';
			if ( $check['detail'] ) {
				echo '<br><span class="description">' . esc_html( $check['detail'] ) . '</span>';
			}
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>' . esc_html__( 'Checks 404 handling, llms.txt, robots.txt, key facts, reviews and unused plugins.', 'kms-core' ) . '</p>';
	}
	echo '<p><a class="button" href="' . esc_url( $run_url ) . '">' . esc_html__( 'Run checks', 'kms-core' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( home_url( '/llms.txt' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'View llms.txt', 'kms-core' ) . '</a> ';
	echo '<a class="button" href="' . esc_url( 'https://search.google.com/test/rich-results?url=' . rawurlencode( home_url( '/' ) ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Test homepage in Google Rich Results', 'kms-core' ) . '</a></p>';
	echo '</div>';
}
