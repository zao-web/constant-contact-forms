<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Helper_Functions_Test extends TestCase {

	function test_functions_exist() {
		$this->assertTrue( function_exists( 'constant_contact_get_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_display_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_get_forms' ) );
	}

	function test_constant_contact_maybe_display_optin_notification() {

	}

	function test_constant_contact_maybe_display_review_notification() {

	}
}
