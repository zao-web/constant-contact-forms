<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Notifications
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Notifications_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Notifications' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
