
jQuery(function($) {
	// AJAX Preview Button
	$('input.pds-submit', '#pdm_form').click( pds_preview_change );
	
	$('input, select', '#pdm_form').change( pds_preview_change );
	$('input.pds_image_input', '#pdm_form').change( update_image_thumbnail );
	$('a.value-toggle', '#pdm_form').click( pds_value_toggle );
	
	$('div.types input', '#pdm_form').change( pds_background_type );
	
	// Generic Slider
	$('input.slider').each(function() {
		// Show/hide slider on input click
		$(this).click(function( e ) {
			
			// Only init sliders on click for faster page load
			if ( ! $(this).parent().next().hasClass('pds_slider_ui') ) {
				
				var input_id = $(this).attr('id');
				var input_val = parseInt( $(this).val() );
				var opts = {
					min: $(this).data('min'),
					max: $(this).data('max'),
					range: "min",
					value: input_val + 1,
					slide: function(event, ui) {
						$(this).prev().find('input').val(ui.value - 1);
					},
					change: function(event, ui) {
						$(this).prev().find('input').change();
					}
				};
				
				$(this).parent().after('<div class="pds_slider_ui"></div>')
				$(this).parent().next().slider(opts);

			}
			
			$(this).parent().next().toggle();

		});
	});
	
	// Gradient Slider
	$( '.stop-markers .marker:eq(0)' ).slider({
		min: 0,
		max: 100,
		value: 0,
		slide: marker_slide
	});
	$( '.stop-markers .marker:eq(1)' ).slider({
		min: 0,
		max: 100,
		value: 100,
		slide: marker_slide
	});
	$('.stop-markers .marker').unbind('click');
	$('.stop-markers .marker').dblclick( handle_color_picker );
	// $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +" - $" + $( "#slider-range" ).slider( "values", 1 ) );
	
	function marker_slide(event, ui){
		// console.log( ui.value );
	}
	
	function update_handle_color( ibox ) {
		var index = $( ibox ).index();

		var hex = $(ibox).val();
		
		$( ibox ).parent().find('div.stop-markers > div:eq('+index+') a')
			.css({
				'backgroundColor': '#' + hex
			});
	}
	
	function handle_color_picker( ) {
		var index = $( this ).index();
		console.log( index );
		var found = $( this ).parent().parent().parent().find('input.grad_color').eq(index).click();
		
		return false;
	}
	

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
			update_handle_color(el);
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
		
		update_handle_color( $(this) );
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

function pds_preview_change() {
	var $ = jQuery;
	
	// Display waiting graphic
	var waiting = $('#pdm_form img.waiting').show();
	window.response_wrapper = $('#pdm_form span.response').html('');
	
	// Get form info
	var data = $('#pdm_form').serialize();
	
	if ( ! $(this).hasClass('pds-submit') ) {
		data = data + '&preview=1';
	}
	
	$.post(ajaxurl, data, function( response ) {
		
		$.cookie('pdstyles_preview_update', '1', {path: '/'});
		$.cookie('pdstyles_preview_id', response.id, {path: '/'});
		$.cookie('pdstyles_preview_href', response.href, {path: '/'});
		
		$(response_wrapper).html( response.message );
		waiting.hide();
		
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
	
	var new_val = $(this).next().val();
	var old_class = $(this).data('type') +'-'+ old_val.replace('.', '');
	var new_class = $(this).data('type') +'-'+ new_val.replace('.', '');
	
	$(this).removeClass( old_class ).addClass( new_class );
	
	$(this).next().change();
	
	return false;
}

function update_image_thumbnail( ) {
	var $ = jQuery;
	
	$(this).parent().find('a').attr('href', $(this).val() ).removeClass('hidden');
	$(this).parent().find('img').attr('src', $(this).val() );
	
}

function pds_background_type() {
	var $ = jQuery;
	
	var target = $(this).parent().parent().nextAll('div').filter('div.pds_' + $(this).val() );

	if ( $(this).is(':checked') ) {
		target.show();
		
		// Uncheck Gradient if Image is checked
		if ( 'Image' == $(this).val() ) $(this).parent().parent().find('input[value="Gradient"]').attr('checked', '').change();
		// Uncheck Image if Gradient is checked
		if ( 'Gradient' == $(this).val() ) $(this).parent().parent().find('input[value="Image"]').attr('checked', '').change();
		
	}else {
		target.hide();
	}
}