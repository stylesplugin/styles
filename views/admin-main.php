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
	<?php screen_icon("themes"); ?>
	<h2><?php _e( 'Styles' , 'pd-styles' ); ?></h2>
	
	<form method="post" id="pdm_form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data" name="post">
		<?php wp_nonce_field( 'pd-styles-update-options' ); ?>
		<?php
			// Todo: Rewrite settings output using WP Settings API
			// http://codex.wordpress.org/Settings_API
	
			$this->files->active_file->output();
			// FB::log($this->files->active_file, '$this->files->active_file');
		?>

		<input type="hidden" name="action" class="action" value="pdstyles-update-options" />

	</form>
				
</div>
