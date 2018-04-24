<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Settings
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Settings_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Settings' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
