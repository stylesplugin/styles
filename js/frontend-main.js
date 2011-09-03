jQuery(document).ready( function($) {
	
	storm_check_preview();
	
});

function storm_check_preview() {
	var cookie = {
		update: jQuery.cookie('pdstyles_preview_update'),
		id: jQuery.cookie('pdstyles_preview_id'),
		href: jQuery.cookie('pdstyles_preview_href')
	};

	if ( cookie.update == 1 ) {
		jQuery.cookie('pdstyles_preview_update', '0', {path: '/'});

		jQuery( 'link[id="bsm-scaffold-css"]' ).attr('href', cookie.href);
	}

	setTimeout('storm_check_preview()', 1000);
}



