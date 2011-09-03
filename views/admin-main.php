<?php
/**
 * Intermingled PHP and HTML for the options page
 *
 * This file contains all PHP and HTML required for the PD Styles Appearance Page in the admin of WordPress
 *
 * @package pd-styles
 * @subpackage admin-main
 * @since 0.1
 */

?>
<div class="wrap pd-styles">
	
	<?php screen_icon('themes'); ?>
	<h2><?php _e( 'Styles' , 'pd-styles' ); ?></h2>
	
	<form method="post" id="pdm_form" action="<?php esc_attr_e($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data" name="post">
		<?php 
			wp_nonce_field( 'pd-styles-update-options' );
			settings_fields('pd-styles');
			do_settings_sections('PDStyles_Settings');
		?>
		
		<p class="submit">
			<input class="pds-submit button-primary" type="submit" value="<?php _e('Save Changes'); ?>" />
			
			<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
			<span class="response"> </span>
		</p>
		
		<input type="hidden" name="action" class="action" value="pdstyles-update-options" />

	</form>
				
</div>
