<?php
/**
 * Fired during plugin activation.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Coffee_Widget_Activator {

    public static function activate() {
        $default_widget = array(
            'id'                    => 'metatronslove',
            'color'                 => '#FFDD00',
            'position'              => 'right',
            'margin_x'              => 18,
            'margin_y'              => 18,
            'message'               => 'Like my projects? Buy me a coffee!',
            'description'           => 'Support my work with a coffee',
            'enabled'               => 1,
            'button_type'           => 'emoji',
            'button_emoji'          => '☕',
            'button_svg'            => '',
            'button_png_url'        => '',
            // Payment settings
            'crypto_address'        => '',
            'crypto_network'        => 'quai',
            'nowpayments_api_key'   => '',
            'coingate_api_key'      => '',
            'bitpay_api_key'        => '',
            'moonpay_api_key'       => '',
            'stripe_publishable_key' => '',
            'stripe_secret_key'     => '',
            'paypal_client_id'      => '',
            'paypal_secret'         => '',
            'paypal_email'          => '',
            'payment_methods'       => array( 'crypto', 'nowpayments', 'stripe', 'paypal' ),
            'donation_tiers'        => array( '5', '10', '20', '50' ),
            'membership_enabled'    => 0,
            'membership_tiers'      => array(),
        );

        $default_style = array( 'custom_css' => '' );
        $default_code  = array( 'custom_js' => '' );

        if ( false === get_option( 'coffee_widget_settings' ) ) {
            update_option( 'coffee_widget_settings', $default_widget );
        }
        if ( false === get_option( 'coffee_widget_style' ) ) {
            update_option( 'coffee_widget_style', $default_style );
        }
        if ( false === get_option( 'coffee_widget_code' ) ) {
            update_option( 'coffee_widget_code', $default_code );
        }
    }
}
