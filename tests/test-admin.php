<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Admin_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin' ) );
	}

	function test_class_access() {

		$this->assertTrue( constant_contact()->admin instanceof ConstantContact_Admin );
	}
}
