<?php
/**
 * Enquiry form: every submission is saved in WordPress (Enquiries) and emailed,
 * then the student is offered a one-tap WhatsApp follow-up.
 *
 * Spam protection uses a honeypot, a JavaScript check and a time trap instead of
 * a nonce, so the form keeps working on cached pages (an expired nonce on a
 * cached page would silently lose real leads). Suspected spam is still stored,
 * flagged, and not emailed — nothing is thrown away.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lead fields shown in admin and exports.
 *
 * @return array<string, string>
 */
function kms_lead_fields() {
	return array(
		'kms_email'     => __( 'Email', 'kms-core' ),
		'kms_phone'     => __( 'WhatsApp / phone', 'kms-core' ),
		'kms_country'   => __( 'Country / city', 'kms-core' ),
		'kms_timezone'  => __( 'Time zone (from browser)', 'kms-core' ),
		'kms_interest'  => __( 'Interested in', 'kms-core' ),
		'kms_format'    => __( 'Format', 'kms-core' ),
		'kms_level'     => __( 'Level', 'kms-core' ),
		'kms_when'      => __( 'Preferred times', 'kms-core' ),
		'kms_message'   => __( 'Message', 'kms-core' ),
		'kms_found'     => __( 'How they found us', 'kms-core' ),
		'kms_page'      => __( 'Sent from page', 'kms-core' ),
		'kms_status'    => __( 'Status', 'kms-core' ),
		'kms_spam'      => __( 'Spam check', 'kms-core' ),
		'kms_tips'      => __( 'Wants practice tips by email', 'kms-core' ),
	);
}

/**
 * Options for the "interested in" select: classes + other enquiry types.
 *
 * @return array<string, string> value => label
 */
function kms_interest_options() {
	$options = array( 'not-sure' => __( 'Not sure yet — please advise', 'kms-core' ) );
	foreach ( kms_get_courses() as $course ) {
		$options[ $course->post_name ] = get_the_title( $course );
	}
	$options['retreat']     = __( 'A retreat in Pushkar or the Himalayas', 'kms-core' );
	$options['performance'] = __( 'Booking a live performance', 'kms-core' );
	return $options;
}

/**
 * "How did you find us?" choices. "AI assistant" measures the AI-engine work directly.
 *
 * @return array<string, string>
 */
function kms_found_options() {
	return array(
		''            => __( 'Choose…', 'kms-core' ),
		'google'      => __( 'Google search', 'kms-core' ),
		'ai'          => __( 'ChatGPT, Gemini or another AI assistant', 'kms-core' ),
		'youtube'     => __( 'YouTube', 'kms-core' ),
		'social'      => __( 'Instagram or Facebook', 'kms-core' ),
		'tripadvisor' => __( 'TripAdvisor', 'kms-core' ),
		'friend'      => __( 'A friend or teacher', 'kms-core' ),
		'pushkar'     => __( 'I visited Pushkar', 'kms-core' ),
		'other'       => __( 'Other', 'kms-core' ),
	);
}

/**
 * Render the enquiry form.
 *
 * @param array $atts { course:string, title:string, compact:bool }.
 * @return string
 */
function kms_render_enquiry_form( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'course'  => '',
			'title'   => '',
			'compact' => '',
		),
		$atts,
		'kms_enquiry_form'
	);
	kms_need_script();

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only preselect.
	$selected = $atts['course'] ? $atts['course'] : ( isset( $_GET['course'] ) ? sanitize_title( wp_unslash( $_GET['course'] ) ) : '' );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag.
	$status   = isset( $_GET['kms_enquiry'] ) ? sanitize_key( wp_unslash( $_GET['kms_enquiry'] ) ) : '';
	$uid      = wp_unique_id( 'kms-f' );
	$privacy  = kms_page_url( 'privacy' );

	ob_start();
	?>
	<div class="km-enquiry" id="enquire">
		<?php if ( $atts['title'] ) : ?>
			<h2 class="km-enquiry__title"><?php echo esc_html( $atts['title'] ); ?></h2>
		<?php endif; ?>

		<div class="km-enquiry__done" data-km-done <?php echo 'sent' === $status ? '' : 'hidden'; ?> role="status" tabindex="-1">
			<p class="km-enquiry__done-title"><?php echo kms_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Thank you — your message has arrived.', 'kms-core' ); ?></p>
			<p><?php echo esc_html( kms_fact( 'response_time' ) ); ?></p>
			<p><?php esc_html_e( 'Want a faster reply? Send us a quick WhatsApp message too:', 'kms-core' ); ?></p>
			<?php echo kms_whatsapp_button( __( 'Hi! I just sent an enquiry on your website.', 'kms-core' ), __( 'Continue on WhatsApp', 'kms-core' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<?php if ( 'error' === $status ) : ?>
			<p class="km-notice km-notice--error" role="alert"><?php esc_html_e( 'Sorry, your message could not be sent. Please try again, or contact us on WhatsApp.', 'kms-core' ); ?></p>
		<?php endif; ?>

		<form class="km-form" data-km-form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" <?php echo 'sent' === $status ? 'hidden' : ''; ?>>
			<input type="hidden" name="action" value="kms_enquiry">
			<input type="hidden" name="kms_js" value="0" data-km-js>
			<input type="hidden" name="kms_ts" value="" data-km-ts>
			<input type="hidden" name="kms_timezone" value="" data-km-tzfield>
			<input type="hidden" name="kms_page" value="<?php echo esc_url( is_singular() ? get_permalink() : home_url( '/' ) ); ?>" data-km-page>
			<div class="km-form__hp" aria-hidden="true">
				<label for="<?php echo esc_attr( $uid ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'kms-core' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $uid ); ?>-website" name="kms_website" tabindex="-1" autocomplete="off">
			</div>

			<div class="km-form__grid">
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-name"><?php esc_html_e( 'Your name', 'kms-core' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="<?php echo esc_attr( $uid ); ?>-name" name="kms_name" required autocomplete="name" maxlength="120">
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'Email', 'kms-core' ); ?> <span aria-hidden="true">*</span></label>
					<input type="email" id="<?php echo esc_attr( $uid ); ?>-email" name="kms_email" required autocomplete="email" maxlength="160">
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-phone"><?php esc_html_e( 'WhatsApp number (with country code)', 'kms-core' ); ?></label>
					<input type="tel" id="<?php echo esc_attr( $uid ); ?>-phone" name="kms_phone" autocomplete="tel" maxlength="40" placeholder="+1 555 123 4567">
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-country"><?php esc_html_e( 'Where do you live?', 'kms-core' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $uid ); ?>-country" name="kms_country" autocomplete="country-name" maxlength="80" placeholder="<?php esc_attr_e( 'City, country', 'kms-core' ); ?>">
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-interest"><?php esc_html_e( 'I am interested in', 'kms-core' ); ?> <span aria-hidden="true">*</span></label>
					<select id="<?php echo esc_attr( $uid ); ?>-interest" name="kms_interest" required data-km-interest>
						<?php foreach ( kms_interest_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-format"><?php esc_html_e( 'Format', 'kms-core' ); ?></label>
					<select id="<?php echo esc_attr( $uid ); ?>-format" name="kms_format">
						<option value="online"><?php esc_html_e( 'Online classes', 'kms-core' ); ?></option>
						<option value="pushkar"><?php esc_html_e( 'In person in Pushkar', 'kms-core' ); ?></option>
						<option value="either"><?php esc_html_e( 'Either / not sure', 'kms-core' ); ?></option>
					</select>
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-level"><?php esc_html_e( 'Your level', 'kms-core' ); ?></label>
					<select id="<?php echo esc_attr( $uid ); ?>-level" name="kms_level">
						<option value="beginner"><?php esc_html_e( 'Complete beginner', 'kms-core' ); ?></option>
						<option value="some"><?php esc_html_e( 'Some experience', 'kms-core' ); ?></option>
						<option value="advanced"><?php esc_html_e( 'Experienced musician', 'kms-core' ); ?></option>
					</select>
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-when"><?php esc_html_e( 'Times that suit you', 'kms-core' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $uid ); ?>-when" name="kms_when" maxlength="160" placeholder="<?php esc_attr_e( 'e.g. weekday evenings, my time', 'kms-core' ); ?>">
				</p>
				<p class="km-field">
					<label for="<?php echo esc_attr( $uid ); ?>-found"><?php esc_html_e( 'How did you find us?', 'kms-core' ); ?></label>
					<select id="<?php echo esc_attr( $uid ); ?>-found" name="kms_found">
						<?php foreach ( kms_found_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p class="km-field km-field--full">
					<label for="<?php echo esc_attr( $uid ); ?>-message"><?php esc_html_e( 'Anything else? (optional)', 'kms-core' ); ?></label>
					<textarea id="<?php echo esc_attr( $uid ); ?>-message" name="kms_message" rows="3" maxlength="2000"></textarea>
				</p>
			</div>

			<p class="km-field km-field--check">
				<input type="checkbox" id="<?php echo esc_attr( $uid ); ?>-tips" name="kms_tips" value="1">
				<label for="<?php echo esc_attr( $uid ); ?>-tips"><?php esc_html_e( 'Also send me occasional free practice tips by email (optional).', 'kms-core' ); ?></label>
			</p>

			<p class="km-form__actions">
				<button type="submit" class="km-btn km-btn--primary km-btn--lg" data-km-submit><?php esc_html_e( 'Request my free consultation', 'kms-core' ); ?></button>
				<span class="km-form__or"><?php esc_html_e( 'or', 'kms-core' ); ?></span>
				<?php echo kms_whatsapp_button( '', __( 'Message us on WhatsApp', 'kms-core' ), 'km-btn--outline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>
			<p class="km-form__privacy">
				<?php esc_html_e( 'We only use your details to reply to you. We never share them.', 'kms-core' ); ?>
				<?php if ( $privacy ) : ?>
					<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Privacy policy', 'kms-core' ); ?></a>
				<?php endif; ?>
			</p>
			<p class="km-form__error" data-km-error hidden role="alert"></p>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'kms_enquiry_form', 'kms_render_enquiry_form' );

/**
 * Handle a submission (normal POST or fetch()).
 */
function kms_handle_enquiry() {
	$is_ajax = isset( $_SERVER['HTTP_X_KMS_AJAX'] );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see file header: honeypot + time trap instead of nonce.
	$get = static function ( $key, $max = 200 ) {
		return isset( $_POST[ $key ] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST[ $key ] ) ), 0, $max ) : '';
	};

	$name     = $get( 'kms_name', 120 );
	$email    = sanitize_email( $get( 'kms_email', 160 ) );
	$message  = isset( $_POST['kms_message'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['kms_message'] ) ), 0, 2000 ) : '';
	$interest = sanitize_title( $get( 'kms_interest', 80 ) );
	$interest = '' === $interest ? 'not-sure' : $interest;
	$page     = esc_url_raw( $get( 'kms_page', 500 ) );
	$honeypot = $get( 'kms_website' );
	$js       = $get( 'kms_js', 2 );
	$ts       = (int) $get( 'kms_ts', 20 );
	// phpcs:enable

	if ( '' === $page ) {
		$page = (string) wp_get_referer();
	}
	$redirect = $page && wp_validate_redirect( $page, '' ) ? $page : home_url( '/' );
	$fail     = static function ( $reason ) use ( $is_ajax, $redirect ) {
		if ( $is_ajax ) {
			wp_send_json_error( array( 'reason' => $reason ), 400 );
		}
		wp_safe_redirect( add_query_arg( 'kms_enquiry', 'error', remove_query_arg( 'kms_enquiry', $redirect ) ) . '#enquire' );
		exit;
	};

	if ( '' === $name || ! is_email( $email ) ) {
		$fail( 'missing' );
	}

	// Honeypot filled = certain bot: drop silently but report success to the bot.
	if ( '' !== $honeypot ) {
		if ( $is_ajax ) {
			wp_send_json_success();
		}
		wp_safe_redirect( add_query_arg( 'kms_enquiry', 'sent', $redirect ) . '#enquire' );
		exit;
	}

	// Rate limit: 5 submissions per 10 minutes per visitor.
	$ip_hash = wp_hash( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
	$count   = (int) get_transient( 'kms_rl_' . $ip_hash );
	if ( $count >= 5 ) {
		$fail( 'rate' );
	}
	set_transient( 'kms_rl_' . $ip_hash, $count + 1, 10 * MINUTE_IN_SECONDS );

	$spam = array();
	if ( '1' !== $js ) {
		$spam[] = 'no-js';
	}
	if ( $ts && ( time() * 1000 - $ts ) < 3000 ) {
		$spam[] = 'too-fast';
	}
	if ( preg_match_all( '#https?://#i', $message ) > 2 ) {
		$spam[] = 'links';
	}

	$options  = kms_interest_options();
	$interest_label = isset( $options[ $interest ] ) ? $options[ $interest ] : $interest;

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'kms_lead',
			'post_status' => 'private',
			/* translators: 1: name, 2: interest */
			'post_title'  => sprintf( __( '%1$s — %2$s', 'kms-core' ), $name, $interest_label ),
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		$fail( 'store' );
	}

	$data = array(
		'kms_email'    => $email,
		'kms_phone'    => $get( 'kms_phone', 40 ),
		'kms_country'  => $get( 'kms_country', 80 ),
		'kms_timezone' => $get( 'kms_timezone', 60 ),
		'kms_interest' => $interest_label,
		'kms_format'   => $get( 'kms_format', 20 ),
		'kms_level'    => $get( 'kms_level', 20 ),
		'kms_when'     => $get( 'kms_when', 160 ),
		'kms_message'  => $message,
		'kms_found'    => array_key_exists( $get( 'kms_found', 20 ), kms_found_options() ) ? kms_found_options()[ $get( 'kms_found', 20 ) ] : '',
		'kms_page'     => $page,
		'kms_status'   => 'new',
		'kms_spam'     => $spam ? implode( ', ', $spam ) : 'ok',
		'kms_tips'     => isset( $_POST['kms_tips'] ) ? 'yes' : 'no', // phpcs:ignore WordPress.Security.NonceVerification.Missing
	);
	foreach ( $data as $key => $value ) {
		update_post_meta( $lead_id, $key, $value );
	}

	if ( ! $spam ) {
		kms_email_lead( $lead_id, $name, $data );
	}

	if ( $is_ajax ) {
		wp_send_json_success( array( 'id' => $lead_id ) );
	}
	wp_safe_redirect( add_query_arg( 'kms_enquiry', 'sent', remove_query_arg( 'kms_enquiry', $redirect ) ) . '#enquire' );
	exit;
}
add_action( 'admin_post_nopriv_kms_enquiry', 'kms_handle_enquiry' );
add_action( 'admin_post_kms_enquiry', 'kms_handle_enquiry' );

/**
 * Email the school (and optionally the student).
 *
 * @param int    $lead_id Lead post ID.
 * @param string $name    Student name.
 * @param array  $data    Lead fields.
 */
function kms_email_lead( $lead_id, $name, $data ) {
	$to = kms_fact( 'lead_email' );
	$to = $to ? array_filter( array_map( 'sanitize_email', explode( ',', $to ) ) ) : array( get_option( 'admin_email' ) );

	$lines = array( __( 'Name', 'kms-core' ) . ': ' . $name );
	foreach ( kms_lead_fields() as $key => $label ) {
		if ( isset( $data[ $key ] ) && '' !== $data[ $key ] && ! in_array( $key, array( 'kms_status', 'kms_spam' ), true ) ) {
			$lines[] = $label . ': ' . $data[ $key ];
		}
	}
	$phone_digits = preg_replace( '/\D+/', '', $data['kms_phone'] );
	if ( $phone_digits ) {
		$lines[] = '';
		$lines[] = __( 'Reply on WhatsApp', 'kms-core' ) . ': https://wa.me/' . $phone_digits;
	}
	$lines[] = __( 'Open in WordPress', 'kms-core' ) . ': ' . admin_url( 'post.php?post=' . $lead_id . '&action=edit' );

	/* translators: 1: interest, 2: name, 3: country */
	$subject = sprintf( __( 'New enquiry: %1$s — %2$s (%3$s)', 'kms-core' ), $data['kms_interest'], $name, $data['kms_country'] ? $data['kms_country'] : __( 'country not given', 'kms-core' ) );
	$headers = array( 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $name ) . ' <' . $data['kms_email'] . '>' );

	wp_mail( $to, $subject, implode( "\n", $lines ), $headers );

	if ( kms_fact_on( 'autoreply' ) ) {
		$body = sprintf(
			/* translators: 1: name, 2: reply time promise, 3: WhatsApp link, 4: school name */
			__( "Namaste %1\$s,\n\nThank you for contacting us. %2\$s\n\nFor a faster reply, message us on WhatsApp: %3\$s\n\nWith music,\n%4\$s", 'kms-core' ),
			$name,
			kms_fact( 'response_time' ),
			kms_whatsapp_url( __( 'Hi! I just sent an enquiry on your website.', 'kms-core' ) ),
			kms_fact( 'name' )
		);
		/* translators: %s: school name */
		wp_mail( $data['kms_email'], sprintf( __( 'We received your message — %s', 'kms-core' ), kms_fact( 'name' ) ), $body );
	}
}

/* -------------------------------------------------------------------------
 * Admin: list columns, detail box, CSV export
 * ---------------------------------------------------------------------- */

/**
 * Enquiry list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function kms_lead_columns( $columns ) {
	return array(
		'cb'           => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'        => __( 'Name and interest', 'kms-core' ),
		'kms_email'    => __( 'Email', 'kms-core' ),
		'kms_phone'    => __( 'WhatsApp', 'kms-core' ),
		'kms_country'  => __( 'Country', 'kms-core' ),
		'kms_status'   => __( 'Status', 'kms-core' ),
		'kms_spam'     => __( 'Spam check', 'kms-core' ),
		'date'         => __( 'Received', 'kms-core' ),
	);
}
add_filter( 'manage_kms_lead_posts_columns', 'kms_lead_columns' );

/**
 * Enquiry list cell.
 *
 * @param string $column  Column key.
 * @param int    $post_id Lead ID.
 */
function kms_lead_column_value( $column, $post_id ) {
	$value = (string) get_post_meta( $post_id, $column, true );
	if ( 'kms_phone' === $column && $value ) {
		$digits = preg_replace( '/\D+/', '', $value );
		echo '<a href="' . esc_url( 'https://wa.me/' . $digits ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
		return;
	}
	if ( 'kms_email' === $column && $value ) {
		echo '<a href="' . esc_url( 'mailto:' . $value ) . '">' . esc_html( $value ) . '</a>';
		return;
	}
	echo esc_html( $value );
}
add_action( 'manage_kms_lead_posts_custom_column', 'kms_lead_column_value', 10, 2 );

/**
 * Status choices.
 *
 * @return array<string, string>
 */
function kms_lead_statuses() {
	return array(
		'new'        => __( 'New', 'kms-core' ),
		'contacted'  => __( 'Contacted', 'kms-core' ),
		'trial'      => __( 'Trial class booked', 'kms-core' ),
		'enrolled'   => __( 'Enrolled', 'kms-core' ),
		'closed'     => __( 'Not interested', 'kms-core' ),
		'spam'       => __( 'Spam', 'kms-core' ),
	);
}

/**
 * Enquiry detail box with a status selector.
 *
 * @param WP_Post $post Lead.
 */
function kms_render_lead_box( $post ) {
	wp_nonce_field( 'kms_save_lead', 'kms_lead_nonce' );
	echo '<table class="widefat striped"><tbody>';
	foreach ( kms_lead_fields() as $key => $label ) {
		if ( 'kms_status' === $key ) {
			continue;
		}
		$value = (string) get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table><p><label for="kms-lead-status"><strong>' . esc_html__( 'Status', 'kms-core' ) . '</strong></label> <select id="kms-lead-status" name="kms_status">';
	$current = (string) get_post_meta( $post->ID, 'kms_status', true );
	foreach ( kms_lead_statuses() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
	$phone = preg_replace( '/\D+/', '', (string) get_post_meta( $post->ID, 'kms_phone', true ) );
	if ( $phone ) {
		echo '<p><a class="button button-primary" target="_blank" rel="noopener" href="' . esc_url( 'https://wa.me/' . $phone ) . '">' . esc_html__( 'Reply on WhatsApp', 'kms-core' ) . '</a></p>';
	}
}

/**
 * Save enquiry status.
 *
 * @param int $post_id Lead ID.
 */
function kms_save_lead_status( $post_id ) {
	if ( ! isset( $_POST['kms_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kms_lead_nonce'] ) ), 'kms_save_lead' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['kms_status'] ) ) {
		return;
	}
	$status = sanitize_key( wp_unslash( $_POST['kms_status'] ) );
	if ( array_key_exists( $status, kms_lead_statuses() ) ) {
		update_post_meta( $post_id, 'kms_status', $status );
	}
}
add_action( 'save_post_kms_lead', 'kms_save_lead_status' );

/**
 * Export button above the enquiry list.
 *
 * @param string $which top|bottom.
 */
function kms_lead_export_button( $which ) {
	global $typenow;
	if ( 'kms_lead' !== $typenow || 'top' !== $which ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=kms_leads_csv' ), 'kms_leads_csv' );
	echo '<div class="alignleft actions"><a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Download CSV', 'kms-core' ) . '</a></div>';
}
add_action( 'manage_posts_extra_tablenav', 'kms_lead_export_button' );

/**
 * Stream all enquiries as CSV.
 */
function kms_export_leads_csv() {
	if ( ! current_user_can( 'edit_others_posts' ) || ! check_admin_referer( 'kms_leads_csv' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'kms-core' ) );
	}
	$leads = get_posts(
		array(
			'post_type'      => 'kms_lead',
			'post_status'    => array( 'private', 'publish', 'draft' ),
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=kms-enquiries-' . wp_date( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- BOM so Excel reads UTF-8.
	fputcsv( $out, array_merge( array( __( 'Date', 'kms-core' ), __( 'Name and interest', 'kms-core' ) ), array_values( kms_lead_fields() ) ) );
	foreach ( $leads as $lead ) {
		$row = array( get_the_date( 'Y-m-d H:i', $lead ), $lead->post_title );
		foreach ( array_keys( kms_lead_fields() ) as $key ) {
			$cell = (string) get_post_meta( $lead->ID, $key, true );
			// Neutralise spreadsheet formulas.
			$row[] = preg_match( '/^[=+\-@]/', $cell ) ? "'" . $cell : $cell;
		}
		fputcsv( $out, $row );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_kms_leads_csv', 'kms_export_leads_csv' );

/* -------------------------------------------------------------------------
 * Privacy: Tools → Export / Erase Personal Data
 * ---------------------------------------------------------------------- */

/**
 * Find enquiries by email.
 *
 * @param string $email Email address.
 * @return WP_Post[]
 */
function kms_leads_by_email( $email ) {
	return get_posts(
		array(
			'post_type'      => 'kms_lead',
			'post_status'    => 'any',
			'posts_per_page' => 100,
			'meta_key'       => 'kms_email', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $email, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
}

/**
 * Register exporter and eraser.
 *
 * @param array $items Registered items.
 * @return array
 */
function kms_register_privacy_exporter( $items ) {
	$items['kms-core'] = array(
		'exporter_friendly_name' => __( 'Music school enquiries', 'kms-core' ),
		'callback'               => static function ( $email ) {
			$data = array();
			foreach ( kms_leads_by_email( $email ) as $lead ) {
				$fields = array(
					array(
						'name'  => __( 'Received', 'kms-core' ),
						'value' => get_the_date( 'Y-m-d H:i', $lead ),
					),
				);
				foreach ( kms_lead_fields() as $key => $label ) {
					$fields[] = array(
						'name'  => $label,
						'value' => (string) get_post_meta( $lead->ID, $key, true ),
					);
				}
				$data[] = array(
					'group_id'    => 'kms-enquiries',
					'group_label' => __( 'Enquiries', 'kms-core' ),
					'item_id'     => 'kms-lead-' . $lead->ID,
					'data'        => $fields,
				);
			}
			return array(
				'data' => $data,
				'done' => true,
			);
		},
	);
	return $items;
}
add_filter( 'wp_privacy_personal_data_exporters', 'kms_register_privacy_exporter' );

/**
 * Register eraser.
 *
 * @param array $items Registered items.
 * @return array
 */
function kms_register_privacy_eraser( $items ) {
	$items['kms-core'] = array(
		'eraser_friendly_name' => __( 'Music school enquiries', 'kms-core' ),
		'callback'             => static function ( $email ) {
			$removed = false;
			foreach ( kms_leads_by_email( $email ) as $lead ) {
				$removed = (bool) wp_delete_post( $lead->ID, true ) || $removed;
			}
			return array(
				'items_removed'  => $removed,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
		},
	);
	return $items;
}
add_filter( 'wp_privacy_personal_data_erasers', 'kms_register_privacy_eraser' );
