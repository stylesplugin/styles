wp.customize( '@setting_font_size@', function( value ) {
	value.bind( function( newval ) {
		$('@selector@').css('font-size', newval + 'px' );
	} );
} );