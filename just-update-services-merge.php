<?php
/*
Plugin Name: Just Update Services Merge
Plugin URI: 
Description: 
Tags: 
Author: Alexander Prokopenko
Author URI: http://justcoded.com/
Version: 1.0
Donate link: 
*/

define('JUS_ROOT', dirname(__FILE__));
define('JUS_TEXTDOMAIN', 'just-update-services-merge');

if(!function_exists('pa')){
function pa($mixed, $stop = false) {
	$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
	$print = array($key => $mixed); echo( '<pre>'.htmlentities(print_r($print,1)).'</pre>' );
	if($stop == 1) exit();
}
}

?>