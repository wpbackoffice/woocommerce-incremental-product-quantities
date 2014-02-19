<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ_Quantity_Validations' ) ) :

class IPQ_Quantity_Validations {
	
	public function __construct() {
	
		add_action( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 5, 6 );
		add_action( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 4, 5 );

	}

	/*
	*	Add to Cart Validation to ensure quantity ordered follows the user's rules.
	*
	*	@access public 
	*	@param  boolean passed
	*	@param  int		product_id
	*	@param  int 	quantity
	*	@param  boolean from_cart
	*	@param  int 	variation_id
	*	@param  array	variations
	*	@param	string 	cart_item_key
	*	@return boolean
	*
	*/
	public function add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = null, $variations = null, $cart_item_key = null ) {

		return $this->validate_single_product( $passed, $product_id, $quantity, false, $variation_id, $variations );
		
	}
	
	/*
	*	Cart Update Validation to ensure quantity ordered follows the user's rules.
	*
	*	@access public 
	*	@param  boolean passed
	*	@param  string	cart_item_key
	*	@param  array 	values
	*	@param  int 	quantity
	*	@return boolean
	*
	*/
	public function update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {

		return $this->validate_single_product( $passed, $values['product_id'], $quantity, true, $values['variation_id'], $values['variation'] );
		
	}
	
	/*
	*	Validates a single product based on the quantity rules applied to it.
	*	It will also validate based on the quantity in the cart.
	*
	*	@access public 
	*	@param  boolean passed
	*	@param  int		product_id
	*	@param  int 	quantity
	*	@param  boolean from_cart
	*	@param  int 	variation_id
	*	@param  array	variations
	*	@return boolean
	*	
	*/
	
	public function validate_single_product( $passed, $product_id, $quantity, $from_cart, $variation_id = null, $variations = null ) {
		global $woocommerce, $product, $ipq;
		
		$product = get_product( $product_id );
		$title = $product->get_title();
	
		// Return Defaults if it isn't a simple product
		if( $product->product_type != 'simple' ) {
			return true;
		}
	
		// Get the applied rule and values - if they exist
		$rule = wpbo_get_applied_rule( $product );
		$values = wpbo_get_value_from_rule( 'all', $product, $rule );
		
		if ( $values != null )
			extract( $values ); // $min_value, $max_value, $step, $priority
				
		// Inactive Products can be ignored
		if ( $values == null )
			return true;
	
		// Min Validation
		if ( $min_value != null && $quantity < intval( $min_value ) ) {
			
			if ( $ipq->wc_version >= 2.1 ) {
				wc_add_notice( sprintf( __( "You must add a minimum of %s %s's to your cart.", 'woocommerce' ), $min_value, $title ), 'error' );
			
			// Old Validation Style Support	
			} else {
				$woocommerce->add_error( sprintf( __( "You must add a minimum of %s %s's to your cart.", 'woocommerce' ), $min_value, $title ) );
			}
			
			return false;
		}
	
		// Max Validation
		if ( $max_value != null && $quantity > intval( $max_value ) ) {
			
			if ( $ipq->wc_version >= 2.1 ) {
				wc_add_notice( sprintf( __( "You may only add a maximum of %s %s's to your cart.", 'woocommerce' ), $max_value, $title ), 'error' );
			
			// Old Validation Style Support	
			} else {
				$woocommerce->add_error( sprintf( __( "You may only add a maximum of %s %s's to your cart.", 'woocommerce' ), $max_value, $title ) );
			}
			return false;
		}
		
		// Subtract the min value from quantity to calc remainder if min value exists
		if ( $min_value != 0 ) {
			$rem_qty = $quantity - $min_value;
		} else {
			$rem_qty = $quantity;
		}
		
		// Step Validation	
		if ( $step != null && $rem_qty % $step != 0 ) {
		
			if ( $ipq->wc_version >= 2.1 ) {
				wc_add_notice( sprintf( __( "You may only add a %s in multiples of %s to your cart.", 'woocommerce' ), $title, $step ), 'error' );
			
			// Old Validation Style Support	
			} else {
				$woocommerce->add_error( sprintf( __( "You may only add a %s in multiples of %s to your cart.", 'woocommerce' ), $title, $step ) );
			}
			
			return false;
		}
		
		// Don't run Cart Validations if user is updating the cart
		if ( $from_cart != true ) {
		
			// Get Cart Quantity for the product
			foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if( $product_id == $_product->id ) {
					$cart_qty = $values['quantity'];
				}
			}
			
			//  If there aren't any items in the cart already, ignore these validations
			if ( isset( $cart_qty ) and $cart_qty != null ) {
			
				// Total Cart Quantity Min Validation
				if ( $min_value != null && ( $quantity + $cart_qty ) < $min_value ) {
					
					if ( $ipq->wc_version >= 2.1 ) {
						wc_add_notice( sprintf( __( "Your cart must have a minimum of %s %s's to proceed.", 'woocommerce' ), $min_value, $title ), 'error' );
					
					// Old Validation Style Support	
					} else {
						$woocommerce->add_error( sprintf( __( "Your cart must have a minimum of %s %s's to proceed.", 'woocommerce' ), $min_value, $title ) );
					}
					return false;
				}
			
				// Total Cart Quantity Max Validation
				if ( $max_value != null && ( $quantity + $cart_qty ) > $max_value ) {
					
					if ( $ipq->wc_version >= 2.1 ) {
						wc_add_notice( sprintf( __( "You can only purchase a maximum of %s %s's at once and your cart already has %s %s's in it already.", 'woocommerce' ), $max_value, $title, $cart_qty, $title ), 'error' );
					
					// Old Validation Style Support	
					} else {
						$woocommerce->add_error( sprintf( __( "You can only purchase a maximum of %s %s's at once and your cart already has %s %s's in it already.", 'woocommerce' ), $max_value, $title, $cart_qty, $title ) );
					}
					return false;
				}
				
				// Subtract the min value from cart quantity to calc remainder if min value exists
				if ( $min_value != 0 ) {
					$cart_qty_rem = $quantity + $cart_qty - $min_value;
				} else {
					$cart_qty_rem = $quantity + $cart_qty;
				}
				
				// Total Cart Quantity Step Validation
				if ( $step != null && $step != 0 && $cart_qty_rem != 0 && $cart_qty_rem % $step != 0 ) {
					if ( $ipq->wc_version >= 2.1 ) {
						wc_add_notice( sprintf( __("You may only purchase %s in multiples of %s.", 'woocommerce' ), $title, $step ), 'error' );
					
					// Old Validation Style Support	
					} else {
						$woocommerce->add_error( sprintf( __("You may only purchase %s in multiples of %s.", 'woocommerce' ), $title, $step ) );
					}
					return false;
				}
			}
		}
		
		return true;
	}

}

endif;

return new IPQ_Quantity_Validations();
