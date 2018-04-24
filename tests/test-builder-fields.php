<?php
/**
 * @package ConstantContact_Tests
 * @subpackage BuilderFields
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Builder_Fields_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Builder_Fields' ) );
	}

	function teardown() {
		parent::teardown();
	}
}
