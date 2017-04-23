jQuery( document ).ready( function ( $ ) {

	// $('#styles_installation_notices').remove();

	$( 'div.styles-notice .notice-dismiss' ).click( function(){
		$.get( 
			ajaxurl, 
			{
				'action': 'styles-dismiss-notice',
				'key': $(this).parent().data('key')
			}, 
			function( response ){
				console.log( response );
			}
		);

		// Notices in Customizer
		var $customizer_wrapper = $( '#styles_installation_notices' );
		if ( $customizer_wrapper.length ) {

			$(this).parent().slideUp( 400, function(){
				// If all notices hidden, hide wrapper
				if ( ! $(this).siblings().filter(':visible').length ) {
					$customizer_wrapper.slideUp( 200 );
				} 
			});

		}

	} );

} );