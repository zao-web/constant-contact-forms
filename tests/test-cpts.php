<?php
/**
 * @package ConstantContact_Tests
 * @subpackage CPTS
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_CPTS_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
		$this->cpts = new ConstantContact_CPTS( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_CPTS' ) );
	}

	function test_post_types_exist() {
		$this->markTestIncomplete();

		$this->assertTrue( post_type_exists( 'ctct_forms' ) );

		// Test pre-connection.
		$this->assertFalse( post_type_exists( 'ctct_lists' ) );

		update_option( 'ctct_token', 'insignificant string' );

		$this->cpts->lists_post_type();
		$this->assertTrue( post_type_exists( 'ctct_lists' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
