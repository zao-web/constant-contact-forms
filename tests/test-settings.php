<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Settings
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Settings_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Settings' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->settings instanceof ConstantContact_Settings );
	}
}
