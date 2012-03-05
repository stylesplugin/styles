<?php
/**
 * Intermingled PHP and HTML for the options page
 *
 * This file contains all PHP and HTML required for WordPress Admin > Appearance > Styles
 */

$mac = ( false === strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh') ) ? '' : 'macos';

?>
<div class="wrap StormStyles">
	
	<?php screen_icon('themes'); ?>
	<h2><?php _e( 'Styles' , 'styles' ); ?></h2>
	
	<form method="post" id="StormForm" class="<?php echo $mac ?>" action="<?php esc_attr_e($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data" name="post">
		
		<?php 
			settings_errors();
			settings_fields('styles'); // includes nonce
			$css = get_option('styles-'.get_template());
		?>
		
		<?php if ( isset($_GET['settings']) || ( empty( $this->styles->css->contents ) && empty($css) ) ) : ?>
			<p><a href="themes.php?page=styles">Appearance</a> Settings</p>
			
			<?php do_settings_sections('styles-general'); ?>
		
			<p class="submit">
				<input class="button-primary" type="submit" value="<?php _e('Save API Key'); ?>" />
			</p>
			
		<?php else: ?>
			<p>Appearance <a href="themes.php?page=styles&amp;settings">Settings</a></p>
			
			<?php do_settings_sections('styles-gui'); ?>

			<p class="submit">
				<input class="storm-submit button-primary" type="submit" value="<?php _e('Save Changes'); ?>" />

				<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
				<span class="response"> </span>
			</p>

			<input type="hidden" name="action" class="action" value="styles-update-options" />
		
		<?php endif; ?>

	</form>
				
</div>
