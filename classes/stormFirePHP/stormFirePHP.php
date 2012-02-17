<?php

if ( !class_exists('FirePHP') ) {
	ob_start();
	include dirname(__FILE__).'/FirePHPCore/fb.php';
}

if ( false !== strpos($_SERVER['DOCUMENT_ROOT'], '/Users/') || WP_DEBUG === true || BSM_DEVELOPMENT === true ) {
	// Development
	error_reporting(E_ALL & ~E_NOTICE );
	
	$firePHP = FirePHP::getInstance(true);
	$firePHP->registerErrorHandler();
	$firePHP->registerExceptionHandler();
	
	register_shutdown_function('stormFirePHPShutdown');
}else {
	FB::setEnabled(false);
}

function stormFirePHPShutdown() {
	$error=error_get_last();
   if($error=null) FB::log($error['message'], 'Fatal Error '.basename($error['file']).':'.$error['line']);
}