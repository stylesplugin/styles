<?php
/**
 * Minimal includes from Anthony Short's CSS Scaffold
 * @link https://github.com/anthonyshort/scaffold
 */
require dirname ( __FILE__ ) . '/scaffold-bare/CSS.php';
require dirname ( __FILE__ ) . '/scaffold-bare/Observable.php';
require dirname ( __FILE__ ) . '/scaffold-bare/Observer.php';
require dirname ( __FILE__ ) . '/scaffold-bare/Extension.php';
require dirname ( __FILE__ ) . '/scaffold-bare/NestedSelectors.php';
require dirname ( __FILE__ ) . '/scaffold-bare/Properties.php';
require dirname ( __FILE__ ) . '/scaffold-bare/Compressor.php';

class Storm_CSS_Processor {
	
	var $helper;
	var $contents;
	var $original;
	var $import_paths;
	var $styles;
	
	// URL to CSS3PIE behavior
	var $PIE;
	
	// Queue of active Google Font @imports to be added to CSS head
	var $google_fonts = array();
	
	// Gets all selectors
	private $regex = '(IDENTIFIER)?\s*BLOCK';
	
	function __construct( $styles, $contents ) {
		$this->styles = $styles;
		
		$this->PIE = $this->styles->wp->plugin_url().'/js/PIE/PIE.php';
		
		// Load CSS source
		$this->contents = $contents;

		// Where to search for embedded files. Used by background-replace
		$this->import_paths = array( get_stylesheet_directory(), $styles->wp->plugin_dir_path(), );
		
		// Init helper objects
		$this->helper = new Scaffold_Helper_CSS();
		$this->nested_selectors = new Scaffold_Extension_NestedSelectors();
		$this->properties = new Scaffold_Extension_Properties();
		
		add_action( 'styles_before_process', array($this->nested_selectors, 'styles_before_process'), 10, 1 );
		add_action( 'styles_before_process', array($this, 'before_process'), 10, 1 );
		add_action( 'styles_before_process', array($this->properties, 'styles_before_process'), 20, 1 );
		
		add_action( 'styles_process',        array($this, 'register_property'), 15, 1 );
		add_action( 'styles_process',        array($this, 'process'), 20, 1 );
		
		add_action( 'styles_after_process',  array($this, 'post_process'), 20, 1 );
		
		// Minify
		// $this->contents = Minify_CSS_Compressor::process($this->contents);
	}
	
	/**
	 * Registers the supported properties
	 * @access public
	 * @return array
	 */
	public function register_property( $styles ) {
		global $system;
		
		$this->behaviorpath = $system . 'extensions/CSS3/behaviors/';
		$styles->css->properties->register('background',array($this,'background')); // Causes multiple gradients
		$styles->css->properties->register('background-color',array($this,'background_rgba'));
		$styles->css->properties->register('border-radius',array($this,'border_radius'));
		$styles->css->properties->register('box-shadow',array($this,'box_shadow'));
		$styles->css->properties->register('opacity',array($this,'opacity'));
		// $properties->register('text_shadow',array($this,'text_shadow'));
		// $properties->register('transition',array($this,'transition'));
		
	}

	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function before_process( $styles ) {
		// Create a real regex string
		$regex = $this->helper->create_regex($this->regex);
		$id_mask = '/[^a-zA-Z0-9\s]/';
		
		// Get all selectors
		if( preg_match_all('/'.$regex.'/xs', $styles->css->contents, $matches) ) {
			// Iterate through selectors
			foreach ($matches[0] as $key => $value) {
				$selector = trim($matches[1][$key]);
				$values = $this->helper->ruleset_to_array($matches[2][$key]);

				if (empty($values)) continue;

				// Values we're getting from the CSS
				$default = $label = $id = '';
				$group = 'General';
				$enable = 'all';
				extract($values, EXTR_IF_EXISTS);

				if ( empty($label) && empty($id) ) {
					continue;
				}
				
				// Set ID from group+label if it doesn't exist
				if ( empty($id) ) {
					// Strip non alpha-numeric
					$id = preg_replace($id_mask, '', $group).'.'.preg_replace($id_mask, '', $label);
					// Replace white-space of any length with a hyphen
					$id = preg_replace('/[\s]+/', '.', strtolower($id));
				}
				
				// Add items to variables array, keeping extra IDs if they exist
				$styles->variables[$id]['group']     = $group;
				$styles->variables[$id]['label']     = $label;
				$styles->variables[$id]['id']        = $id;
				$styles->variables[$id]['enable']    = $enable;
				$styles->variables[$id]['selector']  = $selector;
				$styles->variables[$id]['form_name'] = "variables[$id][values]";
				$styles->variables[$id]['form_id']   = 'st_'.md5($id);
				
				// Organize variables IDs into groups
				$styles->groups[$group][] = $id;
			}
		}

		// Remove properties
		$styles->css->contents = $this->helper->remove_properties( 'value',  $styles->css->contents );
		$styles->css->contents = $this->helper->remove_properties( 'group',  $styles->css->contents );
		$styles->css->contents = $this->helper->remove_properties( 'label',  $styles->css->contents );
		$styles->css->contents = $this->helper->remove_properties( 'id',     $styles->css->contents );
		$styles->css->contents = $this->helper->remove_properties( 'enable', $styles->css->contents );
		
		// Remove empty selectors, keep selectors with content remaining
		if( preg_match_all('/'.$regex.'/xs', $styles->css->contents, $matches) ) {
			
			// Clear CSS contents
			$styles->css->contents = '';

			// Iterate through selectors and add back those with values
			foreach ($matches[0] as $key => $value) {
				// Ignore non-alphanumeric (strip white space)
				$test_value = preg_replace('/[^0-9a-z]/i', '', $matches[2][$key] );
				if ( !empty( $test_value ) ) {
					// Add back to CSS contents if something besides whitespace is there
					$styles->css->contents .= $value."\n";
				}
			}
		}
	}
	
	public function process( $styles ) {
		
		foreach( $styles->variables as $id => $el ) {
			$selector = $el['selector'];
			// $active, $css, $image, $bg_color, $stops, $color
			// $font_size, $font_family, $font_weight, $font_style, $text_transform, $line_height
			
			if ( empty($el['values']) ) { continue; }

			extract( $el['values'] );

			if ( empty($selector) ) { continue; }
			if ( empty($active) && empty($color) && empty($font_size) && empty($font_family) && empty($font_weight) && empty($font_style) && empty($text_transform) && empty($line_height) ) {
				continue;
			}

			$properties = '';

			// Create new styles
			switch( $active ) {
				case 'image':

					if ( $image_replace ) {
						$properties .= $this->image_replace($image);
					}else {
						$properties .= "background-image: url($image);" ;
					}

					break;
				case 'gradient':
				
					$properties .= $this->linear_gradient($css) ;

					break;
				case 'bg_color':
				
					$properties .= 'background-image:url();'; // Until the UI supports both at once
					$properties .= $this->background_rgba($css);
					
					break;
				case 'transparent':
				
					$properties .= 'transparent url();' ;
				
					break;
				case 'hide':
				
					$properties .= 'display:none;' ;
					
					break;

			}
			
			$properties .= $this->wp_font( $el['values'] );
			
			// Add selector and properties to CSS source
			$styles->css->contents .= "$selector { $properties }\n" ;
			
		} // end foreach
		
	}
	
	/**
	 * Add Google @import declarations to the beginning of the CSS Source
	 */
	public function post_process( $styles ) {
		foreach ( $this->google_fonts as $family => $src ) {
			$imports .= "@import url(http://fonts.googleapis.com/css?family=$src);\r";
		}
		
		$styles->css->contents = $imports.$styles->css->contents;
	}
	
	public function wp_font( $values ) {
		// $color, $font_size, $font_family, $font_weight
		// $font_style, $text_transform, $line_height
		extract( $values );

		if ( is_object( $this->styles->wp->admin_settings )) {
			$opts = $this->styles->wp->admin_settings;
		}else {
			FB::error('Couldn\'t load $this->styles->wp->admin_settings in '.__FILE__);
			return;
		}

		if ( array_key_exists( $font_family, $opts->families) ) {
			$font_family = $opts->families[$font_family];
		}else if ( array_key_exists( $font_family, $opts->google_families) ) { // Check for Google Fonts
			$this->google_fonts[$font_family] = $opts->google_families[$font_family]; // Add family name to @imports queue
			$font_family = "\"$font_family\""; // Set CSS
		}
		
		$output = '';
		
		$color = trim($color, '#');
		
		if ($color)          $output .= "color: #$color;";
		if ($font_size)      $output .= "font-size: {$font_size}px;";
		if ($font_family)    $output .= "font-family: $font_family;";
		if ($line_height)    $output .= "line-height: $line_height;";
		if ($font_style)     $output .= "font-style: $font_style;";
		if ($font_weight)    $output .= "font-weight: $font_weight;";
		if ($text_transform) $output .= "text-transform: $text_transform;";
		
		return $output;

	}
	
	public function wp_background($value, $scaffold, $meta) {

		$id = $this->create_id($meta, $id);
		$key = md5($id);

		extract( $this->extract($value, $id) );
		if ( $stops = $this->find_linear_gradient($value) ) { $form_value = $stops; 
		}else if ( $furl = $this->find_background_url($value)  ) { $form_value = $furl;
		}else { $form_value = $value; }

		// Populate found array for WP UI generation
		// $this->found[$group][$key] = array(
		// 			'value' => $form_value,
		// 			'group' => $group,
		// 			'label' => $label,
		// 			'id'    => $id,
		// 			'key'   => $key,
		// 			'class' => 'StormStyles_Extension_Background',
		// 		);

		// Extract values saved from WP form:
		//   $active, $css, $image, $color, $stops
		@extract( $this->styles->variables[$key] ); 

		if ( $active && $css ) {
			switch( $active ) {
				case 'image':
				
					// This requires *something* in url() for replace-url to work.
					if ( $match = $this->find_background_url( $value ) ) {
						// Declaration was originally an image. Just replace URL.
						$value = str_replace($match, $image, $value);
					}else {
						// Set background
						$value = "transparent url($image)";
						$meta['property'] = "background: $value";
					}
					break;
				case 'gradient':
					$value = "linear-gradient( $css )";
					$meta['property'] = "linear-gradient( $css );";
					break;
				case 'color':
					$meta['property'] = $value = $this->background_rgba($css);
					break;
				case 'transparent':
					$value = 'transparent url();';
					break;
				case 'hide':
					return 'display:none;';
					break;

			}
			
		}

		// Remove Group & Label
		$prop = $meta['property'];
		if ( false !== strpos( $prop, $this->meta_gliph ) ) {
			$prop = substr($meta['property'], 0, strrpos($meta['property'], $this->meta_gliph));
		}
		
		// Remove -wp- prefix
		$meta['property'] = trim(str_replace('-wp-background', 'background', $prop), ';').';';
		
		return $this->background($value, $scaffold, $meta);
	}
	
	public function background($value, $scaffold, $meta) {
		extract( $this->extract($value) );
		
		if ( ($match = $this->find_linear_gradient( $value ))  ) {
			return $this->linear_gradient($match);
		}
		if ( $this->is_image_replace( $value ) ) {
			return $this->image_replace($value);
		}
		
		if ( $value[0] == '#' || $value == 'transparent url()' ) { // Background color
			return "background: $value;";
		}
		
		if ( ( $url = $this->find_background_url($value) ) && ($original = $this->find_background_url($meta['property']) ) ) {
			$meta['property'] = str_replace( $original, $url, $meta['property'] );
		}
		
		if ( false !== strpos($meta['property'], 'url(#)') ) {
			return '';
		}

		return $meta['property'];
	}
	
	
	public function wp_background_color($value, $scaffold, $meta) {
		
		// Change color picker to http://www.digitalmagicpro.com/jPicker/
		$id = $this->create_id($meta, $id);
		$key = md5($id);
		
		extract( $this->extract($value, $id) );
		
		// Populate found array for WP UI generation
		// $this->found[$group][$key] = array(
		// 			'value' => $value,
		// 			'group' => $group,
		// 			'label' => $label,
		// 			'id'    => $id,
		// 			'key'   => $key,
		// 			'class' => 'StormStyles_Extension_Color',
		// 		);
		
		// Extract values saved from WP form
		@extract( $this->styles->variables[$key] );

		if ( !empty($color) ) { $value = $color; }

		return $this->background_rgba($value);
	}
	
	public function linear_gradient($stops) {
		if ( empty($stops) ) return;
		
		// background: -webkit-gradient(linear, 0 0, 0 100%, from($from) to($to)); /*old webkit*/
		return "
     background: -webkit-linear-gradient($stops); /*new webkit*/
     background:    -moz-linear-gradient($stops); /*gecko*/
     background:     -ms-linear-gradient($stops); /*IE10 preview*/
     background:      -o-linear-gradient($stops); /*opera 11.10+*/
     background:         linear-gradient($stops); /*CSS3 browsers*/
-pie-background:         linear-gradient($stops); /*PIE*/
       behavior: url($this->PIE);";
	}
	
	/**
	 * Enables rgba backgrounds in IE
	 *
	 * Uses a fliter to emulate rgba backgrounds in IE.
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function background_rgba($value) {
		if ( empty($value) ) return;
		
		@extract($this->rgba_to_ahex( $value, true ));
		
		if ( $ms_color && isset($r) && isset($g) && isset($b) && isset($a) ) {

			$css = "background-color: transparent;" /*background-color: $hex_color; */
				. "background-color: rgba($r, $g, $b, $a);"
				. "*background: none; /* ie7 */"
				. "-ms-filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='$ms_color',EndColorStr='$ms_color');zoom: 1;"
				. "filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='$ms_color',EndColorStr='$ms_color');zoom: 1;";
				
		} else {
			$css = "background-color: $value;";
		}
		return $css;
	}
	
	/**
	 * Parses image-replace properties
	 * @package 		Scaffold
	 * @author 			Anthony Short <anthonyshort@me.com>
	 * @copyright 		2009-2010 Anthony Short. All rights reserved.
	 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
	 * @link 			https://github.com/anthonyshort/csscaffold/master
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function image_replace($value) {

		$path = str_replace( home_url(), '', $value );

		if( $file = $this->find( $path ) ) {

			// Get the size of the image file
			$size = GetImageSize($file);
			$width = $size[0];
			$height = $size[1];
			
			// Make sure theres a value so it doesn't break the css
			if(!$width && !$height) {
				$width = $height = 0;
			}
			
			// Build the CSS
			$css = 'background:url('.$value.') no-repeat 0 0;height:0;padding-top:'.$height.'px;width:'.$width.'px;display:block;text-indent:-9999px;overflow:hidden;';
		
			return $css;
		}else {
			return "/* Error: could not find file: $value */";
		}
		
		
	}
	
	private function find_background_url($value) {

		$url = preg_match('/
				(?:(?:replace-)?url\(\s*)      	 # required url( -- passive groups
				[\'"]?               # maybe quote
				([^\'\"\)]*)?                # 1 = Maybe URI
				[\'"]?               # maybe end quote
				(?:\\s*\\))?         # maybe )
			/xsi',
			$value,
			$match
		);

		if( $match[1] ) { // Requires something in url() to return true... Should adapt to allow replace-url() or url()
			return $match[1];
		}else {
			return false;
		}
	}
	
	private function find_linear_gradient($value) {
		
		// Match linear-gradient(-45deg, red 0%, orange 15%, yellow 30%, green 45%, blue 60%, indigo 75%, violet 100%);
		
		if ( false !== strpos($value, 'linear-gradient') ) {
			preg_match( '/\(([^*]*)\)/', $value, $match );
			$stops = $match[1];
			return $stops;
			
			// $stops = explode(',', $value );
			// return $stops;
			
			// // Find hex colors
			// $regexp = '/#([a-fA-F0-9]{3}){1,2}/';
			// preg_match_all($regexp,$value,$matches);
            // 
			// if ( 2 == count($matches[0]) ) {
			// 	return $matches[0];
			// }else {
			// 	return false;
			// }
		}else {
			return false;
		}
	}
	
	private function is_image_replace($value) {
		if ( false !== strpos($value, 'replace-url') ) {
			return true;
		}else {
			return false;
		}
	}
	
	private function extract($value, $id = '') {

		$glyphpos = strpos( $value, $this->meta_gliph );

		if ( false !== $glyphpos && $value[$glyphpos-1] !== ':' ) { // Catch http:// when glyph == //
			$meta = substr($value, strrpos($value, $this->meta_gliph) + strlen($this->meta_gliph) );
			$value = substr($value, 0, strrpos($value, $this->meta_gliph));
		}
		
		if ( false === strpos($meta, '.') ) {
			$label = $meta;
		}else if ( !empty($meta) ){
			$meta = explode($this->meta_separator, $meta);
			$group = $meta[0];
			$label = $meta[1];
		}
		$group = trim( $group );

		if ( empty( $group ) ) { $group = 'General'; }
		if ( empty( $label ) ) { $label = $id; }
		
		$output = array(
			'value' => $value,
			'group' => $group,
			'label' => $label,
		);
		
		foreach($output as &$val) {
			$val = trim($val);
		}
		
		return $output;
	}
	
	/**
	 * Expands border-radius property
	 *
	 * Adds -moz- and -webkit- variants of border-radius.
	 * Uses ie-css3.htc for IE support.
	 *   (http://www.fetchak.com/ie-css3/)
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function border_radius($value, $scaffold, $meta) {
		return "-moz-border-radius:{$value};"
			. "-webkit-border-radius:{$value};"
			. "-khtml-border-radius:{$value};"
			. "border-radius:{$value};"
			. "behavior: url($this->PIE);";
	}

	/**
	 * Expands box-shadow property
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function box_shadow($value) {
		 return "-moz-box-shadow:{$value};"
				. "-webkit-box-shadow:{$value};"
				. "box-shadow:{$value};"
				. "behavior: url($this->PIE);";
	}

	/**
	 * Enables opacity in IE
	 *
	 * Uses a fliter to set opacity in IE.
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function opacity($value) {
		$regexp = '/\d?\.\d+/';
		if (preg_match($regexp,$value,$match)) {
			$opacity = $match[0];
			$ms_opacity = round(100*$opacity);
			$msie = '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity='.$ms_opacity.')";'
						."filter: alpha(opacity=$ms_opacity);";
		}
		$css = $msie
			."-khtml-opacity: $value;"
			."-moz-opacity: $value;"
			."opacity: $value;";
		
		return $css;
	}
	
	private function create_id($meta) {
		$selector = trim($meta['selector']);
		$property = substr($meta['property'], 0, strpos($meta['property'], ':') ); // Returns property name (before colon)
		
		return $selector.'{'.$property.'}';
	}
	
	public function rgba_to_ahex($value) {
		$regexp = '/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d\.]+)\s*\)/';
		if (preg_match($regexp,$value,$match)) {

			list(,$r,$g,$b,$a) = $match;
			$hex_color = $this->rgb2html($r,$g,$b);

			$hex_a = dechex(255*floatval($a));
			$hex_a = (strlen($hex_a) < 2?'0':'').$hex_a;
			
			return array(
				'r'=>$r,
				'g'=>$g,
				'b'=>$b,
				'a'=>$a,
				'ms_color'=> '#' . $hex_a . substr($hex_color,1),
				'hexa' => $hex_color.$hex_a,
			);
			
		} else return false;
	}
	
	/**
	 * From Scaffold_Extension_CSS3
	 *
	 * @package 		Scaffold
	 * @author 			Ben Cates <ben.cates@gmail.com>
	 */
	private function html2rgb($color) {
		if ($color[0] == '#')
			$color = substr($color, 1);

		if (strlen($color) == 6)
			list($r, $g, $b) = array(
				$color[0].$color[1],
				$color[2].$color[3],
				$color[4].$color[5]
			);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array(
				$color[0].$color[0],
				$color[1].$color[1],
				$color[2].$color[2]
			);
		else return false;

		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

		return array($r, $g, $b);
	}
	
	/**
	 * From Scaffold_Extension_CSS3
	 *
	 * @package 		Scaffold
	 * @author 			Ben Cates <ben.cates@gmail.com>
	 */
	private function rgb2html($r, $g=-1, $b=-1) {
		if (is_array($r) && sizeof($r) == 3)
			list($r, $g, $b) = $r;

		$r = intval($r); $g = intval($g); $b = intval($b);

		$r = dechex($r<0?0:($r>255?255:$r));
		$g = dechex($g<0?0:($g>255?255:$g));
		$b = dechex($b<0?0:($b>255?255:$b));

		$color = (strlen($r) < 2?'0':'').$r;
		$color .= (strlen($g) < 2?'0':'').$g;
		$color .= (strlen($b) < 2?'0':'').$b;

		return '#'.$color;
	}
	
	/**
	 * From Scaffold_Extension_CSS3
	 *
	 * @package 		Scaffold
	 * @author 			Ben Cates <ben.cates@gmail.com>
	 */
	private function xy2rs($x, $y) {
		$rotation = round( atan2(-$y,$x) * 180/pi() );
		$strength = round( sqrt($x*$x) + sqrt($y*$y) );
		return array($rotation, $strength);
	}
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return mixed
	 * @author Anthony Short <anthonyshort@me.com>
	 */
	public function find( $url ) {
		if ( file_exists($url) ) { return $url; }

		if($url[0] == '/' OR $url[0] == '\\')
		{
			$path = $_SERVER['DOCUMENT_ROOT'].$url;
			if ( !file_exists($path) ) {
				$path = untrailingslashit(ABSPATH).$url;
				if ( !file_exists($path) ) {
					$path = false;
				}
			}
		}
		else
		{
			$import_paths = $this->import_paths;
			array_unshift($import_paths, dirname($this->basepath));
			
			foreach ( $import_paths as $import_path ) {
				$path = $import_path.DIRECTORY_SEPARATOR.$url;
				if ( file_exists($path) ) {
					break;
				}
			}
			if ( !file_exists($path) ) {
				$path = false;
			}
		}
		
		return $path;
	}
	
}