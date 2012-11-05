jQuery( document ).ready( function ( $ ) {

	$( '.styles-subsection' ).hide();

	$( '.styles-subsection-title' ).click(function () {
		var _this = $( this );
		$( this ).parent().toggleClass( 'open' );
		_this.next().toggle();
		return false;
	} ).next().hide();

} );