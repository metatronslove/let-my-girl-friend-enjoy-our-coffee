<?php
/**
 * Admin class for Coffee Widget.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Coffee_Widget_Admin {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles( $hook ) {
        if ( strpos( $hook, $this->plugin_name ) === false ) {
            return;
        }
        wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'wp-color-picker' );
    }

    public function enqueue_scripts( $hook ) {
        if ( strpos( $hook, $this->plugin_name ) === false ) {
            return;
        }
        wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );
        wp_localize_script( $this->plugin_name . '-admin', 'coffee_widget_admin', array(
            'invalid_url' => __( 'Please enter a valid URL.', 'coffee-widget' ),
        ) );
    }

    public function generate_widget_js() {
        header( 'Content-Type: application/javascript; charset=UTF-8' );
        $widget_options = get_option( 'coffee_widget_settings', array() );
        $style_options  = get_option( 'coffee_widget_style', array() );
        $code_options   = get_option( 'coffee_widget_code', array() );
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/widget-js.php';
        wp_die();
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            __( 'Coffee Widget Dashboard', 'coffee-widget' ),
            __( 'Coffee Widget', 'coffee-widget' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_dashboard_page' ),
            'dashicons-coffee',
            30
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Dashboard', 'coffee-widget' ),
            __( 'Dashboard', 'coffee-widget' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_dashboard_page' )
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Widget Settings', 'coffee-widget' ),
            __( 'Settings', 'coffee-widget' ),
            'manage_options',
            $this->plugin_name . '-settings',
            array( $this, 'display_settings_page' )
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Payment Methods', 'coffee-widget' ),
            __( 'Payment Methods', 'coffee-widget' ),
            'manage_options',
            $this->plugin_name . '-payment',
            array( $this, 'display_payment_page' )
        );

        add_submenu_page(
            $this->plugin_name,
            __( 'Help & Support', 'coffee-widget' ),
            __( 'Help & Support', 'coffee-widget' ),
            'manage_options',
            $this->plugin_name . '-support',
            array( $this, 'display_support_page' )
        );
    }

    public function display_dashboard_page() {
        include_once 'partials/dashboard-local.php';
    }

    public function display_settings_page() {
        include_once 'partials/admin-settings.php';
    }

    public function display_payment_page() {
        include_once 'partials/admin-payment.php';
    }

    public function display_support_page() {
        include_once 'partials/admin-support.php';
    }

    public function register_settings() {
        register_setting(
            'coffee_widget_options',
            'coffee_widget_settings',
            array( $this, 'sanitize_widget_settings' )
        );

        register_setting(
            'coffee_widget_style_options',
            'coffee_widget_style',
            array( $this, 'sanitize_style_settings' )
        );

        register_setting(
            'coffee_widget_code_options',
            'coffee_widget_code',
            array( $this, 'sanitize_code_settings' )
        );
    }

    public function sanitize_widget_settings( $input ) {
        $sanitized = array();
        
        // Fixed ID
        $sanitized['id'] = 'metatronslove';
        
        // Get existing settings to preserve values not in this form submission
        $existing = get_option( 'coffee_widget_settings', array() );
        
        // Button appearance - preserve existing if not in input
        $sanitized['color']       = isset( $input['color'] ) ? sanitize_hex_color( $input['color'] ) : ( $existing['color'] ?? '#FFDD00' );
        $sanitized['position']    = isset( $input['position'] ) && in_array( $input['position'], array( 'left', 'right' ) ) ? $input['position'] : ( $existing['position'] ?? 'right' );
        $sanitized['margin_x']    = isset( $input['margin_x'] ) ? intval( $input['margin_x'] ) : ( $existing['margin_x'] ?? 18 );
        $sanitized['margin_y']    = isset( $input['margin_y'] ) ? intval( $input['margin_y'] ) : ( $existing['margin_y'] ?? 18 );
        $sanitized['message']     = isset( $input['message'] ) ? sanitize_text_field( $input['message'] ) : ( $existing['message'] ?? 'Support my work' );
        $sanitized['description'] = isset( $input['description'] ) ? sanitize_text_field( $input['description'] ) : ( $existing['description'] ?? '' );
        $sanitized['enabled']     = isset( $input['enabled'] ) ? 1 : 0;

        $sanitized['button_type']   = isset( $input['button_type'] ) && in_array( $input['button_type'], array( 'emoji', 'svg', 'png' ) ) ? $input['button_type'] : ( $existing['button_type'] ?? 'emoji' );
        $sanitized['button_emoji']  = isset( $input['button_emoji'] ) ? sanitize_text_field( $input['button_emoji'] ) : ( $existing['button_emoji'] ?? '☕' );
        $sanitized['button_svg']    = isset( $input['button_svg'] ) ? wp_kses_post( $input['button_svg'] ) : ( $existing['button_svg'] ?? '' );
        $sanitized['button_png_url'] = isset( $input['button_png_url'] ) ? esc_url_raw( $input['button_png_url'] ) : ( $existing['button_png_url'] ?? '' );

        // Payment settings - preserve existing if not in input
        $sanitized['crypto_address']       = isset( $input['crypto_address'] ) ? sanitize_text_field( $input['crypto_address'] ) : ( $existing['crypto_address'] ?? '' );
        $sanitized['crypto_network']       = isset( $input['crypto_network'] ) ? sanitize_text_field( $input['crypto_network'] ) : ( $existing['crypto_network'] ?? 'quai' );
        $sanitized['nowpayments_api_key']  = isset( $input['nowpayments_api_key'] ) ? sanitize_text_field( $input['nowpayments_api_key'] ) : ( $existing['nowpayments_api_key'] ?? '' );
        $sanitized['coingate_api_key']     = isset( $input['coingate_api_key'] ) ? sanitize_text_field( $input['coingate_api_key'] ) : ( $existing['coingate_api_key'] ?? '' );
        $sanitized['bitpay_api_key']       = isset( $input['bitpay_api_key'] ) ? sanitize_text_field( $input['bitpay_api_key'] ) : ( $existing['bitpay_api_key'] ?? '' );
        $sanitized['moonpay_api_key']      = isset( $input['moonpay_api_key'] ) ? sanitize_text_field( $input['moonpay_api_key'] ) : ( $existing['moonpay_api_key'] ?? '' );
        $sanitized['stripe_publishable_key'] = isset( $input['stripe_publishable_key'] ) ? sanitize_text_field( $input['stripe_publishable_key'] ) : ( $existing['stripe_publishable_key'] ?? '' );
        $sanitized['stripe_secret_key']    = isset( $input['stripe_secret_key'] ) ? sanitize_text_field( $input['stripe_secret_key'] ) : ( $existing['stripe_secret_key'] ?? '' );
        $sanitized['paypal_client_id']     = isset( $input['paypal_client_id'] ) ? sanitize_text_field( $input['paypal_client_id'] ) : ( $existing['paypal_client_id'] ?? '' );
        $sanitized['paypal_secret']        = isset( $input['paypal_secret'] ) ? sanitize_text_field( $input['paypal_secret'] ) : ( $existing['paypal_secret'] ?? '' );
        $sanitized['paypal_email']         = isset( $input['paypal_email'] ) ? sanitize_email( $input['paypal_email'] ) : ( $existing['paypal_email'] ?? '' );
        
        // Payment methods - CRITICAL: preserve if not in input
        if ( isset( $input['payment_methods'] ) && is_array( $input['payment_methods'] ) ) {
            $sanitized['payment_methods'] = array_map( 'sanitize_text_field', $input['payment_methods'] );
        } else {
            $sanitized['payment_methods'] = $existing['payment_methods'] ?? array( 'crypto' );
        }
        
        $sanitized['donation_tiers']       = isset( $input['donation_tiers'] ) ? array_map( 'floatval', $input['donation_tiers'] ) : ( $existing['donation_tiers'] ?? array( '5', '10', '20', '50' ) );
        $sanitized['membership_enabled']   = isset( $input['membership_enabled'] ) ? 1 : 0;
        $sanitized['membership_tiers']     = isset( $input['membership_tiers'] ) ? maybe_serialize( $input['membership_tiers'] ) : ( $existing['membership_tiers'] ?? array() );
        
        // Proxy scripts
        if ( isset( $input['proxy_scripts'] ) && is_array( $input['proxy_scripts'] ) ) {
            $sanitized['proxy_scripts'] = array();
            foreach ( $input['proxy_scripts'] as $key => $script ) {
                $sanitized['proxy_scripts'][ $key ] = array(
                    'url' => esc_url_raw( $script['url'] ),
                    'type' => isset( $script['type'] ) ? sanitize_text_field( $script['type'] ) : 'javascript',
                    'cache_ttl' => intval( $script['cache_ttl'] ),
                    'description' => isset( $script['description'] ) ? sanitize_text_field( $script['description'] ) : ''
                );
            }
        } else {
            $sanitized['proxy_scripts'] = $existing['proxy_scripts'] ?? array();
        }
        
        return $sanitized;
    }

    public function sanitize_style_settings( $input ) {
        return array( 'custom_css' => wp_kses_post( $input['custom_css'] ) );
    }

    public function sanitize_code_settings( $input ) {
        return array( 'custom_js' => $input['custom_js'] );
    }
}
