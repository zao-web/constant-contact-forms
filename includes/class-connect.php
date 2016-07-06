<?php
/**
 * ConstantContact_Connect
 *
 * @package ConstantContact_Connect
 * @author Pluginize
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

/**
 * Class ConstantContact_Connect
 */
class ConstantContact_Connect {

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 * @since  1.0.0
	 */
	private $key = 'ctct_options_connect';

	/**
	 * CtctOAuth2 object
	 *
	 * @var object
	 * @since  1.0.0
	 */
	private $oauth = '';

	/**
	 * Api Error message
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $error_message = '';

	/**
	 * Current page redirect Url
	 *
	 * @var string
	 * @since  1.0.0
	 */
	private $redirect_url = '';

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	public $should_encrypt = false;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'maybe_connect' ) );

		add_action( 'admin_init', array( $this, 'maybe_disconnect' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Watches for our specific $_GET paramaters and if we get a connect request,
	 * pass it to our auth server class to process
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function maybe_connect() {

		// If we have this get, we may be getting an connect attempt, so lets
		// verify it and potentially process it
		if ( isset( $_GET['cc_connect_attempt'] ) && is_user_logged_in() ) {

			// Call our access token processing
			constant_contact()->authserver->verify_and_save_access_token_return();
		}
	}

	/**
	 * init oauth
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init_oauth() {
		$this->redirect_url = add_query_arg(
			array(
				'post_type' => 'ctct_forms',
				'page'      => 'ctct_options_connect',
			),
			admin_url( 'edit.php' )
		);

		// Instantiate the CtctOAuth2 class.
		$oath_connect = new CtctOAuth2(
			constant_contact()->api->get_api_token( 'CTCT_APIKEY' ),
			constant_contact()->api->get_api_token( 'CTCT_SECRETKEY' ),
			add_query_arg( array( 'auth' => 'ctct' ), get_site_url() )
		);

		// Make sure that the connect worked before setting as class prop
		if ( $oath_connect ) {
			$this->oauth = $oath_connect;
		}
	}

	/**
	 * Add menu options page
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'Connect', 'constantcontact' ),
			__( 'Connect', 'constantcontact' ),
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  1.0.0
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		// Only run if logged in user can manage site options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$this->save_oauth();

		wp_enqueue_style( 'constant-contact-oath', constant_contact()->url() . 'assets/css/oath.css' );

		wp_localize_script( 'ctct_form', 'texts', array( 'disconnect_confirm' => __( 'Are you sure you want to disconnect?', 'constantcontact' ) ) );

		wp_enqueue_script( 'ctct_form' );
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>">
			<?php if ( constantcontact_api()->get_api_token() ) : ?>
				<div class="message notice">
					<p>
						<?php esc_html_e( 'Account connected to Constant Contact.', 'constantcontact' ); ?>
					</p>
				</div>

				<form method="post" action="<?php echo esc_url( $this->redirect_url ); ?>">
					<?php wp_nonce_field( 'ctct-admin-disconnect', 'ctct-admin-disconnect' ); ?>
					<input type="hidden" id="ctct-disconnect" name="ctct-disconnect" value="true">
					<input type="submit" class="button-primary ctct-disconnect" value="<?php esc_html_e( 'Disconnect', 'constantcontact' ); ?>">
				</form>

			<?php else : ?>
				<p class="ctct-description">
					<?php esc_html_e( 'To take full advantage of the Constant Contact for WordPress plugin, we recommend having either an active account or an active free trial with Constant Contact. Click the "Try Us Free" button to sign up for a free 60-day trial. Or click the "Connect Plugin" button to log in to your Constant Contact account (by connecting, you authorize this plugin to access your account) ', 'constantcontact' ); ?>
				</p>
				<a class="button button-primary" href="https://www.constantcontact.com"><?php esc_attr_e( 'Try Us Free', 'constantcontact' ); ?></a>
				<?php

				// Get our middleware link
				$auth_link = constant_contact()->authserver->do_connect_url();

				// If we have a link, then display the connect button
				if ( $auth_link ) { ?>
					<a href="<?php echo $auth_link ?>" class="button-primary ctct-connect">
						<?php esc_html_e( 'Connect Plugin', 'constantcontact' ); ?>
					</a>
				<?php } ?>

				<p class="ctct-description">
					<?php esc_html_e( 'NOTE: It is possible to use the plugin without an active Constant Contact account or trial.  In this scenario you will be able to create forms within a WordPress site, but submitted form completions will be individually emailed to the Site Admin.', 'constantcontact' ); ?>
				</p>
			<?php endif; ?>

		</div>
		<?php
	}

	public function save_oauth() {
		$this->init_oauth();
		$response = false;

		// If the 'code' query parameter is present in the uri, the code can exchanged for an access token.
		if ( isset( $_GET['code'] ) && is_admin() ) {
			try {
				$response = $this->oauth->getAccessToken( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );
			} catch ( OAuth2Exception $ex ) {
				constant_contact()->api->log_errors( $ex->getErrors() );
			}
		}

		// Save auth token to options.
		if ( $response && isset( $response['access_token'] ) && $response['access_token'] ) {
			$this->update_token( sanitize_text_field( $response['access_token'] ) );
		}
	}

	/**
	 * Disconnect from api
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public function maybe_disconnect() {

		// Only run if logged in user can manage site options.
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Make sure we ahve our nonce key
		if ( ! isset( $_POST['ctct-admin-disconnect'] ) ) {
			return;
		}

		// Make sure we want to disconnect
		if ( ! isset( $_POST['ctct-disconnect'] ) ) {
			return;
		}

		// Verify that nonce
		if ( wp_verify_nonce( $_POST['ctct-admin-disconnect'], 'ctct-admin-disconnect' ) ) {

			// Delete access token.
			delete_option( 'ctct_token' );
		}
	}

	/**
	 * Get an encrypted value
	 *
	 * @since  1.0.0
	 * @param  string $key key to save to
	 */
	public function e_get( $check_key, $fallback_to_ctct_opt = false ) {

		if ( ! constant_contact()->is_encryption_ready() ) {
			return get_option( $check_key, '' );
		}

		// Get our key
		$key = $this->get_encrpyt_key();

		// Get our saved token
		if ( $fallback_to_ctct_opt ) {

			// if we want to fallback, we'll get the nested option
			$options = get_option( 'ctct_options_settings', false );
			if ( $options && isset( $options[ $check_key ] ) ) {
				$encrypted_token = $options[ $check_key ];
			} else {
				return false;
			}
		} else {

			// Otherwise get normal option
			$encrypted_token = get_option( $check_key, false );

			// Make sure we have something
			if ( ! $encrypted_token ) {
				return false;
			}
		}

		try {
			// Try to decrypt it
			$return = Crypto::decrypt( $encrypted_token, $key );
		} catch ( Exception $e ) {
			// otherwise just return the raw val
			$return = '';
		}

		// Return data
		return $return;

	}

	/**
	 * Set an encrypted value
	 *
	 * @since  1.0.0
	 * @param  string $key  key to save to
	 * @param  string $data data to save
	 */
	public function e_set( $check_key, $data ) {

		if ( ! constant_contact()->is_encryption_ready() ) {
			update_option( $check_key, $data );
			return $data;
		}

		// Get our key
		$key = $this->get_encrpyt_key();

		// Encrypt
		$saved = Crypto::encrypt( $data, $key );

		// Save option
		update_option( $check_key, $saved );

		// Return data
		return $saved;
	}

	/**
	 * Secure API access token
	 *
	 * @since  1.0.0
	 * @param string $access_token api access token.
	 * @return void
	 */
	private function update_token( $access_token ) {
		return $this->e_set( 'ctct_token', $access_token );
	}

	/**
	 * Get saved API token
	 *
	 * @since  1.0.0
	 * @return string token
	 */
	public function get_api_token() {

		// Clean up our old tokens
		$this->check_deleted_legacy_token();

		return $this->e_get( 'ctct_token' );
	}

	/**
	 * If we have a legacy token, let's re-save it
	 *
	 * @since  1.0.0
	 */
	public function check_deleted_legacy_token() {

		// Get our old token
		$legacy = get_option( '_ctct_token' );

		// If we got a legacy value, reencrypt and delete it
		if ( $legacy ) {
			// Update our token with our legacy data
			$this->update_token( $legacy );
			delete_option( '_ctct_token' );
		}
	}

	/**
	 * Get our encrypt key
	 *
	 * @since  1.0.0
	 * @return string key to use for encrypt
	 */
	public function get_encrpyt_key() {

		if ( ! constant_contact()->is_encryption_ready() ) {
			return 'ctct_key';
		}

		// Get our key
		$key = get_option( 'ctct_key', false );

		// If we don't have one, make one
		if ( ! $key ) {
			$key = $this->generate_and_save_key();
		}

		// return it
		return Key::loadFromAsciiSafeString( $key );
	}

	/**
	 * Generates and saves a new key
	 *
	 * @since  1.0.0
	 * @return object key
	 */
	public function generate_and_save_key() {

		// If we can't run encryption stuff, then don't.
		if ( ! constant_contact()->is_encryption_ready() ) {
			return 'ctct_key';
		}

		$key = Key::createNewRandomKey();
		$key = $key->saveToAsciiSafeString();
		$updated = update_option( 'ctct_key', $key );

		if ( ! $updated ) {
			$key = $this->generate_and_save_key();
		}

		return $key;
	}
}
