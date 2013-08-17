/*global wp, jQuery, wp_styles_notices */
jQuery( document ).ready( function ( $ ) {

	styles_installation_notices();
	/**
	 * Prompt users if a notice is sent by styles-admin.php
	 */
	function styles_installation_notices() {
		if ( wp_styles_notices.length === 0 ) {
			return;
		}

		var $notices = $( '<div id="styles_installation_notices"></div>' )
		             .addClass( 'accordion-section-content' )
		             .show();

		jQuery.each( wp_styles_notices, function( index, value ){
			$notices.append( value );
		});

		$( '#customize-info' ).prepend( $notices );
	}

	add_control_label_spans();
	/**
	 * Wrap content after long-dash in span
	 */
	function add_control_label_spans() {
		// Long dash, not hyphen
		var delimeter = '::';

		$( 'span.customize-control-title:contains(' + delimeter + ')' ).each( function(){
			var html, parts;

			html = $(this).html();
			parts = html.split( delimeter );

			if ( 2 === parts.length ) {
				html = parts[0] + '<span class="styles-type">' + parts[1] + '</span>';
				$(this).html( html );
			}

		});
	}

	populate_google_fonts();
	/**
	 * Copy array of google fonts into font select element
	 * Doing it this cuts about 200kb off of page load size
	 */
	function populate_google_fonts() {
		var google_families = { 'Abel': 'Abel', 'Aclonica': 'Aclonica', 'Actor': 'Actor', 'Allan': 'Allan:bold', 'Allerta': 'Allerta', 'Allerta Stencil': 'Allerta+Stencil', 'Amaranth': 'Amaranth:700,400,italic700,italic400', 'Andika': 'Andika', 'Angkor': 'Angkor', 'Annie Use Your Telescope': 'Annie+Use+Your+Telescope', 'Anonymous Pro': 'Anonymous+Pro:bold,italicbold,normal,italic', 'Anton': 'Anton', 'Architects Daughter': 'Architects+Daughter', 'Arimo': 'Arimo:italicbold,bold,normal,italic', 'Artifika': 'Artifika', 'Arvo': 'Arvo:italic,bold,italicbold,normal', 'Asset': 'Asset', 'Astloch': 'Astloch:normal,bold', 'Aubrey': 'Aubrey', 'Bangers': 'Bangers', 'Battambang': 'Battambang:bold,normal', 'Bayon': 'Bayon', 'Bentham': 'Bentham', 'Bevan': 'Bevan', 'Bigshot One': 'Bigshot+One', 'Black Ops One': 'Black+Ops+One', 'Bokor': 'Bokor', 'Bowlby One': 'Bowlby+One', 'Bowlby One SC': 'Bowlby+One+SC', 'Brawler': 'Brawler', 'Buda': 'Buda:300', 'Cabin': 'Cabin:italic600,500,italicbold,italic500,italic400,400,600,bold', 'Cabin Sketch': 'Cabin+Sketch:bold', 'Calligraffitti': 'Calligraffitti', 'Candal': 'Candal', 'Cantarell': 'Cantarell:italic,bold,italicbold,normal', 'Cardo': 'Cardo', 'Carme': 'Carme', 'Carter One': 'Carter+One', 'Caudex': 'Caudex:italic,italic700,400,700', 'Cedarville Cursive': 'Cedarville+Cursive', 'Chenla': 'Chenla', 'Cherry Cream Soda': 'Cherry+Cream+Soda', 'Chewy': 'Chewy', 'Coda': 'Coda:800', 'Coda Caption': 'Coda+Caption:800', 'Coming Soon': 'Coming+Soon', 'Content': 'Content:bold,normal', 'Copse': 'Copse', 'Corben': 'Corben:700', 'Comfortaa': 'Comfortaa', 'Cousine': 'Cousine:italic,normal,italicbold,bold', 'Covered By Your Grace': 'Covered+By+Your+Grace', 'Crafty Girls': 'Crafty+Girls', 'Crimson Text': 'Crimson+Text:700,italic400,400,italic600,italic700,600', 'Crushed': 'Crushed', 'Cuprum': 'Cuprum', 'Damion': 'Damion', 'Dancing Script': 'Dancing+Script:bold,normal', 'Dangrek': 'Dangrek', 'Dawning of a New Day': 'Dawning+of+a+New+Day', 'Delius': 'Delius:400', 'Delius Swash Caps': 'Delius+Swash+Caps:400', 'Delius Unicase': 'Delius+Unicase:400', 'Didact Gothic': 'Didact+Gothic', 'Droid Arabic Kufi': 'Droid+Arabic+Kufi:bold,normal', 'Droid Arabic Naskh': 'Droid+Arabic+Naskh:normal,bold', 'Droid Sans': 'Droid+Sans:bold,normal', 'Droid Sans Mono': 'Droid+Sans+Mono', 'Droid Sans Thai': 'Droid+Sans+Thai:bold,normal', 'Droid Serif': 'Droid+Serif:bold,normal,italicbold,italic', 'Droid Serif Thai': 'Droid+Serif+Thai:bold,normal', 'EB Garamond': 'EB+Garamond', 'Expletus Sans': 'Expletus+Sans:500,italic600,600,italic400,italic700,700,400,italic500', 'Federo': 'Federo', 'Fontdiner Swanky': 'Fontdiner+Swanky', 'Forum': 'Forum', 'Francois One': 'Francois+One', 'Freehand': 'Freehand', 'GFS Didot': 'GFS+Didot', 'GFS Neohellenic': 'GFS+Neohellenic:italic,italicbold,normal,bold', 'Gentium Basic': 'Gentium+Basic:italicbold,bold,normal,italic', 'Geo': 'Geo:normal,oblique', 'Geostar': 'Geostar', 'Geostar Fill': 'Geostar+Fill', 'Give You Glory': 'Give+You+Glory', 'Gloria Hallelujah': 'Gloria+Hallelujah', 'Goblin One': 'Goblin+One', 'Goudy Bookletter 1911': 'Goudy+Bookletter+1911', 'Gravitas One': 'Gravitas+One', 'Gruppo': 'Gruppo', 'Hammersmith One': 'Hammersmith+One', 'Hanuman': 'Hanuman:normal,bold', 'Holtwood One SC': 'Holtwood+One+SC', 'Homemade Apple': 'Homemade+Apple', 'IM Fell DW Pica': 'IM+Fell+DW+Pica:italic,normal', 'IM Fell DW Pica SC': 'IM+Fell+DW+Pica+SC', 'IM Fell Double Pica': 'IM+Fell+Double+Pica:normal,italic', 'IM Fell Double Pica SC': 'IM+Fell+Double+Pica+SC', 'IM Fell English': 'IM+Fell+English:italic,normal', 'IM Fell English SC': 'IM+Fell+English+SC', 'IM Fell French Canon': 'IM+Fell+French+Canon:italic,normal', 'IM Fell French Canon SC': 'IM+Fell+French+Canon+SC', 'IM Fell Great Primer': 'IM+Fell+Great+Primer:italic,normal', 'IM Fell Great Primer SC': 'IM+Fell+Great+Primer+SC', 'Inconsolata': 'Inconsolata', 'Indie Flower': 'Indie+Flower', 'Irish Grover': 'Irish+Grover', 'Irish Growler': 'Irish+Growler', 'Istok Web': 'Istok+Web:italic700,400,700,italic400', 'Josefin Sans': 'Josefin+Sans:italic600,italic100,600,italic400,700,italic700,100,italic300,400,300', 'Josefin Sans Std Light': 'Josefin+Sans+Std+Light', 'Josefin Slab': 'Josefin+Slab:100,italic600,700,italic400,600,italic100,italic300,300,400,italic700', 'Judson': 'Judson:700,italic400,400', 'Jura': 'Jura:400,500,600,300', 'Just Another Hand': 'Just+Another+Hand', 'Just Me Again Down Here': 'Just+Me+Again+Down+Here', 'Kameron': 'Kameron:400,700', 'Kelly Slab': 'Kelly+Slab', 'Kenia': 'Kenia', 'Khmer': 'Khmer', 'Koulen': 'Koulen', 'Kranky': 'Kranky', 'Kreon': 'Kreon:700,400,300', 'Kristi': 'Kristi', 'La Belle Aurore': 'La+Belle+Aurore', 'Lato': 'Lato:italic300,300,900,700,italic100,100,italic700,400,italic900,italic400', 'League Script': 'League+Script:400', 'Leckerli One': 'Leckerli+One', 'Lekton': 'Lekton:italic,400,700', 'Limelight': 'Limelight', 'Lobster': 'Lobster', 'Lobster Two': 'Lobster+Two:italic400,700,400,italic700', 'Lora': 'Lora:italic,normal,bold,italicbold', 'Love Ya Like A Sister': 'Love+Ya+Like+A+Sister', 'Loved by the King': 'Loved+by+the+King', 'Luckiest Guy': 'Luckiest+Guy', 'Maiden Orange': 'Maiden+Orange', 'Mako': 'Mako', 'Marvel': 'Marvel:400,700,italic700,italic400', 'Maven Pro': 'Maven+Pro:700,900,500,400', 'Meddon': 'Meddon', 'MedievalSharp': 'MedievalSharp', 'Megrim': 'Megrim', 'Merriweather': 'Merriweather:700,900,400,300', 'Metal': 'Metal', 'Metrophobic': 'Metrophobic', 'Miama': 'Miama', 'Michroma': 'Michroma', 'Miltonian': 'Miltonian', 'Miltonian Tattoo': 'Miltonian+Tattoo', 'Modern Antiqua': 'Modern+Antiqua', 'Molengo': 'Molengo', 'Monofett': 'Monofett', 'Moul': 'Moul', 'Moulpali': 'Moulpali', 'Mountains of Christmas': 'Mountains+of+Christmas', 'Muli': 'Muli:italic400,400,italic300,300', 'Nanum Brush Script': 'Nanum+Brush+Script', 'Nanum Gothic': 'Nanum+Gothic:800,700,normal', 'Nanum Gothic Coding': 'Nanum+Gothic+Coding:normal,700', 'Nanum Myeongjo': 'Nanum+Myeongjo:700,normal,800', 'Nanum Pen Script': 'Nanum+Pen+Script', 'Neucha': 'Neucha', 'Neuton': 'Neuton:italic,normal', 'Neuton Cursive': 'Neuton+Cursive', 'News Cycle': 'News+Cycle', 'Nixie One': 'Nixie+One', 'Nobile': 'Nobile:700,italic500,400,italic700,500,italic400', 'Nothing You Could Do': 'Nothing+You+Could+Do', 'Nova Cut': 'Nova+Cut', 'Nova Flat': 'Nova+Flat', 'Nova Mono': 'Nova+Mono', 'Nova Oval': 'Nova+Oval', 'Nova Round': 'Nova+Round', 'Nova Script': 'Nova+Script', 'Nova Slim': 'Nova+Slim', 'Nova Square': 'Nova+Square', 'Nunito': 'Nunito:700,300,400', 'OFL Sorts Mill Goudy TT': 'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'OFL Sorts Mill Goudy TT': 'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'Odor Mean Chey': 'Odor+Mean+Chey', 'Old Standard TT': 'Old+Standard+TT:italic,bold,normal', 'Open Sans': 'Open+Sans:italic300,italic800,600,300,italic400,italic600,italic700,700,800,400', 'Open Sans Condensed': 'Open+Sans+Condensed:italic300,300', 'Orbitron': 'Orbitron:500,900,400,700', 'Oswald': 'Oswald', 'Over the Rainbow': 'Over+the+Rainbow', 'Ovo': 'Ovo', 'PT Sans': 'PT+Sans:italic,bold,normal,italicbold', 'PT Sans Caption': 'PT+Sans+Caption:normal,bold', 'PT Sans Narrow': 'PT+Sans+Narrow:normal,bold', 'PT Serif': 'PT+Serif:italic,normal,bold,italicbold', 'PT Serif Caption': 'PT+Serif+Caption:normal,italic', 'Pacifico': 'Pacifico', 'Patrick Hand': 'Patrick+Hand', 'Paytone One': 'Paytone+One', 'Pecita': 'Pecita', 'Permanent Marker': 'Permanent+Marker', 'Philosopher': 'Philosopher:bold,normal,italic,italicbold', 'Play': 'Play:bold,normal', 'Playfair Display': 'Playfair+Display', 'Podkova': 'Podkova', 'Pompiere': 'Pompiere', 'Preahvihear': 'Preahvihear', 'Puritan': 'Puritan:bold,italic,italicbold,normal', 'Quattrocento': 'Quattrocento', 'Quattrocento Sans': 'Quattrocento+Sans', 'Radley': 'Radley', 'Raleway': 'Raleway:100', 'Rationale': 'Rationale', 'Redressed': 'Redressed', 'Reenie Beanie': 'Reenie+Beanie', 'Rochester': 'Rochester', 'Rock Salt': 'Rock+Salt', 'Rokkitt': 'Rokkitt:700,400', 'Rosario': 'Rosario', 'Ruslan Display': 'Ruslan+Display', 'Schoolbell': 'Schoolbell', 'Shadows Into Light': 'Shadows+Into+Light', 'Shanti': 'Shanti', 'Siamreap': 'Siamreap', 'Siemreap': 'Siemreap', 'Sigmar One': 'Sigmar+One', 'Six Caps': 'Six+Caps', 'Slackey': 'Slackey', 'Smokum': 'Smokum', 'Smythe': 'Smythe', 'Sniglet': 'Sniglet:800', 'Snippet': 'Snippet', 'Special Elite': 'Special+Elite', 'Stardos Stencil': 'Stardos+Stencil:normal,bold', 'Sue Ellen Francisco': 'Sue+Ellen+Francisco', 'Sunshiney': 'Sunshiney', 'Suwannaphum': 'Suwannaphum', 'Swanky and Moo Moo': 'Swanky+and+Moo+Moo', 'Syncopate': 'Syncopate:normal,bold', 'Tangerine': 'Tangerine:normal,bold', 'Taprom': 'Taprom', 'Tenor Sans': 'Tenor+Sans', 'Terminal Dosis Light': 'Terminal+Dosis+Light', 'Thabit': 'Thabit:italic,italicbold,normal,bold', 'The Girl Next Door': 'The+Girl+Next+Door', 'Tienne': 'Tienne:400,900,700', 'Tinos': 'Tinos:italicbold,normal,italic,bold', 'Tulpen One': 'Tulpen+One', 'Ubuntu': 'Ubuntu:bold,300,normal,italicbold,italic,italic500,500,italic300', 'Ultra': 'Ultra', 'UnifrakturCook': 'UnifrakturCook:bold', 'UnifrakturMaguntia': 'UnifrakturMaguntia', 'Unkempt': 'Unkempt', 'Unna': 'Unna', 'VT323': 'VT323', 'Varela': 'Varela', 'Varela Round': 'Varela+Round', 'Vibur': 'Vibur', 'Vollkorn': 'Vollkorn:bold,italic,italicbold,normal', 'Waiting for the Sunrise': 'Waiting+for+the+Sunrise', 'Wallpoet': 'Wallpoet', 'Walter Turncoat': 'Walter+Turncoat', 'Wire One': 'Wire+One', 'Yanone Kaffeesatz': 'Yanone+Kaffeesatz:700,200,400,300', 'Yellowtail': 'Yellowtail', 'Yeseva One': 'Yeseva+One', 'Zeyada': 'Zeyada', /*'jsMath cmbx10': 'jsMath+cmbx10', 'jsMath cmex10': 'jsMath+cmex10', 'jsMath cmmi10': 'jsMath+cmmi10', 'jsMath cmr10': 'jsMath+cmr10', 'jsMath cmsy10': 'jsMath+cmsy10', 'jsMath cmti10': 'jsMath+cmti10',*/ };
		var google_options;

		$.each( google_families, function( name, value ){
			google_options += "<option value='" + name + "'>" + name + "</option>";
		});

		$( 'select.styles-font-family' ).append( google_options ).each( function(){
			var selected = $(this).data('selected');
			$(this).find( 'option[value="' + selected + '"]' ).attr('selected', 'selected');
		} );
	}

	set_background_position_control_behavior();
	/**
	 * Display X and Y values and their units, with special displaying of keywords
	 */
	function set_background_position_control_behavior() {
		$( '.styles-background-position-unit-keywords' ).each( function () {
			var $select = $(this);
			var unit_setting = wp.customize.value( $select.data('unit-setting') );
			var value_setting = wp.customize.value( $select.data('value-setting') );
			var ok_to_select_keyword = true;

			// Select the keyword option if the value has the corresponding percentage
			var update_select = function() {
				var container = $select.closest('.background-position-dimension');
				if ( ok_to_select_keyword && unit_setting() === '%' ) {
					var value = parseInt(value_setting(), 10);
					var keyword_options = $select.find('[value="%"][data-percent="' + value + '"]');
					if (keyword_options.length) {
						keyword_options.first().prop('selected', true);
						container.addClass( 'keyword' );
					}
					else {
						$select.find('[value="%"]:not([data-keyword])').prop('selected', true);
						container.removeClass( 'keyword' );
					}
				}
				else {
					container.removeClass( 'keyword' );
					$select.find('[value="' + unit_setting() + '"]:not([data-keyword])').prop('selected', true);
				}
			};

			// Hide value input if we selected a keyword
			$select.on( 'change', function () {
				var $option = $(this.options[this.selectedIndex]);
				if ( typeof $option.data('percent') !== 'undefined' ) {
					ok_to_select_keyword = true;
					value_setting( $option.data('percent') );
				}
				unit_setting( $option.prop('value') );
				ok_to_select_keyword = false;
			});

			value_setting.bind( function () {
				update_select();
			});
			unit_setting.bind( function () {
				update_select();
			});
			update_select();
			ok_to_select_keyword = false;
		});
	}
} );