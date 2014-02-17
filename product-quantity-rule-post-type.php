<?php

/*
*	Register Quantity Rule Post Type
*/
add_action( 'init', 'wpbo_quantity_rule_init' );

function wpbo_quantity_rule_init() {
  $labels = array(
    'name'               => 'Quantity Rules',
    'singular_name'      => 'Quantity Rule',
    'add_new'            => 'Add New',
    'add_new_item'       => 'Add New Rule',
    'edit_item'          => 'Edit Rule',
    'new_item'           => 'New Rule',
    'all_items'          => 'All Rules',
    'view_item'          => 'View Rule',
    'search_items'       => 'Search Ruless',
    'not_found'          => 'No rules found',
    'not_found_in_trash' => 'No rules found in Trash',
    'parent_item_colon'  => '',
    'menu_name'          => 'Quantity Rules'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => false,
    'publicly_queryable' => false,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'quantity-rule' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title' ),
    'taxonomies' 		 => array(),
  );

  register_post_type( 'quantity-rule', $args );
}

/*
*	Register Custom Columns for List View
*/
add_action( 'manage_edit-quantity-rule_columns', 'wpbo_quantity_rule_columns', 10, 2 );

function wpbo_quantity_rule_columns( $column, $post_id ) {
 	
 	unset( $column['date'] );
 	
    $new_columns['priority'] = __('Priority');
    $new_columns['min'] = __('Minimum');
    $new_columns['max'] = __('Maximum');
    $new_columns['step'] = __('Step Value');     
    $new_columns['cats'] = __('Categories');
    $new_columns['date'] = __('Date');
    
    return array_merge( $column, $new_columns );
}

/*
*	Get Custom Columns Values for List View
*/
add_action('manage_quantity-rule_posts_custom_column', 'wpbo_manage_quantity_rule_columns', 10, 2);
 
function wpbo_manage_quantity_rule_columns($column_name, $id) {
    
    switch ($column_name) {
    
	    case 'priority':
	        echo get_post_meta( $id, '_priority', true );
	        break;
	 
	    case 'min':
   	        echo get_post_meta( $id, '_min', true );
	        break;
	        
	    case 'max':
   	        echo get_post_meta( $id, '_max', true );
	        break;
	        
	    case 'step':
	        echo get_post_meta( $id, '_step', true );	       
	        break;
	        
	    case 'cats':
	   		$cats = get_post_meta( $id, '_cats', false);
	   			   		
	   		foreach ( $cats[0] as $cat ){

	   			$taxonomy = 'product_cat'; 	
		   		$term = get_term_by( 'id', $cat, $taxonomy );
	   			$link = get_term_link( $term );	
	   			
	   			echo "<a href='" . $link . "'>" . $term->name . "</a><br />";	
	   		}
	        break;  
	        
	    default:
	        break;
    } 
}   

/*
*	Make Custom Columns Sortable
*/
add_filter( 'manage_edit-quantity-rule_sortable_columns', 'wpbo_sortable_quantity_rule_columns' );  

function wpbo_sortable_quantity_rule_columns( $columns ) {  
    
    $columns['priority'] = __('Priority');
    $columns['min'] = __('Minimum');
    $columns['max'] = __('Maximum');
    $columns['step'] = __('Step Value');
  
    return $columns;  
}  

/*
*	Register and Create Rule Options Meta Box for Quantity Rules
*/
add_action( 'add_meta_boxes', 'wpbo_quantity_rule_meta_init' );

function wpbo_quantity_rule_meta_init() {
	add_meta_box(
		'wpbo-quantity-rule-meta', 
		'Set Quantity Rule Options', 
		'wpbo_quantity_rule_meta', 
		'quantity-rule', 
		'normal', 
		'high'
	);
}

function wpbo_quantity_rule_meta( $post ) {
	
	$min  = get_post_meta( $post->ID, '_min', true);
	$max  = get_post_meta( $post->ID, '_max', true);
	$step = get_post_meta( $post->ID, '_step', true);
	$priority = get_post_meta( $post->ID, '_priority', true);
	
	// Create Nonce Field
	wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_rule_value_nonce' );
	
	?>
		<div class="wpbo-meta">
			<label for="min">Minimum</label>
			<input type="number" name="min" id="min" value="<?php echo $min ?>" />
		
			<label for="max">Maximum</label>
			<input type="number" name="max" id="max" value="<?php echo $max ?>" />
			
			<label for="step">Step Value</label>
			<input type="number" name="step" id="step" value="<?php echo $step ?>" />
			
			<label for="step">Priority</label>
			<input type="number" name="priority" id="priority" value="<?php echo $priority ?>" />			
		</div>
		<p><em>*Note - the minimum value must be greater then or equal to the step value.</em><br />
		<em>*Note - The rule with the lowest priority number will be used if multiple rules are applied to a single product.</em></p>
	<?php	
}


/*
*	Register and Create Product Category Meta Box for quantity Rule
*/
add_action( 'add_meta_boxes', 'wpbo_quantity_rule_tax_init' );

function wpbo_quantity_rule_tax_init() {
	add_meta_box(	
		'wpbo-quantity-rule-tax-meta', 
		'Product Categories', 
		'wpbo_quantity_rule_tax_meta', 
		'quantity-rule', 
		'normal', 
		'high'
	);
}

function wpbo_quantity_rule_tax_meta( $post ) {

	// Get selected categories
	$cats = get_post_meta( $post->ID, '_cats', false);

	if ( $cats != null ) {
		$cats = $cats[0];
	}
	
	// Get all possible categories
	$tax_name = 'product_cat';
	
	$args = array( 
		'parent' => 0,
		'hide_empty' => false
		);
	
	$terms = get_terms( $tax_name, $args );
	
	if ( $terms ){
		
		// Create Nonce Field
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpbo_tax_nonce' );
	
		echo '<ul class="rule-product-cats level-1">';
		foreach ( $terms as $term ) {
			wpbo_print_tax_inputs( $term, $tax_name, $cats, 2 );
		}
		echo '</ul>';
	}
}

/*
*	Will Recursivly Print all Product Categories with heirarcy included
*/
function wpbo_print_tax_inputs( $term, $taxonomy_name, $cats, $level ) { 
	
	// Echo Single Item
	?>
		<li>
			<input type="checkbox" id="_wpbo_cat_<?php echo $term->term_id ?>" name="_wpbo_cat_<?php echo $term->term_id ?>" <?php if ( is_array( $cats ) and in_array( $term->term_id, $cats )) echo 'checked="checked"' ?> /><?php echo $term->name; ?>
		</li>
	<?php 
	
	// Get any Children
	$children = get_term_children( $term->term_id, $taxonomy_name );
	
	// Continue to print children if they exist
	if ( $children ){
		echo '<ul class="level-' . $level . '">';
		$level++;
		foreach ( $children as $child_id ){
			$child = get_term_by( 'id', $child_id, $taxonomy_name );
			// If the child is at the second level relative to the last printed element, exclude it
			if ( is_object( $child ) and $child->parent == $term->term_id ) {
				wpbo_print_tax_inputs( $child, $taxonomy_name, $cats, $level );
			}
		}
		echo '</ul>';
	}
}

/*
*	Register and Create Meta Box to encourage user to install our thumbnail plugin
*/
add_action( 'add_meta_boxes', 'wpbo_input_thumbnail_notice' );

function wpbo_input_thumbnail_notice() {

	// Only show eta box if user has not installed thumbnail plugin
	
	if ( !in_array( 'woocommerce-thumbnail-input-quantities/woocommerce-thumbnail-input-quantity.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
		add_meta_box(	
			'wpbo-input-thumbnail-notice', 
			'Notice', 
			'wpbo_input_thumbnail_notice_meta', 
			'quantity-rule', 
			'side', 
			'high'
		);
	}
}

function wpbo_input_thumbnail_notice_meta( $post ) {
	
	echo "We've noticed you do not have <a href='http://wordpress.org/plugins/woocommerce-thumbnail-input-quantities/' target='_blank'>WooCommerce Thumbnail Input Quantity</a> installed. <br /><br />It is <strong>highly recommended</strong> so your users can use your quantity rules from product thumbnails.";
	
}

/*
*	Register and Create Meta Box to encourage user to install our thumbnail plugin
*/
add_action( 'add_meta_boxes', 'wpbo_company_notice' );

function wpbo_company_notice() {

	add_meta_box(	
		'wpbo-company-notice', 
		'Message from WP BackOffice', 
		'wpbo_company_notice_meta', 
		'quantity-rule', 
		'side', 
		'low'
	);
}

function wpbo_company_notice_meta( $post ) {
	
	?>
		<a href="http://www.wpbackoffice.com" target="_blank"><img src="<?php echo plugins_url() ?>/woocommerce-incremental-product-quantities/wpbo-logo.png" /></a>
		<p>
			<a href="http://www.wpbackoffice.com" target="_blank">WooCommerce Hosting, Customization, Support</a>
		</p>
	<?php 
	
}

/*
*	Save Rule Meta Values
*/
add_action( 'save_post', 'wpbo_save_quantity_rule_meta');

function wpbo_save_quantity_rule_meta( $post_id ) {
	
	// Validate Post Type
	if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
		return;
	}
	
	// Validate User
	if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

	// Verify Nonce
    if ( ! isset( $_POST["_wpbo_rule_value_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_rule_value_nonce"], plugin_basename( __FILE__ ) ) ) {
        return;
    }

	/* Make sure $min >= step */
	if( isset( $_POST['min'] ) ) {
		$min = $_POST['min'];
	}
	
	if ( isset( $_POST['step'] ) and isset( $min ) ) {
		if ( $min < $_POST['step']) {
			$min = $_POST['step'];
		}
	}
	
	if ( isset( $min ) ) {
		update_post_meta( $post_id, '_min', wpbo_validate_number( $min ) );
	}
	
	if ( isset( $min ) ) {
		update_post_meta( $post_id, '_max', wpbo_validate_number( $_POST['max'] ) );
	}
	
	if ( isset( $min ) ) {
		update_post_meta( $post_id, '_step', wpbo_validate_number( $_POST['step'] ) );
	}
	
	if ( isset( $min ) ) {
		update_post_meta( $post_id, '_priority', wpbo_validate_number( $_POST['priority'] ) );
	}
	
}

/*
*	Save Rule Taxonomy Values
*/
add_action( 'save_post', 'wpbo_save_quantity_rule_taxes');

function wpbo_save_quantity_rule_taxes( $post_id ) {
	
	// Validate Post Type
	if ( ! isset( $_POST['post_type'] ) or $_POST['post_type'] !== 'quantity-rule' ) {
		return;
	}
	
	// Validate User
	if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

	// Verify Nonce
    if ( ! isset( $_POST["_wpbo_tax_nonce"] ) or ! wp_verify_nonce( $_POST["_wpbo_tax_nonce"], plugin_basename( __FILE__ ) ) ) {
        return;
    }

	// Check which Categories have been selected
	$tax_name = 'product_cat';
	$args = array( 'hide_empty' => false );
	$terms = get_terms( $tax_name, $args );
	$cats = array();

	// See which terms were included
	foreach ( $terms as $term ) {
		$term_name = '_wpbo_cat_' . $term->term_id;
		if ( isset( $_POST[ $term_name ] ) and $_POST[ $term_name ] == 'on' ) {
			array_push( $cats, $term->term_id );		
		} 
	}
	
	// Add them to the post meta
	delete_post_meta( $post_id, '_cats' );
	update_post_meta( $post_id, '_cats', $cats, false );

} 

/*
*	Validate inputs as numbers and set them to null if 0
*/
function wpbo_validate_number( $number ) {
	
	$number = intval( $number );
	
	if ( $number == 0 ) {
		return null;
	} elseif ( $number < 0 ) {
		return null;
	} 
	
	return $number;
}