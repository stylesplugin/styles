
jQuery(function($) {
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
	
	$('div.gradPicker', '#StormForm').gradientPicker();
	$('div.bgPicker', '#StormForm').bgPicker();

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
	
	
	// AJAX Preview Button
	$('input.storm-submit', '#StormForm').click( pds_preview_change );
	$('input, select', '#StormForm').change( pds_preview_change );
	$('input.pds_image_input', '#StormForm').change( update_image_thumbnail );
	$('a.value-toggle', '#StormForm').click( pds_value_toggle );
	
	$('div.types input', '#StormForm').change( pds_background_type );
});



// Images Uploader
function show_image_uploader(id) {
	blogicons = {
		uploadid: id
	}
	tb_show('', storm_admin.mediaUploadURL+'?type=image&amp;TB_iframe=true');
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
	var waiting = $('#StormForm img.waiting').show();
	window.response_wrapper = $('#StormForm span.response').html('');
	
	// Get form info
	var data = $('#StormForm').serialize();
	
	if ( ! $(this).hasClass('storm-submit') ) {
		data = data + '&preview=1';
	}

	$.post(ajaxurl, data, function( response ) {
		if ( response.message.indexOf('updated') != -1 ) {
			$.cookie('pdstyles_preview_update', '1', {path: '/'});
			$.cookie('pdstyles_preview_id', response.id, {path: '/'});
			$.cookie('pdstyles_preview_href', response.href, {path: '/'});
		}
		
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

// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos
(function($) {
	$.bgPicker = function(element, options) {

		// plugin's default options
		// this is private property and is	accessible only from inside the plugin
		var defaults = {

			// foo: 'bar',

			// if your plugin is event-driven, you may provide callback capabilities for its events.
			// execute these functions before or after events of your plugin, so that users may customize
			// those particular events without changing the plugin's code
			// onFoo: function() {}

		}

		// to avoid confusions, use "plugin" to reference the current instance of the object
		var plugin = this;

		// this will hold the merged default, and user-provided options
		// plugin's properties will be available through this object like:
		// plugin.settings.propertyName from inside the plugin or
		// element.data('bgPicker').settings.propertyName from outside the plugin, where "element" is the
		// element the plugin is attached to;
		plugin.settings = {}

		var $element = $(element),	// reference to the jQuery version of DOM element the plugin is attached to
			 element = element;		// reference to the actual DOM element
			
		var $ui = $element.find('div.ui'),
			$type_links = $element.find('div.types a'),
			$active  = $element.find('div.data input[name$="[active]"]'),
			$css   = $element.find('div.data input[name$="[css]"]'),
			$image = $element.find('div.data input[name$="[image]"]'),
			$stops = $element.find('div.data input[name$="[stops]"]'),
			$color = $element.find('div.data input[name$="[color]"]');
			
		var timeoutID;
			

		// the "constructor" method that gets called when the object is created
		plugin.init = function() {
			// the plugin's final properties are the merged default and user-provided options (if any)
			plugin.settings = $.extend({}, defaults, options);

			$type_links.click( type_click );
			type_switch( $active.val() );
			
			$color.add( $stops ).add( $image ).change( update_css );
			$image.change( update_image_preview );
			
		}
		
		//
		// public methods
		// 
		// plugin.foo_public_method = function() {}
		
		
		//
		// private methods
		//
		var update_css = function () {
			$css.val( $(this).val() ).change();
		}
		
		var type_click = function() {
			var new_type = $(this).data('type');
		
			if ( new_type == $active.val() ) {
				return false;
			}else {
				type_switch( new_type );
			}
			
			return false;
		}
		
		var type_switch = function( type ) {
			$ui.empty();
			
			$type_links.removeClass('active').each( function(){
				if (type == $(this).data('type')) { $(this).addClass('active'); }
			});
			
			switch ( type ) {
				case 'color':       load_color();       break;
				case 'transparent': load_transparent(); break;
				case 'gradient':    load_gradient();    break;
				case 'image':       load_image();       break;
				case 'hide':        load_hide();       break;
			}
			
			$active.val( type );
		}
		
		var load_hide = function() {
			$css.val('hide').change();
		}
		
		var load_transparent = function() {
			$css.val('transparent').change();
		}
		
		var load_gradient = function() {
			var gradientPicker = $('<div class="gradPicker" />').gradientPicker({
				$stops: $stops
			});
			
			$css.val( $stops.val() ).change();
			
			$ui.append( gradientPicker );
		}
		
		var load_image = function() {
			var imagePicker = $('<input type="button" class="button" value="Select Image" />').click( show_image_uploader );
			var imagePreview = $('<img class="imagePreview" />').attr('src', $image.val() );
			
			$css.val( $image.val() ).change();
			
			$ui.append( imagePicker ).append( imagePreview );
		}
		
		var update_image_preview = function() {
			$ui.find('img.imagePreview').attr('src', $image.val() );
		}
		
		var load_color = function() {
			
			$color.data('color',  $color.val().replace('#', '') );
			$css.val( $color.val() ).change();
			
			var colorPicker = $('<div/>').jPicker({
				images: {
					clientPath: storm_admin.pluginURL + '/js/jpicker/images/'
					,colorMap: {
						width: 128
						,height: 128
					}
					,colorBar: {
						width: 20
						,height: 128
					}
				}
				,window: {
					alphaSupport: true
				}
				,color: {
					active: new $.jPicker.Color({ hex: $color.val() })
				}
				,localization: {
					text: {
						newColor: ' '
						,currentColor: ' '
					}
				}
			},
			function(color, context) { /* Okay button clicked */ },
			function(color, context) { /* Live color slide */
				if ( color.val() == null ) {
					var rgba = 'transparent';
				}else {
					var alpha = Math.round( color.val('a') / 255 * 100 ) / 100;
					var rgba = 'rgba('+color.val('r')+','+color.val('g')+','+color.val('b')+','+alpha+')';
				}

				$color.val( rgba );
				$css.val( rgba );
			},
			function(color, context) { /* Cancel button clicked */ }
			);
			
			$ui.append( colorPicker );
			
			/*
			var colorPicker = $('<div class="colorPicker" />').ColorPicker( {
				onSubmit: function(hsb, hex, rgb, el) {
					setColor(el, hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					if ( $color.data('color') ) {
						$(this).ColorPickerSetColor( $color.data('color') );
					}
				},
				onHide: function (colpkr) {
					var el = $(colpkr).data('colorpicker').el;
					var hex = $(colpkr).find('div.colorpicker_hex input').val();

					setColor(el, hex);

					return true;
				},
				onChange: function(hsb, hex, rgb) {
					var el = $(this).data('colorpicker').el;

					setColor(el, hex);
				}
			} ).css( 
				'backgroundColor', $color.val()
			);
			*/
		}
		
		var setColor = function(el, hex) {
			$(el).each(function() {
				$color.val( '#'+hex ).data('color', hex);
				$(this).css('backgroundColor', $color.val() );
			});
			
			$css.val( $color.val() );
			
			clearTimeout(timeoutID);
			
			timeoutID = setTimeout( function(){
				$css.change();
			}, 500);
		}

		var show_image_uploader = function() {
			blogicons = {
				uploadid: $image.attr('id')
			}
			tb_show('', storm_admin.mediaUploadURL+'?type=image&amp;TB_iframe=true');
		}

		plugin.init();
		
	}

	// add the plugin to the jQuery.fn object
	$.fn.bgPicker = function(options) {

		// iterate through the DOM elements we are attaching the plugin to
		return this.each(function() {

			// if plugin has not already been attached to the element
			if (undefined == $(this).data('bgPicker')) {

				// create a new instance of the plugin
				// pass the DOM element and the user-provided options as arguments
				var plugin = new $.bgPicker(this, options);

				// in the jQuery version of the element
				// store a reference to the plugin object
				// you can later access the plugin and its methods and properties like
				// element.data('bgPicker').publicMethod(arg1, arg2, ... argn) or
				// element.data('bgPicker').settings.propertyName
				$(this).data('bgPicker', plugin);

			}

		});

	}

})(jQuery);