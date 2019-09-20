<?php
namespace EPEX;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Main file
 */
class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	public $admin_page;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Register autoloader.
	 */
	private function register_autoloader() {
		require EPEX_PATH . 'includes/autoloader.php';
		Autoloader::run();
	}

	/**
	 * Initialize plugin parts
	 *
	 * @return void
	 */
	public function init_components() {
		$this->admin_page = new Admin_Page();
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {

		// There is nothing to do on front-end
		if ( ! is_admin() ) {
			return;
		}

		$this->register_autoloader();
		$this->init_components();

	}

}

Plugin::instance();
