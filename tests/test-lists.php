<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Lists
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Lists_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Lists' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
