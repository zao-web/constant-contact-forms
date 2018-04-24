<?php
/**
 * @package ConstantContact_Tests
 * @subpackage DisplayShortcode
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Display_Shortcode_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();
		$this->ds = new ConstantContact_Display_Shortcode( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display_Shortcode' ) );
	}

	function test_get_form_empty_values() {
		$this->assertEmpty( $this->ds->get_form('non_int') );

		$form = $this->factory->post->create(['post_title' => 'Test Form Display Shortcode Empty']);
		$this->remove_post_meta( $form );

		$this->assertEmpty( $this->ds->get_form( $form ) );
	}

	function test_get_form_has_values() {
		$this->markTestIncomplete();
		$form = $this->factory->post->create(['post_title' => 'Test Form Display Shortcode Filled']);
		$this->assertNotEmpty( $this->ds->get_form( $form ) );
	}

	// Unset meta values.
	function remove_post_meta( $post_id ) {
		$meta = get_post_meta( $post_id );

		$keys = array_keys( $meta );
		foreach( $keys as $key ) {
			delete_post_meta( $post_id, $key );
		}
	}

	function teardown() {
		parent::teardown();
	}
}
