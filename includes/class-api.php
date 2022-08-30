<?php

/**
 * Constant Contact API class.
 *
 * @package ConstantContact
 * @subpackage API
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */
require 'Ctct/autoload.php';

use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Powers connection between site and Constant Contact API.
 *
 * @since 1.0.0
 */
class ConstantContact_API {


	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Access token.
	 *
	 * @since 1.3.0
	 * @var bool
	 */
	protected $access_token = false;
	protected $expires_in   = false;

	private string $last_error = '';
	private string $body       = '';
	private string $host       = '';
	private int $status_code   = 200;
	private string $next       = '';

	private $session_callback = null;

	public bool $PKCE = true;

	public int $this_user_id = 0;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_action( 'init', [ $this, 'cct_init' ] );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function cct_init() {

		$this->this_user_id = get_current_user_id();

		$this->expires_in   = (int) constant_contact()->connect->e_get( '_ctct_expires_in' );
		$this->access_token = constant_contact()->connect->e_get( 'ctct_access_token' );

		if ( ! $this->check_authorization() && $this->access_token ) {
			if ( $this->refresh_the_access_token() ) {
				constant_contact_maybe_log_it( 'AccessToken', 'Successfully exchanged!' );
			}
		}

	}

	/**
	 * Get new instance of ConstantContact.
	 *
	 * @since 1.0.0
	 *
	 * @return object ConstantContact_API.
	 */
	public function cc() {
		return new ConstantContact_Client( $this->get_api_token() );
	}

	/**
	 * Returns API token string to access API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type api key type.
	 * @return string Access API token.
	 */
	public function get_api_token() {

		$url = constant_contact()->connect->get_api_token();

		return $url;
	}

	/**
	 * Checks if the user have privileges
	 *
	 * @since 1.0.0
	 */
	public function check_authorization() {

		$privileges = $this->cc()->get_user_privileges();

		if ( isset( $privileges['error_key'] ) && 'unauthorized' === $privileges['error_key'] ) {
			return false;
		}

		return true;
	}


	/**
	 * Info of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current connected ctct account info.
	 */
	public function get_account_info() {

		if ( ! $this->is_connected() ) {
			return [];
		}

		$acct_data = get_transient( 'constant_contact_acct_info' );

		/**
		 * Filters whether or not to bypass transient with a filter.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $value Whether or not to bypass.
		 */
		$bypass_acct_cache = apply_filters( 'constant_contact_bypass_acct_info_cache', true );

		if ( false === $acct_data || $bypass_acct_cache ) {

			try {

				$acct_data = $this->cc()->get_account_info();

				if ( isset( $acct_data['error_message'] ) ) {
					constant_contact_maybe_log_it( 'Authentication', $acct_data['error_message'] );
					return false;
				} else {
					set_transient( 'constant_contact_acct_info', $acct_data, 1 * HOUR_IN_SECONDS );
				}
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $acct_data;
	}

	/**
	 * Contacts of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current connect ctct account contacts.
	 */
	public function get_contacts() {
		if ( ! $this->is_connected() ) {
			return [];
		}

		$contacts = get_transient( 'ctct_contact' );

		if ( false === $contacts ) {
			try {
				$contacts = $this->cc()->get_contacts( $this->get_api_token() );
				set_transient( 'ctct_contact', $contacts, 1 * HOUR_IN_SECONDS );
				return $contacts;
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $contacts;
	}

	/**
	 * Lists of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_skip_cache Whether or not to skip cache.
	 * @return array Current connect ctct lists.
	 */
	public function get_lists( $force_skip_cache = false ) {

		if ( ! $this->is_connected() ) {
			return [];
		}

		$lists = get_transient( 'ctct_lists' );

		if ( $force_skip_cache ) {
			$lists = false;
		}

		if ( false === $lists ) {

			try {

				$lists = $this->cc()->get_lists();
				$lists = $lists['lists'];

				if ( is_array( $lists ) ) {
					set_transient( 'ctct_lists', $lists, 1 * HOUR_IN_SECONDS );
					return $lists;
				}
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $lists;
	}

	/**
	 * Get an individual list by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id List ID.
	 * @return mixed
	 */
	public function get_list( $id ) {

		$id = esc_attr( $id );

		if ( ! $id ) {
			return [];
		}

		if ( ! $this->is_connected() ) {
			return [];
		}

		$list = get_transient( 'ctct_list_' . $id );

		if ( false === $list ) {
			try {
				$list = $this->cc()->get_list( $id );
				set_transient( 'ctct_lists_' . $id, $list, 1 * HOUR_IN_SECONDS );
				return $list;
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $list;
	}


	/**
	 * Add List to the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_list API data for new list.
	 * @return array Current connect ctct lists.
	 */
	public function add_list( $new_list = [] ) {

		if ( empty( $new_list ) || ! isset( $new_list['id'] ) ) {
			return [];
		}

		$return_list = [];

		try {
			$list = $this->cc()->get_list( esc_attr( $new_list['id'] ) );
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		if ( ! isset( $list[0]['error_key'] ) ) {
			return $list;
		}

		try {

			$list = new ContactList();

			$list->name = isset( $new_list['name'] ) ? esc_attr( $new_list['name'] ) : '';

			/**
			 * Filters the list status to use when adding a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->add_list( $list );
			if ( isset( $return_list[0]['error_message'] ) ) {
				// TODO: check why it's not going to catch
				throw new Exception( $return_list[0]['error_message'] );
			}
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->xdebug_message;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $return_list;
	}

	/**
	 * Update List from the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updated_list api data for list.
	 * @return array current connect ctct list
	 */
	public function update_list( $updated_list = [] ) {

		$return_list = false;

		try {

			$list = new ContactList();

			$list->id       = isset( $updated_list['id'] ) ? esc_attr( $updated_list['id'] ) : '';
			$list->name     = isset( $updated_list['name'] ) ? esc_attr( $updated_list['name'] ) : '';
			$list->favorite = isset( $updated_list['favorite'] ) ? esc_attr( $updated_list['favorite'] ) : false;

			/**
			 * Filters the list status to use when updating a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->update_list( $list );
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $return_list;
	}

	/**
	 * Delete List from the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updated_list API data for list.
	 * @return array Current connect ctct list.
	 */
	public function delete_list( $updated_list = [] ) {

		if ( ! isset( $updated_list['id'] ) ) {
			return [];
		}

		$list = false;

		try {
			$list = $this->cc()->delete_list( $updated_list['id'] );
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $list;
	}

	/**
	 * Create a new contact or update an existing contact.
	 * This method uses the email_address string value you include in the
	 * request body to determine if it should create an new contact or update
	 * an existing contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @param array $new_contact New contact data.
	 * @param int   $form_id     ID of the form being processed.
	 * @return array Current connect contact.
	 */
	public function add_contact( $new_contact = [], $form_id = 0 ) {

		if ( empty( $new_contact ) ) {
			return [];
		}

		if ( ! isset( $new_contact['email'] ) ) {
			return [];
		}

		$api_token = $this->get_api_token();
		$email     = sanitize_email( $new_contact['email'] );

		// Set our list data. If we didn't get passed a list and got this far, just generate a random ID.
		$list = isset( $new_contact['list'] ) ? $new_contact['list'] : 'cc_' . wp_generate_password( 15, false );

		$return_contact = false;

		try {

			// Remove ctct-instance if present to avoid errors.
			if ( array_key_exists( 'ctct-instance', $new_contact ) ) {
				unset( $new_contact['ctct-instance'] );
			}

			$return_contact = $this->create_update_contact( $list, $email, $new_contact, $form_id );

		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		$new_contact = $this->clear_email( $new_contact );
		$new_contact = $this->clear_phone( $new_contact );
		constant_contact_maybe_log_it( 'API', 'Submitted contact data', $new_contact );

		return $return_contact;
	}

	/**
	 * Obfuscate the left side of email addresses at the `@`.
	 *
	 * @since 1.7.0
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	private function clear_email( array $contact ) {
		$clean = [];
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) ) {
				$clean[ $contact_key ] = $this->clear_email( $contact_value );
			} elseif ( is_email( $contact_value ) ) {
				$email_parts           = explode( '@', $contact_value );
				$clean[ $contact_key ] = implode( '@', [ '***', $email_parts[1] ] );
			} else {
				$clean[ $contact_key ] = $contact_value;
			}
		}
		return $clean;
	}

	/**
	 * Obfuscate phone numbers.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since 1.13.0
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	private function clear_phone( array $contact ) {
		$clean = $contact;
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) && ! empty( $contact_value['key'] ) && $contact_value['key'] === 'phone_number' ) {
				$clean[ $contact_key ]['val'] = '***-***-****';
			}
		}
		return $clean;
	}

	/**
	 * Helper method to update contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @throws CtctException API exception?
	 *
	 * @param string|array $list      List name(s).
	 * @param array        $user_data User data.
	 * @param string       $email   email to be updated.
	 * @param string       $form_id   Form ID being processed.
	 * @return mixed                  Response from API.
	 */
	public function create_update_contact( $list, $email, $user_data, $form_id ) {

		$contact = new Contact();

		$contact->email_address = sanitize_text_field( $email );

		$this->add_to_list( $contact, $list );

		try {
			$contact = $this->set_contact_properties( $contact, $user_data, $form_id, true );
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $this->cc()->create_update_contact(
			(array) $contact
		);

	}

	/**
	 * Helper method to push as much data from a form as we can into the
	 * Constant Contact contact thats in a list.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 * @since 1.4.5 Added $updated paramater.
	 *
	 * @param object $contact   Contact object.
	 * @param array  $user_data Bunch of user data.
	 * @param string $form_id   Form ID being processed.
	 * @param bool   $updated   Whether or not we are updating a contact. Default false.
	 * @throws CtctException $error An exception error.
	 * @return object Contact object, with new properties.
	 */
	public function set_contact_properties( $contact, $user_data, $form_id, $updated = false ) {
		if ( ! is_object( $contact ) || ! is_array( $user_data ) ) {
			$error = new CtctException();
			$error->setErrors( [ 'type', esc_html__( 'Not a valid contact to set properties to.', 'constant-contact-forms' ) ] );
			throw $error;
		}

		unset( $user_data['list'] );

		$address   = null;
		$count     = 1;
		$textareas = 0;
		if ( ! $updated ) {
			$contact->notes = [];
		}
		$contact->create_source = 'Contact';
		foreach ( $user_data as $original => $value ) {
			$key   = sanitize_text_field( isset( $value['key'] ) ? $value['key'] : false );
			$value = sanitize_text_field( isset( $value['val'] ) ? $value['val'] : false );

			if ( ! $key || ! $value ) {
				continue;
			}

			switch ( $key ) {
				case 'email':
				case 'website':
					// Do nothing, as we already captured.
					break;
				case 'phone_number':
					$contact->cell_phone = $value;
					break;
				case 'company':
					$contact->company_name = $value;
					break;
				case 'street_address':
				case 'line_2_address':
				case 'city_address':
				case 'state_address':
				case 'zip_address':
					if ( null === $address ) {
						$address = new Ctct\Components\Contacts\Address();
					}

					switch ( $key ) {
						case 'street_address':
							$address->address_type = 'PERSONAL';
							$address->line1        = $value;
							break;
						case 'line_2_address':
							$address->line2 = $value;
							break;
						case 'city_address':
							$address->city = $value;
							break;
						case 'state_address':
							$address->state        = $value;
							$address->country_code = 'us';
							break;
						case 'zip_address':
							$address->postal_code = $value;
							break;
					}
					break;
				case 'birthday_month':
				case 'birthday_day':
				case 'birthday_year':
				case 'anniversery_day':
				case 'anniversary_month':
				case 'anniversary_year':
				case 'custom':
					// Dont overload custom fields.
					if ( $count > 15 ) {
						break;
					}

					// Retrieve our original label to send with API request.
					$original_field_data = $this->plugin->process_form->get_original_fields( $form_id );
					$custom_field_name   = '';
					$should_include      = apply_filters( 'constant_contact_include_custom_field_label', false, $form_id );
					if ( false !== strpos( $original, 'custom___' ) && $should_include ) {
						$custom_field       = ( $original_field_data[ $original ] );
						$custom_field_name .= $custom_field['name'] . ': ';
					}

					$custom = new Ctct\Components\Contacts\CustomField();

					$custom = $custom->create(
						[
							'name'  => 'CustomField' . $count,
							'value' => $custom_field_name . $value,
						]
					);

					$contact->addCustomField( $custom );
					$count++;
					break;
				case 'custom_text_area':
					$textareas++;
					// API version 2 only allows for 1 note for a given request.
					// Version 3 will allow multiple notes.
					if ( $textareas > 1 ) {
						break;
					}
					if ( ! $updated ) {
						$unique_id        = explode( '___', $original );
						$contact->notes[] = [
							'created_date'  => date( 'Y-m-d\TH:i:s' ),
							'id'            => $unique_id[1],
							'modified_date' => date( 'Y-m-d\TH:i:s' ),
							'note'          => $value,
						];
					} else {
						$contact->notes[0]->note .= ' ' . $value;
					}
					break;
				default:
					try {
						$contact->$key = $value;
					} catch ( Exception $e ) {
						$errors   = [];
						$extra    = constant_contact_location_and_line( __METHOD__, __LINE__ );
						$errors[] = $extra . $e->getErrors();
						$this->log_errors( $errors );
						constant_contact_forms_maybe_set_exception_notice( $e );
						break;
					}

					break;
			} // End switch.
		} // End foreach.

		if ( null !== $address ) {
			$contact->addAddress( $address );
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to log errors.
	 *
	 * @param array $errors Errors from API.
	 */
	public function log_errors( $errors ) {
		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				constant_contact_maybe_log_it(
					'API',
					$error
				);
			}
		}
	}

	/**
	 * Process api error response.
	 *
	 * @since 1.0.0
	 * @since 1.8.6 Deprected
	 *
	 * @throws Exception Throws Exception if encountered while attempting to process error message.
	 *
	 * @param array $error API error repsonse.
	 * @return mixed
	 */
	private function api_error_message( $error ) {

		if ( ! isset( $error->error_key ) ) {
			return false;
		}

		constant_contact_maybe_log_it(
			'API',
			$error->error_key . ': ' . $error->error_message,
			$error
		);

		switch ( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				$this->access_token = false;
				return esc_html__( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', 'constant-contact-forms' );
			case 'mashery.not.authorized.over.qps':
				$this->pause_api_calls();
				return;
			default:
				return false;
		}
	}

	/**
	 * Rate limit ourselves to not bust API call rate limit.
	 *
	 * @since 1.0.0
	 */
	public function pause_api_calls() {
		 sleep( 1 );
	}

	/**
	 * Make sure we don't over-do API requests, helper method to check if we're connected.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean If connected.
	 */
	public function is_connected() {
		static $token = null;

		if ( null === $token ) {
			$token = get_option( 'ctct_access_token', false ) ? true : false;
		}

		return $token;
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 * @return string Connect URL.
	 */
	public function get_connect_link() {

		static $proof = null;

		if ( null === $proof ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		return constant_contact()->authserver->do_connect_url( $proof );
	}

	/**
	 * Helper method to output a link for our Account Tab.
	 *
	 * @since 1.0.0
	 * @return string Connect URL.
	 */
	public function get_account_link() {

		return admin_url( 'edit.php' );
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 *
	 * @return string Signup URL.
	 */
	public function get_signup_link() {
		static $proof = null;

		if ( null === $proof ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		return constant_contact()->authserver->do_signup_url( $proof );
	}

	/**
	 * Maybe get the disclosure address from the API Organization Information.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $as_parts If true return an array.
	 * @return mixed
	 */
	public function get_disclosure_info( $as_parts = false ) {
		/*
		 * [
		 *     [name] => Business Name
		 *     [address] => 555 Business Place Ln., Beverly Hills, CA, 90210
		 * ]
		 */

		static $address_fields = [ 'line1', 'city', 'state_code', 'postal_code' ];

		// Grab disclosure info from the API.
		$account_info = $this->get_account_info();

		if ( empty( $account_info ) ) {
			return $as_parts ? [] : '';
		}

		$disclosure = [
			'name'    => empty( $account_info->organization_name ) ? constant_contact_get_option( '_ctct_disclose_name', '' ) : $account_info->organization_name,
			'address' => constant_contact_get_option( '_ctct_disclose_address', '' ),
		];

		if ( empty( $disclosure['name'] ) ) {
			return $as_parts ? [] : '';
		}

		// Determine the address to use for disclosure from the API.
		if (
			isset( $account_info->organization_addresses )
			&& count( $account_info->organization_addresses )
		) {
			$organization_address = array_shift( $account_info->organization_addresses );
			$disclosure_address   = [];

			if ( is_array( $address_fields ) ) {
				foreach ( $address_fields as $field ) {
					if ( isset( $organization_address[ $field ] ) && strlen( $organization_address[ $field ] ) ) {
						$disclosure_address[] = $organization_address[ $field ];
					}
				}
			}

			$disclosure['address'] = implode( ', ', $disclosure_address );
		} elseif ( empty( $disclosure['address'] ) ) {
			unset( $disclosure['address'] );
		}

		if ( ! empty( $account_info->website ) ) {
			$disclosure['website'] = $account_info->website;
		}

		return $as_parts ? $disclosure : implode( ', ', array_values( $disclosure ) );
	}

	public function session( string $key, ?string $value ) {
		if ( $this->session_callback ) {
			return call_user_func( $this->session_callback, $key, $value );
		}
		if ( null === $value ) {
			$value = get_user_meta( $this->this_user_id, $key, true );
			delete_user_meta( $this->this_user_id, $key, $value );

			return $value;
		}

		update_user_meta( $this->this_user_id, $key, $value );

		return $value;
	}

	/**
	 * Add contact to one or more lists.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.9.0
	 * @todo Update addList to use v3
	 *
	 * @param  Contact      $contact Contact object.
	 * @param  string|array $list    Single list ID or array of lists.
	 * @return void
	 */
	private function add_to_list( $contact, $list ) {
		if ( empty( $list ) ) {
			return;
		}

		$list = is_array( $list ) ? $list : [ $list ];

		foreach ( $list as $list_id ) {
			$contact->addListId( esc_attr( $list_id ) );
		}
	}

	/**
	 * Refresh the access token.
	 */
	public function refresh_the_access_token(): bool {

		$this->access_token = constant_contact()->connect->e_get( 'ctct_access_token' );
		constant_contact_maybe_log_it( 'Access Token:', $this->access_token );

		$url   = constant_contact()->authserver->get_auth_server_link();
		$proof = esc_attr( wp_generate_password( 35, false ) );
		// Create full request URL
		$body = [
			'proof' => $proof,
			'type'  => 'refresh_the_accesstoken',
			'site'  => get_site_url(),
		];

		$response = wp_remote_get(
			$url,
			[
				'body'    => $body,
				'timeout' => 20,
			]
		);

		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( $response['body'], true );
		}

		if ( empty( $data ) ) {
			constant_contact_maybe_log_it( 'Refresh Token Error:', 'Problem getting response from middleware' );
			return false;
		}

		constant_contact()->connect->e_set( 'ctct_access_token', $data['token'], true );

		constant_contact_maybe_log_it( 'Refresh', 'Refreshed the token.' );
		$this->access_token = constant_contact()->connect->e_get( 'ctct_access_token' );
		constant_contact_maybe_log_it( 'New Access Token:', $this->access_token );

		return true;

	}

}

/**
 * Helper function to get/return the ConstantContact_API object.
 *
 * @since 1.0.0
 *
 * @return object ConstantContact_API
 */
function constantcontact_api() {
	return constant_contact()->api;
}
