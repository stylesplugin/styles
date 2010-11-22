jQuery(document).ready( function($) {
	var data = {
		action: 'pdstyles-frontend-load'
	};
	
	$('head').append('<link rel="stylesheet" href="/?scaffold&file=lib/css/frontend.css" type="text/css" />');
	
	$.post(pds_frontend.ajaxurl, data, function( response ) {
		
		$('body').prepend(response);
		
	} );
});