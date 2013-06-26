wp.customize( '@setting_font_size@', function( value ) {
	value.bind( function( newval ) {
		if ( false == newval ) { newval = ''; }else { newval = newval + 'px' }
		$('@selector@').css('font-size', newval );
	} );
} );