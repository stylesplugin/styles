<link rel='stylesheet' href='/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin,dashboard&amp;ver=466b2454247efc0a3125372b6b862725' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='/wp-admin/css/colors-fresh.css?ver=20100610' type='text/css' media='all' />
<!--[if lte IE 7]>
<link rel='stylesheet' id='ie-css'  href='/wp-admin/css/ie.css?ver=20100610' type='text/css' media='all' />
<![endif]-->
<link rel='stylesheet' id='thickbox-css'  href='/wp-includes/js/thickbox/thickbox.css?ver=20090514' type='text/css' media='all' />
<link rel='stylesheet' id='pds-colorpicker-css'  href='/wp-content/plugins/pdstyles/lib/js/colorpicker/css/colorpicker.css?ver=0.1' type='text/css' media='all' />
<link rel='stylesheet' id='pd-styles-admin-css-css'  href='/?scaffold&#038;file=lib%2Fcss%2Fadmin.css&#038;ver=0.1' type='text/css' media='screen' />

<div id="pds_frontend" class="wrap pd-styles" style="display:none;">
	<div class="handle"></div>
	<div class="postbox-container left-column">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
	
				<form method="post" id="pdm_form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data" name="post">

					<?php
						FB::log($this, '$this');
					?>
					<?php $this->variables[ $this->permalink ]->output(); ?>
					
					<input type="hidden" name="action" class="action" value="pdstyles-update-options" />
						
					
					<p class="submit">
						<!-- <input id="pds_preview" type="button" class="button" value="<?php _e('Preview'); ?>" /> -->
						<input id="pds_save" type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
						
						<img id="pds_waiting" class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
						<span id="pds_response" class="response"> </span>
					</p>

				</form>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
var userSettings = {
		'url': '/',
		'uid': '1',
		'time':'1290137514'
	},
	ajaxurl = '/wp-admin/admin-ajax.php',
	pagenow = 'appearance_page_pd-styles',
	typenow = '',
	adminpage = 'appearance_page_pd-styles',
	thousandsSeparator = ',',
	decimalPoint = '.',
	isRtl = 0;
//]]>
</script>
<script type='text/javascript' src='/wp-content/plugins/pdstyles/lib/js/admin-main.js?ver=0.1'></script>
<script type="text/javascript">
	pds_frontend_init();
</script>
		