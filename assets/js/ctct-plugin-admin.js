window.CTCTBuilder = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
	}

	// Cache all the things.
	that.cache = function() {

		that.$c = {
			window: $( window ),
			body:   $( 'body' ),
			hide:   '.cmb2-id--ctct-new-list',
		};

		that.$c.optinfields = {
			list     : $( '.cmb2-id--ctct-list' ),
			default  : $( '.cmb2-id--ctct-opt-in-default' ),
			hide     : $( '.cmb2-id--ctct-opt-in-hide' ),
			instruct : $( '.cmb2-id--ctct-opt-in-instructions' ),
		}
	}

	// Triggers our leave warning if we modify things in the form
	that.triggerLeaveWarning = function() {
		$( window ).bind( 'beforeunload', function(){
			return ctct_texts.leavewarning;
		});
	}

	// Removes our binding of our leavce warning
	that.unbindLeaveWarning = function() {
		$( window ).unbind( 'beforeunload' );
	}

	// Combine all events.
	that.bindEvents = function() {

		// Trigger before saving post
		$( '#post' ).submit( function () {

			// Make sure our email dropdown reverts from disbled, as CMB2 doesn't save those values
			$( '.ctct-email-disabled' ).removeClass( 'disabled' ).prop( 'disabled', false );

			// Unbind our leave warning, so we don't trigger it when we shouldn't.
			that.unbindLeaveWarning();
		});

		// Make description non-draggable, so we don't run into weird cmb2 issues
		$( '#ctct_description_metabox h2.hndle' ).removeClass( 'ui-sortable-handle, hndle' );

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {
			that.modifyFields();

			// Trigger our leave warning
			that.triggerLeaveWarning();
		});

		// If we get a row added, then do our stuff
		$( document ).on( 'cmb2_add_row', function() {

			// Automatically set new rows to be 'custom' field type
			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'custom' );

			// Modfiy the field we need to modify
			that.modifyFields();

			// Trigger our leave warning
			that.triggerLeaveWarning();
		});

		// If we modify the opt in checkbox, then toggle fields if we have to
		$( '#_ctct_opt_in' ).change( function() {
			that.toggleOptInFields();
		});

		// On load, toggle our optin fields and run our new row
		// functionality to apply to all saved values
		that.modifyFields();
		that.toggleOptInFields();
    }

    // Toggle un-needed optin fields if we're not showing the opt-in
    that.toggleOptInFields = function() {

    	// If checked, show them
    	if ( $( '#_ctct_opt_in' ).prop( 'checked' ) ) {
    		that.$c.optinfields.list.show();
    		that.$c.optinfields.default.show();
    		that.$c.optinfields.hide.show();
    		that.$c.optinfields.instruct.show();
    	} else {
    		that.$c.optinfields.list.hide();
    		that.$c.optinfields.default.hide();
    		that.$c.optinfields.hide.hide();
    		that.$c.optinfields.instruct.hide();
    	}
    }

	// Disable required email fields.
	that.modifyFields = function() {

		// Set that we haven't found an email
		var foundEmail = false;

		// Loop through all fields to modify them
		$( '#cmb2-metabox-ctct_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping' ).each( function( key, value ) {

			// Set some of our helper paramaters
			var $field_parent = $( this ).find( '.cmb-field-list' );
			var $button       = $( $field_parent ).find( '.cmb-remove-group-row' );
			var $required     = $( $field_parent ).find( '.required input[type=checkbox]' );
			var $requiredRow  = $required.closest( '.cmb-row' );
			var $map          = $( $field_parent ).find( '.map select option:selected' );
			var $mapName      = $map.text();
			var $fieldTitle   = $( this ).find( 'h3' );

			// Set our field row to be the name of the selected option
			$fieldTitle.text( $mapName );

			// If we haven't yet found an email field, and this is our email field
			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {

				// Set that we found an email field
				foundEmail = true;

				// Make it required
				$required.prop( 'checked', true );

				// Set it to be 'disabled'
				$( value ).find( 'select' ).addClass( 'disabled ctct-email-disabled' ).prop( 'disabled', true );

				// Hide the required row
				$requiredRow.hide();

				// Hide the remove row button
				$button.hide();

			} else {

				// Verify its not disabled
				$( value ).find( 'select' ).removeClass( 'disabled ctct-email-disabled' ).prop( 'disabled', false );

				// If we're not an email field, reshow the required field
				$requiredRow.show();

				// and the remove button
				$button.show();
			}

		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );



/**
 * Modal Script
 * 
 * @author jomurgel
 */
window.Modal_Object = {};
( function( window, $, app ) {

    // Private variable
    var modal = document.getElementsByClassName('modal');

    // Constructor
    app.init = function() {
        app.cache();

        if ( app.meetsRequirements() ) {
            app.bindEvents();
        }
    };

    // Cache all the things
    app.cache = function() {
        app.$c = {
            window: $(window),
            modalSelector: $( '.modal' ),
        };
    };

    // Combine all events
    app.bindEvents = function() {
        app.$c.window.on( 'load', app.doModal );
    };

    // Do we meet the requirements?
    app.meetsRequirements = function() {
        return app.$c.modalSelector.length;
    };

    // Some function
    app.doModal = function() {
        $(".modal-close").click(function(){
	        $('.modal').removeClass('modal-open');
	    });
    };

    // Engage
    $( app.init );

})( window, jQuery, window.Modal_Object );
window.CTCTSupport = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
	}

	// Cache all the things.
	that.cache = function() {
		that.$c = {
			window: $( window ),
			body: $( 'body' ),
			hide: '.answer',
		};
	}

	// Combine all events.
	that.bindEvents = function() {
		$( that.$c.hide ).hide();
        // Show fields based on selection
        $('.question').on( 'click', function(e) {
			that.metaShowHide( $(this).next('.answer') );
		});
    }

    // Function to handle which items should be showing/hiding
    that.metaShowHide = function(showem) {
        var hideThese = $( that.$c.hide ).not(showem);
        showem.slideDown('fast');
        hideThese.hide();
    }

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTSupport );

window.CTCTForms = {};
	( function( window, $, that ) {

		// Constructor.
		that.init = function() {
			that.cache();
			that.bindEvents();
		}

		// Cache all the things.
		that.cache = function() {
			that.$c = {
				window: $( window ),
				body: $( 'body' ),
				disconnect: '.ctct-disconnect',
			};
		}

		// Combine all events.
		that.bindEvents = function() {

            $( that.$c.disconnect ).on( 'click', function(e) {
				confirm( ctct_texts.disconnectconfirm );
			});
        }

		// Engage!
		$( that.init );

})( window, jQuery, window.CTCTForms );
