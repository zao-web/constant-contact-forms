<?php
/**
 * @package ConstantContact_Tests
 * @subpackage NotificationContent
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Notification_Content_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Notification_Content' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->notification_content instanceof ConstantContact_Notification_Content );
	}
}
