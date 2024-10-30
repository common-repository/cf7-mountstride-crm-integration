<?php

/**
 *
 * @link              https://profiles.wordpress.org/vsourz1td/
 * @since             1.0.0
 * @package           Cf7_To_Mountstride
 *
 * @wordpress-plugin
 * Plugin Name:       Integration of Contact form 7 to Mountstride CRM
 * Plugin URI:        https://wordpress.org/plugins/cf7-to-mountstride/
 * Description:       Integration of Contact form 7 to Mountstride CRM for WordPress is the optimum way to manage leads in easiest way. It’s a plug & play tool which allows you to capture and store customer’s data by integrating the Contact Form and our plugin. No hand-written customization code required.
 * Version:           1.1.0
 * Author:            Vsourz Digital
 * Author URI:        https://profiles.wordpress.org/vsourz1td/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-to-mountstride
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CF72MUT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


define('CF72MUT_TEXT_DOMAIN', 'cf7_2_mount');


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CF7_TO_MOUNTSTRIDE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf7-to-mountstride-activator.php
 */
function activate_cf7_to_mountstride() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-mountstride-activator.php';
	Cf7_To_Mountstride_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf7-to-mountstride-deactivator.php
 */
function deactivate_cf7_to_mountstride() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-mountstride-deactivator.php';
	Cf7_To_Mountstride_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cf7_to_mountstride' );
register_deactivation_hook( __FILE__, 'deactivate_cf7_to_mountstride' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-to-mountstride.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7_to_mountstride() {

	$plugin = new Cf7_To_Mountstride();
	$plugin->run();

}
run_cf7_to_mountstride();