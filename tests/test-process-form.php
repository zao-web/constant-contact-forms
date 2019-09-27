<?php
/**
 * @package ConstantContact_Tests
 * @subpackage ProcessForm
 * @author Pluginize
 * @since 1.0.0
 */

use PHPUnit\Framework\TestCase;

class ConstantContact_Process_Form_Test extends TestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Process_Form' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->process_form instanceof ConstantContact_Process_Form );
	}
}
