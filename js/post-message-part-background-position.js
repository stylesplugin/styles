(function () {
	var set_background_position = function () {
		var x_value = wp.customize.value('@setting@[x_value]')();
		var x_unit = wp.customize.value('@setting@[x_unit]')();
		var y_value = wp.customize.value('@setting@[y_value]')();
		var y_unit = wp.customize.value('@setting@[y_unit]')();
		var css_property = '';
		css_property += x_value.toString() + x_unit;
		css_property += ' ';
		css_property += y_value.toString() + y_unit;
		$('@selector@').css( 'background-position', css_property );
	};

	wp.customize( '@setting@[x_value]', function( value ) {
		value.bind( function() {
			set_background_position();
		} );
	} );
	wp.customize( '@setting@[x_unit]', function( value ) {
		value.bind( function() {
			set_background_position();
		} );
	} );
	wp.customize( '@setting@[y_value]', function( value ) {
		value.bind( function() {
			set_background_position();
		} );
	} );
	wp.customize( '@setting@[y_unit]', function( value ) {
		value.bind( function() {
			set_background_position();
		} );
	} );

}());
