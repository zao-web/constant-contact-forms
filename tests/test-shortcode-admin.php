<?php
/**
 * @package ConstantContact_Tests
 * @subpackage ShortcodeAdmin
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Shortcode_Admin_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Shortcode_Admin' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
