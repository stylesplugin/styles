jQuery(function($) {
	
	$('input.slider').each(function() {
		var input_id = $(this).attr('id');
		var input_val = parseInt( $(this).val() );
		
		var opts = {
			min: 1,
			max: 100,
			range: "min",
			value: input_val + 1,
			slide: function(event, ui) {
				$(this).prev().val(ui.value - 1);
			}

		};
	
		var slide = $('<div></div>').insertAfter( $(this) ).slider(opts); 	// was <div id="slider-'+input_id+'"></div>
		
	});
	
	
});