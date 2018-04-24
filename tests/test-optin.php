<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Mail
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Optin_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Optin' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->optin instanceof ConstantContact_Optin );
	}

	function test_can_track() {
		$optin = constant_contact()->optin;
		// replace this with some actual testing code
		$this->assertFalse( $optin->can_track() );
	}
}
