<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Mail
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Mail_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Mail' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
