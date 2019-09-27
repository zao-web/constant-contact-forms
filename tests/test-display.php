<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Display
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Display_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->display instanceof ConstantContact_Display );
	}
}
