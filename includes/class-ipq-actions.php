<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ_Actions' ) ) :

class IPQ_Actions {
	
	public $rule = 'unset';
	
	public function __construct() {
		
		add_action( 'init', array( $this, 'apply_product_min_notification' ) );
		
		
	}

	/*
	*	Adds minimum product notification at correct action level 
	*	if users applies message
	*
	*	@access public 
	*	@return void
	*/								
	public function apply_product_min_notification() {

		$settings = get_option( 'ipq_options' );
		extract( $settings );
		
		if ( isset( $ipq_show_min_qty_note ) and $ipq_show_min_qty_note == 'on' ) {
			
			// Get add_to_cart action priority
			global $wp_filter;
			$action_to_check = 'woocommerce_single_product_summary';
			$target_function = 'woocommerce_template_single_add_to_cart';
			$cart_priority = has_filter( $action_to_check, $target_function );
			
			// Set the priory level based on add to cart
			if ( $cart_priority == null ) {
				$priority = 30;
				
			} elseif ( isset( $ipq_show_min_qty_note_pos ) and $ipq_show_min_qty_note_pos == 'below' ) {
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
		$settings = get_option( 'ipq_options' );
		extract( $settings );
		
		// Get minimum value for product 
		$rule = wpbo_get_applied_rule( $product );
		$min = wpbo_get_value_from_rule( 'min', $product, $rule );
		
		if ( isset( $ipq_min_qty_text ) ) {
			$qty_pattern = '/\%QTY\%/';
			$note_text = preg_replace($qty_pattern, $min, $ipq_min_qty_text);
			
			// Output result with optional custom class
			echo "<span ";
			if ( isset( $ipq_min_qty_class ) and $ipq_min_qty_class != '' )
				echo "class='" . $ipq_min_qty_class . "'>";
			echo $note_text;
			echo "</span>";
		}
	}
}

endif;

return new IPQ_Actions();
