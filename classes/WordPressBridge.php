<?php
/**
 * Scaffold_Extension_WordPressBridge
 *
 * Preloads variables to use within the CSS from the WordPress Styles plugin.
 * 
 * @package 		Scaffold
 * @author 			Paul Clark <pdclark (at) brainstormmedia.com>
 * @copyright 		2011 Brainstorm Media. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 */
class Scaffold_Extension_WordPressBridge extends Scaffold_Extension
{	
	/**
	 *  Raw values for use in property functions. Meant to replace $variables.
	 */
	public $vals = array();
	
	/**
	 * All found properties
	 * 
	 * @var array
	 **/
	var $found = array();
	
	/**
	 * URL to CSS3PIE behavior
	 * 
	 * @var string
	 **/
	var $PIE;
	
	var $meta_gliph = '~';
	var $meta_separator = '.';
	
	function __construct( $config= array() ) {
		parent::__construct($config);
		
		global $StormStylesController;
		
		$css_permalink = $StormStylesController->permalink;

		$this->PIE = $StormStylesController->plugin_url().'/js/PIE/PIE.php';

		if ( $this->config['preview'] ) {
			
			$preview = get_option('StormStyles-preview');
			if( is_object( $preview[ $css_permalink ] ) ) {
				$this->vals = $preview[ $css_permalink ]->get('css');
			}

		}else if( is_object( $StormStylesController->options['variables'][ $css_permalink ] ) ) {
			$this->vals = $StormStylesController->options['variables'][ $css_permalink ]->get('css');
		}
	}
	
	/**
	 * Registers the supported properties
	 * @access public
	 * @param $properties Scaffold_Extension_Properties
	 * @return array
	 */
	public function register_property($properties) {
		global $system;
		
		$this->behaviorpath = $system . 'extensions/CSS3/behaviors/';
		$properties->register('background',array($this,'background'));
		$properties->register('background-color',array($this,'background_color'));
		$properties->register('-wp-background',array($this,'wp_background'));
		$properties->register('-wp-background-color',array($this,'wp_background_color'));
		$properties->register('-wp-font',array($this,'wp_font'));
		$properties->register('border-radius',array($this,'border_radius'));
		$properties->register('box-shadow',array($this,'box_shadow'));
		$properties->register('opacity',array($this,'opacity'));
		// $properties->register('text_shadow',array($this,'text_shadow'));
		// $properties->register('transition',array($this,'transition'));
	}

	/**
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function initialize($source,$scaffold) {
		$this->source = $source;
	}
	
	public function wp_background_color($value, $scaffold, $meta) {
		
		// Change color picker to http://www.digitalmagicpro.com/jPicker/
		$id = $this->create_id($meta, $id);
		$key = md5($id);
		
		extract( $this->extract($value, $id) );
		
		// Populate found array for WP UI generation
		$this->found[$group][$key] = array(
			'value' => $value,
			'group' => $group,
			'label' => $label,
			'id'    => $id,
			'key'   => $key,
			'class' => 'StormStyles_Extension_Color',
		);
		
		// Extract values saved from WP form
		@extract( $this->vals[$group][$key] );

		if ( !empty($color) ) { $value = $color; }

		return $this->background_color($value);
	}
	
	public function wp_font($value, $scaffold, $meta) {
		$id = $this->create_id($meta, $id);
		$key = md5($id);
		
		extract( $this->extract($value, $id) );
		
		// Populate found array for WP UI generation
		$this->found[$group][$key] = array(
			'value' => $value,
			'group' => $group,
			'label' => $label,
			'id'    => $id,
			'key'   => $key,
			'class' => 'StormStyles_Extension_Font',
		);
		
		// Extract values saved from WP form
		@extract( $this->vals[$group][$key] );
		
		$opts = new StormStyles_Extension_Font();
		$font_family = $opts->families[$font_family];

		if ( !empty($font_size) && !empty($font_family) ) {
			
			if (!empty($line_height)) { $line_height = '/'.$line_height;}
			
			$output = "font: $font_style $font_weight {$font_size}px{$line_height} $font_family;";
			if (!empty($text_transform))	$output .= "text-transform:{$text_transform};";
			
			return $output;
		}else {
			return "font: $value";
		}

	}
	
	public function wp_background($value, $scaffold, $meta) {
		$id = $this->create_id($meta, $id);
		$key = md5($id);

		extract( $this->extract($value, $id) );

		if ( $stops = $this->find_linear_gradient($value) ) { $form_value = $stops; 
		}else if ( $furl = $this->find_background_url($value)  ) { $form_value = $furl;
		}else { $form_value = $value; }

		$class = 'StormStyles_Extension_Background';

		// Populate found array for WP UI generation
		$this->found[$group][$key] = array(
			'value' => $form_value,
			'group' => $group,
			'label' => $label,
			'id'    => $id,
			'key'   => $key,
			'class' => $class,
		);

		// Extract values saved from WP form -- $active, $css, $image, $color, $stops
		@extract( $this->vals[$group][$key] ); 
		
		if ( $active && $css ) {
			
			switch( $active ) {
				case 'image':
					if ( $match = $this->find_background_url( $value ) ) {
						// Declaration was originally an image. Just replace URL.
						$value = str_replace($match, $image, $value);
					}else {
						// Run image replace
						$value = "replace-url($image)";
					}
					break;
				case 'gradient':
					$value = "linear-gradient( $css )";
					break;
				case 'color':
					$value = "$css url()";
					break;
				case 'transparent':
					$value = 'transparent url()';
					break;

			}
			
		}
	
		// Remove Group & Label
		$prop = $meta['property'];
		if ( false !== strpos( $prop, $this->meta_gliph ) ) {
			$prop = substr($meta['property'], 0, strrpos($meta['property'], $this->meta_gliph));
		}
		
		// Remove -wp- prefix
		$meta['property'] = str_replace('-wp-background', 'background', $prop).';';
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
		
		return $meta['property'];
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
	
	public function linear_gradient($stops) {
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
	
	public function background_color($value) {
		$regexp = '/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d\.]+)\s*\)/';
		if (preg_match($regexp,$value,$match)) {

			list(,$r,$g,$b,$a) = $match;
			$hex_color = $this->rgb2html($r,$g,$b);

			$hex_a = dechex(255*floatval($a));
			$hex_a = (strlen($hex_a) < 2?'0':'').$hex_a;
			$ms_color = '#' . $hex_a . substr($hex_color,1);

			$css = "background-color: transparent;" /*background-color: $hex_color; */
				. "background-color: rgba($r, $g, $b, $a);"
				. "filter: progid:DXImageTransform.Microsoft.gradient("
					. "startColorStr='$ms_color',EndColorStr='$ms_color');";
		} else $css = "background-color: $value;";
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
		
		$value = str_replace( 'http://'.$_SERVER['HTTP_HOST'], '', $value);
		
		if( ($url = $this->find_background_url($value) ) && ($file = $this->source->find($url)) ) {

			// Get the size of the image file
			$size = GetImageSize($file);
			$width = $size[0];
			$height = $size[1];
			
			// Make sure theres a value so it doesn't break the css
			if(!$width && !$height) {
				$width = $height = 0;
			}
			
			// Build the selector
			$properties = 'background:url('.$url.') no-repeat 0 0;height:0;padding-top:'.$height.'px;width:'.$width.'px;display:block;text-indent:-9999px;overflow:hidden;';
		
			return $properties;
		}else {
			return "/* Error: could not find file: $value */";
		}
		
		
	}
	
	private function find_background_url($value) {
		
		$url = preg_match('/
				(?:(?:replace-)?url\(\s*)      	 # required url( -- passive groups
				[\'"]?               # maybe quote
				([^\'\"\)]*)                # 1 = URI
				[\'"]?               # maybe end quote
				(?:\\s*\\))?         # maybe )
			/xsi',
			$value,
			$match
		);

		if( $match[1] ) {
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

		if ( false !== strpos( $value, $this->meta_gliph ) ) {
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

		if ( empty( $group ) ) { $group = 'default'; }
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
	
}