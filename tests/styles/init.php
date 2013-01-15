<?php

class Tests_Styles_Init extends WP_UnitTestCase {
	/* Styles Object */
	var $styles;

	public function setUp() {
		parent::setUp();
		global $storm_styles;
		$this->styles = $storm_styles;
	}

	public function test_is_plugin_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->assertTrue( is_plugin_active( STYLES_BASENAME ) );
	}

	public function test_is_plugin_initialized() {
		$this->assertFalse( null == $this->styles );
	}

	public function test_styles_version_matches_plugin_header() {
		$data = get_plugin_data( WP_PLUGIN_DIR .'/'. STYLES_BASENAME, false, false);
		$this->assertEquals( $data['Version'], $this->styles->version );
	}

}
