<?php

class Styles_Control_Text extends Styles_Control {
	var $suffix = 'Font';

	// CSS Templates
	var $template = '$selector { $value }';
	var $template_font_size = 'font-size: $valuepx;';
	var $template_font_family = 'font-family: $value;';

	static $families = array( 'Arial' => 'Arial, Helvetica, sans-serif', 'Bookman' => 'Bookman, Palatino, Georgia, serif', 'Century Gothic' => '"Century Gothic", Helvetica, Arial, sans-serif', 'Comic Sans MS' => '"Comic Sans MS", Arial, sans-serif', 'Courier' => 'Courier, monospace', 'Garamond' => 'Garamond, Palatino, Georgia, serif', 'Georgia' => 'Georgia, Times, serif', 'Helvetica' => 'Helvetica, Arial, sans-serif', 'Lucida Grande' => '"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif', 'Palatino' => 'Palatino, Georgia, serif', 'Tahoma' => 'Tahoma, Verdana, Helvetica, sans-serif', 'Times' => 'Times, Georgia, serif', 'Trebuchet MS' => '"Trebuchet MS", Tahoma, Helvetica, sans-serif', 'Verdana' => 'Verdana, Tahoma, sans-serif', );
	static $google_families = array( 'Abel' => 'Abel', 'Aclonica' => 'Aclonica', 'Actor' => 'Actor', 'Allan' => 'Allan:bold', 'Allerta' => 'Allerta', 'Allerta Stencil' => 'Allerta+Stencil', 'Amaranth' => 'Amaranth:700,400,italic700,italic400', 'Andika' => 'Andika', 'Angkor' => 'Angkor', 'Annie Use Your Telescope' => 'Annie+Use+Your+Telescope', 'Anonymous Pro' => 'Anonymous+Pro:bold,italicbold,normal,italic', 'Anton' => 'Anton', 'Architects Daughter' => 'Architects+Daughter', 'Arimo' => 'Arimo:italicbold,bold,normal,italic', 'Artifika' => 'Artifika', 'Arvo' => 'Arvo:italic,bold,italicbold,normal', 'Asset' => 'Asset', 'Astloch' => 'Astloch:normal,bold', 'Aubrey' => 'Aubrey', 'Bangers' => 'Bangers', 'Battambang' => 'Battambang:bold,normal', 'Bayon' => 'Bayon', 'Bentham' => 'Bentham', 'Bevan' => 'Bevan', 'Bigshot One' => 'Bigshot+One', 'Black Ops One' => 'Black+Ops+One', 'Bokor' => 'Bokor', 'Bowlby One' => 'Bowlby+One', 'Bowlby One SC' => 'Bowlby+One+SC', 'Brawler' => 'Brawler', 'Buda' => 'Buda:300', 'Cabin' => 'Cabin:italic600,500,italicbold,italic500,italic400,400,600,bold', 'Cabin Sketch' => 'Cabin+Sketch:bold', 'Calligraffitti' => 'Calligraffitti', 'Candal' => 'Candal', 'Cantarell' => 'Cantarell:italic,bold,italicbold,normal', 'Cardo' => 'Cardo', 'Carme' => 'Carme', 'Carter One' => 'Carter+One', 'Caudex' => 'Caudex:italic,italic700,400,700', 'Cedarville Cursive' => 'Cedarville+Cursive', 'Chenla' => 'Chenla', 'Cherry Cream Soda' => 'Cherry+Cream+Soda', 'Chewy' => 'Chewy', 'Coda' => 'Coda:800', 'Coda Caption' => 'Coda+Caption:800', 'Coming Soon' => 'Coming+Soon', 'Content' => 'Content:bold,normal', 'Copse' => 'Copse', 'Corben' => 'Corben:700', 'Comfortaa' => 'Comfortaa', 'Cousine' => 'Cousine:italic,normal,italicbold,bold', 'Covered By Your Grace' => 'Covered+By+Your+Grace', 'Crafty Girls' => 'Crafty+Girls', 'Crimson Text' => 'Crimson+Text:700,italic400,400,italic600,italic700,600', 'Crushed' => 'Crushed', 'Cuprum' => 'Cuprum', 'Damion' => 'Damion', 'Dancing Script' => 'Dancing+Script:bold,normal', 'Dangrek' => 'Dangrek', 'Dawning of a New Day' => 'Dawning+of+a+New+Day', 'Delius' => 'Delius:400', 'Delius Swash Caps' => 'Delius+Swash+Caps:400', 'Delius Unicase' => 'Delius+Unicase:400', 'Didact Gothic' => 'Didact+Gothic', 'Droid Arabic Kufi' => 'Droid+Arabic+Kufi:bold,normal', 'Droid Arabic Naskh' => 'Droid+Arabic+Naskh:normal,bold', 'Droid Sans' => 'Droid+Sans:bold,normal', 'Droid Sans Mono' => 'Droid+Sans+Mono', 'Droid Sans Thai' => 'Droid+Sans+Thai:bold,normal', 'Droid Serif' => 'Droid+Serif:bold,normal,italicbold,italic', 'Droid Serif Thai' => 'Droid+Serif+Thai:bold,normal', 'EB Garamond' => 'EB+Garamond', 'Expletus Sans' => 'Expletus+Sans:500,italic600,600,italic400,italic700,700,400,italic500', 'Federo' => 'Federo', 'Fontdiner Swanky' => 'Fontdiner+Swanky', 'Forum' => 'Forum', 'Francois One' => 'Francois+One', 'Freehand' => 'Freehand', 'GFS Didot' => 'GFS+Didot', 'GFS Neohellenic' => 'GFS+Neohellenic:italic,italicbold,normal,bold', 'Gentium Basic' => 'Gentium+Basic:italicbold,bold,normal,italic', 'Geo' => 'Geo:normal,oblique', 'Geostar' => 'Geostar', 'Geostar Fill' => 'Geostar+Fill', 'Give You Glory' => 'Give+You+Glory', 'Gloria Hallelujah' => 'Gloria+Hallelujah', 'Goblin One' => 'Goblin+One', 'Goudy Bookletter 1911' => 'Goudy+Bookletter+1911', 'Gravitas One' => 'Gravitas+One', 'Gruppo' => 'Gruppo', 'Hammersmith One' => 'Hammersmith+One', 'Hanuman' => 'Hanuman:normal,bold', 'Holtwood One SC' => 'Holtwood+One+SC', 'Homemade Apple' => 'Homemade+Apple', 'IM Fell DW Pica' => 'IM+Fell+DW+Pica:italic,normal', 'IM Fell DW Pica SC' => 'IM+Fell+DW+Pica+SC', 'IM Fell Double Pica' => 'IM+Fell+Double+Pica:normal,italic', 'IM Fell Double Pica SC' => 'IM+Fell+Double+Pica+SC', 'IM Fell English' => 'IM+Fell+English:italic,normal', 'IM Fell English SC' => 'IM+Fell+English+SC', 'IM Fell French Canon' => 'IM+Fell+French+Canon:italic,normal', 'IM Fell French Canon SC' => 'IM+Fell+French+Canon+SC', 'IM Fell Great Primer' => 'IM+Fell+Great+Primer:italic,normal', 'IM Fell Great Primer SC' => 'IM+Fell+Great+Primer+SC', 'Inconsolata' => 'Inconsolata', 'Indie Flower' => 'Indie+Flower', 'Irish Grover' => 'Irish+Grover', 'Irish Growler' => 'Irish+Growler', 'Istok Web' => 'Istok+Web:italic700,400,700,italic400', 'Josefin Sans' => 'Josefin+Sans:italic600,italic100,600,italic400,700,italic700,100,italic300,400,300', 'Josefin Sans Std Light' => 'Josefin+Sans+Std+Light', 'Josefin Slab' => 'Josefin+Slab:100,italic600,700,italic400,600,italic100,italic300,300,400,italic700', 'Judson' => 'Judson:700,italic400,400', 'Jura' => 'Jura:400,500,600,300', 'Just Another Hand' => 'Just+Another+Hand', 'Just Me Again Down Here' => 'Just+Me+Again+Down+Here', 'Kameron' => 'Kameron:400,700', 'Kelly Slab' => 'Kelly+Slab', 'Kenia' => 'Kenia', 'Khmer' => 'Khmer', 'Koulen' => 'Koulen', 'Kranky' => 'Kranky', 'Kreon' => 'Kreon:700,400,300', 'Kristi' => 'Kristi', 'La Belle Aurore' => 'La+Belle+Aurore', 'Lato' => 'Lato:italic300,300,900,700,italic100,100,italic700,400,italic900,italic400', 'League Script' => 'League+Script:400', 'Leckerli One' => 'Leckerli+One', 'Lekton' => 'Lekton:italic,400,700', 'Limelight' => 'Limelight', 'Lobster' => 'Lobster', 'Lobster Two' => 'Lobster+Two:italic400,700,400,italic700', 'Lora' => 'Lora:italic,normal,bold,italicbold', 'Love Ya Like A Sister' => 'Love+Ya+Like+A+Sister', 'Loved by the King' => 'Loved+by+the+King', 'Luckiest Guy' => 'Luckiest+Guy', 'Maiden Orange' => 'Maiden+Orange', 'Mako' => 'Mako', 'Marvel' => 'Marvel:400,700,italic700,italic400', 'Maven Pro' => 'Maven+Pro:700,900,500,400', 'Meddon' => 'Meddon', 'MedievalSharp' => 'MedievalSharp', 'Megrim' => 'Megrim', 'Merriweather' => 'Merriweather:700,900,400,300', 'Metal' => 'Metal', 'Metrophobic' => 'Metrophobic', 'Miama' => 'Miama', 'Michroma' => 'Michroma', 'Miltonian' => 'Miltonian', 'Miltonian Tattoo' => 'Miltonian+Tattoo', 'Modern Antiqua' => 'Modern+Antiqua', 'Molengo' => 'Molengo', 'Monofett' => 'Monofett', 'Moul' => 'Moul', 'Moulpali' => 'Moulpali', 'Mountains of Christmas' => 'Mountains+of+Christmas', 'Muli' => 'Muli:italic400,400,italic300,300', 'Nanum Brush Script' => 'Nanum+Brush+Script', 'Nanum Gothic' => 'Nanum+Gothic:800,700,normal', 'Nanum Gothic Coding' => 'Nanum+Gothic+Coding:normal,700', 'Nanum Myeongjo' => 'Nanum+Myeongjo:700,normal,800', 'Nanum Pen Script' => 'Nanum+Pen+Script', 'Neucha' => 'Neucha', 'Neuton' => 'Neuton:italic,normal', 'Neuton Cursive' => 'Neuton+Cursive', 'News Cycle' => 'News+Cycle', 'Nixie One' => 'Nixie+One', 'Nobile' => 'Nobile:700,italic500,400,italic700,500,italic400', 'Nothing You Could Do' => 'Nothing+You+Could+Do', 'Nova Cut' => 'Nova+Cut', 'Nova Flat' => 'Nova+Flat', 'Nova Mono' => 'Nova+Mono', 'Nova Oval' => 'Nova+Oval', 'Nova Round' => 'Nova+Round', 'Nova Script' => 'Nova+Script', 'Nova Slim' => 'Nova+Slim', 'Nova Square' => 'Nova+Square', 'Nunito' => 'Nunito:700,300,400', 'OFL Sorts Mill Goudy TT' => 'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'OFL Sorts Mill Goudy TT' => 'OFL+Sorts+Mill+Goudy+TT:italic,normal', 'Odor Mean Chey' => 'Odor+Mean+Chey', 'Old Standard TT' => 'Old+Standard+TT:italic,bold,normal', 'Open Sans' => 'Open+Sans:italic300,italic800,600,300,italic400,italic600,italic700,700,800,400', 'Open Sans Condensed' => 'Open+Sans+Condensed:italic300,300', 'Orbitron' => 'Orbitron:500,900,400,700', 'Oswald' => 'Oswald', 'Over the Rainbow' => 'Over+the+Rainbow', 'Ovo' => 'Ovo', 'PT Sans' => 'PT+Sans:italic,bold,normal,italicbold', 'PT Sans Caption' => 'PT+Sans+Caption:normal,bold', 'PT Sans Narrow' => 'PT+Sans+Narrow:normal,bold', 'PT Serif' => 'PT+Serif:italic,normal,bold,italicbold', 'PT Serif Caption' => 'PT+Serif+Caption:normal,italic', 'Pacifico' => 'Pacifico', 'Patrick Hand' => 'Patrick+Hand', 'Paytone One' => 'Paytone+One', 'Pecita' => 'Pecita', 'Permanent Marker' => 'Permanent+Marker', 'Philosopher' => 'Philosopher:bold,normal,italic,italicbold', 'Play' => 'Play:bold,normal', 'Playfair Display' => 'Playfair+Display', 'Podkova' => 'Podkova', 'Pompiere' => 'Pompiere', 'Preahvihear' => 'Preahvihear', 'Puritan' => 'Puritan:bold,italic,italicbold,normal', 'Quattrocento' => 'Quattrocento', 'Quattrocento Sans' => 'Quattrocento+Sans', 'Radley' => 'Radley', 'Raleway' => 'Raleway:100', 'Rationale' => 'Rationale', 'Redressed' => 'Redressed', 'Reenie Beanie' => 'Reenie+Beanie', 'Rochester' => 'Rochester', 'Rock Salt' => 'Rock+Salt', 'Rokkitt' => 'Rokkitt:700,400', 'Rosario' => 'Rosario', 'Ruslan Display' => 'Ruslan+Display', 'Schoolbell' => 'Schoolbell', 'Shadows Into Light' => 'Shadows+Into+Light', 'Shanti' => 'Shanti', 'Siamreap' => 'Siamreap', 'Siemreap' => 'Siemreap', 'Sigmar One' => 'Sigmar+One', 'Six Caps' => 'Six+Caps', 'Slackey' => 'Slackey', 'Smokum' => 'Smokum', 'Smythe' => 'Smythe', 'Sniglet' => 'Sniglet:800', 'Snippet' => 'Snippet', 'Special Elite' => 'Special+Elite', 'Stardos Stencil' => 'Stardos+Stencil:normal,bold', 'Sue Ellen Francisco' => 'Sue+Ellen+Francisco', 'Sunshiney' => 'Sunshiney', 'Suwannaphum' => 'Suwannaphum', 'Swanky and Moo Moo' => 'Swanky+and+Moo+Moo', 'Syncopate' => 'Syncopate:normal,bold', 'Tangerine' => 'Tangerine:normal,bold', 'Taprom' => 'Taprom', 'Tenor Sans' => 'Tenor+Sans', 'Terminal Dosis Light' => 'Terminal+Dosis+Light', 'Thabit' => 'Thabit:italic,italicbold,normal,bold', 'The Girl Next Door' => 'The+Girl+Next+Door', 'Tienne' => 'Tienne:400,900,700', 'Tinos' => 'Tinos:italicbold,normal,italic,bold', 'Tulpen One' => 'Tulpen+One', 'Ubuntu' => 'Ubuntu:bold,300,normal,italicbold,italic,italic500,500,italic300', 'Ultra' => 'Ultra', 'UnifrakturCook' => 'UnifrakturCook:bold', 'UnifrakturMaguntia' => 'UnifrakturMaguntia', 'Unkempt' => 'Unkempt', 'Unna' => 'Unna', 'VT323' => 'VT323', 'Varela' => 'Varela', 'Varela Round' => 'Varela+Round', 'Vibur' => 'Vibur', 'Vollkorn' => 'Vollkorn:bold,italic,italicbold,normal', 'Waiting for the Sunrise' => 'Waiting+for+the+Sunrise', 'Wallpoet' => 'Wallpoet', 'Walter Turncoat' => 'Walter+Turncoat', 'Wire One' => 'Wire+One', 'Yanone Kaffeesatz' => 'Yanone+Kaffeesatz:700,200,400,300', 'Yellowtail' => 'Yellowtail', 'Yeseva One' => 'Yeseva+One', 'Zeyada' => 'Zeyada', /*'jsMath cmbx10' => 'jsMath+cmbx10', 'jsMath cmex10' => 'jsMath+cmex10', 'jsMath cmmi10' => 'jsMath+cmmi10', 'jsMath cmr10' => 'jsMath+cmr10', 'jsMath cmsy10' => 'jsMath+cmsy10', 'jsMath cmti10' => 'jsMath+cmti10',*/ );

	public function __construct( $group, $element ) {
		parent::__construct( $group, $element );

		if ( !empty( $element['template_font_size'] ) ) {
			$this->template_font_size = $element['template_font_size'];
		}

		if ( !empty( $element['template_font_family'] ) ) {
			$this->template_font_family = $element['template_font_family'];
		}

	}

	/**
	 * Register item with $wp_customize
	 */
	public function add_item() {
		global $wp_customize;

		$args_size = array( 
			'default' => $this->default,
			'type' => 'option',
			'capability' => 'edit_theme_options',
		);

		// Only use postMessage if CSS template hasn't been overridden
		if ( empty( $this->element['template'] ) && empty( $this->element['template-font-size'] ) ) {
			$args_size[ 'transport' ] = 'postMessage';
		}

		$args_family = array( 
			'default' => $this->default, 
			'type' => 'option', 
			'capability' => 'edit_theme_options', 
		);

		$wp_customize->add_setting( $this->setting.'[font_size]', $args_size );
		$wp_customize->add_setting( $this->setting.'[font_family]', $args_family );

		$wp_customize->add_control( new Styles_Customize_Text_Control( $wp_customize, Styles_Helpers::get_control_id( $this->id ), array(
			'label'    => __( $this->label, 'styles' ),
			'section'  => $this->group,
			'settings' => array( 
				'font_size'    => $this->setting.'[font_size]',
				'font_family'    => $this->setting.'[font_family]',
			),
			'priority' => $this->priority . $this->group_priority,
		) ) );
	}

	/**
	 * Return CSS based on setting value
	 *
	 * @return string
	 */
	public function get_css(){
		$value = $this->get_element_setting_value();

		$css = $this->get_css_font_size( $value );
		$css .= $this->get_css_font_family( $value );

		if ( !empty( $css ) ) {
			$args = array(
				'template' => $this->template,
				'value' => $css,
			);
			$css = $this->apply_template( $args );
		}

		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_text', $css );
	}

	public function get_css_font_size( $value ) {
		if ( is_array( $value ) ) { $value = $value['font_size']; }

		$css = '';
		if ( $value ) {
			$args = array(
				'template' => $this->template_font_size,
				'value' => $value,
			);
			$css = $this->apply_template( $args );
		}

		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_font_size', $css );
	}

	public function get_css_font_family( $value = false ) {
		if ( !$value ) { return ''; }
		if ( is_array( $value ) ) { $value = $value['font_family']; }

		if ( array_key_exists( $value, self::$families ) ) {

			// Standard Font families. Convert values to stacks.
			$value = self::$families[ $value ];
		
		}else if ( array_key_exists( $value, self::$google_families ) ) {
		
			// Google Font families
			global $storm_styles;
			$src = self::$google_families[ $value ];
			// Add Google Font @import to beginning of CSS
			$storm_styles->css->google_fonts .= "@import url(//fonts.googleapis.com/css?family=$src);\r";
		
		}

		$css = '';
		if ( $value ) {
			$args = array(
				'template' => $this->template_font_family,
				'value' => $value,
			);
			$css = $this->apply_template( $args );
		}

		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_font_family', $css );
	}

	public function post_message( $js ) {
		$setting_font_size = $this->setting . '[font_size]';
		$selector = str_replace( "'", "\'", $this->selector );

		$js .= str_replace(
			array( '@setting_font_size@', '@selector@' ),
			array( $setting_font_size, $selector ),
			file_get_contents( STYLES_DIR . '/js/post-message-part-text.js' )
		);

		return $js . PHP_EOL;
	}

}

if (class_exists('WP_Customize_Control')) :

class Styles_Customize_Text_Control extends WP_Customize_Control {
	public $type = 'text_formatting';

	public function render_content() {
		?>
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php
		$this->font_size();
		$this->font_family();
	}

	public function font_size() {
		$saved_value = $this->value( 'font_size' );

		?>
        <label>
            <input type="text" placeholder="Size" value="<?php echo esc_attr( $saved_value ); ?>" <?php $this->link( 'font_size' ); ?> class="styles-font-size"/> px
        </label>
		<?php
	}

	public function font_family() {
		$saved_value = $this->value( 'font_family' );

		foreach ( Styles_Control_Text::$families as $name => $value ) {
			if ( empty( $value ) ) continue;
			$fonts[esc_attr( $name )] = $name;
		}

		foreach ( Styles_Control_Text::$google_families as $name => $value ) {
			if ( empty( $value ) ) continue;
			$fonts[esc_attr( $name )] = $name;
		}

		?>

        <label>           
            <select <?php $this->link( 'font_family' ); ?> class="styles-font-family" data-selected="<?php echo $saved_value ?>">
	            <option class="label first" value="">Select Font</option>

	            <option class="label" value="">Standard Fonts</option>
				<?php foreach ( Styles_Control_Text::$families as $name => $value ) : if ( empty( $value ) ) continue; ?>
	           		<option value='<?php esc_attr_e( $name ) ?>' <?php selected( $name, $saved_value ) ?> ><?php echo $name ?></option>
				<?php endforeach; ?>

	            <option class="label" value="">Google Fonts</option>
	            <?php // Google fonts populated by styles-customize.js to save bandwidth ?>
	        </select>
        </label>
		<?php
	}
}

endif;