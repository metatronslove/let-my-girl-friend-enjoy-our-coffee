<?php
/**
 * Plugin Name:       Let My Girl Friend Enjoy Our Coffee
 * Plugin URI:        https://one.fanclub.rocks/wordpress-coffee-widget
 * Description:       A powerful, free donation and payment widget that allows your visitors to support you via credit card or cryptocurrency (Quai). No monthly fees, no third-party subscriptions – just your wallet and free services.
 * Version:           1.0.0
 * Author:            Metatron's Love
 * Author URI:        https://one.fanclub.rocks
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coffee-widget
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'COFFEE_WIDGET_VERSION', '1.0.0' );

/**
 * Activation hook.
 */
function coffee_widget_activate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-activator.php';
    Coffee_Widget_Activator::activate();
}
register_activation_hook( __FILE__, 'coffee_widget_activate' );

/**
 * Deactivation hook.
 */
function coffee_widget_deactivate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-deactivator.php';
    Coffee_Widget_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'coffee_widget_deactivate' );

/**
 * Load the main plugin class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin.php';

/**
 * Start the plugin.
 */
function coffee_widget_run() {
    $plugin = new Coffee_Widget_Plugin();
    $plugin->run();
}
coffee_widget_run();
