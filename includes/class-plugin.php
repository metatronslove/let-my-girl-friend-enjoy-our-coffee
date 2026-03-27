<?php
/**
 * The core plugin class.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Coffee_Widget_Plugin {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'coffee-widget';
        $this->version = COFFEE_WIDGET_VERSION;

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_ajax_hooks();
    }

    /**
     * Load all required dependencies.
     */
    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plugin-ajax.php';

        $this->loader = new Coffee_Widget_Loader();
    }

    /**
     * Register all admin hooks.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Coffee_Widget_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_ajax_coffee_widget_js', $plugin_admin, 'generate_widget_js' );
        $this->loader->add_action( 'wp_ajax_nopriv_coffee_widget_js', $plugin_admin, 'generate_widget_js' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
    }

    /**
     * Register all public hooks.
     */
    private function define_public_hooks() {
        $plugin_public = new Coffee_Widget_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'output_widget_script' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_public_styles' );
    }

    /**
     * Register AJAX handlers for payments.
     */
    private function define_ajax_hooks() {
        // Instantiate the AJAX class – its constructor adds the hooks
        new Coffee_Widget_Ajax();
    }

    /**
     * Run the plugin.
     */
    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}
