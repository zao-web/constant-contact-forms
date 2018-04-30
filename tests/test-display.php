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

	/**
	 * Test the function to add a verify field.
	 * 1) Test passing incomplete data.
	 * 2) Test passing a bad form id.
	 * 3) Test passing valid information.
	 */
	function test_add_verify_fields() {

		$form_data        = array();
		$verify_key_value = 'verify_key_value';

		/*
		 * Pass in incomplete array data
		 * Test for false
		 */
		$this->assertFalse(
			$this->display->add_verify_fields( $form_data ),
			'Calling add_verify_fields with incomplete form data should return false'
		);

		/*
		 * Pass in array of valid data.
		 * Force bad form_id
		 * test for false return
		 */
		$form_data = [
			'options' => [
				'form_id' => 'bad_form_id',
			],
		];
		$this->assertFalse(
			$this->display->add_verify_fields( $form_data ),
			'Calling add_verify_fields with bad form id should return false'
		);

		/*
		 * Pass in array of valid data plus valid form_id
		 * Set _ctct_verify_key meta value
		 * confirm matching markup return.
		 */

		// Create a form to use.
		$form_id = $this->factory->post->create( array(
			'post_title' => 'Test Form',
			'post_type'  => 'ctct_forms',
			'meta_input' => [
				'_ctct_verify_key' => $verify_key_value,
			],
		) );

		$form_data = [
			'options' => [
				'form_id' => $form_id,
			],
		];

		$valid_result = $this->display->add_verify_fields( $form_data );

		// The returned output should contain a value equal to our set $verify_key_value.
		$this->assertContains( 'value="' . $verify_key_value . '"', $valid_result,
			'Returned input contains the verify key as the value.' );

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

		$test_value = 'test';

		// If the $value (1st parameter) is not blank, we should get the same value back.
		$this->assertEquals( $this->display->get_submitted_value( $test_value, 'test', array(), array() ),
			$test_value,
			'Returns false if no value is sent.'
		);

		// If $submitted_values (4th parameter) is not an array, we should get an empty string.
		$this->assertEquals( $this->display->get_submitted_value( '', 'test', array(), 'string, not an array' ),
			'',
			'Return a blank string if $submitted_vals is not an array.'
		);

		$submitted_name      = 'Bart 2';
		$_POST['first_name'] = $submitted_name; // Submitted name.

		$results = $this->display->get_submitted_value( '', 'first_name',
			array( 'name' => 'first_name' ), // Array of fields.
			array( // Submitted values.
				array(
					'key'  => 'first_name',
					'name' => 'first_name_field_name',
				),
			)
		);
		$this->assertEquals( $results, $submitted_name, 'Submitted value for first_name should be returned.' );
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
