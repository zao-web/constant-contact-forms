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
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_API' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
