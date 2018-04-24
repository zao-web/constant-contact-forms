<?php
/**
 * @package ConstantContact_Tests
 * @subpackage ProcessForm
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Process_Form_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Process_Form' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
