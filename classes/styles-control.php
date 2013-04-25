<?php

abstract class Styles_Control {

	var $group_priority = 1;

	/**
	 * Default setting value 
	 */
	var $default = '';

	// From $element
	var $selector;
	var $type;    
	var $label;   
	var $priority;
	var $id;      
	var $setting; 
	
	/**
	 * Template CSS for $selector and $value to be filled into
	 *
	 * @var string
	 **/
	public $template;

	public function __construct( $group, $element ) {
		$this->group = $group;
		$this->element = $element;

		$this->selector = $element['selector'];
		$this->type     = $element['type'];
		$this->label    = $element['label'];
		$this->priority = $element['priority'];
		$this->id       = $this->get_element_id();
		$this->setting  = $this->get_setting_id();

		if ( !empty( $element['template'] ) ) {
			$this->template = $element['template'];
		}

		if ( empty( $this->label ) ) {
			$this->label = $this->selector . ' ' . $this->suffix;
		}

		if ( empty( $this->selector ) ) { return false; }

		// Setup PostMessage callback if available
		if ( method_exists( $this, 'post_message' ) ) {
			add_filter( 'styles_customize_preview', array( $this, 'post_message' ) );
		}
	}

	/**
	 * Register control and setting with $wp_customize
	 * @return null
	 */
	abstract public function add_item();

	/**
	 * @param array $element Values related to this control, like CSS selector and control type
	 * @return string Unique, sanatized ID for this element based on label and type
	 */
	public function get_element_id() {
		$key = trim( sanitize_key( $this->element['label'] . '_' . $this->element['type'] ), '_' );
		return str_replace( '-', '_', $key );
	}

	/**
	 * @param string $group Name of Customizer group this element is in
	 * @param string $id unique element ID
	 * @return string $setting_id unique setting ID for use in form input names
	 */
	public function get_setting_id() {
		$group = $this->group;
		$id = str_replace( '-', '_', trim( $this->id, '_' ) );

		$setting_id = Styles_Helpers::get_option_key() . "[$group][$id]";

		return $setting_id;
	}

	public function get_element_setting_value() {
		$settings = get_option( Styles_Helpers::get_option_key() );

		$group_id = Styles_Helpers::get_group_id( $this->group );

		$value = $settings[ $group_id ][ $this->id ];

		if ( !empty( $value ) ) {
			return $value;
		}else {
			return false;
		}
	}

	/**
	 * Convert CSS selector into jQuery-compatible selector
	 */
	public function jquery_selector() {
		$selector = str_replace( "'", "\'", $this->selector );

		return $selector;
	}

}