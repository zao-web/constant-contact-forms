<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Check
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Check_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Check' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->check instanceof ConstantContact_Check );
	}
}
