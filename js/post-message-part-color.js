wp.customize( '@setting@', function( value ) {
	value.bind( function( newval ) {
		$('@selector@').css('color', newval );
	} );
} );