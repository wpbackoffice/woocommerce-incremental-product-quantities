<?php 
/*
*	Filter Minimum Quantity Value for Input Boxes for Cart
*
*	@access public 
*	@param  int 	default
*	@param  obj		product
*	@return int		step
*
*/							
add_filter( 'woocommerce_quantity_input_min', 'wpbo_input_min_value', 1, 2);

function wpbo_input_min_value( $default, $product ) {
	
	// Return Defaults if it isn't a simple product
	if( $product->product_type != 'simple' ) {
		return $default;
	}
	
	// Get Rule
	$rule = wpbo_get_applied_rule( $product );
	
	// Get Value from Rule
	$min = wpbo_get_value_from_rule( 'min', $product, $rule );

	// Return Value
	if ( $min == '' or $min == null ) {
		return $default;
	} else {
		return $min;
	}
}

/*
*	Filter Maximum Quantity Value for Input Boxes for Cart
*
*	@access public 
*	@param  int 	default
*	@param  obj		product
*	@return int		step
*
*/
add_filter( 'woocommerce_quantity_input_max', 'wpbo_input_max_value', 1, 2);

function wpbo_input_max_value( $default, $product ) {	
	
	// Return Defaults if it isn't a simple product
	if( $product->product_type != 'simple' ) {
		return $default;
	}
	
	// Get Rule
	$rule = wpbo_get_applied_rule( $product );
	
	// Get Value from Rule
	$max = wpbo_get_value_from_rule( 'max', $product, $rule );

	// Return Value
	if ( $max == '' or $max == null ) {
		return $default;
	} else {
		return $max;
	}
}

/*
*	Filter Step Quantity Value for Input Boxes woocommerce_quantity_input_step for Cart
*
*	@access public 
*	@param  int 	default
*	@param  obj		product
*	@return int		step
*
*/
add_filter( 'woocommerce_quantity_input_step', 'wpbo_input_step_value', 1, 2);

function wpbo_input_step_value( $default, $product ) {
	
	// Return Defaults if it isn't a simple product
	if( $product->product_type != 'simple' ) {
		return $default;
	}
	
	// Get Rule
	$rule = wpbo_get_applied_rule( $product );
	
	// Get Value from Rule
	$step = wpbo_get_value_from_rule( 'step', $product, $rule );

	// Return Value
	if ( $step == '' or $step == null ) {
		return $default;
	} else {
		return $step;
	}
}	

/*
*	Filter Step, Min and Max for Quantity Input Boxes on product pages
*
*	@access public 
*	@param  array 	args
*	@param  obj		product
*	@return array	vals
*
*/
add_filter( 'woocommerce_quantity_input_args', 'wpbo_input_set_all_values', 1, 2 );

function wpbo_input_set_all_values( $args, $product ) {

	// Return Defaults if it isn't a simple product
	if( $product->product_type != 'simple' ) {
		return $args;
	}
	
	// Get Rule
	$rule = wpbo_get_applied_rule( $product );
	
	// Get Value from Rule
	$values = wpbo_get_value_from_rule( 'all', $product, $rule );

	if ( $values == null ) {
		return $args;
	}
	
	$vals = array();
	
	$vals['input_name'] = 'quantity';
	if ( $values['min_value'] != ''  ) {
		$args['min_value'] 	 = $values['min_value'];
	} elseif ( $values['min_value'] == '' and $values['step'] != '' ) {
		$args['min_value'] 	 = $values['step'];
	} else {
		$args['min_value'] 	 = $args['min_value'];
	}

	if ( $values['max_value'] != '' ) {
		$args['max_value'] = $values['max_value'];
	} else {
		$args['max_value'] = $args['max_value'];
	}
	
	if ( $values['step'] != '' ) {
		$args['step'] = $values['step'];
	} else {
		$args['step'] = $args['step'];
	}

	return $args;
}
