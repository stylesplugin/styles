<?php
/*
Plugin Name: Styles: TwentyThirteen
Plugin URI: http://stylesplugin.com
Description: Add Customize options to the <a href="http://wordpress.org/extend/themes/twentythirteen" target="_blank">TwentyThirteen theme</a> using the <a href="http://wordpress.org/extend/plugins/styles/" target="_blank">Styles plugin</a>.
Version: 1.0.6
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Require: Styles 1.0.7
Styles Class: Styles_Child_Theme
*/

if ( !class_exists( 'Styles_Child_Notices' ) ) {
    include dirname( __FILE__ ) . '/classes/styles-child-notices/styles-child-notices.php';
}