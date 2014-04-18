<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ_Actions' ) ) :

class IPQ_Actions {
		
	public function __construct() {
		
		// Conditionally add quantity note to product page
		$settings = get_option( 'ipq_options' );

		if ( $settings['ipq_show_qty_note'] == 'on' ) {
			add_action( 'init', array( $this, 'apply_product_notification' ) );
		}
		
	}

	/*
	*	Adds minimum product notification at correct action level 
	*	if users applies message
	*
	*	@access public 
	*	@return void
	*/								
	public function apply_product_notification() {

		$settings = get_option( 'ipq_options' );
		extract( $settings );
		
		if ( isset( $ipq_show_qty_note ) and $ipq_show_qty_note == 'on' ) {
			
			// Get add_to_cart action priority
			global $wp_filter;
			$action_to_check = 'woocommerce_single_product_summary';
			$target_function = 'woocommerce_template_single_add_to_cart';
			$cart_priority = has_filter( $action_to_check, $target_function );
			
			// Set the priory level based on add to cart
			if ( $cart_priority == null ) {
				$priority = 30;
				
			} elseif ( isset( $ipq_show_qty_note_pos ) and $ipq_show_qty_note_pos == 'below' ) {
				$priority = $cart_priority + 1;
												
			} else {
				$priority = $cart_priority - 1;
			}
			
			add_action( 'woocommerce_single_product_summary', array( $this, 'display_minimum_quantity_note' ), $priority );
			
		}	
	}
	
	/*
	*	Print the minimum quantity note based on user specs
	*
	*	@access public 
	*	@return void
	*/	
	public function display_minimum_quantity_note() {
	
		global $product;
		
		if( $product->product_type == 'grouped' )
			return;
		
		$settings = get_option( 'ipq_options' );
		extract( $settings );
		
		// Get minimum value for product 
		$rule = wpbo_get_applied_rule( $product );
		$min = wpbo_get_value_from_rule( 'min', $product, $rule );
		$max = wpbo_get_value_from_rule( 'max', $product, $rule );
		$step = wpbo_get_value_from_rule( 'step', $product, $rule );

		// If sitewide rule is applied, convert return arrays to values
		if ( $rule == 'sitewide' ) {
			if ( is_array( $min ) )
			$min = $min['min'];
		
			if ( is_array( $max ) )
				$max = $max['max'];
				
			if ( is_array( $step ) )
				$step = $step['step'];
		}
		
		// If the text is set, update and print the output
		if ( isset( $ipq_qty_text ) ) {
			$min_pattern = '/\%MIN\%/';
			$max_pattern = '/\%MAX\%/';
			$step_pattern = '/\%STEP\%/';

			$ipq_qty_text = preg_replace($min_pattern, $min, $ipq_qty_text);
			$ipq_qty_text = preg_replace($max_pattern, $max, $ipq_qty_text);
			$ipq_qty_text = preg_replace($step_pattern, $step, $ipq_qty_text);

			// Output result with optional custom class
			echo "<span";
			if ( isset( $ipq_qty_class ) and $ipq_qty_class != '' )
				echo " class='" . $ipq_qty_class . "'";
			echo ">";	
			echo $ipq_qty_text;
			echo "</span>";
		}
	}
}

endif;

return new IPQ_Actions();
