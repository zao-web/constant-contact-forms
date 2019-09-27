<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Mail
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Mail_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Mail' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->mail instanceof ConstantContact_Mail );
	}
}
