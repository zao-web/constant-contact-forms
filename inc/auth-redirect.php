<?php
/**
 * Auth Redirect
 *
 * @package ConstantContactConnect
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Add auth params to query_vars
 *
 * @param array $vars url params.
 */
function constantcontact_rewrite_add_var( $vars ) {
	$vars[] = 'auth';
	$vars[] = 'code';
	$vars[] = 'username';
	return $vars;
}
add_filter( 'query_vars', 'constantcontact_rewrite_add_var' );

/**
 * Check for query params and redirect
 *
 * @return void
 */
function constantcontact_rewrite_catch() {
	global $wp_query;

	// Only run if logged in user can manage site options.
	if ( ! current_user_can( 'manage_options' ) ) { return; }

	if ( isset( $wp_query->query_vars['code'] ) && 'ctct' === $wp_query->query_vars['auth'] && ! is_admin() ) {

		// Add auth token to options.
		update_option( '_ctct_token', $wp_query->query_vars['code'] );

		// Create a redirect back to connect page.
		$path = add_query_arg( array(
			'post_type' => 'ctct_forms',
			'page' => 'ctct_options_connect',
			'code' => $wp_query->query_vars['code'],
			'user' => $wp_query->query_vars['username'],
		) );
		wp_safe_redirect( admin_url( 'edit.php' . $path ) );
		exit;
	}
}
add_action( 'template_redirect', 'constantcontact_rewrite_catch' );
