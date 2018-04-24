<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Middleware
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Middleware_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Middleware' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
