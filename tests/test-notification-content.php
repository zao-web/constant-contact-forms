<?php
/**
 * @package ConstantContact_Tests
 * @subpackage NotificationContent
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Notification_Content_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Notification_Content' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
