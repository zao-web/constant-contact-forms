<?php

use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;

/**
 * ConstantContact_Connect
 *
 * @package ConstantContactConnect
 * @author Pluginize
 * @since 1.0.0
 */
class ConstantContact_Connect {

	/**
	 * Option key, and option page slug
 	 *
	 * @var string
	 */
	private $key = 'ctct_options_connect';

	/**
	 * Settings page metabox id
	 *
	 * @var string
	 */
	private $metabox_id = 'ctct_option_metabox_connect';

	/**
	 * Settings Page title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Settings Page hook
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * CtctOAuth2 object
	 *
	 * @var object
	 */
	private $oauth = '';

	/**
	 * Holds an instance of the project
	 *
	 * @ConstantContact_Connect
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Get the running object
	 *
	 * @return ConstantContact_Connect
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );

		// Override CMB's getter
		add_filter( 'cmb2_override_option_get_'. $this->key, array( $this, 'get_override' ), 10, 2 );
		// Override CMB's setter
		add_filter( 'cmb2_override_option_save_'. $this->key, array( $this, 'update_override' ), 10, 2 );
	}

	/**
	 * Register our setting to WP
	 * @since  1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );

		// Instantiate the CtctOAuth2 class.
		$this->oauth = new CtctOAuth2( '595r3d4q432c3mdv2jtd3nj9', 'XJ9H8n5m8fqt2WBpSk6E6dJm', get_site_url() . '/constantcontact/?auth=ctct' );
	}

	/**
	 * Add menu options page
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'Connect', constant_contact()->text_domain ),
			__( 'Connect', constant_contact()->text_domain ),
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

		// add_action( "admin_head-{$this->options_page}", array( $this, 'enqueue_js' ) );
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0.0
	 */
	public function admin_page_display() {

		?>
		<style>
		.wp-core-ui .button-primary {
			display: block;
			margin: 20px 0;
		}
		.ctct-logo {
			max-width: 400px;
		}
		.ctct-description {
			max-width: 400px;
		}
		</style>
		<script>
			jQuery.noConflict();
			(function($) {
				$(document).ready(function() {
					$( '#ctct_option_metabox_connect' ).submit( function(e) {
						e.preventDefault();
						window.location.href = '<?php echo esc_url_raw( $this->oauth->getAuthorizationUrl() . '&oauthSignup=true' ); ?>';
					});
				});
			})(jQuery);
		</script>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">

			<?php if ( constantcontact_get_api_token() ) : ?>
				<div class="message notice">
					<p><?php esc_attr_e( 'Account connected to Constant Contact', constant_contact()->text_domain ); ?></p>
				</div>
				<input type="button" class="button-primary" value="Disconnect">

			<?php else : ?>
				<img class="ctct-logo" src="<?php echo constant_contact()->url . '/assets/images/constant-contact-logo.png'?>">
				<p class="ctct-description">
					Click the connect button and login or sign up to Constant Contact. By connecting, you authorize this plugin to access your account on Constant Contact.
				</p>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key, array(
					'save_button' => __( 'Connect to Constant Contact', constant_contact()->text_domain ),
				) ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  1.0.0
	 */
	function add_options_page_metabox() {

		$prefix = 'ctct_connect';

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'		 => $this->metabox_id,
			'hookup'	 => false,
			'cmb_styles' => false,
			'show_on'	=> array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

	}

	/**
	 * Register settings notices for display
	 *
	 * @since  1.0.0
	 * @param  int   $object_id Option key.
	 * @param  array $updated   Array of updated fields.
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Replaces get_option with get_site_option
	 *
	 * @since  1.0.0
	 */
	public function get_override( $test, $default = false ) {
		return get_site_option( $this->key, $default );
	}

	/**
	 * Replaces update_option with update_site_option
	 *
	 * @since  1.0.0
	 */
	public function update_override( $test, $option_value ) {
		return update_site_option( $this->key, $option_value );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  1.0.0
	 * @param  string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Myprefix_Network_Admin object
 *
 * @since  1.0.0
 * @return ConstantContact_Connect object
 */
function ctct_connect_admin() {
	return ConstantContact_Connect::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  1.0.0
 * @param  string $key Options array key.
 * @return mixed Option value
 */
function ctct_get_connect_option( $key = '' ) {
	return cmb2_get_option( myprefix_admin()->key, $key );
}

// Get it started.
ctct_connect_admin();

/**
 * Returns api token string to access api
 *
 * @return string api token
 */
function constantcontact_get_api_token() {
	return false;
}
