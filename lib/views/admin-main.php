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
	<h2><?php _e( 'Styles' , 'pd-styles' ); ?></h2>
	
	<?php /* Warning messages... get out of this view */ ?>
	<?php if ( ! is_writeable( $this->plugin_dir_path().'scaffold/cache' ) ) : ?>
		<div id="pd-styles-override" class="notice">
			<p><?php printf( __( 'The <strong>PD Styles</strong> cache directory is <code>%sscaffold/cache</code> and cannot be modified. That file must be writeable by the webserver to make any changes.', 'pd-styles' ), $this->plugin_dir_path() ); ?>
			<?php _e( 'A simple way of doing that is by changing the permissions temporarily using the CHMOD command or through your ftp client. Make sure it and all files inside it are globally writeable and it should be fine.', 'pd-styles' ); ?></p>
			<?php _e( 'Writeable:', 'pd-styles' ); ?> <code>chmod -R 666 <?php echo $this->plugin_dir_path(); ?>scaffold/cache</code>
		</div>
	<?php endif; ?>

	
	<div class="postbox-container left-column">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
	
				<form method="post" id="pdm_form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data" name="post">
					<?php wp_nonce_field( 'pd-styles-update-options' ); ?>

					<?php $this->variables['/wp-content/plugins/pd-styles/example/vars.css']->output(); ?>
		
					<input type="hidden" name="action" value="update-options" />
						
					
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Update'); ?>" />
						<input id="pds_preview" type="button" class="button" value="<?php _e('Preview'); ?>" />
						
						<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
						<span class="response"> </span>
					</p>

				</form>
				
				<div id="pds_testbox">
					<p>
						<div class="img"></div>
						Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
						<div class="clear"></div>
					</p>
				</div>
				
			</div>
		</div>
	</div>
</div>
