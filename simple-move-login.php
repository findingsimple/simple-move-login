<?php
/*
 * Plugin Name: Simple Move Login
 * Plugin URI: http://plugins.findingsimple.com
 * Description: Helper plugin for moving/protecting wp-login.php against bruteforce attacks
 * Version: 1.0
 * Author: Finding Simple
 * Author URI: http:www.findingsimple.com
 * License: GPLv3
 * Require: WordPress 3.5
 * Text Domain: sml
 */

/**
 * Filter site_url function to change wp-login.php
 * 
 * @param  [type] $url     [description]
 * @param  [type] $path    [description]
 * @param  [type] $scheme  [description]
 * @param  [type] $blog_id [description]
 * @return [type]          [description]
 */
function sml_site_url( $url, $path, $scheme, $blog_id = null ) {

	if ( ($scheme === 'login' || $scheme === 'login_post') && !empty($path) && is_string($path) && strpos($path, '..') === false && strpos($path, 'wp-login.php') !== false ) {

		// Base url
		if ( empty( $blog_id ) || !is_multisite() ) {
			$url = get_option( 'siteurl' );
		} else {
			switch_to_blog( $blog_id );
			$url = get_option( 'siteurl' );
			restore_current_blog();
		}

		$url = set_url_scheme( $url, $scheme );

		return $url . sml_set_path( $path );
	}

	return $url;

}

add_filter( 'site_url', 'sml_site_url', 10, 4);


/**
 * Filter network_site_url function to change wp-login.php
 * 
 * @param  [type] $url    [description]
 * @param  [type] $path   [description]
 * @param  [type] $scheme [description]
 * @return [type]         [description]
 */
function sml_network_site_url( $url, $path, $scheme ) {

	if ( ($scheme === 'login' || $scheme === 'login_post') && !empty($path) && is_string($path) && strpos($path, '..') === false && strpos($path, 'wp-login.php') !== false ) {
		global $current_site;

		$url = set_url_scheme( 'http://' . $current_site->domain . $current_site->path, $scheme );

		return $url . ltrim( sml_set_path( $path ) , '/' );

	}

	return $url;

}

add_filter( 'network_site_url', 'sml_network_site_url', 10, 3);



/**
 * Filter hardcoded wp-login.php redirects
 * 
 * @param  [type] $location [description]
 * @param  [type] $status   [description]
 * @return [type]           [description]
 */
function sml_redirect( $location, $status ) {

	if ( site_url( reset( explode( '?', $location ) ) ) == site_url( 'wp-login.php' ) )
		return sml_site_url( $location, $location, 'login', get_current_blog_id() );

	return $location;

}

add_filter('wp_redirect', 'sml_redirect', 10, 2);



/**
 * Display error/message that the login form is no longer available at this location
 * 
 * @return [type] [description]
 */
function sml_login_init() {
	
	$uri = !empty($GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI']) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : (!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
	
	$uri = parse_url( $uri );
	
	$uri = !empty($uri['path']) ? str_replace( '/', '', basename($uri['path']) ) : '';

	if ( $uri === 'wp-login.php' )
		wp_die(__('No longer here.', 'sml'));

}

add_action( 'login_init', 'sml_login_init', 0 );


/**
 * Utility function for returning the new path / location of the login
 * 
 * @param  [type] $path [description]
 * @return [type]       [description]
 */
function sml_set_path( $path ) {

	if ( !defined( 'SML_NEW_LOGIN_PATH' ) )
		$new_path = 'login.php';
	else
		$new_path = SML_NEW_LOGIN_PATH;

	$path = str_replace('wp-login.php', $new_path , $path);

	return '/' . ltrim( $path, '/' );

}