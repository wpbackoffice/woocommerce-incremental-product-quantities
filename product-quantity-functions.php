<?php 
/*
*	Given the product, this will check which rule is being applied to a product
* 	If there is a rule, the values will be returned otherwise it is inactive 
*	or overridden (from the product meta box).
*
*	@params object	$product WC_Product object
*	@return mixed 	String of rule status / Object top rule post 
*/
function wpbo_get_applied_rule( $product ) {
	
	if ( get_post_meta( $product->id, '_wpbo_deactive', true ) == 'on' ) {
		return 'inactive';
	} elseif ( get_post_meta( $product->id, '_wpbo_override', true ) == 'on' ) {
		return 'override';
	} else {
		return wpbo_get_applied_rule_obj( $product );
	}
}

/*
*	Get the Rule Object thats being applied to a given product.
*	Will return null if no rule is applied.
*
*	@params object	$product WC_Product object
*	@return mixed 	Null if no rule applies / Object top rule post 
*/
function wpbo_get_applied_rule_obj( $product ) {
	
	// Get Product Categories
	$taxonomy = 'product_cat'; 
	$product_terms = wp_get_post_terms( $product->id, $taxonomy );
	
	// Get all Rules
	$args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'post_type'        => 'quantity-rule',
		'post_status'      => 'publish',
	); 
	
	$rules = get_posts( $args );
	$top = null;
	$top_rule = null;
	
	// Loop through the rules and find the ones that apply
	foreach ( $rules as $rule ) {
	 
	 	$apply_rule = false;
	 	
	 	// Get the Rule's Cats and Tags
	 	$cats = get_post_meta( $rule->ID, '_cats' );
	 	
	 	if( $cats != false ) {
		 	$cats = $cats[0];
	 	}

	 	// Loop through the Product's Categories
	 	// If they are in the rule flag it
	 	foreach ( $product_terms as $term ) {
		 	if ( in_array( $term->term_id, $cats )) {
			 	$apply_rule = true;
		 	}
	 	}
	 	
	 	// If the rule applies, check the priority
	 	if ( $apply_rule == true ) {
	 	
	 		$priority = get_post_meta( $rule->ID, '_priority', true );	

	 		if( $priority != '' and $top > $priority or $top == null ) {
	 			$top = $priority;
	 			$top_rule = $rule;
		 	}
		}
	}
	
	return $top_rule;	
}

/*
*	Get the Input Value (min/max/step/priority/all) for a product given a rule
*
*	@params string	$type Product type
*	@params object 	$produt Product Object 
*	@params object	$rule Rule post object
*	@return void 	 
*/
function wpbo_get_value_from_rule( $type, $product, $rule ) {
	
	// Validate $type
	if ( $type != 'min' and $type != 'max' and $type != 'step' and $type != 'all' and $type != 'priority' ) {
		return null;
	}
	
	if ( $rule == null ) {
		return null;
	
	// Return Null if Inactive
	} elseif ( $rule == 'inactive' ) {
		return null;
	
	// Return Product Meta if Override is on
	} elseif ( $rule == 'override' ) {
		
		switch ( $type ) {
			case 'all':
				return array( 
						'min_value' => get_post_meta( $product->id, '_wpbo_minimum', true ),
						'max_value' => get_post_meta( $product->id, '_wpbo_maximum', true ),
						'step' 		=> get_post_meta( $product->id, '_wpbo_step', true )
					);
				break;
			case 'min':
				return get_post_meta( $product->id, '_wpbo_minimum', true );
				break;
			
			case 'max': 
				return get_post_meta( $product->id, '_wpbo_maximum', true );
				break;
				
			case 'step':
				return get_post_meta( $product->id, '_wpbo_step', true );
				break;
				
			case 'priority':
				return null;
				break;
		}		
	
	// Return Values from the Rule based on $type requested
	} else {
	
		switch ( $type ) {
			case 'all':
				return array( 
						'min_value' => get_post_meta( $rule->ID, '_min', true ),
						'max_value' => get_post_meta( $rule->ID, '_max', true ),
						'step' 		=> get_post_meta( $rule->ID, '_step', true ),
						'priority'  => get_post_meta( $rule->ID, '_priority', true )
					);
				break;
				
			case 'min':
				return get_post_meta( $rule->ID, '_min', true );
				break;
			
			case 'max': 
				return get_post_meta( $rule->ID, '_max', true );
				break;
				
			case 'step':
				return get_post_meta( $rule->ID, '_step', true );
				break;
				
			case 'priority':
				return get_post_meta( $rule->ID, '_priority', true );
				break;
		}				
	}
}