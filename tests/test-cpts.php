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
		$this->assertTrue( post_type_exists( 'ctct_forms' ) );

		// Test pre-connection.
		$this->assertFalse( post_type_exists( 'ctct_list' ) );

		// @todo Find a way to connect and re-run test.
		#$this->assertTrue( post_type_exists( 'ctct_list' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
