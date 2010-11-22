jQuery(document).ready( function($) {
	var data = {
		action: 'pdstyles-frontend-load'
	};
	
	$.post(pds_frontend.ajaxurl, data, function( response ) {
		
		$('body').prepend(response);
		
	} );
	
	
	
});

function pds_frontend_init() {
	jQuery( '#pds_frontend' )
		.draggable({ handle: 'div.handle', containment: 'parent' })
		.css('display', 'block')
		.find('input.pds_image_input')
			.change( update_image_thumbnail );

	jQuery('#pds_save').click( pds_save );

}

function pds_save() {
	var $ = jQuery;

	$('#pds_waiting').show();
	$('#pds_response').html('');

	// Get form info
	var data = $('#pds_frontend form:first').serialize();
	
	// + '&preview=1'
	
	$.post(ajaxurl, data, function( response ) {

		$( response.id ).remove();
		$('head').append('<link id="'+response.id+'" rel="stylesheet" href="'+response.href+'" type="text/css" />');

		$('#pds_response').html( response.message );
		$('#pds_waiting').hide();

		setTimeout( function() {
			response_wrapper.fadeOut(500, function() {
				$(this).text('').show();
			});
		}, 2000 );


	}, 'json');

	return false;
}
		

function update_image_thumbnail( ) {
	var $ = jQuery;
	
	$(this).parent().find('a').attr('href', $(this).val() ).removeClass('hidden');
	$(this).parent().find('img').attr('src', $(this).val() );
	
}