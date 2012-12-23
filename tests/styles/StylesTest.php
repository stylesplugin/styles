<?php
require_once dirname( dirname( dirname(__FILE__) ) ).'/styles.php';

class StylesTest extends WP_UnitTestCase {
	var $slug = 'styles/styles.php';
	var $plugin;

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
}


// This will simulate running WordPress' main query.
// See wordpress-tests/lib/testcase.php
// $this->go_to( get_site_url() . '/?p=1' );