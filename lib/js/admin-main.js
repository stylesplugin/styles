
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
	
	$('div.gradpicker').gradientPicker();

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

// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos
(function($) {

    // here we go!
    $.gradientPicker = function(element, options) {

        // plugin's default options
        // this is private property and is  accessible only from inside the plugin
        var defaults = {

            // foo: 'bar',

            // if your plugin is event-driven, you may provide callback capabilities for its events.
            // execute these functions before or after events of your plugin, so that users may customize
            // those particular events without changing the plugin's code
            // onFoo: function() {}

        }

        // to avoid confusion, use "plugin" to reference the current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('gradientPicker').settings.propertyName from outside the plugin, where "element" is the
        // element the plugin is attached to;
        plugin.settings = {}

        var $element = $(element),  // reference to the jQuery version of DOM element the plugin is attached to
             element = element;        // reference to the actual DOM element
		
		var $markerWrap;
		var $preview;
		
		var initComplete = false;

		var stops = new Array();
		
		var colorPickerOpts = {
			eventName: 'dblclick',
			onSubmit: function(hsb, hex, rgb, el) {
				setHandleColor(el, hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				if ( $(this).data('color') ) {
					$(this).ColorPickerSetColor( $(this).data('color') );
				}
			},
			onHide: function (colpkr) {
				var el = $(colpkr).data('colorpicker').el;
				var hex = $(colpkr).find('div.colorpicker_hex input').val();
			
				setHandleColor(el, hex);
			
				return true;
			},
			onChange: function(hsb, hex, rgb) {
				var el = $(this).data('colorpicker').el;
			
				setHandleColor(el, hex);
			}
		};
		
		var sliderOpts = {
			min: 0,
			max: 100,
			value: 0,
			stop: function(){ stopsArrayToInput(); },
			slide: function (){ stopsMarkersToArray(); }
		};

        // Constructor
		plugin.init = function() {
            // the plugin's final properties are the merged default and user-provided options (if any)
            plugin.settings = $.extend({}, defaults, options);
			
			$preview = $('<div class="grad-preview" />');
			$markerWrap = $('<div class="stop-markers" />').click( addMarker );
			
			$element.append( $preview ).append( $markerWrap );
			
			stopsInputToArray();
			stopsArrayToMarkers();
			stopsMarkersToArray();
			
			initComplete = true;
        }

		//
        // public methods
		//
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('gradientPicker').publicMethod(arg1, arg2, ... argn) from outside the plugin, where "element"
        // is the element the plugin is attached to;

		var stopsInputToArray = function () {
			stops.length = 0;
			
			var tmp = $element.find('input.stops').val();
			tmp = tmp.split(',');
			
			$.each(tmp, function(i, val){
				val = $.trim(val);
				val = val.split(' ');
				
				var hex = val[0].replace('#', ''),
					value = val[1].replace('%', '');
				
				stops.push( {
					hex: hex,
					value: value,
					toString: function() { return '#'+this.hex+' '+this.value+'%'; }
				});
				
			});
			plugin.updatePreview();
		}
		
		var stopsArrayToMarkers = function () {
			
			$markerWrap.find('div.marker').slider('destroy').remove();
			$.each( stops, function(i, stop){
				addMarker( {}, stop.value, stop.hex );
			} );

		}
		
		var stopsArrayToInput = function() {
			if ( stops.toString() != $element.find('input.stops').val() ) {
				$element.find('input.stops').val(stops).change();
			}
		}
		
		plugin.updatePreview = function() {
			$preview.css('background', '-moz-linear-gradient(0deg, '+stops+')');
		}
		
		var stopsMarkersToArray = function() {
			if ( !initComplete ) { return; }
			
			stops.length = 0;
			
			$markerWrap.find('div.marker').each(function(){
				var hex   = $(this).data('color'),
				    value = $(this).slider('value');
				
				stops.push({
					hex: hex,
					value: value,
					toString: function() { return '#'+this.hex+' '+this.value+'%'; }
				});
			});
			
			stops.sort( function( a, b ){
				if ( a.value < b.value ) { return -1; }	// Less than 0: Sort "a" to be a lower index than "b"
				if ( a.value > b.value ) { return  1; }	// Greater than 0: Sort "b" to be a lower index than "a".
				return 0; 								// Zero: "a" and "b" should be considered equal, and no sorting performed.
			});
			
			plugin.updatePreview();
		}
		
		//
		// Private Methods
		//
		
		var markerSlide = function(event, ui) {
			// console.log( ui.value );
			stopsMarkersToArray();
		}
		
		var setHandleColor = function(el, hex) {
			$(el).each(function() {
				$(this).data('color', hex).find('a').css('backgroundColor', '#'+hex);
			});
			
			stopsMarkersToArray();
		}
		
		var addMarker = function( e, value, hex ) {
			if ( 'object' == typeof(e.target) && $(e.target).is('a') ) {
				return;
			}
			if ( value === undefined ) { value  = Math.round( e.layerX / $(this).width() * 100 ); }
			if (   hex === undefined ) { hex = '000000'; }
			
			var marker = $('<div class="marker"/>');

			sliderOpts.value = value;
			marker.slider( sliderOpts ).unbind( 'click' ).ColorPicker( colorPickerOpts ).click( removeMarker );
			
			$markerWrap.append( marker );
			
			setHandleColor( marker, hex );
			
		}
		
		var removeMarker = function(e) {
			if ( e.altKey || e === true ) {
				$(this).slider('destroy').remove();
				stopsMarkersToArray();
				stopsArrayToInput();
				return false;
			}
		}

		var rgbtohex = function(rgbString) {
			var parts = rgbString
			        .match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/)
			;
			// parts now should be ["rgb(0, 70, 255", "0", "70", "255"]

			delete (parts[0]);
			for (var i = 1; i <= 3; ++i) {
			    parts[i] = parseInt(parts[i]).toString(16);
			    if (parts[i].length == 1) parts[i] = '0' + parts[i];
			}
			return '#'+parts.join(''); // "0070ff"
		}


        // a private method. for demonstration purposes only - remove it!
        // var foo_private_method = function() {}

        // fire up the plugin!
        // call the "constructor" method
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.gradientPicker = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('gradientPicker')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.gradientPicker(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('gradientPicker').publicMethod(arg1, arg2, ... argn) or
                // element.data('gradientPicker').settings.propertyName
                $(this).data('gradientPicker', plugin);

            }

        });

    }

})(jQuery);




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