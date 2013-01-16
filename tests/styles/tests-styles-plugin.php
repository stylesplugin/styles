<?php

class Tests_Styles_Plugin extends WP_UnitTestCase {
	
	/**
	 * Require that storm-styles appears in head
	 */
	public function test_wp_head_outputs_css() {
		ob_start();
		@do_action( 'wp_head' );
		$wp_head = ob_get_clean();

		$this->assertFalse( false === strpos( $wp_head, 'storm-styles') );
	}	

}
