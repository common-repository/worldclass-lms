<?php
/*
Plugin Name: Worldclass
Plugin URI: http://worldclass.io/wordpress
Description: Turn your WordPress site into a powerful online academy. Create, publish and sell advanced online courses in minutes.
Author: The Worldclass Team
Author URI: http://worldclass.io/team
Text Domain: wcio
Domain Path: /languages/
Version: 1.10
*/

define( 'WCIO_VERSION', '1.10' );

define( 'WCIO_REQUIRED_WP_VERSION', '4.3' );

define( 'WCIO_PLUGIN', __FILE__ );

define( 'WCIO_PLUGIN_BASENAME', plugin_basename( WCIO_PLUGIN ) );

define( 'WCIO_PLUGIN_NAME', trim( dirname( WCIO_PLUGIN_BASENAME ), '/' ) );

define( 'WCIO_PLUGIN_DIR', untrailingslashit( dirname( WCIO_PLUGIN ) ) );

define( 'WCIO_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

define( 'WCIO_PLUGIN_MODULES_DIR', WCIO_PLUGIN_DIR . '/modules' );

if ( ! defined( 'WCIO_ADMIN_READ_CAPABILITY' ) ) {
	define( 'WCIO_ADMIN_READ_CAPABILITY', 'edit_posts' );
}

if ( ! defined( 'WCIO_ADMIN_READ_WRITE_CAPABILITY' ) ) {
	define( 'WCIO_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
}

if ( ! defined( 'WCIO_VERIFY_NONCE' ) ) {
	define( 'WCIO_VERIFY_NONCE', true );
}

require_once WCIO_PLUGIN_DIR . '/loader.php';

/**
 * Loads plugin text domain
 */
function wcio_load_textdomain() {
	load_plugin_textdomain( 'wcio' );
}

add_action( 'init', 'wcio_load_textdomain' );

/**
 * Register admin pages
 */
function wcio_register_admin_pages() {
	// Schedule page
	$page = add_menu_page( __( 'Worldclass Academy', 'wcio' ),
		__( 'Worldclass', 'wcio' ),
		WCIO_ADMIN_READ_WRITE_CAPABILITY,
		'wcio-academy',
		'wcio_academy_page_callback',
		'dashicons-book',
		40);

	add_action( "admin_print_styles-{$page}", 'wcio_plugin_admin_styles' );

}

add_action( 'admin_menu', 'wcio_register_admin_pages' );