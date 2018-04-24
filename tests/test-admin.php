<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Admin_Test extends WP_UnitTestCase {

	/**
	 * Debatable how much of this class we need to test. It's primarily just filtering to display extra data and
	 * setting up options pages.
	 */

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
