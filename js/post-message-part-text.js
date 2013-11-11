wp.customize( '@setting_font_size@', function( value ) {
	value.bind( function( newval ) {
		if ( false === newval ) { newval = ''; }else { newval = newval + 'px'; }
		$('@selector@').css('font-size', newval );
	} );
} );

wp.customize( '@setting_font_family@', function( value ) {
	value.bind( function( newval ) {
		var font = JSON.parse( newval );

		if ( undefined !== font.import_family ) {
			var template = "@import url(//fonts.googleapis.com/css?family=@import_family@);/r";
			var atImport = template.replace( '@import_family@', font.import_family );
			$( '<style>' ).append( atImport ).appendTo( 'head' );
		}

		$('@selector@').css('font-family', font.family );
	} );
} );