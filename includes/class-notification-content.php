<?php
/**
 * Notification content.
 *
 * @package ConstantContact
 * @subpackage Notifications_Content
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Holds notification content for easy manipulation
 */
class ConstantContact_Notification_Content {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Display our notification content for our activation message
	 *
	 * @since   1.0.0
	 */
	public static function activation() {
		$auth_url = add_query_arg( array( 'rmc' => 'wp_admin_connect' ), constant_contact()->api->get_connect_link() );
		$try_url  = add_query_arg( array( 'rmc' => 'wp_admin_try' ), constant_contact()->api->get_signup_link() );

		ob_start();

		?>
		<p class="ctct-notice-intro">
		<?php
			printf(
				esc_attr__( 'Get the most out of the %s plugin &mdash; use it with an active Constant Contact account.', 'constant-contact-forms' ),
				'<strong>' . esc_attr__( 'Constant Contact Forms' ) . '</strong>'
			);
		?>
		</p>
		<p>
			<a href="<?php echo esc_url_raw( $auth_url ); ?>" class="ctct-notice-button button-primary">
				<?php esc_attr_e( 'Connect your account', 'constant-contact-forms' ); ?>
			</a>
			<a href="<?php echo esc_url_raw( $try_url ); ?>" class="ctct-notice-button button-secondary">
				<?php esc_attr_e( 'Try Us Free', 'constant-contact-forms' ); ?>
			</a>

			<?php
				$link_start = sprintf(
					'<a href="%s">',
					admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_about' )
				);
				printf(
					/* translators: placeholders around "Learn More" hold html `<a>` tag. */
					__( '%sLearn More%s about the power of email marketing.', 'constant-contact-forms' ),
					$link_start,
					'</a>'
				)
			?>
		</p>
		<?php

		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Notification content for our 'too many lists' error
	 *
	 * @since 1.0.0
	 *
	 * @return string Notification text.
	 */
	public static function too_many_lists() {
		return __( 'You currently have a large number of lists in your Constant Contact account. You may experience some issues with syncing them.', 'constant-contact-forms' );
	}

	/**
	 * Notification content for opt-in notice.
	 *
	 * @since 1.2.0
	 *
	 * @return string Notification text.
	 */
	public static function optin_admin_notice() {
		add_filter( 'wp_kses_allowed_html', 'constant_contact_filter_html_tags_for_optin' );

		ob_start();
		?>

		<div class="admin-notice-logo">
			<img src="<?php echo constant_contact()->url; ?>/assets/images/ctct-admin-notice-logo.png" alt="<?php esc_attr_e( 'Constant Contact logo', 'constant-contact-forms' ); ?>" />
		</div>
		<div class="admin-notice-message"><h4><?php esc_html_e( 'Constant Contact Forms for WordPress data tracking opt-in', 'constant-contact-forms' ); ?></h4>
			<div><input type="checkbox" id="ctct_admin_notice_tracking_optin" name="ctct_admin_notice_tracking_optin" value="yes" />
			</div>
			<div>
				<?php _e( "Allow Constant Contact to track anonymous data about usage of the Constant Contact Forms plugin.<br/>You can change this opt - in within the plugin's settings page at any time.", 'constant-contact-forms' ); ?>
			</div>
		</div>
		<?php
		$output = ob_get_clean();
		// Be a good citizen, clean up after ourselves.
		#remove_filter( 'wp_kses_allowed_html', 'constant_contact_filter_html_tags_for_optin' );
		return $output;
	}

	/**
	 * Sample update notification for updating to 1.0.1
	 *
	 * @since   1.0.0
	 * @return string notification text Text.
	 */
	public static function v1_0_1() {

		// This is an example of outputting the text for a notification.
		// The @codingStand.. is to suppress PHPCS warnings about commented code
		// @codingStandardsIgnoreLine
		// return __( 'Welcome to v1.0.1 of Constant Contact.', 'constant-contact-forms' );
	}
}

/**
 * Filters in the input to our allowed tags for our admin notice.
 *
 * @since 1.2.0
 *
 * @param array $allowedtags Allowed HTML.
 * @return array Allowed HTML.
 */
function constant_contact_filter_html_tags_for_optin( $allowedtags = array() ){
	$allowedtags['input'] = array( 'type' => true, 'id' => true, 'name' => true, 'value' => true );
	return $allowedtags;
}

/**
 * Adds our opt-in notification to the notification system.
 *
 * @since 1.2.0
 *
 * @param array $notifications Array of notifications pending to show.
 * @return array Array of notifications to show.
 */
function constant_contact_add_optin_notification( $notifications = array() ) {

	$notifications[] = array(
		'ID' => 'optin_admin_notice',
		'callback' => array( 'ConstantContact_Notification_Content', 'optin_admin_notice' ),
		'require_cb' => 'constant_contact_maybe_display_optin_notification',
	);
	return $notifications;
}
add_filter( 'constant_contact_notifications', 'constant_contact_add_optin_notification' );
