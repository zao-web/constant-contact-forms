<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Connect
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Connect_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Connect' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->connect instanceof ConstantContact_Connect );
	}
}
