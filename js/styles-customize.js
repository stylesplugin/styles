jQuery( document ).ready( function ( $ ) {

	/*
	$( '.styles-subsection' ).hide();

	$( '.styles-subsection-title' ).click(function () {
		var _this = $( this );
		$( this ).parent().toggleClass( 'open-subsection' );
		_this.next().toggle();
		return false;
	} ).next().hide();
	*/

	var text_color = $('li.customize-control-text_formatting input.color-picker-hex');
	text_color.wpColorPicker();

} );
