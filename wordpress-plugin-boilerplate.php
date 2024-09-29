<?php
/**
 * Plugin Name: WordPress Plugin Boilerplate
 * Plugin URI: https://drazen.bebic.dev/projects/wordpress-plugin-boilerplate
 * Description: Starter template for a WordPress plugin.
 * Author: Drazen Bebic
 * Author URI: https://drazen.bebic.dev
 * Version: 0.1.0
 */

namespace WordPressPluginBoilerplate;

defined( 'ABSPATH' ) || exit;

// Require the composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Require function files.
require_once __DIR__ . '/src/functions/core.php';

// Define the plugin version.
if ( ! defined( 'WPPB_PLUGIN_VERSION' ) ) {
	define( 'WPPB_PLUGIN_VERSION', '0.9.1' );
}

// Define WPPB_PLUGIN_FILE.
if ( ! defined( 'WPPB_PLUGIN_FILE' ) ) {
	define( 'WPPB_PLUGIN_FILE', __FILE__ );
}

// Define WPPB_PLUGIN_DIR.
if ( ! defined( 'WPPB_PLUGIN_DIR' ) ) {
	define( 'WPPB_PLUGIN_DIR', __DIR__ );
}

// Define WPPB_PLUGIN_URL.
if ( ! defined( 'WPPB_PLUGIN_URL' ) ) {
	define( 'WPPB_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/src/' );
}

// Define WPPB_DB_DATE_FORMAT.
if ( ! defined( 'WPPB_DB_DATE_FORMAT' ) ) {
	define( 'WPPB_DB_DATE_FORMAT', 'Y-m-d H:i:s' );
}

/**
 * Main instance of the plugin.
 *
 * @return Main
 */
Main::instance();
