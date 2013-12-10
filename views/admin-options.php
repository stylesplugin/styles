<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php _e( 'Styles', 'styles' ); ?></h2>

	<form name="styles_options" method="post" action="options.php">

		<?php settings_fields( 'styles' ); ?>
		<?php do_settings_sections( 'styles' ); ?>
		<?php submit_button( 'Save' ); ?>
	
	</form>

</div>