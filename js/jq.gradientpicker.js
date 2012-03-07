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
		
		var $markerWrap, $preview, $stops, $presets;
		
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
			
			$presets = $('<div class="presets" />');
			$preview = $('<div class="grad-preview" />');
			$markerWrap = $('<div class="stop-markers" />').click( addMarker );
			
			$stops = plugin.settings.$stops;
			
			$element.append( $presets, $preview, $markerWrap );
			
			stopsInputToArray();
			stopsArrayToMarkers();
			stopsMarkersToArray();
			
			setupPresets();
			
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
		
		// --- Presets
		
		var setupPresets = function() {
			var li, css,
				cssTemplate = 'background: -moz-linear-gradient(VAL);background: -webkit-linear-gradient(VAL);background: -o-linear-gradient(VAL);background: -ms-linear-gradient(VAL);background: linear-gradient(VAL);',
				presets = [
					'#000000 0%, #ffffff 100%'
					,'#e2e2e2 0%, #dbdbdb 50%, #d1d1d1 51%, #fefefe 100%'
					,'#d8e0de 0%, #aebfbc 22%, #99afab 33%, #8ea6a2 50%, #829d98 67%, #4e5c5a 82%, #0e0e0e 100%'
					,'#b8e1fc 0%,#a9d2f3 10%,#90bae4 25%,#90bcea 37%,#90bff0 50%,#6ba8e5 51%,#a2daf5 83%,#bdf3fd 100%'
					,'#3b679e 0%,#2b88d9 50%,#207cca 51%,#7db9e8 100%'
					,'#b3dced 0%,#29b8e5 50%,#bce0ee 100%'
					,'#cedbe9 0%,#aac5de 17%,#6199c7 50%,#3a84c3 51%,#419ad6 59%,#4bb8f0 71%,#3a8bc2 84%,#26558b 100%'
					,'#e1ffff 0%,#e1ffff 7%,#e1ffff 12%,#fdffff 12%,#e6f8fd 30%,#c8eefb 54%,#bee4f8 75%,#b1d8f5 100%'
					,'#e4f5fc 0%, #bfe8f9 50%, #9fd8ef 51%, #2ab0ed 100%'
					// ,'#00b7ea 0%, #009ec3 100%'
					,'#ff0000 0%,#ff9d00 20%,#fbff00 40%,#1fcf00 60%,#0011ff 80%,#c910c9 100%'
				],
				$list = $('<ul/>');
			
			$.each( presets, function( index, value ){
				
				css = cssTemplate.replace(/VAL/g, '-45deg, '+value);
				
				li = $('<li/>')
					.attr( 'style', css )
					.data('value', value)
					.click( loadPreset );
				
				$list.append( li );
				
			});
			
			$presets.append( $list );
			
			// If we don't have a gradient loaded by now,
			// load the first preset
			if ( stops.length == 1 ) {
				$presets.find('li:first').click();
			}
			
		}
		
		var loadPreset = function() {
			$stops.val( $(this).data('value') );
			
			stopsInputToArray();
			stopsArrayToMarkers();
			$stops.change();
			// stopsMarkersToArray();
			
		}
		
		// --- Sliders
		
		var markerSlide = function(event, ui) {
			var thisY = $(this).offset().top;
			var mouseY = event.originalEvent.pageY;

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
		
		var setHandleColor = function(el, hex, newMarker) {
			$(el).each(function() {
				$(this).data('color', hex).find('a').css('backgroundColor', '#'+hex);
			});
			
			if ( newMarker !== true ){
				stopsMarkersToArray();
			}
		}
		
		var addMarker = function( e, value, hex ) {
			if ( 'object' == typeof(e.target) && $(e.target).is('a') ) {
				return;
			}
			
			if ( e.layerX === undefined ) {
				var offset = $(this).offset();
				if ( offset != null ) { e.layerX = e.pageX - offset.left; }
			}
			
			if ( value === undefined ) { value  = Math.round( e.layerX / $(this).width() * 100 ); }
			if (   hex === undefined ) { hex = '000000'; }
			
			if ( isNaN(value) ) return;
			
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
			
			setHandleColor( marker, hex, true );
			
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
