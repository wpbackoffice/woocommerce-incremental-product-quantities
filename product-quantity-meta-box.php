<?php
/*
*	Creates a custom metabox for products to get
*	Minimum Quantites and Step Sizes.
*/

/*
*	Register Rule Meta Box for Product Page
*/
add_action( 'add_meta_boxes', 'wpbo_meta_box_create' );

function wpbo_meta_box_create() {
	
	add_meta_box(
		'wpbo_product_info', 
		__('Product Quantity Rules', 'woocommerce'), 
		'wpbo_product_info', 
		'product', 
		'normal', 
		'high' 
	);
}

/*
*	Display Rule Meta Box
*/
function wpbo_product_info( $post ) {
	global $product;
	global $woocommerce;
	
	// Get the product and see what rules are being applied
	$pro = get_product( $post );
	$rule_result = wpbo_get_applied_rule( $pro );

	// Only Show information if the product is 'Simple'
	if ( $pro->product_type != 'simple' ):
		echo "<div class='rule-message not-simple'>Sorry but you can only apply rules to 'Simple Products'.</div>";
	else:
		// If there isn't a rule mark rule as null, otherwise get the id
		if ( $rule_result != 'inactive' and $rule_result != 'override' ) {
			$rule = $rule_result;
			$values = wpbo_get_value_from_rule( 'all', $pro, $rule );
		} else {
			$rule = $rule_result;
		}
	
		// Display Rule Being Applied
		if ( $rule == 'inactive' ) {
			echo "<div class='inactive-rule rule-message'>No rule is being applied becasue you've deactivted the plugin for this product.</div>";
		} elseif ( $rule == 'override' ) {
			echo "<div class='overide-rule rule-message'>The values below are being used because you've chosen to override any rules for this product.</div>";
		} elseif ( ! isset( $rule->post_title ) or $rule->post_title == null ) {
			echo "<div class='no-rule rule-message'>The values below will be used becasue there is not rule currently being applied to this product.</div>";
		} else { ?>
			<div class="active-rule">
				<span>Active Rule:</span>
				<a href='<?php echo get_edit_post_link( $rule->ID ) ?>'>
					<?php echo $rule->post_title ?>
				</a>
			</div>
	
			<div class="rule-meta">			
				<span class="meta-value-title">Step Value:</span> 
				<?php if ( $values['step'] == '' ) {
						echo '<span class="meta-value-single">No Step Value</span>';
					} else { 
						echo '<span class="meta-value-single">' . $values['step'] . '</span>'; 
					} 
				?>
				<span class="meta-value-title">Minimum Quantity:</span>
				<?php if ( $values['min_value'] == '' ) {
						echo '<span class="meta-value-single">No Minimum Value</span>';
					} else { 
						echo '<span class="meta-value-single">' . $values['min_value'] . '</span>'; 
					} 
				?>
				<span class="meta-value-title">Maximum Quantity:</span>
				<?php if ( $values['max_value'] == '' ) {
						echo '<span class="meta-value-single">No Maximum Value</span>';
					} else { 
						echo '<span class="meta-value-single">' . $values['max_value'] . '</span>'; 
					} 
				?>
				<span class="meta-value-title">Priority Level:</span>
				<?php if ( $values['priority'] == '' ) {
						echo '<span class="meta-value-single">No Priority Level</span>';
					} else { 
						echo '<span class="meta-value-single">' . $values['priority'] . '</span>'; 
					} 
				?>
			</div>
		<?php
		}
	
		// Get the current values if they exist
		$deactive  = get_post_meta( $post->ID, '_wpbo_deactive', true );
		$step  = get_post_meta( $post->ID, '_wpbo_step',     true );
		$min   = get_post_meta( $post->ID, '_wpbo_minimum',  true );
		$max   = get_post_meta( $post->ID, '_wpbo_maximum',  true );
		$over  = get_post_meta( $post->ID, '_wpbo_override', true );
		
		// Create Nonce Field
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_product_rule_nonce' );
		
		// Print the form ?>	
		<div class="rule-input-boxes">
			<input type="checkbox" name="_wpbo_deactive" <?php if ( $deactive == 'on' ) echo 'checked'; ?> />
			<span>Deactivate Quantity Rules on this Product?</span>
			
			<input type="checkbox" name="_wpbo_override" <?php if ( $over == 'on' ) echo 'checked'; ?> />
			<span>Override Quantity Rules with Values Below</span>
			
			<label for="_wpbo_step">Step Value</label>
			<input type="number" name="_wpbo_step" value="<?php echo $step; ?>" />
			
			<label for="_wpbo_minimum">Minimum Quantity</label>
			<input type="number" name="_wpbo_minimum" value="<?php echo $min; ?>" />
			
			<label for="_wpbo_maximum">Maximum Quantity</label>
			<input type="number" name="_wpbo_maximum" value="<?php echo $max; ?>" />
		</div>
		<p><em>*Note - the minimum value must be greater then or equal to the step value.</em></p>
		<?php
	endif; // Product Type Must Equal Simple
}

/*
*	Handle Saving Meta Box Data
*/
add_action( 'save_post', 'wpbo_save_quantity_meta');

function wpbo_save_quantity_meta( $post_id ) {

	// Validate Post Type
	if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'product' ) {
		return;
	}
	
	// Validate User
	if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Verify Nonce
    if ( ! isset( $_POST["_wpbo_product_rule_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_product_rule_nonce"], plugin_basename( __FILE__ ) ) ) {
        return;
    }

	// Update Rule Meta Values
	if( isset( $_POST['_wpbo_deactive'] )) {
		update_post_meta( 
			$post_id, 
			'_wpbo_deactive', 
			strip_tags( $_POST['_wpbo_deactive'] )
		);
		
	} else {
		update_post_meta( 
			$post_id, 
			'_wpbo_deactive', 
			'' 
		);
	}
	
	if ( isset( $_POST['_wpbo_minimum'] )) {
		$min  = $_POST['_wpbo_minimum'];
	}
	
	if ( isset( $_POST['_wpbo_step'] )) {
		$step = $_POST['_wpbo_step'];
	}
	
	/* Make sure min >= step */
	if ( isset( $step ) and isset( $min ) ) {
		if ( $min < $step ) {
			$min = $step;
		}
	}
	
	if( isset( $_POST['_wpbo_step'] )) {
		update_post_meta( 
			$post_id, 
			'_wpbo_step', 
			strip_tags( $_POST['_wpbo_step'] )
		);
	}
	
	if( isset( $_POST['_wpbo_minimum'] )) {
		update_post_meta( 
			$post_id, 
			'_wpbo_minimum', 
			strip_tags( $min )
		);
	}
	
	if( isset( $_POST['_wpbo_maximum'] )) {
		update_post_meta( 
			$post_id, 
			'_wpbo_maximum', 
			strip_tags( $_POST['_wpbo_maximum'] )
		);
	}
	
	if( isset( $_POST['_wpbo_override'] )) {
		update_post_meta( 
			$post_id, 
			'_wpbo_override', 
			strip_tags( $_POST['_wpbo_override'] )
		);
	} else {
		update_post_meta( 
			$post_id, 
			'_wpbo_override', 
			'' 
		);
	}

}