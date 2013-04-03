<?php

class Tests_Styles_Customize extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		if ( !class_exists( 'Styles_Customize' ) ) {
			require_once WP_PLUGIN_DIR . '/' . dirname( STYLES_BASENAME ) . '/classes/styles-customize.php';
		}
	}

	public function test_get_settings_not_empty() {
		$settings = Styles_Customize::get_settings();
		$this->assertFalse( empty( $settings ) );
	}

	/*
	public function test_load_settings_from_theme_file() {
		// Move customize.json if it exists
		// Write a known customize.json
		// Test that it reads
		// Move original back
	}
	*/

}
