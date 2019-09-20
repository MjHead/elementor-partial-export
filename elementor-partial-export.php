<?php
/**
 * Plugin Name: Elementor Partial Export
 * Plugin URI:  https://crocoblock.com/
 * Description: Export required parts of content created by Elementor
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: elementor-partial-export
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

add_action( 'plugins_loaded', 'epex_init' );

/**
 * Initializes plugin on plugins_loaded hook
 *
 * @return void
 */
function epex_init() {

	define( 'EPEX_VERSION', '1.0.0' );

	define( 'EPEX__FILE__', __FILE__ );
	define( 'EPEX_PLUGIN_BASE', plugin_basename( EPEX__FILE__ ) );
	define( 'EPEX_PATH', plugin_dir_path( EPEX__FILE__ ) );
	define( 'EPEX_URL', plugins_url( '/', EPEX__FILE__ ) );

	require EPEX_PATH . 'includes/plugin.php';

}
