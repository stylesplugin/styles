jQuery( document ).ready( function ( $ ) {

	styles_installation_notices();
	/**
	 * Prompt users if a notice is sent by styles-admin.php
	 */
	function styles_installation_notices() {
		if ( wp_styles_notices.length == 0 ) {
			return;
		}

		var $notices = $( '<div id="styles_installation_notices"></div>' )
		             .addClass( 'accordion-section-content' )
		             .show();

		jQuery.each( wp_styles_notices, function( index, value ){
			$notices.append( value );
		});

		$( '#customize-info' ).prepend( $notices );
	}

	add_control_label_spans();
	/**
	 * Wrap content after long-dash in span
	 */
	function add_control_label_spans() {
		// Long dash, not hyphen
		var delimeter = '::';

		$( 'span.customize-control-title:contains(' + delimeter + ')' ).each( function(){
			var html, parts;

			html = $(this).html();
			parts = html.split( delimeter );

			if ( 2 == parts.length ) {
				html = parts[0] + '<span class="styles-type">' + parts[1] + '</span>';
				$(this).html( html );
			}

		});
	}

	chosen_fix_overflow();
	/**
	 * Set overflow and height on section wrapping font menu
	 * Allows menu to spill out without messing up rest of layout
	 */
	function chosen_fix_overflow() {
		$('select.sfm').bind( 'chosen:showing_dropdown', function(){
			var $wrapper = $(this).parent().parent();
			$wrapper.css( 'height', $wrapper.height() +'px' );
			$wrapper.css('overflow', 'visible');
		} );
	}

} );