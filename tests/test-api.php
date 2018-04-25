<?php
/**
 * @package ConstantContact_Tests
 * @subpackage API
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_API_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
		$this->api = new ConstantContact_API( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_API' ) );
	}

	function test_get_api_token() {

	}

	function test_get_account_info() {

	}

	function test_get_contacts() {

	}

	function test_get_lists() {

	}

	function test_get_list() {

	}

	function test_add_list() {

	}

	function test_update_list() {

	}

	function test_delete_list() {

	}

	function test_add_contact() {

	}

	function test_set_contact_properties() {

	}

	function test_api_error_message() {

	}

	function test_is_connected() {

	}

	function test_get_connect_link() {

	}

	function test_get_signup_link() {

	}

	function test_get_disclosure_info() {

	}

	function teardown() {
		parent::teardown();
	}
}
