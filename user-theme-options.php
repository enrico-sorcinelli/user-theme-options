<?php
/**
 * User Theme Options plugin for WordPress.
 *
 * @package user-theme-options
 *
 * Plugin Name: User Theme Options
 * Plugin URI:  https://github.com/enrico-sorcinelli/user-theme-options
 * Description: A WordPress plugin that allow to grant access permissions to Appearance menu.
 * Author:      Enrico Sorcinelli
 * Author URI:  https://github.com/enrico-sorcinelli/user-theme-options/graphs/contributors
 * Text Domain: user-theme-options
 * Domain Path: /languages/
 * Version:     1.1.0
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

// Plugins constants.
define( 'USER_THEME_OPTIONS_VERSION', '1.1.0' );
define( 'USER_THEME_OPTIONS_BASEDIR', dirname( __FILE__ ) );
define( 'USER_THEME_OPTIONS_BASEURL', plugin_dir_url( __FILE__ ) );

// Enable debug prints on error_log (only when WP_DEBUG is true).
if ( ! defined( 'USER_THEME_OPTIONS_DEBUG' ) ) {
	define( 'USER_THEME_OPTIONS_DEBUG', false );
}

if ( ! class_exists( 'User_Theme_Options' ) ) {

	require_once USER_THEME_OPTIONS_BASEDIR . '/php/class-user-theme-options.php';

	/**
	 * Init the plugin.
	 *
	 * Define USER_THEME_OPTIONS_AUTOENABLE to `false` in your wp-config.php to disable.
	 */
	function user_theme_options_init() {

		if ( defined( 'USER_THEME_OPTIONS_AUTOENABLE' ) && false === USER_THEME_OPTIONS_AUTOENABLE ) {
			return;
		}

		// Instantiate our plugin class and add it to the set of globals.
		// Create plugin instance object only under administration interface.
		if ( is_admin() || is_network_admin() ) {
			$GLOBALS['user_theme_options'] = User_Theme_Options::get_instance( array( 'debug' => USER_THEME_OPTIONS_DEBUG && WP_DEBUG ) );
		}
	}

	// Activate the plugin once all plugin have been loaded.
	add_action( 'plugins_loaded', 'user_theme_options_init' );

	// Activation/Deactivation hooks.
	register_uninstall_hook( __FILE__, array( 'User_Theme_Options', 'plugin_uninstall' ) );
}
