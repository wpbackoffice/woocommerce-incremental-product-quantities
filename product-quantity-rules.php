<?php
/*
Plugin Name: WooCommerce Incremental Product Quantities
Plugin URI: http://www.wpbackoffice.com/plugins/woocommerce-incremental-product-quantities/
Description: Sell products in increments by setting minimum and maximum quantities as well as the intervals in between. Highly recommended to also install 'WooCommerce Thumbnail Input Quantities' to allow users to add your custom quantites from product thumbnails.
Version: 1.1.4
Author: WP BackOffice
Author URI: http://www.wpbackoffice.com
*/ 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ' ) ) :

class IPQ {
	
	public $wc_version;
	
	/**
	 * @var WooCommerce Supplier instance
	 * @since 2.1
	 */
	protected static $_instance = null;
	
	/**
	 * Main Incremental Product Quantities Instance
	 *
	 * @return WooCommerce Supplier - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		
		// Include Required Files
		require_once( 'product-quantity-functions.php' );
		require_once( 'product-quantity-filters.php' );
		require_once( 'product-quantity-meta-box.php' );
		require_once( 'product-quantity-rule-post-type.php' );
		require_once( 'product-quantity-validations.php' );
		
		// Add Scripts and styles		
		add_action( 'wp_enqueue_scripts', array( $this, 'input_value_validation' ) );	
		add_action( 'admin_init', array( $this, 'quantity_styles' ) );
		
		// Set WC Version Number 
		add_action( 'init', array( $this, 'get_wc_version' ) );
		
		// Control Admin Notices
		add_action( 'admin_notices', array( $this, 'thumbnail_plugin_notice' ) );
		add_action( 'admin_init', array( $this, 'thumbnail_plugin_notice_ignore' ) );

	}

	/*
	*	Include JS to round any value that isn't a multiple of the 
	*	step up.
	*/	
	public function input_value_validation() {
		wp_enqueue_script( 
			'wpbo_validation', 
			plugins_url() . '/woocommerce-incremental-product-quantities/wpbo_input_value_validation.js',
			array( 'jquery' )
		);
	}
	
	/*
	*	Include Styles
	*/	
	public function quantity_styles() {
		wp_enqueue_style( 
			'wpbo_quantity_styles', 
			plugins_url() . '/woocommerce-incremental-product-quantities/styles.css'
		);
	}
	
	/*
	*	Set what version of WooCommerce the user has installed 
	*/	
	public function get_wc_version() {
	
		global $wpbo_wc_version;
		
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';
		
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			$this->wc_version = $plugin_folder[$plugin_file]['Version'];
		} else {
			$this->wc_version = NULL;
		}
	}
	
	/*
	* 	General Admin Notice to Encourage users to download thumbnail input as well
	*/	
	public function thumbnail_plugin_notice() {
		global $current_user;
		
		$user_id = $current_user->ID; 
		
		// Check if Thumbnail Plugin is activated	
		if ( !in_array( 'woocommerce-thumbnail-input-quantity/woocommerce-thumbnail-input-quantity.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		
			// Check if User has Dismissed this message already
			if ( ! get_user_meta( $user_id, 'wpbo_thumbnail_input_notice' ) ) {
				
				echo '<div class="updated">
			       <p><strong>Notice:</strong> It is highly recommended you install and active the <a href="http://wordpress.org/plugins/woocommerce-thumbnail-input-quantities/" target="_blank">WooCommerce Thumbnail Input Quantites</a> plugin to display input boxes on products thumbnails. <a href="';
			       
			       // Echo the current url 
			       echo site_url() . $_SERVER['REQUEST_URI'];
			       
			       // Echo notice variable as nth get variable with &
			       if ( strpos( $_SERVER['REQUEST_URI'] , '?' ) !== false ) {
				       echo '&wpbo_thumbnail_plugin_dismiss=0';
				   // Echo notice variable as first get variable with ?
			       } else {
				       echo '?wpbo_thumbnail_plugin_dismiss=0';
			       }
			       
			    echo '">Dismiss Notice</a></p></div>';
			}
		} 
	}
	
	/*
	*	Make Admin Notice Dismissable
	*/	
	public function thumbnail_plugin_notice_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		
		if ( isset($_GET['wpbo_thumbnail_plugin_dismiss']) && '0' == $_GET['wpbo_thumbnail_plugin_dismiss'] ) {
			add_user_meta($user_id, 'wpbo_thumbnail_input_notice', 'true', true);
		}
	}

}

endif;

/**
 * Returns the main instance of WCS to prevent the need to use globals.
 *
 * @since  1.0
 * @return WCS_Supplier
 */
function IPQ() {
	return IPQ::instance();
}

// Global for backwards compatibility.
$GLOBALS['ipq'] = IPQ();