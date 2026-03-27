<?php
/**
 * Public-facing functionality for Coffee Widget.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Coffee_Widget_Public {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_public_styles() {
        wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );
    }

    public function output_widget_script() {
        $options = get_option( 'coffee_widget_settings' );
        
        if ( empty( $options['enabled'] ) ) {
            return;
        }

        $plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
        $proxy_url = $plugin_url . 'js/coffee-partner-scripts-proxy.php';

        // Enqueue jQuery first (required)
        wp_enqueue_script( 'jquery' );

        // =============================================================
        // ENQUEUE SCRIPTS VIA PROXY (to bypass tracking prevention)
        // =============================================================

        // Enqueue Stripe via proxy if enabled
        if ( in_array( 'stripe', (array) $options['payment_methods'] ) && ! empty( $options['stripe_publishable_key'] ) ) {
            wp_enqueue_script( 
                'stripe-v3-proxy', 
                add_query_arg( array( 'script' => 'stripe', 'min' => 1 ), $proxy_url ),
                array(), 
                $this->version, 
                true 
            );
        }

        // Enqueue PayPal via proxy if enabled
        if ( in_array( 'paypal', (array) $options['payment_methods'] ) && ! empty( $options['paypal_client_id'] ) ) {
            wp_enqueue_script( 
                'paypal-sdk-proxy', 
                add_query_arg( array( 'script' => 'paypal', 'min' => 1 ), $proxy_url ),
                array(), 
                $this->version, 
                true 
            );
        }

        // =============================================================
        // MAIN WIDGET SCRIPT
        // =============================================================
        
        // Main widget script via AJAX
        $ajax_url = admin_url( 'admin-ajax.php?action=coffee_widget_js' );
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            $ajax_url .= '&min=1';
        }

        // Build dependencies array (only include scripts that are actually enqueued)
        $dependencies = array( 'jquery' );
        if ( wp_script_is( 'stripe-v3-proxy', 'enqueued' ) ) {
            $dependencies[] = 'stripe-v3-proxy';
        }
        if ( wp_script_is( 'paypal-sdk-proxy', 'enqueued' ) ) {
            $dependencies[] = 'paypal-sdk-proxy';
        }

        wp_enqueue_script(
            $this->plugin_name . '-widget',
            $ajax_url,
            $dependencies,
            $this->version,
            true
        );

        // =============================================================
        // LOCALIZE SCRIPT WITH CONFIGURATION
        // =============================================================
        
        // Get payment methods as array (ensure it's an array)
        $payment_methods = isset( $options['payment_methods'] ) && is_array( $options['payment_methods'] ) 
            ? $options['payment_methods'] 
            : array( 'crypto' );

        // Get donation tiers as array
        $donation_tiers = isset( $options['donation_tiers'] ) && is_array( $options['donation_tiers'] ) 
            ? $options['donation_tiers'] 
            : array( '5', '10', '20', '50' );

        wp_localize_script( $this->plugin_name . '-widget', 'coffeeWidgetData', array(
            'i18n'     => $this->get_translation_strings(),
            'ajaxurl'  => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'coffee_widget_payment' ),
            'proxyUrl' => $proxy_url,
            'config'   => array(
                'id'                      => 'metatronslove',
                'color'                   => esc_attr( isset( $options['color'] ) ? $options['color'] : '#FFDD00' ),
                'position'                => esc_attr( isset( $options['position'] ) ? $options['position'] : 'right' ),
                'margin_x'                => intval( isset( $options['margin_x'] ) ? $options['margin_x'] : 18 ),
                'margin_y'                => intval( isset( $options['margin_y'] ) ? $options['margin_y'] : 18 ),
                'message'                 => esc_attr( isset( $options['message'] ) ? $options['message'] : 'Support my work' ),
                'description'             => esc_attr( isset( $options['description'] ) ? $options['description'] : '' ),
                'button_type'             => esc_attr( isset( $options['button_type'] ) ? $options['button_type'] : 'emoji' ),
                'button_emoji'            => esc_attr( isset( $options['button_emoji'] ) ? $options['button_emoji'] : '☕' ),
                'button_svg'              => isset( $options['button_svg'] ) ? $options['button_svg'] : '',
                'button_png_url'          => esc_url( isset( $options['button_png_url'] ) ? $options['button_png_url'] : '' ),
                'pluginUrl'               => $plugin_url,
                // Payment settings
                'crypto_address'          => esc_attr( isset( $options['crypto_address'] ) ? $options['crypto_address'] : '' ),
                'crypto_network'          => esc_attr( isset( $options['crypto_network'] ) ? $options['crypto_network'] : 'quai' ),
                'nowpayments_api_key'     => esc_attr( isset( $options['nowpayments_api_key'] ) ? $options['nowpayments_api_key'] : '' ),
                'coingate_api_key'        => esc_attr( isset( $options['coingate_api_key'] ) ? $options['coingate_api_key'] : '' ),
                'bitpay_api_key'          => esc_attr( isset( $options['bitpay_api_key'] ) ? $options['bitpay_api_key'] : '' ),
                'moonpay_api_key'         => esc_attr( isset( $options['moonpay_api_key'] ) ? $options['moonpay_api_key'] : '' ),
                'stripe_publishable_key'  => esc_attr( isset( $options['stripe_publishable_key'] ) ? $options['stripe_publishable_key'] : '' ),
                'paypal_client_id'        => esc_attr( isset( $options['paypal_client_id'] ) ? $options['paypal_client_id'] : '' ),
                'paypal_email'            => esc_attr( isset( $options['paypal_email'] ) ? $options['paypal_email'] : '' ),
                'payment_methods'         => $payment_methods,
                'donation_tiers'          => $donation_tiers,
                'membership_enabled'      => isset( $options['membership_enabled'] ) ? (int) $options['membership_enabled'] : 0,
            )
        ) );
    }

    private function get_translation_strings() {
        return array(
            'support'               => __( 'Support', 'coffee-widget' ),
            'donate'                => __( 'Donate', 'coffee-widget' ),
            'membership'            => __( 'Membership', 'coffee-widget' ),
            'crypto'                => __( 'Crypto', 'coffee-widget' ),
            'credit_card'           => __( 'Credit Card', 'coffee-widget' ),
            'paypal'                => __( 'PayPal', 'coffee-widget' ),
            'nowpayments'           => __( 'NOWPayments', 'coffee-widget' ),
            'coingate'              => __( 'CoinGate', 'coffee-widget' ),
            'bitpay'                => __( 'BitPay', 'coffee-widget' ),
            'moonpay'               => __( 'MoonPay', 'coffee-widget' ),
            'stripe'                => __( 'Stripe', 'coffee-widget' ),
            'choose_amount'         => __( 'Choose amount', 'coffee-widget' ),
            'custom_amount'         => __( 'Custom amount', 'coffee-widget' ),
            'enter_amount'          => __( 'Enter amount (USD)', 'coffee-widget' ),
            'donate_now'            => __( 'Donate Now', 'coffee-widget' ),
            'pay_with_card'         => __( 'Pay with Card', 'coffee-widget' ),
            'buy_crypto'            => __( 'Buy Crypto', 'coffee-widget' ),
            'processing'            => __( 'Processing...', 'coffee-widget' ),
            'payment_success'       => __( 'Payment successful!', 'coffee-widget' ),
            'payment_failed'        => __( 'Payment failed', 'coffee-widget' ),
            'crypto_address'        => __( 'Crypto Address', 'coffee-widget' ),
            'copy_address'          => __( 'Copy Address', 'coffee-widget' ),
            'copied'                => __( 'Copied!', 'coffee-widget' ),
            'crypto_address_label'  => __( 'Send cryptocurrency directly to this address:', 'coffee-widget' ),
            'crypto_note'           => __( 'Funds go directly to your wallet. No fees.', 'coffee-widget' ),
            'your_crypto_address'   => __( 'Your wallet address:', 'coffee-widget' ),
            'nowpayments_desc'      => __( 'Pay with credit card – crypto goes directly to your wallet.', 'coffee-widget' ),
            'coingate_desc'         => __( 'Pay with credit card – crypto to your wallet.', 'coffee-widget' ),
            'bitpay_desc'           => __( 'Enterprise-grade crypto payments. Best for US businesses.', 'coffee-widget' ),
            'moonpay_desc'          => __( 'Buy crypto with credit card – then send it to the address below.', 'coffee-widget' ),
            'moonpay_note'          => __( 'After purchasing crypto, please send it to the wallet address above.', 'coffee-widget' ),
            'membership_plans'      => __( 'Membership Plans', 'coffee-widget' ),
            'monthly'               => __( 'Monthly', 'coffee-widget' ),
            'yearly'                => __( 'Yearly', 'coffee-widget' ),
            'subscribe'             => __( 'Subscribe', 'coffee-widget' ),
            'crypto_network'        => __( 'Network', 'coffee-widget' ),
            'crypto_note'           => __( 'Funds go directly to your wallet. No fees.', 'coffee-widget' ),
            'pay_with_card'         => __( 'Pay with Card', 'coffee-widget' ),
            'nowpayments_desc'      => __( 'Pay with credit card – crypto goes directly to your wallet.', 'coffee-widget' ),
            'crypto_address_label'  => __( 'Send cryptocurrency directly to this address:', 'coffee-widget' ),            
        );
    }
}
