<?php
/**
 * Intermingled PHP and HTML for the options page
 *
 * This file contains all PHP and HTML required for WordPress Admin > Appearance > Styles
 */

$mac = ( false === strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh') ) ? '' : 'macos';

?>
<div class="wrap styles-admin">
	
	<div class="head-wrap">
		<?php screen_icon('themes'); ?>
		<h2><?php _e( 'Styles' , 'styles' ); ?></h2>

		<p><a href="customize.php"><?php esc_html_e( 'Customize Appearance', 'styles' ); ?></a></p>

		<h3><?php esc_html__( 'Settings', 'styles' ); ?></h3>
	</div>

	<div class="postbox-container primary"> 
		<form method="post" id="styles-form" class="<?php echo $mac ?>" action="<?php esc_attr_e($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data" name="post">
			
			<?php 
				settings_errors();
				settings_fields('styles'); // includes nonce
				$css = get_option('styles-'.get_template());
			?>

				<?php do_settings_sections('styles-general'); ?>

				<p class="submit">
					<input class="button-primary" type="submit" value="<?php _e('Save API Key'); ?>" />
				</p>

		</form>
	</div>

	<div class="postbox-container secondary">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<?php
					if ( is_array($this->get_option('meta_boxes') ) ) {
						foreach ($this->get_option('meta_boxes') as $key => $box) {
							$this->postbox( $box->id, $box->title, $box->content );
						}
					}
				?>
			</div>
			<br/><br/><br/>
		</div>
	</div>
				
</div>
