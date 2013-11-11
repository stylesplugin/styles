<div class="wrap" id="styles-font-menu-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Menu', 'styles-font-menu'); ?></h2>

	<p><a href="#" id="generate-previews">Generate Font Previews</a></p>

	<h3 class="example-output">Example output</h3>
	<p><?php do_action( 'styles_font_menu' ); ?></p>

	<?php echo Markdown( file_get_contents( dirname( dirname( __FILE__ ) ) . '/readme.md' ) ); ?>


</div>

<script>

	/**
	 * Change heading font-family on menu change event
	 */
	(function($){

		var $headings = $( 'h2,h3', '#styles-font-menu-readme' );
		
		$('select.sfm').change( function(){
			$(this).data('stylesFontDropdown').preview_font_change( $headings );
		});

	})(jQuery);

	/**
	 * Generate Font Previews
	 */
	(function($){
		$('#generate-previews').click( function(){
			var $first = $('optgroup.google-fonts option:first');
			generate_preview( $first );
			return false;
		} );

		// Testing
		// setTimeout( function(){ $('#generate-previews').click(); }, 500 );

		function generate_preview( $option ){
			var name = $option.text();

			$('#generate-previews').after( '<br/>Generating '+ name );

			$.get( styles_google_options.admin_ajax, {
				"action": "styles-font-preview",
				"font-family": name
			}, function( data, textStatus, jqXHR ){

				var img = $('<img>').attr( 'src', data ).addClass('sfm-preview');

				$('#generate-previews').after( img ).after( '<br/>' );

				$next = $option.next( 'option' );
				if ( $next.length > 0 ) {
					generate_preview( $next );
				}else {
					$('#generate-previews').after( '<br/>Done' );
				}
			} );
		}
	})(jQuery);

	/**
	 * Modify readme.md content:
	 *  - Hide directions on how to get to this page
	 *  - Hide menu screenshot (live demo displayed above)
	 */
	(function($){

		// Remove image of example output
		$('h3.example-output').nextAll('h2').first().remove();
		$('img[src*="example-output.gif"]').remove();

		// Remove directions on how to get to this demo
		var $demo = $('h2:contains(Live Demo)');
		$demo.nextUntil('h2').remove();
		$demo.remove();

	})(jQuery);

</script>