<?php
/**
 * Plugin Name: Verso Headless API
 * Description: Provides the WordPress backend for the Verso headless application.
 * Version: 0.1.0
 * Text Domain: verso
 *
 * @package Verso
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VERSO_VERSION', '0.1.0' );
define( 'VERSO_PLUGIN_FILE', __FILE__ );
define( 'VERSO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VERSO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialize the plugin.
 *
 * Feature modules will be registered here as their scope is agreed and implemented.
 *
 * @return void
 */
function verso_init() {
	// Intentionally empty: plugin bootstrap placeholder.
}
add_action( 'plugins_loaded', 'verso_init' );
