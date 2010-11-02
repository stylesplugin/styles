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

// Update options if something was submitted
if ( $_POST['action'] == 'update-options' && check_admin_referer('pd-styles-update-options') ) {
	// Uses $this->update() sanitation callback
	update_option('pd-styles', $_POST );
	
	// Scaffold objects have already been built by this point
	// Avoid displaying stale data
	header('Location: '.$_SERVER['REQUEST_URI']);
}
?>
<div class="wrap pd-styles">
	<h2><?php _e( 'Styles' , 'pd-styles' ); ?></h2>
	<?php if ( ! is_writeable( $this->plugin_dir_path().'scaffold/cache' ) ) : ?>
	<div id="pd-styles-override" class="notice">
		<p><?php printf( __( 'The <strong>PD Styles</strong> cache directory is <code>%sscaffold/cache</code> and cannot be modified. That file must be writeable by the webserver to make any changes.', 'pd-styles' ), $this->plugin_dir_path() ); ?>
		<?php _e( 'A simple way of doing that is by changing the permissions temporarily using the CHMOD command or through your ftp client. Make sure it and all files inside it are globally writeable and it should be fine.', 'pd-styles' ); ?></p>
		<?php _e( 'Writeable:', 'pd-styles' ); ?> <code>chmod -R 666 <?php echo $this->plugin_dir_path(); ?>scaffold/cache</code>
	</div>
	<?php endif; ?>
	<?php /* !! Constants.xml is hard coded... Look into getting location from CSScaffold*/ ?>
	<?php $constants = $this->plugin_dir_path().'cache/constants.xml'; ?>
	<?php if ( ! is_writeable( $constants ) && file_exists( $constants ) ) : ?>
		<div id="pd-styles-override" class="notice">
			<p><?php printf( __( 'The <strong>PD Styles</strong> constants.xml file is <code>%s</code> and cannot be modified. That file must be writeable by the webserver to make any changes.', 'pd-styles' ), $constants ); ?>
			<?php _e( 'A simple way of doing that is by changing the permissions temporarily using the CHMOD command or through your ftp client. Make sure it and all files inside it are globally writeable and it should be fine.', 'pd-styles' ); ?></p>
			<?php _e( 'Writeable:', 'pd-styles' ); ?> <code>chmod -R 666 <?php echo $constants ?></code>
		</div>
	<?php endif; ?>
	
	<form method="post" id="pdm_form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data" name="post">
		<?php wp_nonce_field( 'pd-styles-update-options' ); ?>

		<?php
		
			foreach ( $this->css_variables as $group => $variables ) {
				$label = ( !empty( $variables['label'] )) ? $variables['label'] : $group;
	
				echo '<h2>'.$label.'</h2>';
				
				foreach ( $variables as $key => $ui ) {
					if ( is_object($ui) ) {
					
						$ui->output();
					
					}
				}
			}

		?>
		
		<input type="hidden" name="action" value="update-options" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Update'); ?>" />
		</p>
	</form>
</div>
