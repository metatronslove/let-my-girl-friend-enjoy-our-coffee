<?php
/**
 * AJAX handlers for payment processing.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Coffee_Widget_Ajax {

    public function __construct() {
        // Stripe
        add_action( 'wp_ajax_coffee_widget_create_payment_intent', array( $this, 'create_payment_intent' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_create_payment_intent', array( $this, 'create_payment_intent' ) );

        // PayPal
        add_action( 'wp_ajax_coffee_widget_create_paypal_order', array( $this, 'create_paypal_order' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_create_paypal_order', array( $this, 'create_paypal_order' ) );
        add_action( 'wp_ajax_coffee_widget_capture_paypal_order', array( $this, 'capture_paypal_order' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_capture_paypal_order', array( $this, 'capture_paypal_order' ) );

        // NOWPayments
        add_action( 'wp_ajax_coffee_widget_create_nowpayments_invoice', array( $this, 'create_nowpayments_invoice' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_create_nowpayments_invoice', array( $this, 'create_nowpayments_invoice' ) );
        add_action( 'wp_ajax_coffee_widget_check_nowpayments_payment', array( $this, 'check_nowpayments_payment' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_check_nowpayments_payment', array( $this, 'check_nowpayments_payment' ) );

        // CoinGate
        add_action( 'wp_ajax_coffee_widget_create_coingate_invoice', array( $this, 'create_coingate_invoice' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_create_coingate_invoice', array( $this, 'create_coingate_invoice' ) );

        // BitPay
        add_action( 'wp_ajax_coffee_widget_create_bitpay_invoice', array( $this, 'create_bitpay_invoice' ) );
        add_action( 'wp_ajax_nopriv_coffee_widget_create_bitpay_invoice', array( $this, 'create_bitpay_invoice' ) );
    }

    /**
     * Validate and sanitize amount input.
     */
    private function validate_and_sanitize_amount( $amount ) {
        if ( ! isset( $amount ) ) {
            return false;
        }
        $amount = floatval( $amount );
        return $amount > 0 ? $amount : false;
    }

    /**
     * Validate and sanitize nonce input.
     */
    private function validate_nonce( $nonce ) {
        if ( ! isset( $nonce ) ) {
            return false;
        }
        $nonce = sanitize_text_field( wp_unslash( $nonce ) );
        return wp_verify_nonce( $nonce, 'coffee_widget_payment' );
    }

    /**
     * Stripe: Create a PaymentIntent for one-time donation.
     */
    public function create_payment_intent() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if amount exists
        if ( ! isset( $_POST['amount'] ) ) {
            wp_send_json_error( 'Amount missing' );
        }
        
        // Sanitize and validate amount
        $raw_amount = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
        $amount = $this->validate_and_sanitize_amount( $raw_amount );
        if ( ! $amount ) {
            wp_send_json_error( 'Invalid amount' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $secret_key = isset( $options['stripe_secret_key'] ) ? $options['stripe_secret_key'] : '';

        if ( empty( $secret_key ) ) {
            wp_send_json_error( 'Stripe secret key not configured' );
        }

        // Check if Stripe PHP library is available
        if ( ! class_exists( 'Stripe\Stripe' ) ) {
            wp_send_json_error( 'Stripe PHP library missing. Please install via Composer or contact your hosting provider.' );
        }

        try {
            \Stripe\Stripe::setApiKey( $secret_key );
            $intent = \Stripe\PaymentIntent::create([
                'amount' => intval( $amount * 100 ),
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);
            wp_send_json_success( [ 'client_secret' => $intent->client_secret ] );
        } catch ( Exception $e ) {
            wp_send_json_error( 'Stripe error: ' . $e->getMessage() );
        }
    }

    /**
     * PayPal: Create an order.
     */
    public function create_paypal_order() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if amount exists
        if ( ! isset( $_POST['amount'] ) ) {
            wp_send_json_error( 'Amount missing' );
        }
        
        // Sanitize and validate amount
        $raw_amount = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
        $amount = $this->validate_and_sanitize_amount( $raw_amount );
        if ( ! $amount ) {
            wp_send_json_error( 'Invalid amount' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $client_id = isset( $options['paypal_client_id'] ) ? $options['paypal_client_id'] : '';
        $client_secret = isset( $options['paypal_secret'] ) ? $options['paypal_secret'] : '';

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            wp_send_json_error( 'PayPal API credentials not configured' );
        }

        // Use live or sandbox
        $api_url = 'https://api-m.paypal.com';
        
        // Get access token
        $auth = base64_encode( $client_id . ':' . $client_secret );
        $response = wp_remote_post( $api_url . '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'body'    => 'grant_type=client_credentials',
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'PayPal auth error: ' . $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! isset( $body['access_token'] ) ) {
            wp_send_json_error( 'Failed to get PayPal access token' );
        }
        $access_token = $body['access_token'];

        // Create order
        $order_response = wp_remote_post( $api_url . '/v2/checkout/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
            'body'    => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format( $amount, 2, '.', '' ),
                        ],
                    ],
                ],
            ]),
        ] );

        if ( is_wp_error( $order_response ) ) {
            wp_send_json_error( 'PayPal order creation failed: ' . $order_response->get_error_message() );
        }

        $order_data = json_decode( wp_remote_retrieve_body( $order_response ), true );
        if ( ! isset( $order_data['id'] ) ) {
            wp_send_json_error( 'PayPal order creation failed: ' . wp_json_encode( $order_data ) );
        }
        
        wp_send_json_success( [ 'order_id' => $order_data['id'] ] );
    }

    /**
     * PayPal: Capture an order after approval.
     */
    public function capture_paypal_order() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if order_id exists
        if ( ! isset( $_POST['order_id'] ) ) {
            wp_send_json_error( 'Order ID missing' );
        }
        
        $order_id = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );
        if ( empty( $order_id ) ) {
            wp_send_json_error( 'Invalid order ID' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $client_id = isset( $options['paypal_client_id'] ) ? $options['paypal_client_id'] : '';
        $client_secret = isset( $options['paypal_secret'] ) ? $options['paypal_secret'] : '';

        $api_url = 'https://api-m.paypal.com';
        
        $auth = base64_encode( $client_id . ':' . $client_secret );
        $response = wp_remote_post( $api_url . '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . $auth,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'body'    => 'grant_type=client_credentials',
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'PayPal auth error' );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $access_token = isset( $body['access_token'] ) ? $body['access_token'] : '';
        
        if ( empty( $access_token ) ) {
            wp_send_json_error( 'Failed to get PayPal access token' );
        }

        $capture = wp_remote_post( $api_url . "/v2/checkout/orders/{$order_id}/capture", [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
        ] );

        if ( is_wp_error( $capture ) ) {
            wp_send_json_error( 'Capture failed: ' . $capture->get_error_message() );
        }

        $capture_data = json_decode( wp_remote_retrieve_body( $capture ), true );
        if ( isset( $capture_data['status'] ) && $capture_data['status'] === 'COMPLETED' ) {
            wp_send_json_success( [ 'message' => 'Payment successful!' ] );
        } else {
            wp_send_json_error( 'Capture not completed: ' . wp_json_encode( $capture_data ) );
        }
    }

    /**
     * NOWPayments: Create an invoice.
     */
    public function create_nowpayments_invoice() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if amount exists
        if ( ! isset( $_POST['amount'] ) ) {
            wp_send_json_error( 'Amount missing' );
        }
        
        // Sanitize and validate amount
        $raw_amount = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
        $amount = $this->validate_and_sanitize_amount( $raw_amount );
        if ( ! $amount ) {
            wp_send_json_error( 'Invalid amount' );
        }

        $currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : 'USD';
        $crypto_currency = isset( $_POST['crypto_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['crypto_currency'] ) ) : 'usdt';

        $options = get_option( 'coffee_widget_settings' );
        $api_key = isset( $options['nowpayments_api_key'] ) ? $options['nowpayments_api_key'] : '';

        if ( empty( $api_key ) ) {
            wp_send_json_error( 'NOWPayments API key not configured' );
        }

        $response = wp_remote_post( 'https://api.nowpayments.io/v1/invoice', [
            'headers' => [
                'x-api-key' => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'price_amount' => $amount,
                'price_currency' => $currency,
                'pay_currency' => strtolower( $crypto_currency ),
                'order_id' => uniqid( 'cw_' ),
                'order_description' => 'Donation via Coffee Widget',
            ]),
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'NOWPayments API error: ' . $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['id'] ) ) {
            wp_send_json_success( [
                'invoice_id' => $body['id'],
                'invoice_url' => $body['invoice_url'],
            ] );
        } else {
            wp_send_json_error( 'Failed to create invoice: ' . ( isset( $body['message'] ) ? $body['message'] : 'Unknown error' ) );
        }
    }

    /**
     * NOWPayments: Check payment status.
     */
    public function check_nowpayments_payment() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if invoice_id exists
        if ( ! isset( $_POST['invoice_id'] ) ) {
            wp_send_json_error( 'Invoice ID missing' );
        }
        
        $invoice_id = intval( $_POST['invoice_id'] );
        if ( $invoice_id <= 0 ) {
            wp_send_json_error( 'Invalid invoice ID' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $api_key = isset( $options['nowpayments_api_key'] ) ? $options['nowpayments_api_key'] : '';

        $response = wp_remote_get( "https://api.nowpayments.io/v1/invoice/{$invoice_id}", [
            'headers' => [
                'x-api-key' => $api_key,
            ],
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'Failed to check status' );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        wp_send_json_success( [
            'status' => isset( $body['payment_status'] ) ? $body['payment_status'] : 'pending',
        ] );
    }

    /**
     * CoinGate: Create an invoice.
     */
    public function create_coingate_invoice() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if amount exists
        if ( ! isset( $_POST['amount'] ) ) {
            wp_send_json_error( 'Amount missing' );
        }
        
        // Sanitize and validate amount
        $raw_amount = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
        $amount = $this->validate_and_sanitize_amount( $raw_amount );
        if ( ! $amount ) {
            wp_send_json_error( 'Invalid amount' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $api_key = isset( $options['coingate_api_key'] ) ? $options['coingate_api_key'] : '';

        if ( empty( $api_key ) ) {
            wp_send_json_error( 'CoinGate API key not configured' );
        }

        $response = wp_remote_post( 'https://api.coingate.com/v2/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'price_amount' => $amount,
                'price_currency' => 'USD',
                'receive_currency' => strtoupper( isset( $options['crypto_network'] ) ? $options['crypto_network'] : 'BTC' ),
                'order_id' => uniqid( 'cw_' ),
                'title' => 'Donation via Coffee Widget',
            ]),
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'CoinGate API error' );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['id'] ) ) {
            wp_send_json_success( [
                'invoice_id' => $body['id'],
                'invoice_url' => $body['payment_url'],
            ] );
        } else {
            wp_send_json_error( 'Failed to create invoice: ' . ( isset( $body['message'] ) ? $body['message'] : 'Unknown error' ) );
        }
    }

    /**
     * BitPay: Create an invoice.
     */
    public function create_bitpay_invoice() {
        // Check if nonce exists and is valid
        if ( ! isset( $_POST['nonce'] ) ) {
            wp_send_json_error( 'Nonce missing' );
        }
        
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'coffee_widget_payment' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        // Check if amount exists
        if ( ! isset( $_POST['amount'] ) ) {
            wp_send_json_error( 'Amount missing' );
        }
        
        // Sanitize and validate amount
        $raw_amount = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
        $amount = $this->validate_and_sanitize_amount( $raw_amount );
        if ( ! $amount ) {
            wp_send_json_error( 'Invalid amount' );
        }

        $options = get_option( 'coffee_widget_settings' );
        $api_key = isset( $options['bitpay_api_key'] ) ? $options['bitpay_api_key'] : '';

        if ( empty( $api_key ) ) {
            wp_send_json_error( 'BitPay API key not configured' );
        }

        // Note: BitPay requires a more complex setup with pairing code.
        // This is a simplified placeholder. Full integration requires additional steps.
        wp_send_json_error( 'BitPay integration requires additional setup. Please use NOWPayments or CoinGate instead.' );
    }
}
