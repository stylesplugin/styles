<?php

class Tests_Storm_Styles_Customizer extends WP_UnitTestCase {
	/* Styles Object */
	var $styles;

	public function setUp() {
		parent::setUp();
		global $storm_styles;
		$this->styles = $storm_styles;
	}

	public function test_get_settings_not_empty() {
		var_dump( Storm_Styles_Customizer::get_settings() );
	}

}
