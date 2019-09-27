<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Updates
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Updates_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Updates' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->updates instanceof ConstantContact_Updates );
	}
}
