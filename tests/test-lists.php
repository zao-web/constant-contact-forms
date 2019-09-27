<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Lists
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Lists_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Lists' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->lists instanceof ConstantContact_Lists );
	}
}
