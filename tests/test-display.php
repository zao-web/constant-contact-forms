<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Display
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Display_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
