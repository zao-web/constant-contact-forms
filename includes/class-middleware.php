<?php
/**
 * Constant Contact Middleware.
 *
 * @package ConstantContact
 * @subpackage Middleware
 * @author Constant Contact
 * @since 1.0.1
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers our OAuth connection to the middleware Constant Contact server.
 *
 * @since 1.0.1
 */
class ConstantContact_Middleware {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.1
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.1
	 *
	 * @param object $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get our auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @param string $proof      Proof.
	 * @param array  $extra_args Array of extra arguements.
	 * @return string Auth server link.
	 */
	public function do_connect_url( $proof = '', $extra_args = [] ) {

		$auth_server_link = $this->get_auth_server_link();

		if ( ! $auth_server_link ) {
			return '';
		}

		return $this->add_query_args_to_link( $auth_server_link, $proof, $extra_args );
	}

	/**
	 * Build out our signup version of the connect url.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $proof Proof key.
	 * @return string        Signup/connect url.
	 */
	public function do_signup_url( $proof = '' ) {
		return $this->do_connect_url( $proof, [ 'new_signup' => true ] );
	}

	/**
	 * Add our query args for proof and site callback to our auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @param string $link       Auth server link.
	 * @param string $proof      Proof value.
	 * @param array  $extra_args Array of extra args to append.
	 * @return string
	 */
	public function add_query_args_to_link( $link, $proof, $extra_args = [] ) {
		$return = add_query_arg(
			[
				'ctct-auth'  => 'auth',
				'ctct-proof' => esc_attr( $proof ),
				'ctct-site'  => get_site_url(),
			],
			$link
		);

		if ( ! empty( $extra_args ) ) {
			$return = add_query_arg( $extra_args, $return );
		}

		return $return;
	}

	/**
	 * Gets our base auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @return string URL of auth server base.
	 */
	public function get_auth_server_link() {
		return 'https://ctctmwv3.wdslab.com';
	}

	/**
	 * Generates a random key, saves to the DB and returns it.
	 *
	 * @since 1.0.1
	 *
	 * @return string proof key
	 */
	public function set_verification_option() {

		static $proof = null;

		if ( null === $proof ) {
			$proof = esc_attr( wp_generate_password( 35, false ) );
			update_option( 'ctct_connect_verification', $proof );
		}

		return $proof;
	}

	/**
	 * Verify a returned request from the auth server, and save the returned token.
	 *
	 * @since  1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to verify request.
	 *
	 * @return boolean Is valid?
	 */
	public function verify_and_save_access_token_return() {
		$proof = filter_input( INPUT_GET, 'proof', FILTER_SANITIZE_STRING );

		constant_contact_maybe_log_it( 'Proof Returned: ', $proof );

		$token         = filter_input( INPUT_GET, 'token', FILTER_SANITIZE_STRING );
		$key           = filter_input( INPUT_GET, 'key', FILTER_SANITIZE_STRING );
		$token_expiry  = filter_input( INPUT_GET, 'token_expiry', FILTER_SANITIZE_STRING );
		$refresh_token = filter_input( INPUT_GET, 'refresh_token', FILTER_SANITIZE_STRING );

		// If we get this, we'll want to start our process of
		// verifying the proof that the middleware server gives us
		// so that we can ignore any malicious entries that are sent to us
		// Sanitize our expected data.
		$proof         = ! empty( $proof ) ? sanitize_text_field( $proof ) : false;
		$token         = ! empty( $token ) ? sanitize_text_field( $token ) : false;
		$key           = ! empty( $key ) ? sanitize_text_field( $key ) : false;
		$token_expiry  = ! empty( $token_expiry ) ? sanitize_text_field( $token_expiry ) : false;
		$refresh_token = ! empty( $refresh_token ) ? sanitize_text_field( $refresh_token ) : false;

		// If we're missing any piece of data, we failed.
		if ( ! $proof || ! $token || ! $key || ! $refresh_token ) {
			constant_contact_maybe_log_it( 'Authentication', 'Proof, token, refresh token or key missing for access verification.' );
			return false;
		}

		if ( ! $this->verify_proof( $proof ) ) {
			constant_contact_maybe_log_it( 'Authentication', 'Authorization verification failed.' );
			return false;
		}

		constant_contact_maybe_log_it( 'Authentication', 'Authorization verification succeeded.' );

		// securing the tokens
		constant_contact()->connect->e_set( 'ctct_access_token', sanitize_text_field( $token ), true );
		constant_contact()->connect->e_set( 'ctct_refresh_token', sanitize_text_field( $refresh_token ), true );
		constant_contact()->connect->e_set( '_ctct_expires_in', sanitize_text_field( $token_expiry ) );

		return true;
	}

	/**
	 * Verifies a given proof from a request against our DB, and does cleanup.
	 *
	 * @since 1.0.0
	 *
	 * @param string $proof Proof string to check.
	 * @return boolean Whether or not its our expected proof.
	 */
	public function verify_proof( $proof ) {

		$expected_proof = get_option( 'ctct_connect_verification' );
		constant_contact_maybe_log_it( 'Proof Sent: ', $expected_proof );
		delete_option( 'ctct_connect_verification' );

		return ( $proof === $expected_proof );
	}
}
