// Gradient Picker plugin
// @author pdclark
(function($) {

	// here we go!
	$.gradientPicker = function(element, options) {

		// to avoid confusion, use "plugin" to reference the current instance of the object
		var plugin = this;

		// this will hold the merged default, and user-provided options
		// plugin's properties will be available through this object like:
		// plugin.settings.propertyName from inside the plugin or
		// element.data('gradientPicker').settings.propertyName from outside the plugin, where "element" is the
		// element the plugin is attached to;
		plugin.settings = {}

		var $element = $(element),  // reference to the jQuery version of DOM element the plugin is attached to
			element = element;		// reference to the actual DOM element
		
		var $markerWrap, $preview, $stops;
		
		var initComplete = false;
		
		var timeoutID;
		
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
		
		// plugin's default options
		// this is private property and is  accessible only from inside the plugin
		var defaults = {

			// foo: 'bar',
			$stops: $element.find('input.stops'),

			// if your plugin is event-driven, you may provide callback capabilities for its events.
			// execute these functions before or after events of your plugin, so that users may customize
			// those particular events without changing the plugin's code
			// onFoo: function() {}

		}

		// Constructor
		plugin.init = function() {
			// the plugin's final properties are the merged default and user-provided options (if any)
			plugin.settings = $.extend({}, defaults, options);
			
			$preview = $('<div class="grad-preview" />');
			$markerWrap = $('<div class="stop-markers" />').click( addMarker );
			
			$stops = plugin.settings.$stops;
			
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

		plugin.updatePreview = function() {
			var css = 'background:-webkit-linear-gradient(0deg, '+stops+');background:-moz-linear-gradient(0deg, '+stops+');background:-ms-linear-gradient(0deg, '+stops+');background:-o-linear-gradient(0deg, '+stops+');background:linear-gradient(0deg, '+stops+');';
			
			$preview.attr('style', css);

			stopsArrayToInput();
		}
		
		//
		// Private Methods
		//
		
		// --- Data passing
		
		var stopsInputToArray = function () {
			stops.length = 0;
			
			var tmp = $stops.val();
			tmp = tmp.split(',');
			
			$.each(tmp, function(i, val){
				val = $.trim(val);
				val = val.split(' ');
				
				if ( val[0] != undefined ){ var hex = val[0].replace('#', ''); }
				else { var hex = '#000000'; }
				
				if ( val[1] != undefined ){ var value = val[1].replace('%', ''); }
				else { var value = ''; }
				
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
			if ( stops.toString() != $stops.val() ) {
				
				clearTimeout(timeoutID);
				
				timeoutID = setTimeout( function(){
					$stops.val(stops).change();
				}, 500);
				
			}
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
		
		// --- Sliders
		
		var markerSlide = function(event, ui) {
			var thisY = $(this).offset().top;
			var mouseY = event.pageY;
			
			if ( (mouseY - thisY) > 25 ) { // Mouse has moved more than X pixels below slider
				if ( ! $(this).data('remove') ) { // Slider not yet flagged 'remove'
					$(this).data('remove', true); // Flag as 'remove'
					$(this).css('opacity', '.2');
				}
			}else { // Mouse within X pixels of slider
				if ( $(this).data('remove') ) { // Slider still flagged
					$(this).data('remove', false); // Unflag it
					$(this).css('opacity', '1');
				}
			}
			
			stopsMarkersToArray();
		}
		
		var markerStop = function( event, ui ) {
			if ( $(this).data('remove') ) {
				$(this).slider('destroy').remove();
				stopsMarkersToArray();
			}
			stopsArrayToInput();
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
			
			var sliderOpts = {
				min: 0,
				max: 100,
				value: value,
				stop: markerStop,
				slide: markerSlide
			};
			
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
		
		// --- Utility

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
