<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Helper_Functions_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

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

	/**
	 * Test the function that retrieves contact forms by getting an empty set then
	 * creating some forms and getting them back.
	 */
	function test_constant_contact_get_forms() {

		// Start with no forms created, confirm empty results.
		$forms = constant_contact_get_forms();
		$this->assertEmpty( $forms, 'There should be nothing returned for forms.' );

		// Create some forms.
		$this->factory->post->create_many( 5, array(
			'post_type'  => 'ctct_forms',
			'meta_input' => [
				'_ctct_description' => 'Test description',
			],
		) );

		// Confirm found results.
		$forms = constant_contact_get_forms();

		$this->assertCount( 5, $forms, 'We should get back 5 forms that we just created.' );
	}

	function test_constant_contact_display_shortcode() {
		$this->assertEquals(
			'[ctct form="1"]',
			constant_contact_display_shortcode(1)
		);
	}

	function test_constant_contact_has_redirect_uri() {
		$ctctform = $this->factory->post->create(['post_title' => 'Test Form Has Redirect URI']);

		$this->assertFalse( constant_contact_has_redirect_uri( $ctctform ) );

		update_post_meta( $ctctform, '_ctct_redirect_uri', 'https://www.constantcontact.com' );
		$this->assertTrue( constant_contact_has_redirect_uri( $ctctform ) );
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
		$dirty = constant_contact_clean_url( [] );

		$this->assertTrue( is_array( $dirty ) );

		$cleanurl = constant_contact_clean_url( 'www.constantcontact.com' );
		$this->assertEquals(
			'http://www.constantcontact.com',
			$cleanurl
		);

		$cleanurl1 = constant_contact_clean_url( 'constantcontact.com' );
		$this->assertEquals(
			'http://constantcontact.com',
			$cleanurl1
		);

		$cleanurl2 = constant_contact_clean_url( 'https://www.constantcontact.com' );
		$this->assertEquals(
			'https://www.constantcontact.com',
			$cleanurl2
		);

		$cleanurl3 = constant_contact_clean_url( 'http://www.constantcontact.com' );
		$this->assertEquals(
			'http://www.constantcontact.com',
			$cleanurl3
		);

		// @todo Test is_ssl usage somehow. Pass in non-https version of domain, receive https version. Need to remember that is_ssl() is for the WP install, and not any ole url.
	}

	function test_constant_contact_debugging_enabled() {
		// Default state.
		$this->assertFalse(
			constant_contact_debugging_enabled()
		);

		cmb2_update_option( constant_contact()->settings->key, '_ctct_logging', 'on' );

		// Should be true as we've set the option
		$this->assertTrue(
			constant_contact_debugging_enabled()
		);

		// Should be false because we unset the setting again.
		cmb2_update_option( constant_contact()->settings->key, '_ctct_logging', '' );
		$this->assertFalse(
			constant_contact_debugging_enabled()
		);

		// Our legacy constant is set, so we should have debugging enabled.
		define( 'CONSTANT_CONTACT_DEBUG_MAIL', true );
		$this->assertTrue(
			constant_contact_debugging_enabled()
		);

		// We should have our constant set AND our debugging mode enabled.
		cmb2_update_option( constant_contact()->settings->key, '_ctct_logging', 'on' );
		$this->assertTrue(
			constant_contact_debugging_enabled()
		);
	}

	function teardown() {
		parent::teardown();
	}
}
