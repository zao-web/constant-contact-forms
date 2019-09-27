<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Middleware
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Middleware_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Middleware' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->authserver instanceof ConstantContact_Middleware );
	}
}
