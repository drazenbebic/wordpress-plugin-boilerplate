<?php
/**
 * Plugin Name: WordPress Plugin Template
 * Plugin URI: https://drazen.bebic.dev
 * Description: Starter template for a WordPress plugin.
 * Author: Drazen Bebic
 * Author URI: https://drazen.bebic.dev
 * Version: 0.1.0
 */

namespace WordPressPlugin;

defined( 'ABSPATH' ) || exit;

// Require the composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Require function files.
require_once __DIR__ . '/src/functions/core.php';

// Define the plugin version.
if ( ! defined( 'WPP_PLUGIN_VERSION' ) ) {
	define( 'WPP_PLUGIN_VERSION', '0.9.1' );
}

// Define WPP_PLUGIN_FILE.
if ( ! defined( 'WPP_PLUGIN_FILE' ) ) {
	define( 'WPP_PLUGIN_FILE', __FILE__ );
}

// Define WPP_PLUGIN_DIR.
if ( ! defined( 'WPP_PLUGIN_DIR' ) ) {
	define( 'WPP_PLUGIN_DIR', __DIR__ );
}

// Define WPP_PLUGIN_URL.
if ( ! defined( 'WPP_PLUGIN_URL' ) ) {
	define( 'WPP_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/src/' );
}

// Define WPP_DB_DATE_FORMAT.
if ( ! defined( 'WPP_DB_DATE_FORMAT' ) ) {
	define( 'WPP_DB_DATE_FORMAT', 'Y-m-d H:i:s' );
}

/**
 * Main instance of the plugin.
 *
 * Returns the main instance of the plugin to prevent the need to use globals.
 *
 * @return Main
 */
function wpp_main(): Main {
	return Main::instance();
}

wpp_main();
