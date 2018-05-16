<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Check
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Check_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();

		$this->check = new ConstantContact_Check( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Check' ) );
	}

	/**
	 * Test criteria needed to display the debug information for the server.
	 * 1) Test for blank output when nothing is set.
	 * 2) Test for blank output when only is_admin() is true.
	 * 3) Test for blank output when is_admin() is true and user can manage_options.
	 * 4) Test for returned output when is_admin() is true, user can manage_options and
	 *    $_GET[ 'ctct-debug-server-check'] is set.
	 */
	function test_maybe_display_debug_info() {
		// We should not get any output if:
		// 1) The $_GET[ 'ctct-debug-server-check'] is not set.
		// 2) We are not logged in with a user with manage_options capabilities.
		// 3) We are not in the admin area.
		// Expect a blank because we don't meet any of the three criteria.
		ob_start();
		$this->check->maybe_display_debug_info();
		$result = ob_get_clean();
		$this->assertEquals( '', $result, 'No debug output when nothing is set.' );

		// Set the screen as an admin page. But we should still get nothing back.
		set_current_screen( 'edit-post' );
		ob_start();
		$this->check->maybe_display_debug_info();
		$result = ob_get_clean();
		$this->assertEquals( '', $result, 'No debug output when only is_admin() is true.' );

		// Log in as an admin. But since we are still not passing the $_GET variable, we should still get nothing back.
		$admin_id = $this->factory->user->create([
			'role' => 'administrator',
		]);
		wp_set_current_user( $admin_id );
		ob_start();
		$this->check->maybe_display_debug_info();
		$result = ob_get_clean();
		$this->assertEquals( '', $result, "No debug output when \$_GET['ctct-debug-server-check'] is not set." );

		// We should get a response with all criteria met.
		$_GET['ctct-debug-server-check'] = 'display';
		ob_start();
		$this->check->maybe_display_debug_info();
		$result = ob_get_clean();
		$this->assertContains( 'Server Check', $result, 'Debug output should be returned when criteria met.' );
		$this->assertContains( 'Cron Check', $result, 'Debug output should be returned when criteria met.' );
	}

	/**
	 * Test the response from the test_get_checks_to_make function.
	 */
	function test_get_checks_to_make() {
		$response = $this->check->get_checks_to_make();

		$this->assertArrayHasKey( 'functions', $response, 'Main array contains functions key.' );
		$this->assertContains( 'openssl_encrypt', $response['functions'], 'Functions array contains openssl_encrypt.' );
		$this->assertContains( 'openssl_decrypt', $response['functions'], 'Functions array contains openssl_decrypt.' );
		$this->assertArrayHasKey( 'classes', $response, 'Main array contains classes key.' );
		$this->assertContains( 'CMB2', $response['classes'], 'Classes array contains CMB2.' );
		$this->assertContains( 'WDS_Shortcodes', $response['classes'], 'Classes array contains WDS_Shortcodes.' );
	}

	/**
	 * Test the response of the server checks.
	 */
	function test_display_server_checks() {
		ob_start();
		$this->check->display_server_checks();
		$response = ob_get_clean();

		$this->assertContains( '<table class="ctct-server-check">', $response, 'Output includes table markup.' );
		$this->assertContains( 'openssl_encrypt', $response, 'Check includes openssl_encrypt.' );
		$this->assertContains( 'openssl_decrypt', $response, 'Check includes openssl_decrypt.' );
		$this->assertContains( 'CMB2', $response, 'Check includes CMB2.' );
		$this->assertContains( 'WDS_Shortcodes', $response, 'Check includes WDS_Shortcodes.' );
	}

	/**
	 * Test the exists_text method.
	 * 1) Run a test for a function that should exist.
	 * 2) Run a test for a function that should not exist.
	 * 3) Run a test for a class that should exist.
	 * 4) Run a test for a class that should not exist.
	 * 5) Run a test for arbitrary text with no text type passed.
	 */
	function test_exists_text() {
		$function_exists = $this->check->exists_text( 'constant_contact_is_not_connected', 'f' );
		$this->assertEquals( $function_exists, '✅', 'Check if constant_contact_is_not_connected function exists.' );

		$function_exists = $this->check->exists_text( 'constant_contact_is_not_connected1234', 'f' );
		$this->assertEquals( $function_exists, '🚫', 'Check if constant_contact_is_not_connected1234 function exists.' );

		$class_exists = $this->check->exists_text( 'ConstantContact_Check', 'c' );
		$this->assertEquals( $class_exists, '✅', 'Check if ConstantContact_Check class exists.' );

		$class_exists = $this->check->exists_text( 'ConstantContact_Check1234', 'c' );
		$this->assertEquals( $class_exists, '🚫', 'Check if ConstantContact_Check1234 class exists.' );

		$text_exists = $this->check->exists_text( 'Any text', '' );
		$this->assertEquals( $text_exists, '✅', 'Check if arbitrary text (not function or class) exists.' );
	}

	/**
	 * Test the message returned when cron_spawn is called with the DISABLE_WP_CRON constant set.
	 * TODO: Is there any way to set other constants? It is returned when one is set and it seems to already be set so it will always return on the first item.
	 */
	function test_cron_spawn() {
		if ( ! defined( 'DISABLE_WP_CRON' ) ) {
			define( 'DISABLE_WP_CRON', true );
		}
		$this->assertContains( 'The DISABLE_WP_CRON constant is set to true',
			$this->check->cron_spawn(), 'Check returned string when DISABLE_WP_CRON is set.' );
	}

	function teardown() {
		parent::teardown();
	}
}
