<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'IPQ_Advanced_Rules' ) ) :

class IPQ_Advanced_Rules {
	
	public function __construct() {
		
		// Add Advanced Rules link under quantity rules
		add_action( 'admin_menu', array( $this, 'add_advanced_rule_page' ) );
	}
	
	/*
	* Add Import Page
	*/
	public function add_advanced_rule_page() {
		
		$slug = add_submenu_page(
			'edit.php?post_type=quantity-rule', 
			'Advanced Rules', 
			'Advanced Rules', 
			'edit_posts', 
			basename(__FILE__), 
			array( $this, 'advanced_rules_page_content')
		);
		
		// Load action, checks for posted form
		add_action( "load-{$slug}", array( $this, 'page_loaded') );
		
	}
	
	/*
	* 	Processes save settings if applicable and redirect the user with a success messsage
	*/
	public function page_loaded() {
		
		if ( isset( $_POST["ipq-advanced-rules-submit"] ) and $_POST["ipq-advanced-rules-submit"] == 'Y' ) {
			
			check_admin_referer( "ipq-advanced-rules" );
			$this->save_settings();
			$url_parameters = 'updated=true';
			wp_redirect(admin_url('edit.php?post_type=quantity-rule&page=class-ipq-advanced-rules.php&'.$url_parameters));
			exit;
		}
	}
	
	/*
	*	Update the settings based on the post values
	*/
	public function save_settings() {
		
		// Get Settings
		$settings = get_option( 'ipq_options' );
		
		// Active Rule
		if ( isset( $_POST['ipq_site_rule_active'] ) and $_POST['ipq_site_rule_active'] == 'on' ) {
			$settings['ipq_site_rule_active'] = 'on';
		} else {
			$settings['ipq_site_rule_active'] = '';
		}

		if ( isset( $_POST['ipq_site_min'] )) {
			$min  = wpbo_validate_number( $_POST['ipq_site_min'] );
		}
		
		if ( isset( $_POST['ipq_site_step'] )) {
			$step = wpbo_validate_number( $_POST['ipq_site_step'] );
		}
		
		if ( isset( $_POST['ipq_site_max'] )) {
			$max = wpbo_validate_number( $_POST['ipq_site_max'] );
		}

		/* Make sure min >= step */
		if ( isset( $step ) and isset( $min ) ) {
			if ( $min < $step ) {
				$min = $step;
			}
		}
		
		/* Make sure min <= max */
		if ( isset( $step ) and isset( $max ) ) {
			if ( $min > $max and $max != '' and $max != 0 ) {
				$max = $min;
			}
		}

		// Site Minimum
		if ( isset( $_POST['ipq_site_min'] ) ) {
			$settings['ipq_site_min'] = strip_tags( $min );
		} 
		
		// Site Step 
		if ( isset( $_POST['ipq_site_step'] ) ) {
			$settings['ipq_site_step'] = strip_tags( $step );
		} 
		
		/* Make sure Max > Min */
		if( isset( $_POST['ipq_site_max'] )) {
			
			if ( isset( $min ) and $max < $min and $max != 0 and $max != '' ) {
				$max = $min;
			}

			// Site Maximum
			$settings['ipq_site_max'] = strip_tags( $max );
		}

		// Update Settings
		$updated = update_option( 'ipq_options', $settings );

	}
	
	/**
	*	Advanced Rules Page Content
	*/
	public function advanced_rules_page_content() {
		
		$options = get_option( 'ipq_options' );

		if ($options == false) {
			$options = array();
		}
		
		extract($options);
	
		?>
		<h2>Advanced Rules</h2>
		<form method="post" action="<?php admin_url( 'edit.php?post_type=quantity-rule&page=class-ipq-advanced-rules.php' ); ?>">
			<?php wp_nonce_field( "ipq-advanced-rules" ); ?>
			
			<table class="form-table">
				<tr>
					<th>Activate Site Wide Rules?</th>
					<td><input type='checkbox' name='ipq_site_rule_active' id='ipq_site_rule_active'
						<?php if ( $ipq_site_rule_active != '' ) echo 'checked'; ?>
					 /></td>
				</tr>

				<?php if ( $ipq_site_rule_active != '' ): ?>
				
					<tr>
						<th>Site Wide Product Minimum</th>
						<td><input type='number' name='ipq_site_min' id='ipq_site_min'
							value='<?php if ( $ipq_site_min != '' ) echo $ipq_site_min; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Product Maximum</th>
						<td><input type='number' name='ipq_site_max' id='ipq_site_max'
							value='<?php if ( $ipq_site_max != '' ) echo $ipq_site_max; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th>Site Wide Step Value</th>
						<td><input type='number' name='ipq_site_step' id='ipq_site_step'
							value='<?php if ( $ipq_site_step != '' ) echo $ipq_site_step; ?>'
						 /></td>
					</tr>
					
					<tr>
						<th></th>
						<td>
							<em>*Note - the minimum value must be greater then or equal to the step value.</em>
						</td>
					</tr>
				
				<?php endif; ?>

			</table>
			
			<p class="submit" style="clear: both;">
				<input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
				<input type="hidden" name="ipq-advanced-rules-submit" value="Y" />
			</p>
		</form>
		
		<?php	
	}
}

endif;

return new IPQ_Advanced_Rules();
