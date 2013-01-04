<?php
// require_once dirname( dirname( dirname(__FILE__) ) ).'/styles.php';

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
		$this->assertTrue( is_plugin_active( $this->styles->plugin_basename ) );
	}

	public function test_is_plugin_initialized() {
		$this->assertFalse( null == $this->styles );
	}

	public function test_styles_version_matches_plugin_header() {
		$data = get_plugin_data( WP_PLUGIN_DIR .'/'. $this->styles->plugin_basename, false, false);
		$this->assertEquals( $data['Version'], $this->styles->version );
	}

}


// This will simulate running WordPress' main query.
// See wordpress-tests/lib/testcase.php
// $this->go_to( get_site_url() . '/?p=1' );