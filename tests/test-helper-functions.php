<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Helper_Functions_Test extends WP_UnitTestCase {

	function test_constant_contact_is_connected() {
		$this->markTestIncomplete();
		/*
			test initial state with no connection made
			make connection and set values that we use to test if is connected
			test post-connection state.
		*/
	}

	function test_constant_contact_is_not_connected() {
		$this->markTestIncomplete();
		/*
			similar to constant_contact_is_not_connected() test
			confirm not connect
			make connection, confirm that state.
			remove connection and reconfirm not connected state
		*/
	}

	function test_constant_contact_get_forms() {
		$this->markTestIncomplete();
		/*
			Start with no forms created, confirm empty results
			create some forms.
			confirm found results.
		*/
	}

	function test_constant_contact_display_shortcode() {
		$this->assertEquals(
			'[ctct form="1"]',
			constant_contact_display_shortcode(1)
		);
	}

	function test_constant_contact_has_redirect_uri() {
		/*
		Create form
		add invalid url post meta
		test return value
		overwrite invalid url post meta with valid url post meta
		test return value
		*/
	}

	function test_constant_contact_check_timestamps() {
		$this->markTestIncomplete();
		/*
		First parameter is current evaluated status
		second parameter is an array with a specific key.

		Grab current time, subtract 60 seconds from timestamp, pass in as 2nd param
		grab fresh current time, add 60 seconds to timestamp, pass in as 2nd param

		*/
	}

	function test_constant_contact_clean_url() {
		/*
		Pass in non-string, verify non-string returned untouched.

		Pass in potentially invalid url, see what esc_url expects/checks for for invalid, confirm clean result
		pass in valid url and confirm match returned.

		How to set is_ssl()?

		set ssl to true, pass in http://, confirm https:// result

		*/
	}

	function test_constant_contact_debugging_enabled() {
		/*
		Test default state.

		Set ctct_get_settings_option( '_ctct_logging' ) setting to 'on'
		test result.

		unset option
		define CONSTANT_CONTACT_DEBUG_MAIL to true
		test result.

		re-set logging option
		test result
		*/
	}
}
