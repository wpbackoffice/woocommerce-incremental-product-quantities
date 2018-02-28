<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ_Quantity_Meta_Boxes' ) ) :

class IPQ_Quantity_Meta_Boxes {
	
	public function __construct() {
		
		add_action( 'add_meta_boxes', array( $this, 'meta_box_create' ) );
		add_action( 'save_post', array( $this, 'save_quantity_meta_data' ) );
	}

	/*
	*	Register Rule Meta Box for Product Page for all but external products
	*/
	public function meta_box_create() {
		global $post, $woocommerce;

		if ( $post->post_type == 'product' ) {
			
			$product = get_product( $post->ID );
			$unsupported_product_types = array( 'external', 'grouped' );

			if ( ! in_array( $product->product_type, $unsupported_product_types ) ) {
						
				add_meta_box(
					'wpbo_product_info', 
					__('Product Quantity Rules', 'woocommerce'), 
					array( $this, 'product_meta_box_content' ), 
					'product', 
					'normal', 
					'high' 
				);
			}
		}
	}
	
	/*
	*	Display Rule Meta Box
	*/
	function product_meta_box_content( $post ) {
		global $product;
		global $woocommerce;
		global $wp_roles;
				
		// Get the product and see what rules are being applied
		$pro = get_product( $post );
		
		// Get applied rules by user role
		$roles = $wp_roles->get_names();
		$roles['guest'] = "Guest";
		
		$rules_by_role = array();
		
		// Loop through roles
		foreach ( $roles as $slug => $name ) {
			$rule = wpbo_get_applied_rule( $pro, $slug );

			if ( $rule == 'inactive' or $rule == 'override' or $rule == 'sitewide' )
				continue;
				
			$rules_by_role[$name] = $rule;
		}
		
		// $rule_result = wpbo_get_applied_rule( $pro );
		
	/*
	// If there isn't a rule mark rule as null, otherwise get the id
		if ( $rule_result != 'inactive' and $rule_result != 'override' ) {
			$rule = $rule_result;
			$values = wpbo_get_value_from_rule( 'all', $pro, $rule );
		} else {
			$rule = $rule_result;
		}
	*/
	
		// Display Rule Being Applied
		if ( $rule == 'inactive' ) {
			echo "<div class='inactive-rule rule-message'>No rule is being applied becasue you've deactivated the plugin for this product.</div>";
			
		} elseif ( $rule == 'override' ) {
			echo "<div class='overide-rule rule-message'>The values below are being used because you've chosen to override any applied rules for this product.</div>";
		
		} elseif ( $rule == 'sitewide' ) {
			?>
			<?php $values = wpbo_get_value_from_rule( 'all', $pro, $rule ); ?>
			<div class="active-rule">
				<span>Active Rule:</span>
				<a href='<?php echo admin_url( 'edit.php?post_type=quantity-rule&page=class-ipq-advanced-rules.php' ) ?>'>
					Site Wide Rule
				</a>
				<span class='active-toggle'><a>Show/Hide Active Rules by Role &#x25BC;</a></span>
			</div>
	
			<div class="rule-meta">		
				<table>
					<tr>
						<th>Role</th>
						<th>Rule</th>
						<th>Min</th>
						<th>Max</th>
						<th>Min OOS</th>
						<th>Max OOS</th>
						<th>Step</th>
						<th>Priority</th>
					</tr>
					<tr>
						<td>All</td>
						<td><a href='<?php echo admin_url( 'edit.php?post_type=quantity-rule&page=class-ipq-advanced-rules.php' ) ?>'>Site Wide Rule</a></td>
						<td><?php echo $values['min_value'] ?></td>
						<td><?php echo $values['max_value'] ?></td>
						<td><?php echo $values['min_oos'] ?></td>
						<td><?php echo $values['max_oos'] ?></td>
						<td><?php echo $values['step'] ?></td>
						<td></td>
					</tr>
				</table>
			</div>
			<?php 
		} elseif ( ! isset( $rule->post_title ) or $rule->post_title == null ) {
			echo "<div class='no-rule rule-message'>No rule is currently being applied to this product.</div>";
			
		} else { ?>
			<div class="active-rule">
				<span>Active Rule:</span>
				<a href='<?php echo get_edit_post_link( $rule->ID ) ?>'>
					<?php echo $rule->post_title ?>
				</a>
				<span class='active-toggle'><a>Show/Hide Active Rules by Role &#x25BC;</a></span>
			</div>
	
			<div class="rule-meta">		
				<table>
					<tr>
						<th>Role</th>
						<th>Rule</th>
						<th>Min</th>
						<th>Max</th>
						<th>Min OOS</th>
						<th>Max OOS</th>
						<th>Step</th>
						<th>Priority</th>
					</tr>
				<?php foreach ( $rules_by_role as $role => $rule ): ?>
					<?php if ( $rule != null )
						$values = wpbo_get_value_from_rule( 'all', $pro, $rule ); 
					?>
					<tr>
						<td><?php echo $role ?></td>
						<?php if ( $rule != null ): ?>
							<td><a href='<?php echo get_edit_post_link( $rule->ID ) ?>' target="_blank"><?php echo $rule->post_title ?></a></td>
							<td><?php echo $values['min_value'] ?></td>
							<td><?php echo $values['max_value'] ?></td>
							<td><?php echo $values['min_oos'] ?></td>
							<td><?php echo $values['max_oos'] ?></td>							
							<td><?php echo $values['step'] ?></td>
							<td><?php echo $values['priority'] ?></td>
						<?php else: ?>
							<td>None</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		<?php
		}
	
		// Get the current values if they exist
		$deactive  = get_post_meta( $post->ID, '_wpbo_deactive', true );
		$step  = get_post_meta( $post->ID, '_wpbo_step',     true );
		$min   = get_post_meta( $post->ID, '_wpbo_minimum',  true );
		$max   = get_post_meta( $post->ID, '_wpbo_maximum',  true );
		$over  = get_post_meta( $post->ID, '_wpbo_override', true );
		$min_oos = get_post_meta( $post->ID, '_wpbo_minimum_oos', true );
		$max_oos = get_post_meta( $post->ID, '_wpbo_maximum_oos', true );
		
		// Create Nonce Field
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_product_rule_nonce' );
		
		// Print the form ?>	
		<div class="rule-input-boxes">
			<input type="checkbox" name="_wpbo_deactive" <?php if ( $deactive == 'on' ) echo 'checked'; ?> />
			<span>Deactivate Quantity Rules on this Product?</span>
			
			<input type="checkbox" name="_wpbo_override" id='toggle_override' <?php if ( $over == 'on' ) echo 'checked'; ?> />
			<span>Override Quantity Rules with Values Below</span>
			
			<span class='wpbo_product_values' <?php if ( $over != 'on' ) echo "style='display:none'"?>>
				<label for="_wpbo_step">Step Value</label>
				<input type="number" name="_wpbo_step" value="<?php echo $step; ?>" />
				
				<label for="_wpbo_minimum">Minimum Quantity</label>
				<input type="number" name="_wpbo_minimum" value="<?php echo $min; ?>" />
				
				<label for="_wpbo_maximum">Maximum Quantity</label>
				<input type="number" name="_wpbo_maximum" value="<?php echo $max; ?>" />
				
				<label for="_wpbo_minimum_oos">Out of Stock Minimum</label>
				<input type="number" name="_wpbo_minimum_oos" value="<?php echo $min_oos ?>" />
				
				<label for="_wpbo_maximum_oos">Out of Stock Maximum</label>
				<input type="number" name="_wpbo_maximum_oos" value="<?php echo $max_oos ?>" />
				
				<span class='clear-left'>Note* Maximum values must be larger then minimums</span>
			</span>

		</div>
		<?php
	}
	
	/*
	*	Handle Saving Meta Box Data
	*/	
	public function save_quantity_meta_data( $post_id ) {
	
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
		
		if ( isset( $_POST['_wpbo_minimum'] )) {
			$min  = $_POST['_wpbo_minimum'];
		}
		
		if ( isset( $_POST['_wpbo_step'] )) {
			$step = $_POST['_wpbo_step'];
		}
		
		/* Make sure min >= step */
		/*
		if ( isset( $step ) and isset( $min ) ) {
			if ( $min < $step ) {
				$min = $step;
			}
		}
		*/
		
		if( isset( $_POST['_wpbo_step'] )) {
			update_post_meta( 
				$post_id, 
				'_wpbo_step', 
				strip_tags( wpbo_validate_number( $_POST['_wpbo_step'] ) )
			);
		}
		
		if( isset( $_POST['_wpbo_minimum'] )) {
			if ( $min != 0 ) {
				$min = wpbo_validate_number( $min );
			}
			update_post_meta( 
				$post_id, 
				'_wpbo_minimum', 
				strip_tags( $min )
			);
		}
		
		/* Make sure Max > Min */
		if( isset( $_POST['_wpbo_maximum'] )) {
			$max = $_POST['_wpbo_maximum'];
			if ( isset( $min ) and $max < $min and $max != 0 ) {
				$max = $min;
			}
		
			update_post_meta( 
				$post_id, 
				'_wpbo_maximum', 
				strip_tags( wpbo_validate_number( $max ) )
			);
		}
		
		// Update Out of Stock Minimum
		if( isset( $_POST['_wpbo_minimum_oos'] )) {
			$min_oos = stripslashes( $_POST['_wpbo_minimum_oos'] );
			
			if ( $min_oos != 0 ) {
				$min_oos = wpbo_validate_number( $min_oos );
			}
			update_post_meta( 
				$post_id, 
				'_wpbo_minimum_oos', 
				strip_tags( $min_oos )
			);
		}
		
		// Update Out of Stock Maximum
		if( isset( $_POST['_wpbo_maximum_oos'] )) {
		
			$max_oos = stripslashes( $_POST['_wpbo_maximum_oos'] );
			
			// Allow the value to be unset
			if ( $max_oos != '' ) {
				
				// Validate the number			
				if ( $max_oos != 0 ) {
					$max_oos = wpbo_validate_number( $max_oos );
				} 
				
				// Max must be bigger then min
				if ( isset( $min_oos ) and $min_oos != 0 ) {
					if ( $min_oos > $max_oos )
						$max_oos = $min_oos;
					
				} elseif ( isset( $min ) and $min != 0 ){
					if ( $min > $max_oos ) {
						$max_oos = $min;
					}
				}
			} 
			
			update_post_meta( 
				$post_id, 
				'_wpbo_maximum_oos', 
				strip_tags( $max_oos )
			);

		} 
		
	}
}

endif;

return new IPQ_Quantity_Meta_Boxes();
