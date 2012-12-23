<?php
// Load WordPress test environment
// https://github.com/nb/wordpress-tests
//
// The path to wordpress-tests

$wordpress_tests = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wordpress-tests/bootstrap.php';

if( file_exists( $wordpress_tests ) ) {
    require_once $wordpress_tests;
} else {
    exit( "Couldn't find path to wordpress-tests/bootstrap.php\n" );
}