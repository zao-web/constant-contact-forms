<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Builder
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Builder_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Builder' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->builder instanceof ConstantContact_Builder );
	}
}
