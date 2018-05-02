<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Updates
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_User_Customizations_Test extends WP_UnitTestCase {

	function setup() {
		parent::setup();

		$this->user_customizations = new ConstantContact_User_Customizations( constant_contact() );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_User_Customizations' ) );
	}

	/**
	 * Test the returned success message from a submission.
	 * 1) Test getting the default message back from the form.
	 * 2) Test getting a custom message back when overriding the default message.
	 */
	function test_process_form_success() {
		$success_message = 'Test success message';
		$custom_message  = 'This is a custom message';

		// Create a form to test with.
		$form_id = $this->factory->post->create( array(
			'post_type' => 'ctct_forms',
			'meta_input' => [
				'_ctct_form_submission_success' => 'Test success message',
			],
		) );

		$result = $this->user_customizations->process_form_success( '', $form_id );
		$this->assertEquals( $result, $success_message, "With no custom, we should get the form's success message." );

		$result = $this->user_customizations->process_form_success( $custom_message, $form_id );
		$this->assertEquals( $result, $success_message,
			'With a custom message passed in, we should get the custom message back.'
		);

	}

	function teardown() {
		parent::teardown();
	}
}
