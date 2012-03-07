<?php

/**
 * Generate WordPress admin settings sections and GUI
 **/
class Storm_WP_Settings {
	
	var $families = array( 'Arial'=>'Arial, Helvetica, sans-serif', 'Bookman'=>'Bookman, Palatino, Georgia, serif', 'Century Gothic'=>'"Century Gothic", Helvetica, Arial, sans-serif', 'Comic Sans MS'=>'"Comic Sans MS", Arial, sans-serif', 'Courier'=>'Courier, monospace', 'Garamond'=>'Garamond, Palatino, Georgia, serif', 'Georgia'=>'Georgia, Times, serif', 'Helvetica'=>'Helvetica, Arial, sans-serif', 'Lucida Grande'=>'"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif', 'Palatino'=>'Palatino, Georgia, serif', 'Tahoma'=>'Tahoma, Verdana, Helvetica, sans-serif', 'Times'=>'Times, Georgia, serif', 'Trebuchet MS'=>'"Trebuchet MS", Tahoma, Helvetica, sans-serif', 'Verdana'=>'Verdana, Tahoma, sans-serif', );
	
	var $google_families = array( 'Abel'=>'Abel', 'Aclonica'=>'Aclonica', 'Actor'=>'Actor', 'Allan'=>'Allan:bold', 'Allerta'=>'Allerta', 'Allerta Stencil'=>'Allerta+Stencil', 'Amaranth'=>'Amaranth:700,400,italic700,italic400', 'Andika'=>'Andika', 'Angkor'=>'Angkor', 'Annie Use Your Telescope'=>'Annie+Use+Your+Telescope', 'Anonymous Pro'=>'Anonymous+Pro:bold,italicbold,normal,italic', 'Anton'=>'Anton', 'Architects Daughter'=>'Architects+Daughter', 'Arimo'=>'Arimo:italicbold,bold,normal,italic', 'Artifika'=>'Artifika', 'Arvo'=>'Arvo:italic,bold,italicbold,normal', 'Asset'=>'Asset', 'Astloch'=>'Astloch:normal,bold', 'Aubrey'=>'Aubrey', 'Bangers'=>'Bangers', 'Battambang'=>'Battambang:bold,normal', 'Bayon'=>'Bayon', 'Bentham'=>'Bentham', 'Bevan'=>'Bevan', 'Bigshot One'=>'Bigshot+One', 'Black Ops One'=>'Black+Ops+One', 'Bokor'=>'Bokor', 'Bowlby One'=>'Bowlby+One', 'Bowlby One SC'=>'Bowlby+One+SC', 'Brawler'=>'Brawler', 'Buda'=>'Buda:300', 'Cabin'=>'Cabin:italic600,500,italicbold,italic500,italic400,400,600,bold', 'Cabin Sketch'=>'Cabin+Sketch:bold', 'Calligraffitti'=>'Calligraffitti', 'Candal'=>'Candal', 'Cantarell'=>'Cantarell:italic,bold,italicbold,normal', 'Cardo'=>'Cardo', 'Carme'=>'Carme', 'Carter One'=>'Carter+One', 'Caudex'=>'Caudex:italic,italic700,400,700', 'Cedarville Cursive'=>'Cedarville+Cursive', 'Chenla'=>'Chenla', 'Cherry Cream Soda'=>'Cherry+Cream+Soda', 'Chewy'=>'Chewy', 'Coda'=>'Coda:800', 'Coda Caption'=>'Coda+Caption:800', 'Coming Soon'=>'Coming+Soon', 'Content'=>'Content:bold,normal', 'Copse'=>'Copse', 'Corben'=>'Corben:700', 'Comfortaa' =>'Comfortaa', 'Cousine'=>'Cousine:italic,normal,italicbold,bold', 'Covered By Your Grace'=>'Covered+By+Your+Grace', 'Crafty Girls'=>'Crafty+Girls', 'Crimson Text'=>'Crimson+Text:700,italic400,400,italic600,italic700,600', 'Crushed'=>'Crushed', 'Cuprum'=>'Cuprum', 'Damion'=>'Damion', 'Dancing Script'=>'Dancing+Script:bold,normal', 'Dangrek'=>'Dangrek', 'Dawning of a New Day'=>'Dawning+of+a+New+Day', 'Delius'=>'Delius:400', 'Delius Swash Caps'=>'Delius+Swash+Caps:400', 'Delius Unicase'=>'Delius+Unicase:400', 'Didact Gothic'=>'Didact+Gothic', 'Droid Arabic Kufi'=>'Droid+Arabic+Kufi:bold,normal', 'Droid Arabic Naskh'=>'Droid+Arabic+Naskh:normal,bold', 'Droid Sans'=>'Droid+Sans:bold,normal', 'Droid Sans Mono'=>'Droid+Sans+Mono', 'Droid Sans Thai'=>'Droid+Sans+Thai:bold,normal', 'Droid Serif'=>'Droid+Serif:bold,normal,italicbold,italic', 'Droid Serif Thai'=>'Droid+Serif+Thai:bold,normal', 'EB Garamond'=>'EB+Garamond', 'Expletus Sans'=>'Expletus+Sans:500,italic600,600,italic400,italic700,700,400,italic500', 'Federo'=>'Federo', 'Fontdiner Swanky'=>'Fontdiner+Swanky', 'Forum'=>'Forum', 'Francois One'=>'Francois+One', 'Freehand'=>'Freehand', 'GFS Didot'=>'GFS+Didot', 'GFS Neohellenic'=>'GFS+Neohellenic:italic,italicbold,normal,bold', 'Gentium Basic'=>'Gentium+Basic:italicbold,bold,normal,italic', 'Geo'=>'Geo:normal,oblique', 'Geostar'=>'Geostar', 'Geostar Fill'=>'Geostar+Fill', 'Give You Glory'=>'Give+You+Glory', 'Gloria Hallelujah'=>'Gloria+Hallelujah', 'Goblin One'=>'Goblin+One', 'Goudy Bookletter 1911'=>'Goudy+Bookletter+1911', 'Gravitas One'=>'Gravitas+One', 'Gruppo'=>'Gruppo', 'Hammersmith One'=>'Hammersmith+One', 'Hanuman'=>'Hanuman:normal,bold', 'Holtwood One SC'=>'Holtwood+One+SC', 'Homemade Apple'=>'Homemade+Apple', 'IM Fell DW Pica'=>'IM+Fell+DW+Pica:italic,normal', 'IM Fell DW Pica SC'=>'IM+Fell+DW+Pica+SC', 'IM Fell Double Pica'=>'IM+Fell+Double+Pica:normal,italic', 'IM Fell Double Pica SC'=>'IM+Fell+Double+Pica+SC', 'IM Fell English'=>'IM+Fell+English:italic,normal', 'IM Fell English SC'=>'IM+Fell+English+SC', 'IM Fell French Canon'=>'IM+Fell+French+Canon:italic,normal', 'IM Fell French Canon SC'=>'IM+Fell+French+Canon+SC', 'IM Fell Great Primer'=>'IM+Fell+Great+Primer:italic,normal', 'IM Fell Great Primer SC'=>'IM+Fell+Great+Primer+SC', 'Inconsolata'=>'Inconsolata', 'Indie Flower'=>'Indie+Flower', 'Irish Grover'=>'Irish+Grover', 'Irish Growler'=>'Irish+Growler', 'Istok Web'=>'Istok+Web:italic700,400,700,italic400', 'Josefin Sans'=>'Josefin+Sans:italic600,italic100,600,italic400,700,italic700,100,italic300,400,300', 'Josefin Sans Std Light'=>'Josefin+Sans+Std+Light', 'Josefin Slab'=>'Josefin+Slab:100,italic600,700,italic400,600,italic100,italic300,300,400,italic700', 'Judson'=>'Judson:700,italic400,400', 'Jura'=>'Jura:400,500,600,300', 'Just Another Hand'=>'Just+Another+Hand', 'Just Me Again Down Here'=>'Just+Me+Again+Down+Here', 'Kameron'=>'Kameron:400,700', 'Kelly Slab'=>'Kelly+Slab', 'Kenia'=>'Kenia', 'Khmer'=>'Khmer', 'Koulen'=>'Koulen', 'Kranky'=>'Kranky', 'Kreon'=>'Kreon:700,400,300', 'Kristi'=>'Kristi', 'La Belle Aurore'=>'La+Belle+Aurore', 'Lato'=>'Lato:italic300,300,900,700,italic100,100,italic700,400,italic900,italic400', 'League Script'=>'League+Script:400', 'Leckerli One'=>'Leckerli+One', 'Lekton'=>'Lekton:italic,400,700', 'Limelight'=>'Limelight', 'Lobster'=>'Lobster', 'Lobster Two'=>'Lobster+Two:italic400,700,400,italic700', 'Lora'=>'Lora:italic,normal,bold,italicbold', 'Love Ya Like A Sister'=>'Love+Ya+Like+A+Sister', 'Loved by the King'=>'Loved+by+the+King', 'Luckiest Guy'=>'Luckiest+Guy', 'Maiden Orange'=>'Maiden+Orange', 'Mako'=>'Mako', 'Marvel'=>'Marvel:400,700,italic700,italic400', 'Maven Pro'=>'Maven+Pro:700,900,500,400', 'Meddon'=>'Meddon', 'MedievalSharp'=>'MedievalSharp', 'Megrim'=>'Megrim', 'Merriweather'=>'Merriweather:700,900,400,300', 'Metal'=>'Metal', 'Metrophobic'=>'Metrophobic', 'Miama'=>'Miama', 'Michroma'=>'Michroma', 'Miltonian'=>'Miltonian', 'Miltonian Tattoo'=>'Miltonian+Tattoo', 'Modern Antiqua'=>'Modern+Antiqua', 'Molengo'=>'Molengo', 'Monofett'=>'Monofett', 'Moul'=>'Moul', 'Moulpali'=>'Moulpali', 'Mountains of Christmas'=>'Mountains+of+Christmas', 'Muli'=>'Muli:italic400,400,italic300,300', 'Nanum Brush Script'=>'Nanum+Brush+Script', 'Nanum Gothic'=>'Nanum+Gothic:800,700,normal', 'Nanum Gothic Coding'=>'Nanum+Gothic+Coding:normal,700', 'Nanum Myeongjo'=>'Nanum+Myeongjo:700,normal,800', 'Nanum Pen Script'=>'Nanum+Pen+Script', 'Neucha'=>'Neucha', 'Neuton'=>'Neuton:italic,normal', 'Neuton Cursive'=>'Neuton+Cursive', 'News Cycle'=>'News+Cycle', 'Nixie One'=>'Nixie+One', 'Nobile'=>'Nobile:700,italic500,400,italic700,500,italic400', 'Nothing You Could Do'=>'Nothing+You+Could+Do', 'Nova Cut'=>'Nova+Cut', 'Nova Flat'=>'Nova+Flat', 'Nova Mono'=>'Nova+Mono', 'Nova Oval'=>'Nova+Oval', 'Nova Round'=>'Nova+Round', 'Nova Script'=>'Nova+Script', 'Nova Slim'=>'Nova+Slim', 'Nova Square'=>'Nova+Square', 'Nunito'=>'Nunito:700,300,400', 'OFL Sorts Mill Goudy TT'=>'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'OFL Sorts Mill Goudy TT'=>'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'Odor Mean Chey'=>'Odor+Mean+Chey', 'Old Standard TT'=>'Old+Standard+TT:italic,bold,normal', 'Open Sans'=>'Open+Sans:italic300,italic800,600,300,italic400,italic600,italic700,700,800,400', 'Open Sans Condensed'=>'Open+Sans+Condensed:italic300,300', 'Orbitron'=>'Orbitron:500,900,400,700', 'Oswald'=>'Oswald', 'Over the Rainbow'=>'Over+the+Rainbow', 'Ovo'=>'Ovo', 'PT Sans'=>'PT+Sans:italic,bold,normal,italicbold', 'PT Sans Caption'=>'PT+Sans+Caption:normal,bold', 'PT Sans Narrow'=>'PT+Sans+Narrow:normal,bold', 'PT Serif'=>'PT+Serif:italic,normal,bold,italicbold', 'PT Serif Caption'=>'PT+Serif+Caption:normal,italic', 'Pacifico'=>'Pacifico', 'Patrick Hand'=>'Patrick+Hand', 'Paytone One'=>'Paytone+One', 'Pecita'=>'Pecita', 'Permanent Marker'=>'Permanent+Marker', 'Philosopher'=>'Philosopher:bold,normal,italic,italicbold', 'Play'=>'Play:bold,normal', 'Playfair Display'=>'Playfair+Display', 'Podkova'=>'Podkova', 'Pompiere'=>'Pompiere', 'Preahvihear'=>'Preahvihear', 'Puritan'=>'Puritan:bold,italic,italicbold,normal', 'Quattrocento'=>'Quattrocento', 'Quattrocento Sans'=>'Quattrocento+Sans', 'Radley'=>'Radley', 'Raleway'=>'Raleway:100', 'Rationale'=>'Rationale', 'Redressed'=>'Redressed', 'Reenie Beanie'=>'Reenie+Beanie', 'Rochester'=>'Rochester', 'Rock Salt'=>'Rock+Salt', 'Rokkitt'=>'Rokkitt:700,400', 'Rosario'=>'Rosario', 'Ruslan Display'=>'Ruslan+Display', 'Schoolbell'=>'Schoolbell', 'Shadows Into Light'=>'Shadows+Into+Light', 'Shanti'=>'Shanti', 'Siamreap'=>'Siamreap', 'Siemreap'=>'Siemreap', 'Sigmar One'=>'Sigmar+One', 'Six Caps'=>'Six+Caps', 'Slackey'=>'Slackey', 'Smokum'=>'Smokum', 'Smythe'=>'Smythe', 'Sniglet'=>'Sniglet:800', 'Snippet'=>'Snippet', 'Special Elite'=>'Special+Elite', 'Stardos Stencil'=>'Stardos+Stencil:normal,bold', 'Sue Ellen Francisco'=>'Sue+Ellen+Francisco', 'Sunshiney'=>'Sunshiney', 'Suwannaphum'=>'Suwannaphum', 'Swanky and Moo Moo'=>'Swanky+and+Moo+Moo', 'Syncopate'=>'Syncopate:normal,bold', 'Tangerine'=>'Tangerine:normal,bold', 'Taprom'=>'Taprom', 'Tenor Sans'=>'Tenor+Sans', 'Terminal Dosis Light'=>'Terminal+Dosis+Light', 'Thabit'=>'Thabit:italic,italicbold,normal,bold', 'The Girl Next Door'=>'The+Girl+Next+Door', 'Tienne'=>'Tienne:400,900,700', 'Tinos'=>'Tinos:italicbold,normal,italic,bold', 'Tulpen One'=>'Tulpen+One', 'Ubuntu'=>'Ubuntu:bold,300,normal,italicbold,italic,italic500,500,italic300', 'Ultra'=>'Ultra', 'UnifrakturCook'=>'UnifrakturCook:bold', 'UnifrakturMaguntia'=>'UnifrakturMaguntia', 'Unkempt'=>'Unkempt', 'Unna'=>'Unna', 'VT323'=>'VT323', 'Varela'=>'Varela', 'Varela Round'=>'Varela+Round', 'Vibur'=>'Vibur', 'Vollkorn'=>'Vollkorn:bold,italic,italicbold,normal', 'Waiting for the Sunrise'=>'Waiting+for+the+Sunrise', 'Wallpoet'=>'Wallpoet', 'Walter Turncoat'=>'Walter+Turncoat', 'Wire One'=>'Wire+One', 'Yanone Kaffeesatz'=>'Yanone+Kaffeesatz:700,200,400,300', 'Yellowtail'=>'Yellowtail', 'Yeseva One'=>'Yeseva+One', 'Zeyada'=>'Zeyada', 'jsMath cmbx10'=>'jsMath+cmbx10', 'jsMath cmex10'=>'jsMath+cmex10', 'jsMath cmmi10'=>'jsMath+cmmi10', 'jsMath cmr10'=>'jsMath+cmr10', 'jsMath cmsy10'=>'jsMath+cmsy10', 'jsMath cmti10'=>'jsMath+cmti10', );

	var $weights = array('bold','normal',);
	var $font_styles = array('italic','normal',);
	var $transforms = array('uppercase','lowercase','none',);
	var $line_heights = array('1','1.25','1.5','1.75','2',);
	
	function __construct( $styles ) {
		$this->styles = $styles;

		add_action( 'styles_settings', array($this, 'settings_sections'), 10 );
		add_action( 'styles_settings', array($this, 'settings_items'), 20 );
		add_action( 'styles_init', array($this, 'remote_api'), 0 );
		
		// Sanatize before DB commit
		add_filter( 'styles_before_save_element_values', array($this, 'before_save_element_values'), 10 );

		return true;
	}
	
	/**
	 * Register wp-admin GUI sections.
	 * e.g., General, Header, Footer, Content, Sidebar
	 */
	function settings_sections() {
		
		// General
		add_settings_section(
			'styles-general', // Unique ID 
			'Settings', // Label
			null,   // array('DemoPlugin', 'Overview'), // Description callback
			'styles-general' // Page
		);
		
		// GUI
		foreach( $this->styles->groups as $group => $elements ) {			
			add_settings_section(
				$group, // Unique ID 
				$group, // Label
				null,   // array('DemoPlugin', 'Overview'), // Description callback
				'styles-gui' // Page
			);
		}
	}
	
	/**
	 * Register individual settings fields
	 */
	public function settings_items() {
		
		// General
		add_settings_field(
			'styles-api-key',                   // Unique ID
			'Support License Key',                 // Label
			array( $this, 'api_key_field' ), // Display callback
			'styles-general', // Form page
			'styles-general',                 // Form section
			null                // Args passed to callback
		);
		
		// GUI
		foreach( $this->styles->variables as $key => $element ){
			
			if ( empty( $element['id']) ) { 
				// Skip items that don't exist in the current theme
				continue;
			}
			
			// $form_id, $form_name, $id, $label, $group,$selector
			// $values[ active,css,image,bg_color,stops,$color,
			// 	$font_size, $font_family, $font_weight,
			// 	$font_style, $text_transform, $line_height ]
			extract($element);
			
			add_settings_field(
				$key,                   // Unique ID
				$label,                 // Label
				array($this, 'form_element'), // Display callback
				'styles-gui', // Form page
				$group,                 // Form section
				$element                // Args passed to callback
			);
		}

	}

	/**
	 * Sanitize form values before saving to DB
	 */
	public function before_save_element_values( $values ) {

		extract($values);
		
		if ( !array_key_exists( $font_family, $this->families ) && !array_key_exists( $font_family, $this->google_families ) ) { $font_family = ''; }
		if ( !in_array( $font_weight, $this->weights ) ) { $font_weight = ''; }
		if ( !in_array( $font_style, $this->font_styles ) ) { $font_style = ''; }
		if ( !in_array( $text_transform, $this->transforms ) ) { $text_transform = ''; }
		if ( !in_array( $line_height, $this->line_heights ) ) { $line_height = ''; }
		
		$safe = array(
			'active'         => preg_replace( '/[^a-zA-Z0-9_-]/', '', $active ), // Alphanumeric
			'css'            => strip_tags( $css ),
			'image'          => strip_tags( $image ),
			'image_replace'  => isset( $image_replace ),
			'bg_color'       => preg_replace( '/[^0-9a-fA-F#]/', '', $bg_color), // Hexadecimal, possibly a-hex (9 chars instead of 7)
			'stops'          => strip_tags( $stops ),
			'color'          => preg_replace( '/[^0-9a-fA-F#]/', '', $color), // Hexadecimal, possibly a-hex (9 chars instead of 7)
			'font_size'      => preg_replace('/[^0-9\.]/', '',$font_size), // Number / decimal
			'font_family'    => $font_family   ,
			'font_weight'    => $font_weight   ,
			'font_style'     => $font_style    ,
			'text_transform' => $text_transform,
			'line_height'    => $line_height   ,
		);
		return $safe;
		
	}
	
	/**
	 * Output form fields on settings page
	 */
	function form_element( $el ) {
		
		extract($el); // $form_id, $form_name, $id, $label, $group, $selector, $values
		extract($values); // $active, $css, $image, $bg_color, $stops, $color, $font_size, $font_family, $font_weight, $font_style, $text_transform, $line_height
		
		$rgba = $this->styles->css->rgba_to_ahex( $color );
		
		?>
			<div class="bgPicker">
				<div class="types">
					<a title="Font" href="#" data-type="font">Font</a>
					<a title="Image" href="#" data-type="image">Image</a>
					<a title="Gradient" href="#" data-type="gradient">Gradient</a>
					<a title="Color" href="#" data-type="bg_color">Color</a>
					<a title="Transparent" href="#" data-type="transparent">Transparent</a>
					<?php /* <a title="Hide" href="#" data-type="hide">Hide</a> */ ?>
				</div>
			
				<div class="data">
					<label>Active <input type="text" name="<?php esc_attr_e($form_name) ?>[active]" value="<?php esc_attr_e($active) ?>" /></label>
					<label>CSS    <input type="text" name="<?php esc_attr_e($form_name) ?>[css]"    value="<?php esc_attr_e($css) ?>" /></label>
					<label>Image  <input type="text" name="<?php esc_attr_e($form_name) ?>[image]"  value="<?php esc_attr_e($image) ?>" id="<?php esc_attr_e($form_id.'_image') ?>" /></label>
					<label>Stops  <input type="text" name="<?php esc_attr_e($form_name) ?>[stops]"  value="<?php esc_attr_e($stops) ?>" /></label>
					<label>BG Color  <input type="text" name="<?php esc_attr_e($form_name) ?>[bg_color]"  value="<?php esc_attr_e($bg_color) ?>" data-ahex="<?php esc_attr_e($rgba['hexa']) ?>"/></label>
					
				</div>
				
				<div class="ui"></div>
				
				<div class="background-image">
					<label>
						<input type="checkbox" value="1" name="<?php esc_attr_e($form_name) ?>[image_replace]" <?php checked($image_replace) ?> id="<?php esc_attr_e($form_id.'_image_replace') ?>" />
						Hide text and replace with image
					</label>
				</div>

				<div class="font">
					<input class="pds_color_input" type="text" name="<?php esc_attr_e($form_name) ?>[color]" id="<?php esc_attr_e($form_id) ?>_color" value="<?php esc_attr_e($color) ?>" size="8" maxlength="8" />

					<input name="<?php esc_attr_e($form_name) ?>[font_size]" class="pds_font_input" type="text" id="<?php esc_attr_e($form_id) ?>_font_size" value="<?php esc_attr_e($font_size) ?>" size="2" maxlength="4" />px

					<select name="<?php esc_attr_e($form_name) ?>[font_family]" class="pds_font_select">
						<option class="label first">Font Family</option>

						<option class="label">Standard</option>
						<?php foreach ($this->families as $name => $value ) : if (empty($value)) continue; ?>
							<option value='<?php esc_attr_e($name) ?>' <?php if ( $name == $font_family ) echo 'selected'; ?> ><?php echo $name ?></option>
						<?php endforeach; ?>

						<option class="label" value="http://google.com/webfonts">Google &raquo; Open Viewer</option>
						<?php foreach ($this->google_families as $name => $value ) : if (empty($value)) continue; ?>
							<option value='<?php esc_attr_e($name) ?>' <?php if ( $name == $font_family ) echo 'selected'; ?> ><?php echo $name ?></option>
						<?php endforeach; ?>

					</select>

					<a href="#" title="Bold" class="value-toggle font-weight font-weight-<?php echo $font_weight ?>" data-type="font-weight" data-options='<?php echo json_encode( $this->weights ) ?>' >Weight</a>
					<input name="<?php esc_attr_e($form_name) ?>[font_weight]" class="pds_font_input" type="hidden" id="<?php esc_attr_e($form_id) ?>_font_weight" value="<?php esc_attr_e($font_weight) ?>" />

					<a href="#" title="Italic" class="value-toggle font-style font-style-<?php echo $font_style ?>" data-type="font-style" data-options='<?php echo json_encode( $this->font_styles ) ?>' >Style</a>
					<input name="<?php esc_attr_e($form_name) ?>[font_style]" class="pds_font_input" type="hidden" id="<?php esc_attr_e($form_id) ?>_font_style" value="<?php esc_attr_e($font_style)  ?>" />

					<a href="#" title="Case" class="value-toggle text-transform text-transform-<?php echo $text_transform ?>" data-type="text-transform" data-options='<?php echo json_encode( $this->transforms ) ?>' >Case</a>
					<input name="<?php esc_attr_e($form_name) ?>[text_transform]" class="pds_font_input" type="hidden" id="<?php esc_attr_e($form_id) ?>_text_transform" value="<?php esc_attr_e($text_transform)  ?>" />

					<a href="#" title="Leading" class="value-toggle line-height line-height-<?php echo str_replace('.', '', $line_height ); ?>" data-type="line-height" data-options='<?php echo json_encode( $this->line_heights ) ?>' >Leading</a>
					<input name="<?php esc_attr_e($form_name) ?>[line_height]" class="pds_font_input" type="hidden" id="<?php esc_attr_e($form_id) ?>_line_height" value="<?php esc_attr_e($line_height)  ?>" />
				</div>
				
			</div>

		<?php		
	}
	
	public function api_key_field() {
		$api_key = $this->styles->wp->get_option('api_key');
		
		?>

		<input value="<?php esc_attr_e($api_key) ?>" name="styles_api_key" id="styles_api_key" type="text" class="regular-text" />
		<p>This license key is used for access to theme upgrades and support.

		<?php
	}
	
	public function remote_api() {

		$this->styles->wp->api_options = get_transient('styles-api');
		$css = get_option('styles-'.get_template());

		if (
			!empty( $css ) // Have CSS for the current theme
			&& !empty( $this->styles->wp->api_options )   // API key doesn't need refreshing
			&& empty( $_POST['styles_api_key'] )
		) {
			// Already have CSS for this template
			// API key isn't being set
			return true;
		}
		
		// Check / Set API key
		if ( !empty( $_POST['styles_api_key'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'styles-options') ) {
			$api_key = $_POST['styles_api_key'];
			$this->styles->wp->api_options['api_key'] = $api_key;
			set_transient( 'styles-api', $this->styles->wp->api_options, 60*60*24*7 );
		}else {
			$api_key = $this->styles->wp->get_option('api_key');
		}

		
		// Setup verification request
		$request = array(
			'installed_themes' => array_keys(search_theme_directories()),
			'active_theme' => get_template(),
			'api_key' => $api_key,
			'version' => $this->styles->version,
		);

		$response = wp_remote_get('http://stylesplugin.com?'.http_build_query($request) );
		
		if ( $response['response']['code'] != 200 || is_wp_error( $response ) ) {
			add_settings_error( 'styles-api-key', '404', 'Could not connect to API host. Please try again later.', 'error' );			
		}
		
		$data = json_decode( $response['body'] );
		
		$this->styles->wp->api_options['api_valid']  = $data->api_valid;
		$this->styles->wp->api_options['license']    = $data->license;
		$this->styles->wp->api_options['meta_boxes'] = $data->meta_boxes;

		set_transient( 'styles-api', $this->styles->wp->api_options, $data->transient_expire );
		
		if ( !empty($data->message) ) {
			add_settings_error( 'styles-api-key', 'api-message', $data->message, $data->type );
		}
		if ( !empty($data->supported_themes) ) {
			$this->styles->wp->api_options['supported_themes'] = $data->supported_themes;
		}
		if ( !empty($data->css) ) {
			delete_option('styles-'.get_template() );
			add_option('styles-'.get_template(), $data->css, null, 'no'); // Don't autoload
		}
	}
}