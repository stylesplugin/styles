<?php
/*
Plugin Name: Styles: TwentyEleven
Plugin URI: http://stylesplugin.com
Description: Add Customize options to the <a href="http://wordpress.org/extend/themes/twentyeleven" target="_blank">TwentyEleven theme</a> using the <a href="http://wordpress.org/extend/plugins/styles/" target="_blank">Styles plugin</a>.
Version: 1.0.6
Author: Brainstorm Media
Author URI: http://brainstormmedia.com

Require: Styles 1.0.7
Styles Class: Styles_Child_Theme
*/

$theme = wp_get_theme();

FB::log($theme, '$theme');