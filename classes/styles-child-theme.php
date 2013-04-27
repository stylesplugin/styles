<?php

class Styles_Child_Theme extends Styles_Child_Updatable {

	var $template;

	public function __construct( $args ) {
		parent::__construct( $args );

		$this->template = str_replace( ' ', '-', strtolower( $this->item_name ) );
	}

	public function get_json_path() {
		if ( get_template() != $this->template ) {
			return false;
		}

		$json_file = dirname( $this->plugin_file ) . '/customize.json';

		return $json_file;
	}

}