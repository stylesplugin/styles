<?php
/**
 * Intermingled PHP and HTML for the options page
 *
 * This file contains all PHP and HTML required for the PD Styles Appearance Page in the admin of WordPress
 *
 * @package StormStyles
 * @subpackage admin-main
 * @since 0.1
 */

$mac = ( false === strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh') ) ? '' : 'macos';

?>
<div class="wrap StormStyles">
	
	<?php screen_icon('themes'); ?>
	<h2><?php _e( 'Styles' , 'styles' ); ?></h2>
	
	<form method="post" id="StormForm" class="<?php echo $mac ?>" action="<?php esc_attr_e($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data" name="post">
		<?php 
			wp_nonce_field( 'styles-update-options' );
			settings_fields('styles');
			do_settings_sections('styles-settings-sections');
		?>
		
		<p class="submit">
			<input class="storm-submit button-primary" type="submit" value="<?php _e('Save Changes'); ?>" />
			
			<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
			<span class="response"> </span>
		</p>
		
		<input type="hidden" name="action" class="action" value="styles-update-options" />

	</form>
				
</div>
