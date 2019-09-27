<?php
/**
 * @package ConstantContact_Tests
 * @subpackage DisplayShortcode
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Display_Shortcode_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display_Shortcode' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->display_shortcode instanceof ConstantContact_Display_Shortcode );
	}
}
