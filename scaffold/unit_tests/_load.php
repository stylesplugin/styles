<?php

require_once('simpletest/autorun.php');
require_once('../scaffold/libraries/Bootstrap.php');

include '../scaffold/config.php';

$config['system']  = realpath('../scaffold/') . '/';
$config['cache']   = $config['system'] . 'cache/';

Scaffold::setup($config);