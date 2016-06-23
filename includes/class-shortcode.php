<?php
/**
 * ConstantContact_Shortcode class
 *
 * @package ConstantContactShortcode
 * @author Pluginize
 * @since 1.0.0
 */

if ( class_exists( 'WDS_Shortcodes', false ) && ! class_exists( 'ConstantContact_Shortcode', false ) ) {

	/**
	 * ConstantContact_Shortcode
	 *
	 * Sets up shortcode
	 */
	class ConstantContact_Shortcode extends WDS_Shortcodes {

		/**
		 * The Shortcode Tag
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $shortcode = 'ctct';

		/**
		 * Default attributes applied tot he shortcode.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $atts_defaults = array();

		/**
		 * Shortcode Output
		 *
		 * @since 1.0.0
		 * @return string shortcode html
		 */
		public function shortcode() {

			// Attributes.
			$atts = shortcode_atts( array( 'form' => '' ), $this->shortcode_object->atts );

			// Bail if we don't have a form set.
			if ( ! isset( $atts['form'] ) ) {
				return;
			}

			// Grab all post meta.
			$meta = get_post_meta( $atts['form'] );

			// Bail if we didn't get meta.
			if ( ! $meta ) {
				return;
			}

			// Pass our data into our field method.
			$form_data = $this->get_field_meta( $meta );

			// Return our markup.
			return constant_contact()->display->form( $form_data );

		}

		/**
		 * Proccess cmb2 options into form data array
		 *
		 * @since 1.0.0
		 * @param  array $form_meta post meta.
		 * @return array  form field data
		 */
		public function get_field_meta( $form_meta ) {

			// Bail if we don't have form meta.
			if ( empty( $form_meta ) || ! is_array( $form_meta ) ) {
				return false;
			}

			// Data verificiation for our custom fields group.
			if (
				isset( $form_meta['custom_fields_group'] ) &&
				$form_meta['custom_fields_group'] &&
				isset( $form_meta['custom_fields_group'][0] )
			) {
				// If we passed all the checks, try to grab the data.
				return $this->get_field_values( $form_meta['custom_fields_group'][0], $form_meta );
			}
		}

		/**
		 * Get custom field values from post meta data from form CPT post
		 *
		 * @author Brad Parbs
		 * @param  array $custom_fields custom fields to parse through.
		 * @return string                form field markup
		 */
		public function get_field_values( $custom_fields, $full_data ) {

			// We may get serialized data, so undo that.
			$custom_fields = maybe_unserialize( $custom_fields );

			// Loop through each of our fields.
			foreach ( $custom_fields as $key => $value ) {

				// Make sure we have the parts of our array that we expect
				if (
					! isset( $custom_fields ) ||
					! isset( $custom_fields[ $key ] )
				) {
					continue;
				}

				// Set our field name, if we can.
				if ( isset( $custom_fields[ $key ]['_ctct_field_name'] ) ) {
					$fields['fields'][ $key ]['name'] = $custom_fields[ $key ]['_ctct_field_name'];
				}

				// Set our field mapping, if we can.
				if ( isset( $custom_fields[ $key ]['_ctct_map_select'] ) ) {
					$fields['fields'][ $key ]['map_to'] = $custom_fields[ $key ]['_ctct_map_select'];
				}

				// Set our field description, if we can.
				if ( isset( $custom_fields[ $key ]['_ctct_field_desc'] ) ) {
					$fields['fields'][ $key ]['description'] = $custom_fields[ $key ]['_ctct_field_desc'];
				}

				// Set our field type, hardcoded for now.
				$fields['fields'][ $key ]['type'] = 'text_field';

				// Set our field requirement, if we can.
				if (
					isset( $custom_fields[ $key ]['_ctct_required_field'] ) &&
					'on' == $custom_fields[ $key ]['_ctct_required_field']
				) {
					$fields['fields'][ $key ]['required'] = true;
				} else {
					$fields['fields'][ $key ]['required'] = false;
				}
			}

			// Now that we've finished checking all of our form fields, we'll
			// want to set some general form information here.

			if ( isset( $full_data['_ctct_description'] ) && isset( $full_data['_ctct_description'][0] ) ) {
				$fields['options']['description'] = $full_data['_ctct_description'][0];
			}

			if ( isset( $full_data['_ctct_list'] ) && isset( $full_data['_ctct_list'][0] ) ) {
				$fields['options']['list'] = $full_data['_ctct_list'][0];
			}

			if (
				isset( $full_data['_ctct_opt_in'] ) &&
				$full_data['_ctct_opt_in'] &&
				isset( $full_data['_ctct_opt_in'][0] ) &&
				$full_data['_ctct_opt_in'][0] &&
				'on' === $full_data['_ctct_opt_in'][0]
			) {

				$fields['options']['opt_in'] = true;

				if (
					isset( $full_data['_ctct_opt_in_instructions'] ) &&
					isset( $full_data['_ctct_opt_in_instructions'][0] )
				) {
					$fields['options']['opt_in_instructions'] = $full_data['_ctct_opt_in_instructions'][0];
				}
			} else {
				$fields['options']['opt_in'] = false;
			}

			return $fields;
		}

		/**
		 * Override for attribute getter
		 *
		 * You can use this to override specific attribute acquisition
		 * ex. Getting attributes from options, post_meta, etc...
		 *
		 * @see WDS_Shortcode::att
		 *
		 * @since 1.0.0
		 * @param string      $att	 Attribute to override.
		 * @param string|null $default Default value.
		 * @return string
		 */
		public function att( $att, $default = null ) {
			$current_value = parent::att( $att, $default );
			return $current_value;
		}
	}

}
