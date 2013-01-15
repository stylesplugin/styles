<?php

if ( !class_exists('FirePHP') ) {
	ob_start();
	include dirname(__FILE__).'/FirePHPCore/fb.php';

	$firePHP = FirePHP::getInstance(true);
	$firePHP->registerErrorHandler();
	$firePHP->registerExceptionHandler();
	
	register_shutdown_function('stormFirePHPShutdown');
}

if ( WP_DEBUG || ( defined('STORM_DEVELOPMENT') && STORM_DEVELOPMENT ) ) {
	// Development
	error_reporting( E_ALL );
}else {
	// FB::setEnabled(false);
}

function stormFirePHPShutdown() {
	$error=error_get_last();
   if($error=null) FB::log($error['message'], 'Fatal Error '.basename($error['file']).':'.$error['line']);
}