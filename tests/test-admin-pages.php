<?php
/**
 * @package ConstantContact_Tests
 * @subpackage AdminPages
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Admin_Pages_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin_Pages' ) );
	}

	function test_class_access() {

		$this->assertTrue( constant_contact()->admin_pages instanceof ConstantContact_Admin_Pages );
	}
}
