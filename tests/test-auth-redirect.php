<?php
/**
 * @package ConstantContact_Tests
 * @subpackage AuthRedirect
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Auth_Redirect_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Auth_Redirect' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->auth_redirect instanceof ConstantContact_Auth_Redirect );
	}
}
