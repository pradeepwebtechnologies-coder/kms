<?php
/**
 * Tools → KMS Starter Content: one-click import of classes, FAQs, reviews and key pages.
 *
 * Safe by default: existing items are skipped, new content is created as drafts
 * unless "publish" is ticked, and existing pages are only replaced on request
 * (their previous content stays in Revisions).
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

require_once KMS_CORE_DIR . 'includes/starter-content.php';

/**
 * Import everything.
 *
 * @param array $opts { publish:bool, replace_pages:bool }.
 * @return array<int, string> Log lines.
 */
function kms_import_starter( $opts = array() ) {
	$opts   = wp_parse_args(
		$opts,
		array(
			'publish'       => false,
			'replace_pages' => false,
		)
	);
	$status = $opts['publish'] ? 'publish' : 'draft';
	$log    = array();

	// FAQ groups.
	foreach ( kms_starter_faq_groups() as $slug => $name ) {
		if ( ! term_exists( $slug, 'kms_faq_group' ) ) {
			wp_insert_term( $name, 'kms_faq_group', array( 'slug' => $slug ) );
		}
	}

	// Classes.
	foreach ( kms_starter_courses() as $course ) {
		$existing = get_page_by_path( $course['slug'], OBJECT, 'kms_course' );
		if ( $existing ) {
			/* translators: %s: title */
			$log[] = sprintf( __( 'Class exists, skipped: %s', 'kms-core' ), $course['title'] );
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'kms_course',
				'post_status'  => $status,
				'post_title'   => $course['title'],
				'post_name'    => $course['slug'],
				'post_excerpt' => $course['excerpt'],
				'post_content' => $course['content'],
				'menu_order'   => $course['order'],
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			$log[] = $id->get_error_message();
			continue;
		}
		foreach ( $course['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
		/* translators: %s: title */
		$log[] = sprintf( __( 'Class created: %s', 'kms-core' ), $course['title'] );
	}

	// FAQs.
	$order = 0;
	foreach ( kms_starter_faqs() as $faq ) {
		++$order;
		list( $group, $question, $answer ) = $faq;
		$found = get_posts(
			array(
				'post_type'      => 'kms_faq',
				'post_status'    => 'any',
				'title'          => $question,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $found ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'kms_faq',
				'post_status'  => $status,
				'post_title'   => $question,
				'post_content' => $answer,
				'menu_order'   => $order,
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			wp_set_object_terms( $id, $group, 'kms_faq_group' );
		}
	}
	/* translators: %d: number */
	$log[] = sprintf( __( 'FAQs checked: %d', 'kms-core' ), $order );

	// Reviews (always unverified: the school ticks "Verified" after checking each one).
	$order = 0;
	foreach ( kms_starter_reviews() as $review ) {
		++$order;
		$found = get_posts(
			array(
				'post_type'      => 'kms_review',
				'post_status'    => 'any',
				'title'          => $review['name'],
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $found ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'kms_review',
				'post_status'  => $status,
				'post_title'   => $review['name'],
				'post_content' => $review['text'],
				'menu_order'   => $order,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, 'kms_location', $review['loc'] );
		update_post_meta( $id, 'kms_source', 'TripAdvisor' );
		update_post_meta( $id, 'kms_date', $review['date'] );
		update_post_meta( $id, 'kms_subject', $review['subject'] );
		update_post_meta( $id, 'kms_course', $review['course'] );
		update_post_meta( $id, 'kms_mode', 'inperson' );
		update_post_meta( $id, 'kms_rating', 5 );
	}
	/* translators: %d: number */
	$log[] = sprintf( __( 'Reviews checked: %d (marked unverified until you check them)', 'kms-core' ), $order );

	// Pages.
	$facts     = get_option( KMS_FACTS_OPTION, array() );
	$facts     = is_array( $facts ) ? $facts : array();
	$fact_keys = array(
		'pricing'                 => 'page_pricing',
		'faq'                     => 'page_faq',
		'refund-policy'           => 'page_refund',
		'about-us'                => 'page_about',
		'hear-from-our-attendees' => 'page_reviews',
		'contact-us'              => 'page_contact',
	);
	foreach ( kms_starter_pages() as $slug => $page ) {
		list( $title, $content, $replaceable ) = $page;
		$existing                              = get_page_by_path( $slug );
		if ( $existing ) {
			if ( $replaceable && $opts['replace_pages'] && $content ) {
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_title'   => $title,
						'post_content' => $content,
					)
				);
				/* translators: %s: title */
				$log[] = sprintf( __( 'Page replaced (old version kept in Revisions): %s', 'kms-core' ), $title );
			} else {
				/* translators: %s: title */
				$log[] = sprintf( __( 'Page exists, left unchanged: %s', 'kms-core' ), $existing->post_title );
			}
			$page_id = $existing->ID;
		} else {
			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'blog' === $slug ? 'publish' : 'draft',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $content,
				),
				true
			);
			if ( is_wp_error( $page_id ) ) {
				$log[] = $page_id->get_error_message();
				continue;
			}
			/* translators: %s: title */
			$log[] = sprintf( __( 'Page created as draft: %s', 'kms-core' ), $title );
		}
		if ( isset( $fact_keys[ $slug ] ) && empty( $facts[ $fact_keys[ $slug ] ] ) ) {
			$facts[ $fact_keys[ $slug ] ] = (string) $page_id;
		}
		// A posts page only exists when the homepage is a static page (as on the live site).
		if ( 'blog' === $slug && 'page' === get_option( 'show_on_front' ) && ! get_option( 'page_for_posts' ) ) {
			update_option( 'page_for_posts', $page_id );
			$log[] = __( 'Blog page set as the posts page.', 'kms-core' );
		}
	}
	update_option( KMS_FACTS_OPTION, $facts );

	flush_rewrite_rules();
	$log[] = __( 'Done. Review the drafts, then publish them.', 'kms-core' );
	return $log;
}

/**
 * Admin page.
 */
function kms_importer_menu() {
	add_management_page( __( 'KMS Starter Content', 'kms-core' ), __( 'KMS Starter Content', 'kms-core' ), 'manage_options', 'kms-starter', 'kms_importer_render' );
}
add_action( 'admin_menu', 'kms_importer_menu' );

/**
 * Render the importer screen.
 */
function kms_importer_render() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = array();
	if ( isset( $_POST['kms_import'] ) && check_admin_referer( 'kms_import' ) ) {
		$log = kms_import_starter(
			array(
				'publish'       => ! empty( $_POST['kms_publish'] ),
				'replace_pages' => ! empty( $_POST['kms_replace'] ),
			)
		);
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'KMS Starter Content', 'kms-core' ); ?></h1>
		<p style="max-width:760px"><?php esc_html_e( 'Creates the 5 online classes (harmonium, singing, bhajan & kirtan, Indian classical vocal, tabla), about 30 FAQs, the 10 TripAdvisor reviews already shown on the site, and the Pricing, FAQ, Refund policy and Blog pages. Everything is written from information already on krishnamusicschool.com. Items that already exist are skipped.', 'kms-core' ); ?></p>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><ul>
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'kms_import' ); ?>
			<p><label><input type="checkbox" name="kms_publish" value="1"> <?php esc_html_e( 'Publish classes, FAQs and reviews immediately (otherwise they are created as drafts)', 'kms-core' ); ?></label></p>
			<p><label><input type="checkbox" name="kms_replace" value="1"> <?php esc_html_e( 'Replace the content of the existing About, Reviews and Contact pages (the old content stays in Revisions)', 'kms-core' ); ?></label></p>
			<p><button type="submit" name="kms_import" value="1" class="button button-primary"><?php esc_html_e( 'Import starter content', 'kms-core' ); ?></button></p>
		</form>
	</div>
	<?php
}
