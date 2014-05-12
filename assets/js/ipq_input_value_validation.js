jQuery(document).ready( function($) {

	/*
	*	Quantity Rule Validation
	*	
	*	If user enters a value that is out of bounds, 
	*	it will be auto corrected to a valid value.
	*/		
	$(".qty").change(function() {

		// Get values from input box
		var new_qty = $(this).val();
		var step = $(this).attr( 'step' );
		var max = $(this).attr( 'max' );
		var min = $(this).attr( 'min' );
		
		// Adjust default values if values are blank
		if ( min == '' || typeof min == 'undefined' ) 
			min = 1;
		
		if ( step == '' || typeof step == 'undefined') 
			step = 1;
		
		// Max Value Validation
		if ( +new_qty > +max && max != '' ) {
			new_qty = max;
		
		// Min Value Validation
		} else if ( +new_qty < +min && min != '' ) {
			new_qty = min;
		}
		
		// Calculate remainder
		var rem = ( new_qty - min ) % step;
				
		// Step Value Value Validation
		if ( rem != 0 ) {
			new_qty = +new_qty + (+step - +rem);
			
			// Max Value Validation
			if ( +new_qty > +max ) {
				new_qty = +new_qty - +step;
			}
		}
				
		// Set the new value
		$(this).val( new_qty );
	});
	
	/*
	*	Make sure minimum equals value 
	*	To Fix: when min = 0 and val = 1 
	*/
	if ( $("body.single-product .qty").val() != $("body.single-product .qty").attr('min') && $("body.single-product .qty").attr('min') != '' ) {
		$("body.single-product .qty").val( $("body.single-product .qty").attr('min') );
	}
	
	/*
	*	Variable Product Support
	*	
	*	Need to overwrite what WC changes with their js
	*/
	
	// Get localized Variables
	if ( typeof ipq_validation !== 'undefined' ) {
		var start_min = ipq_validation.min;
		var start_max = ipq_validation.max;
		var start_step = ipq_validation.step;
	}
	
	// Update input box after variaiton selects are blured
	$('.variations select').bind('blur',function() {
	
		// Update min
		if ( start_min != $('.qty').attr('min') && start_min != '' ) {
			$('.qty').attr('min', start_min );
		}
	
		// Update max
		if ( start_max != $('.qty').attr('max') && start_max != '' ) {
			$('.qty').attr('max', start_max );
		}
		
		// Update step
		if ( start_step != $('.qty').attr('step') && start_step != '' ) {
			$('.qty').attr('step', start_step );
		}
		
		// Make sure intput value is in bounds
		if ( start_min > $('.qty').attr('value') && start_min != '' ) {
			$('.qty').attr('value', start_min );
		}

	});

});
