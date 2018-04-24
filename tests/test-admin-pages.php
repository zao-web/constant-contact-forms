<?php
/**
 * @package ConstantContact_Tests
 * @subpackage AdminPages
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Admin_Pages_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin_Pages' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
