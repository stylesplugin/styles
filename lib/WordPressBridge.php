<?php
/**
 * Scaffold_Extension_WordPressBridge
 *
 * Preloads variables to use within the CSS from the WordPress PD Styles plugin.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
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
	
	function __construct() {
		global $PDStylesFrontendController;
		
		$css_permalink = $PDStylesFrontendController->permalink;
		
		if ( $this->config['preview'] ) {
			
			$preview = get_option('pd-styles-preview');
			if( is_object( $preview[ $css_permalink ] ) ) {
				$this->vals = $preview[ $css_permalink ]->get('css');
			}

		}else if( is_object( $PDStylesFrontendController->options['variables'][ $css_permalink ] ) ) {
			$this->vals = $PDStylesFrontendController->options['variables'][ $css_permalink ]->get('css');
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
		$properties->register('background-color',array($this,'background_color'));
		$properties->register('-wp-background-color',array($this,'wp_background_color'));
		$properties->register('-wp-font',array($this,'wp_font'));
		// $properties->register('border-radius',array($this,'border_radius'));
		// $properties->register('box-shadow',array($this,'box_shadow'));
		// $properties->register('opacity',array($this,'opacity'));
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
	
	public function extract($value) {
		$parts = explode('//', $value);
		foreach($parts as &$part) {
			$part = trim($part);
		}
		$text = explode('.', $parts[1]);
		
		return array(
			'value' => $parts[0],
			'group' => $text[0],
			'label' => $text[1],
		);
	}
	
	public function create_id($meta) {
		$selector = trim($meta['selector']);
		$property = substr($meta['property'], 0, strpos($meta['property'], ':') ); // Returns property name (before colon)
		
		return $selector.'{'.$property.'}';
	}
	
	
	public function wp_background_color($value, $scaffold, $meta) {
		extract( $this->extract($value) );
		
		$id = $this->create_id($meta);
		$key = md5($id);
		
		// Populate found array for WP UI generation
		$this->found[$group][$key] = array(
			'value' => $value,
			'group' => $group,
			'label' => $label,
			'id'    => $id,
			'key'   => $key,
			'class' => 'PDStyles_Extension_Color',
		);
		
		// Extract values saved from WP form
		@extract( $this->vals[$group][$key] );

		if ( !empty($color) ) { $value = $color; }

		return $this->background_color($value);
	}
	
	public function wp_font($value, $scaffold, $meta) {
		extract( $this->extract($value) );
		
		$id = $this->create_id($meta);
		$key = md5($id);
		
		// Populate found array for WP UI generation
		$this->found[$group][$key] = array(
			'value' => $value,
			'group' => $group,
			'label' => $label,
			'id'    => $id,
			'key'   => $key,
			'class' => 'PDStyles_Extension_Font',
		);
		
		// Extract values saved from WP form
		@extract( $this->vals[$group][$key] );
		
		$opts = new PDStyles_Extension_Font();
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
	
	/**
	 * Enables rgba backgrounds in IE
	 *
	 * Uses a fliter to emulate rgba backgrounds in IE.
	 *
	 * @access public
	 * @param $url
	 * @return string
	 */
	public function background_color($value) {
		$regexp = '/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*([\d\.]+)\s*\)/';
		if (preg_match($regexp,$value,$match)) {

			list(,$r,$g,$b,$a) = $match;
			$hex_color = $this->rgb2html($r,$g,$b);

			$hex_a = dechex(255*floatval($a));
			$hex_a = (strlen($hex_a) < 2?'0':'').$hex_a;
			$ms_color = '#' . $hex_a . substr($hex_color,1);

			$css = "background-color: $hex_color;"
				. "background-color: rgba($r, $g, $b, $a);"
				. "filter: progid:DXImageTransform.Microsoft.gradient("
					. "startColorStr='$ms_color',EndColorStr='$ms_color');";
		} else $css = "background-color: $value;";
		return $css;
	}
	
	
	
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

	private function xy2rs($x, $y) {
		$rotation = round( atan2(-$y,$x) * 180/pi() );
		$strength = round( sqrt($x*$x) + sqrt($y*$y) );
		return array($rotation, $strength);
	}
	
}