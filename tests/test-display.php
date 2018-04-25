<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Display
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Display_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();

		$this->display = new ConstantContact_Display( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display' ) );
	}

	function test_form_no_POST() {
		$this->markTestIncomplete();

		/**
		 * Create form.
		 * Set to pending review status
		 * test empty string return
		 * Somehow attempt to match form output
		 */
	}

	function test_form_with_POST() {
		$this->markTestIncomplete();
		/*
		 * Need to fake having $_POST data since we process submissions in the method. Worth investigating changing.
		 * Rest is same as test_form_no_POST
		 */
	}

	function test_add_verify_fields() {
		$this->markTestIncomplete();
		/*
		 * Pass in incomplete array data
		 * Test for false
		 * Pass in array of valid data.
		 * Force bad form_id
		 * test for false return;
		 * Pass in array of valid data plus valid form_id
		 * Set _ctct_verify_key meta value
		 * confirm matching markup return.
		 */
	}

	function test_build_form_fields() {
		$this->markTestIncomplete();
		/*
		 * dataprovider!
		 *
		 * pass in null $form_data
		 * Test for empty string return
		 * Pass in options index for $form_data and make sure 'form_id' value set.
		 * test without description and with description.
		 * check description method output.
		 *
		 * set fields index in $form_data, confirm key/value values.
		 * set fields to match our primary field types first
		 * set fields to match available field types.
		 * compare opt in method return value.
		 *
		 * get markup concatenator for not worrying about whitespace and such.
		 */
	}

	function test_field() {
		$this->markTestIncomplete();
		/**
		 * Oh boy
		 *
		 * Dataprovider!
		 */
	}

	function test_get_submitted_value() {
		$this->markTestIncomplete();
		/**
		 * If $value exists, test matching return value. It returns early
		 * If submitted_vals param is not an array, return empty string.
		 *
		 * Need to try setting $_POST values as well.
		 */
	}

	/**
	 * Test for return of paragraph with associated classes and passed message.
	 */
	function test_message() {

		$custom_class      = 'test';
		$custom_message    = 'This is a test';
		$generated_message = $this->display->message( $custom_class, $custom_message );

		$this->assertContains( 'class="ctct-message ' . $custom_class, $generated_message,
			'Generated message contains the custom class.' );
		$this->assertContains( $custom_message, $generated_message,
			'Generated message contains custom message.' );
	}

	function test_description() {
		$this->markTestIncomplete();

		/**
		 * Test return of span with class and message value.
		 * Set current user with edit_posts caps
		 * Test return and include checking for edit form link.
		 */
	}

	function teardown() {
		parent::teardown();
	}
}
