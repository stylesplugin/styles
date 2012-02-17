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

		jQuery.ajax({
			url: cookie.href,
			success: function( data ){
				var style = jQuery('<style id="storm-scaffold-css">').html(data);
				jQuery('head #storm-scaffold-css').remove();
				jQuery('head').append(style);
			}
		});
	}

	setTimeout('storm_check_preview()', 1000);
}



