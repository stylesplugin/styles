jQuery(function($) {
	
	// Slider
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
	
	// Color Picker
	$('input.pdm_hex').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onHide: function (colpkr) {
			var el = $(colpkr).data('colorpicker').el;
			var hex = $(colpkr).find('div.colorpicker_hex input').val();

			$(el).val(hex).css({
				'backgroundColor': '#' + hex,
				'color': '#' + hex
			});
			
			return true;
		},
		onChange: function(hsb, hex, rgb) {
			var el = $(this).data('colorpicker').el;
			$(el).val(hex).css({
				'backgroundColor': '#' + hex,
				'color': '#' + hex
			});
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	})
	.each(function(){
		$(this).css({
		'backgroundColor': '#' + $(this).val(),
		'color': '#' + $(this).val()
		});
	});
	
	
});