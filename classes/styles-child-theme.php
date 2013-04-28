<?php

class Styles_Child_Theme extends Styles_Child_Updatable {

	var $template;
	var $styles_css;

	public function __construct( $args ) {
		parent::__construct( $args );

		$this->template = str_replace( ' ', '-', strtolower( $this->item_name ) );

		// If styles.css exists in the plugin folder, prepend it to final CSS output
		$this->styles_css = dirname( $this->plugin_file ) . '/style.css';
		if ( file_exists( $this->styles_css ) ) {
			add_filter( 'styles_css_output', array( $this, 'styles_css_output' ) );
		}
	}

	public function get_json_path() {
		if ( Styles_Helpers::get_template() != $this->template ) {
			return false;
		}

		$json_file = dirname( $this->plugin_file ) . '/customize.json';

		return $json_file;
	}

	public function styles_css_output( $css ) {
		$css = file_get_contents( $this->styles_css ) . $css;
		return $css;
	}

}