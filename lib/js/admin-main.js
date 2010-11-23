jQuery(function($) {

	// AJAX Preview Button
	$('#pds_preview').click( pds_preview );
	$('input, select', '#pds_frontend').change( pds_preview_change );
	
	$('a.value-toggle', '#pdm_form').click( pds_value_toggle );
	
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
			
			$(el).change();
			
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
	tb_show('', '/wp-admin/media-upload.php?type=image&amp;TB_iframe=true');
}

// send image url to the options field
function send_to_editor(h) {
	// get the image src from the html code in h
	var re = new RegExp('<img src="([^"]+)"');
	var m = re.exec(h);
	jQuery('#'+blogicons.uploadid).val(m[1]).change();
	tb_remove();
}

function pds_preview() {
	var $ = jQuery;
	
	var waiting = $(this).nextAll('img.waiting').show();
	window.response_wrapper = $(this).nextAll('span.response').html('');
	
	// Get form info
	var data = $(this).parents('form:first').serialize() + '&preview=1';
	
	
	$.post(ajaxurl, data, function( response ) {
		
		$( response.id ).remove();
		$('head').append('<link id="'+response.id+'" rel="stylesheet" href="'+response.href+'" type="text/css" />');
		
		$(response_wrapper).html( response.message );
		waiting.hide();
		
		setTimeout( function() {
			response_wrapper.fadeOut(500, function() {
				$(this).text('').show();
			});
		}, 2000 );
		
		
	}, 'json');

	return false;
}

function pds_preview_change() {
	var $ = jQuery;
	
	var waiting = $('#pds_frontend img.waiting').show();
	window.response_wrapper = $('#pds_frontend span.response').html('');
	
	// Get form info
	var data = $('#pds_frontend form:first').serialize() + '&preview=1';
	
	
	$.post(ajaxurl, data, function( response ) {
		
		$( response.id ).remove();
		$('head').append('<link id="'+response.id+'" rel="stylesheet" href="'+response.href+'" type="text/css" />');
		
		$(response_wrapper).html( response.message );
		waiting.hide();
		
		setTimeout( function() {
			response_wrapper.fadeOut(500, function() {
				$(this).text('').show();
			});
		}, 2000 );
		
		
	}, 'json');

	return false;
}

function pds_value_toggle(){
	var $ = jQuery;
	var old_val = $(this).next().val();
	var options =  $(this).data('options');
	var index = $.inArray( old_val, options );

	if ( -1 != index && (index+1) != options.length ) {
		$(this).next().val( options[ index + 1 ] );
	}else {
		$(this).next().val( options[0] );
	}
	
	$(this).removeClass( $(this).data('type') +'-'+ old_val ).addClass( $(this).data('type') +'-'+ $(this).next().val() );
	
	$(this).next().change();
	
	return false;
}
	