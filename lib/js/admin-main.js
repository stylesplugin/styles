jQuery(function($) {

	// AJAX Preview Button
	$('#pds_preview').click( function(){
		
		var data = {
			action: 'pdstyles_preview',
			rand: Math.random()
		};
		
		var response_wrapper = $(this).nextAll('span.response');
		var waiting = $(this).nextAll('img.waiting');
		
		waiting.show();
		
		$.post(ajaxurl, data, function( response ) {
			$(response_wrapper).html( response );
			waiting.hide();
		});

		return false;
	});

	// Slider
	//	$('input.slider').each(function() {
	//		var input_id = $(this).attr('id');
	//		var input_val = parseInt( $(this).val() );
	//		
	//		var opts = {
	//			min: 1,
	//			max: 200,
	//			range: "min",
	//			value: input_val + 1,
	//			slide: function(event, ui) {
	//				$(this).prev().val(ui.value - 1);
	//			}
    //	
	//		};
	//	
	//		var slide = $('<div></div>').insertAfter( $(this) ).slider(opts); 	// was <div id="slider-'+input_id+'"></div>
	//		
	//	});
	
	// Color Picker
	$('input.pds_color_input').ColorPicker({
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
	

	// thickbox settings
	tb_position = function() {
		var tbWindow = $('#TB_window');
		var width = $(window).width();
		var H = $(window).height();
		var W = ( 720 < width ) ? 720 : width;

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 );
			tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
			if ( typeof document.body.style.maxWidth != 'undefined' )
				tbWindow.css({'top':'20px','margin-top':'0'});
			$('#TB_title').css({'background-color':'#222','color':'#cfcfcf'});
		};

		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
		});
	};

	$(window).resize( function() { tb_position() } );
	
	
	
});


// Images Uploader
function show_image_uploader(id) {
	blogicons = {
		uploadid: id
	}
	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
}

// send image url to the options field
function send_to_editor(h) {
	// get the image src from the html code in h
	var re = new RegExp('<img src="([^"]+)"');
	var m = re.exec(h);
	jQuery('#'+blogicons.uploadid).val(m[1]);
	tb_remove();
}