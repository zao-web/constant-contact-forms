<?php
/**
 * @package ConstantContact_Tests
 * @subpackage BuilderFields
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Builder_Fields_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Builder_Fields' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->builder_fields instanceof ConstantContact_Builder_Fields );
	}
}
