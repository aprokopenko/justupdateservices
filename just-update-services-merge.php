<?php
/*
Plugin Name: Just Update Services Merge
Plugin URI: 
Description: Provide simple interface to edit "Update Services" list.
Tags: update services, ping sites, seo
Author: Alexander Prokopenko
Author URI: http://justcoded.com/
Version: 1.0
Donate link: 
*/

define('JUSM_ROOT', dirname(__FILE__));
define('', 'just-update-services');

if(!function_exists('pa')){
	function pa($mixed, $stop = false) {
		$ar = debug_backtrace(); $key = pathinfo($ar[0]['file']); $key = $key['basename'].':'.$ar[0]['line'];
		$print = array($key => $mixed); echo( '<pre>'.htmlentities(print_r($print,1)).'</pre>' );
		if($stop == 1) exit();
	}
}

/**
*	Plugin init
*/
add_action('plugins_loaded', 'jusm_init');
function jusm_init(){
	if( !is_admin() ) return;
	
	/**
	 *	load translations
	 */
	load_plugin_textdomain( JUS_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	// Add admin page
	add_action( 'admin_menu', 'jusm_admin_menu' );
	
	// AJAX
	add_action('wp_ajax_jusm_ajax_remove_url', 'jusm_ajax_remove_url');
	add_action('wp_ajax_jusm_ajax_ping_site', 'jusm_ajax_ping_site');
}

/**
*	Init admin menu for just update services page
*/
function jusm_admin_menu(){
	add_options_page( __('Just Update Services'), __('Just Update Services'), 'manage_options', 'jusm_update_services', 'jusm_admin_settings_page');
}

/**
*	Show plugin settings page
*/
function jusm_admin_settings_page(){
	// Form submit processing
	if( !empty($_POST['jusm_submit']) && (!empty($_POST['jusm_add_ping_sites']) || !empty($_POST['jusm_del_ping_sites'])) ) {
		jusm_update_ping_sites();
	}
	// Get list of ping sites
	$jusm_ping_sites = jusm_get_ping_sites();
	// Load template
	include( JUSM_ROOT . '/templates/settings_page.tpl.php' );
}

/**
*	Properly enqueue styles and scripts for our settings page.
*/
add_action( 'admin_print_styles', 'jusm_admin_styles' );
function jusm_admin_styles( $hook_suffix ) {
	wp_enqueue_style( 'jusm_update_services', plugins_url( 'assets/styles.css' , __FILE__ ) );
}

add_action( 'admin_print_scripts', 'jusm_admin_scripts' );
function jusm_admin_scripts( $hook_suffix ) {
	wp_enqueue_script( 'jusm_update_services', plugins_url( 'assets/settings_page.js' , __FILE__ ) );
	// add text domain
	wp_localize_script( 'jusm_update_services', 'text_jusm', jusm_get_language_strings() );
}

/**
 *	translation strings for javascript
 */
function jusm_get_language_strings(){
	$strings = array(
		'err_validate_delete_urls' => __('Please add URLs to delete first.'),
		'confirm_delete_single' => __('Are you sure you want to delete "!ping_site"?'),
		'err_ajax_delete' => __('Unable to delete "!ping_site". Please try again'),
		'confirm_delete_multiple' => __('Are you sure you want to delete all URLs listed above?'),
		'err_ajax_ping' => __('Unable to ping site. Please try again later.'),
	);
	return $strings;
}


/**
*	Get ping sites
*/
function jusm_get_ping_sites() {
	$str = get_option('ping_sites');
	$result = FALSE;
	
	if( !empty($str) ) {
		$result = explode('___', preg_replace("/[\s\t\n\r]+/", '___', $str));
		sort($result, SORT_STRING);
	}
	
	return $result;
}

/**
*	Check url
*/
function jusm_check_url($urls = FALSE) {
	$result = FALSE;
	if( $urls ) {
		foreach($urls as $url) {
			if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) $result[] = $url;
		}
	}
	return $result;
}

/**
*	Update ping sites
*/
function jusm_update_ping_sites() {
	$value = $_POST['jusm_add_ping_sites'] ? explode('___', preg_replace("/[\s\t\n\r]+/", '___', $_POST['jusm_add_ping_sites'])) : explode('___', preg_replace("/[\s\t\n\r]+/", '___', $_POST['jusm_del_ping_sites']));
	$value = jusm_check_url($value);
	
	if( $value ) {
		$ping_sites = jusm_get_ping_sites();
		if( !empty($_POST['jusm_add_ping_sites']) ) {
			$result = array_unique(array_merge($ping_sites, $value));
		}
		else {
			$result = array_unique(array_diff($ping_sites, $value));
		}
		
		sort($result, SORT_STRING);
		$result = implode("\n\n", $result);
		update_option('ping_sites', $result);
	}
}

/**
*	Remove url AJAX
*/
function jusm_ajax_remove_url() {
	if ( !empty($_POST['jusm_del_ping_sites']) ) {
		jusm_update_ping_sites();
	}
	die();
}


/**
*	Ping site AJAX
*/
function jusm_ajax_ping_site() {
	if ( !empty($_POST['jusm_ping_site']) ) {
		$url = $_POST['jusm_ping_site'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt ($ch, CURLOPT_REFERER, "http://google.com");
		curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	}
	
	// return response ans stop script
	echo $status_code;
	exit;
}

?>