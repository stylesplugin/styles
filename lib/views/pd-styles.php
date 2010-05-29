<?php
/**
 * Intermingled PHP and HTML for the options page
 *
 * This file contains all PHP and HTML required for the PD Styles Appearance Page in the admin of WordPress
 *
 * @package shadowbox-js
 * @subpackage options-page
 * @since 3.0.0.4
 */
?>
<div class="wrap pd-styles">
	<h2><?php _e( 'PD Styles' , 'pd-styles' ); ?></h2>
	<?php if ( ! is_writeable( $this->plugin_dir_path().'cache' ) ) : ?>
	<div id="pd-styles-override" class="notice">
		<p><?php printf( __( 'The <strong>PD Styles</strong> cache directory is <code>%s/cache</code> and cannot be modified. That file must be writeable by the webserver to make any changes.', 'pd-styles' ), $this->plugin_dir_path() ); ?>
		<?php _e( 'A simple way of doing that is by changing the permissions temporarily using the CHMOD command or through your ftp client. Make sure it and all files inside it are globally writeable and it should be fine.', 'pd-styles' ); ?></p>
		<?php _e( 'Writeable:', 'wp-super-cache' ); ?> <code>chmod -R 666 <?php echo $this->plugin_dir_path(); ?>/cache</code>
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


	<?php FB::log($this, '$this'); ?>

</div>
