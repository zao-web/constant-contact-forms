<?php
/**
 * @package ConstantContact_Tests
 * @subpackage CPTS
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_CPTS_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_CPTS' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->cpts instanceof ConstantContact_CPTS );
	}
}
