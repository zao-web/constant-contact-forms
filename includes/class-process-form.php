<?php
/**
 * ConstantContact_Process_Form class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Process_Form
 */
class ConstantContact_Process_Form {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'cmb2_init', array( $this, 'process_form' ) );
	}

	/**
	 * Process submitted form data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_form() {

		$notice = array();

	    // If the submit button is clicked, send the email.
	    if ( isset( $_POST['ctct-submitted'] ) ) {

			foreach ( $_POST as $key => $value ) {
			    if ( isset( $key ) ) {
					if ( 'ctct-email' === $key ) {
						$email = sanitize_email( $key );
					} else {
						${$key} = sanitize_text_field( $value );
					}
				}
			}

			if ( isset( $_POST['ctct-opti-in'] ) ) {

				$args = array(
					'email' => sanitize_email( $_POST['ctct-email'] ),
					'list' => sanitize_text_field( $_POST['ctct-opti-in'] ),
					'first_name' => 'test name',
					'last_name' => '',
				);

				$contact = constantcontact_api()->add_contact( $args );

				if ( $contact ) {
					set_transient( 'ctct_form_submit_message', 'success' );
				} else {
					set_transient( 'ctct_form_submit_message', 'error' );
				}
			} else {

		        // // sanitize form values
		        // $name   = isset( $_ctct_first_name ) ? $_ctct_first_name : '';
				// $subject = '';
				// $message = '';
				//
		        // // get the blog administrator's email address
		        // $to = get_option( 'admin_email' );
		        // $headers = "From: $name <$email>" . "\r\n";

		        // // If email has been process for sending, display a success message
		        // if ( wp_mail( $to, $subject, $message, $headers ) ) {
		        //     echo '<div>';
		        //     echo '<p>Thanks for contacting me, expect a response soon.</p>';
		        //     echo '</div>';
		        // } else {
		        //     echo 'An unexpected error occurred';
		        // }

			}
		}
	}

/**
 * Form submit success/error messages.
 *
 * @since 1.0.0
 * @return void
 */
function ctct_form_submit_message( $mode = 'echo ') {

	if ( $message = get_transient( 'ctct_form_submit_message' ) ) {

		switch ( $message ) {
			case 'success':
				$message_text = __( 'Your message has been sent!', 'constantcontact' );
			break;
			case 'error':
				$message_text = __( 'Your message failed to send!', 'constantcontact' );
			break;
		}

		$return = sprintf( '<p class="message ' . esc_attr( $message ) . '"> %s </p>', esc_attr( $message_text ) );

		delete_transient( 'ctct_form_submit_message' );

		if ( 'echo' == $mode ) {
			echo $return;
		} else {
			return $return;
		}
	}
}

/**
 * Build form fields for shortcode
 *
 * @since 1.0.0
 * @param  array $form_data formulated cmb2 data for form.
 * @return void
 */
function ctct_build_form_fields( $form_data, $display_mode = 'echo' ) {

	// start our wrapper return var
	$return = '';

	// Check to see if we have a description for the form, and display it
	if (
		isset( $form_data['options'] ) &&
		isset( $form_data['options']['description'] ) &&
		$form_data['options']['description']
	) {
		constant_contact()->display->description( esc_attr( $form_data['options']['description'] ) );
	}

	// Loop through each of our form fields and output it
	foreach ( $form_data['fields'] as $key => $value ) {

		$required = isset( $form_data['fields'][ $key ]['required'] ) ? ' * required' : '';

		$return .= '<div><p><label>' . esc_attr( $form_data['fields'][ $key ]['name'] ) . esc_attr( $required ) . '</label></br>';

		$field_name = esc_attr( $form_data['fields'][ $key ]['map_to'] );
		$field_value = ( isset( $_POST[ 'ctct-' . $form_data['fields'][ $key ]['map_to'] ] ) ? esc_attr( $_POST[ 'ctct-' . $form_data['fields'][ $key ]['map_to'] ] ) : '' );

		switch ( $form_data['fields'][ $key ]['map_to'] ) {

			case 'email':
					$return .= '<input type="email" required name="ctct-' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" tabindex="1" size="40"></p></div>';
			break;
			default:
					$return .= '<input type="text" pattern="[a-zA-Z0-9 ]+" name="ctct-' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" tabindex="1" size="40"></p></div>';
			break;

		}
	}

	if ( isset( $form_data['options']['opt_in'] ) && isset( $form_data['options']['list'] ) ) {
			$return .= '<div><p>';
				$return .= '<input type="checkbox" name="ctct-opti-in" value="' . esc_attr( $form_data['options']['list'] ) . '"/>';
				$return .= esc_attr( $form_data['options']['opt_in'] );
			$return .= '</p></div>';
	}

	if ( 'echo' == $display_mode ) {
		echo $return;
	} else {
		return $return;
	}

}


function ctct_validate_form_fields( $form_data ) {
	return true;
}
