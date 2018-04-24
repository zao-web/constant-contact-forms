<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Mail
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Optin_Test extends WP_UnitTestCase {

	function setup() {
		$this->optin = new ConstantContact_Optin( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Optin' ) );
	}

	function test_class_access() {
		$this->markTestIncomplete();
		$this->assertTrue( constant_contact()->optin instanceof ConstantContact_Optin );
	}

	function test_can_track() {
		// Initial state
		$this->assertFalse( $this->optin->can_track(), 'No options set.' );

		// Data tracking added
		cmb2_update_option( constant_contact()->settings->key, '_ctct_data_tracking', 'on' );
		$this->assertFalse( $this->optin->can_track(), 'Data tracking, but no privacy policy.' );

		// Privacy policy set but not string true
		update_option( 'ctct_privacy_policy_status', 'false' );
		$this->assertFalse( $this->optin->can_track(), 'Privacy policy not string true' );

		// Privacy policy set but not string period
		update_option( 'ctct_privacy_policy_status', [] );
		$this->assertFalse( $this->optin->can_track(), 'Privacy policy not string' );

		update_option( 'ctct_privacy_policy_status', 'true' );
		$this->assertTrue( $this->optin->can_track() );
	}
}
