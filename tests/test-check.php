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

	function teardown() {
		parent::teardown();
	}
}
