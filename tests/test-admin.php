<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Admin_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
