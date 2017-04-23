<?php

require_once dirname( __FILE__ ) . '/styles-font-menu/plugin.php';

class Styles_Control_Text extends Styles_Control {
	var $suffix = 'text';

	// CSS Templates
	var $template = '$selector { $value }';
	var $template_font_size = 'font-size: $valuepx;';
	var $template_font_family = 'font-family: $value;';

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

		$args_size = $this->get_setting_args( 'font_size' );
		$setting_size = $this->setting . '[font_size]';

		$args_family = $this->get_setting_args( 'font_family' );
		// unset( $args_family['transport'] );
		$setting_family = $this->setting . '[font_family]';

		$wp_customize->add_setting( $setting_size, $args_size );
		$wp_customize->add_setting( $setting_family, $args_family );

		$control_args = $this->get_control_args();
		$control_args['settings'] = array(
			'font_size'    => $setting_size,
			'font_family'  => $setting_family,
		);

		$control = new Styles_Customize_Text_Control(
			$wp_customize,
			Styles_Helpers::get_control_id( $this->id ),
			$control_args
		);
		$wp_customize->add_control( $control );
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
			$value = $this->encodeCssValue($value);
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

		// Todo: Validate this!!!
		$font = json_decode( $value );
		if ( !empty( $font->family ) ) {
			$value = $font->family;
		}

		if ( isset( $font->import_family ) ) {
			$import_family = $this->encodeCssValue($font->import_family);
			$styles = Styles_Plugin::get_instance();
			$styles->css->google_fonts[ $value ] = "@import url(//fonts.googleapis.com/css?family={$import_family});\r";
		}

		$css = '';
		if ( $value ) {
			$value = $this->encodeCssValue($value);
			$args = array(
				'template' => $this->template_font_family,
				'value' => $value,
			);
			$css = $this->apply_template( $args );
		}

		// Filter effects final CSS output, but not postMessage updates
		return apply_filters( 'styles_css_font_family', $css );
	}

	private function encodePregMatch($matches) {
		$return = mb_convert_encoding('', 'UTF-8');
		// Convert from UTF-8 to UTF-32BE for easy ord()'ing.
		foreach ($matches as $match) {
			// 32-bit unsigned integer in big endian order.
			$char = mb_convert_encoding( $match, 'UTF-32BE', 'UTF-8' );
			// Unpack will read the 32-bit integer in Network order (BE) and
			// return the resulting integer value as a numeric.
			list( , $ord ) = unpack( 'N', $char );
			// Convert to hex and return.
			$return .= '\\' . dechex( $ord );
		}
		return $return;
	}

	private function encodeCssValue($value) {
		// Ensure UTF-8.
		if (preg_match('/^./us', $value) !== 1) {
			$value = mb_convert_encoding($value, 'UTF-8');
		}
		// Replace most non-alphanumeric characters with their hex equivalents.
		// Special handling for spaces and quotes, which we'll balance at the end of
		// processing. Also allow for hyphens, commas, and #.
		$value = preg_replace_callback('/[^ a-z0-9\'"",#-]|#(?!([a-f0-9]{3}){1,2}\b)/iu', array($this, 'encodePregMatch'), $value);
		// Balance quotes. We can't just count occurrences of " or ' because then
		// mismatching quotes (i.e. '"'"breakout) could result in a break.
		$len = mb_strlen($value);
		$in_quote = FALSE;
		for ( $i = 0; $i < $len; ++ $i ) {
			$char = mb_substr( $value, $i, 1 );
			if (!!$in_quote && $char === $in_quote) {
				$in_quote = FALSE;
			}
			else if (!$in_quote && ($char === '"' || $char === "'")) {
				$in_quote = $char;
			}
		}
		// Make sure all quotes close at the end of the string.
		if (!!$in_quote) {
			$value .= $in_quote;
		}
		return $value;
	}

	public function post_message( $js ) {
		$setting_font_size = $this->setting . '[font_size]';
		$setting_font_family = $this->setting . '[font_family]';
		$selector = str_replace( "'", "\'", $this->selector );

		$js .= str_replace(
			array( '@setting_font_size@', '@setting_font_family@', '@selector@' ),
			array( $setting_font_size, $setting_font_family, $selector ),
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

		ob_start();
		$this->link( 'font_family' );
		$attributes = ob_get_clean();

		do_action( 'styles_font_menu', $attributes, $saved_value );

	}
}

endif;