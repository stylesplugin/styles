<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {
		'url': '/',
		'uid': '1',
		'time':'1290137514'
	},
	ajaxurl = 'http://pdstyles/wp-admin/admin-ajax.php',
	pagenow = 'appearance_page_pd-styles',
	typenow = '',
	adminpage = 'appearance_page_pd-styles',
	thousandsSeparator = ',',
	decimalPoint = '.',
	isRtl = 0;
//]]>
</script>
<link rel='stylesheet' href='http://pdstyles/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin,dashboard&amp;ver=466b2454247efc0a3125372b6b862725' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='http://pdstyles/wp-admin/css/colors-fresh.css?ver=20100610' type='text/css' media='all' />
<!--[if lte IE 7]>
<link rel='stylesheet' id='ie-css'  href='http://pdstyles/wp-admin/css/ie.css?ver=20100610' type='text/css' media='all' />
<![endif]-->
<link rel='stylesheet' id='thickbox-css'  href='http://pdstyles/wp-includes/js/thickbox/thickbox.css?ver=20090514' type='text/css' media='all' />
<link rel='stylesheet' id='pds-colorpicker-css'  href='http://pdstyles/wp-content/plugins/pd-styles/lib/js/colorpicker/css/colorpicker.css?ver=0.1' type='text/css' media='all' />
<link rel='stylesheet' id='pd-styles-admin-css-css'  href='http://pdstyles/?scaffold&#038;file=lib%2Fcss%2Fadmin.css&#038;ver=0.1' type='text/css' media='screen' />
<!-- <link rel='stylesheet' id='pd-styles-admin-css-test-css'  href='http://pdstyles/?scaffold&#038;file=example%2Fvars.css&#038;ver=0.1' type='text/css' media='screen' /> -->
<script type='text/javascript'>
/* <![CDATA[ */
var quicktagsL10n = {
	quickLinks: "(Quick Links)",
	wordLookup: "Enter a word to look up:",
	dictionaryLookup: "Dictionary lookup",
	lookup: "lookup",
	closeAllOpenTags: "Close all open tags",
	closeTags: "close tags",
	enterURL: "Enter the URL",
	enterImageURL: "Enter the URL of the image",
	enterImageDescription: "Enter a description of the image"
};
try{convertEntities(quicktagsL10n);}catch(e){};
/* ]]> */
</script>


<script type='text/javascript'>
/* <![CDATA[ */
var commonL10n = {
	warnDelete: "You are about to permanently delete the selected items.\n  \'Cancel\' to stop, \'OK\' to delete."
};
try{convertEntities(commonL10n);}catch(e){};
var wpAjax = {
	noPerm: "You do not have permission to do that.",
	broken: "An unidentified error has occurred."
};
try{convertEntities(wpAjax);}catch(e){};
var adminCommentsL10n = {
	hotkeys_highlight_first: "",
	hotkeys_highlight_last: ""
};
var thickboxL10n = {
	next: "Next &gt;",
	prev: "&lt; Prev",
	image: "Image",
	of: "of",
	close: "Close",
	noiframes: "This feature requires inline frames. You have iframes disabled or your browser does not support them."
};
try{convertEntities(thickboxL10n);}catch(e){};
/* ]]> */
</script>

<script type='text/javascript' src='http://pdstyles/wp-admin/load-scripts.php?c=1&amp;load=hoverIntent,common,jquery-color,jquery-ui-core,jquery-ui-sortable,postbox,wp-ajax-response,wp-lists,jquery-ui-resizable,jquery-ui-draggable,admin-comments,dashboard,thickbox,media-upload&amp;ver=037ef1718671a442fbb12a5cf0a08e83'></script>
<script type='text/javascript' src='http://pdstyles/wp-content/plugins/pd-styles/lib/js/colorpicker/js/colorpicker.js?ver=0.1'></script>
<script type='text/javascript' src='http://pdstyles/wp-content/plugins/pd-styles/lib/js/admin-main.js?ver=0.1'></script>

<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>

<!-- <link rel="stylesheet" href="/?scaffold&file=lib/css/frontend.css" type="text/css" /> -->

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
						<input id="pds_preview" type="button" class="button" value="<?php _e('Preview'); ?>" />
						<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
						
						<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" /> 
						<span class="response"> </span>
					</p>

				</form>
				
			</div>
		</div>
	</div>
</div>

<script>
	jQuery( '#pds_frontend' )
		.draggable({ handle: 'div.handle', containment: 'parent' })
		.find('input.pds_image_input')
			.change( update_image_thumbnail );
			
	setTimeout( function() {
		jQuery( '#pds_frontend' ).css('display', 'block')
	}, 500);
			

	function update_image_thumbnail( ) {
		var $ = jQuery;
		
		$(this).parent().find('a').attr('href', $(this).val() );
		$(this).parent().find('img').attr('src', $(this).val() );
		
	}
</script>
		