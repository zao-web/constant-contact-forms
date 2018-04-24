<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Updates
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Updates_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Updates' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
