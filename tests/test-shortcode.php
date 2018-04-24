<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Shortcode
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Shortcode_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Shortcode' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
