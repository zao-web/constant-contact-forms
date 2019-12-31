<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;
use Mockery;

class ConstantContact_Helper_Functions_Test extends TestCase {

	protected function setUp() {
		parent::setUp();

		require_once './includes/helper-functions.php';
	}

	function test_functions_exist() {
		$this->assertTrue( function_exists( 'constant_contact_get_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_display_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_get_forms' ) );
	}

	function test_constant_contact_maybe_display_optin_notification() {
		$this->assertFalse( constant_contact_maybe_display_optin_notification() );

		$screen1       = new \stdClass();
		$screen1->base = 'thing';
		$screen2 = new \stdClass();
		$screen2->base = 'dashboard';
		\WP_Mock::userFunction( 'get_current_screen', [
			'return_in_order' => [
				[],
				$screen1,
				$screen2,
			],
		] );

		\WP_Mock::userFunction( 'current_user_can', [
			'return_in_order' => [
				false,
				true,
			],
		] );

		// Each call will invoke get_option twice. By the time get_option is used, we have 3 more cases to test. Probably need 6 return values.
		// At same time though, we don't need to test both versions of our get_options.
		\WP_Mock::userFunction( 'get_option', [
			'return_in_order' => [
				[ '_ctct_data_tracking' => 'on' ],
				'',
				'value',
				'',
			],
		] );

		// get_current_screen isn't an object.
		#$this->assertFalse( constant_contact_maybe_display_optin_notification() );
		// get_current_screen base isn't `dashboard`.
		#$this->assertFalse( constant_contact_maybe_display_optin_notification() );

		// user can't manage options.
		#$this->assertFalse( constant_contact_maybe_display_optin_notification() );
		// _ctct_data_tracking is set to "on".
		#$this->assertFalse( constant_contact_maybe_display_optin_notification() );
		// ctct_privacy_policy_status is not empty.
		#$this->assertFalse( constant_contact_maybe_display_optin_notification() );
		// end of function.
		#$this->assertTrue( constant_contact_maybe_display_optin_notification() );
	}

	function test_constant_contact_maybe_display_review_notification() {

	}

	function test_constant_contact_display_shortcode() {
		$expected = '[ctct form="1"]';

		$actual = constant_contact_display_shortcode( 1 );
		$this->assertSame( $expected, $actual );
	}

	function test_constant_contact_maybe_display_exceptions_notice() {
		\WP_Mock::userFunction( 'get_option', [
			'return_in_order' => ['true', 'false'],
		] );

		$this->assertTrue( constant_contact_maybe_display_exceptions_notice() );
		$this->assertFalse( constant_contact_maybe_display_exceptions_notice() );
	}
}
