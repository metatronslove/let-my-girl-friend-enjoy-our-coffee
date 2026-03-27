<?php
/**
 * GitHub Funding Page for Coffee Widget
 *
 * This file is used as a custom funding URL for GitHub's FUNDING.yml.
 * Displays the widget's payment methods in a full page layout.
 */

// WordPress bootstrap
$coffee_widget_root_dir = dirname( dirname( __FILE__ ) );
$coffee_widget_wp_load  = dirname( dirname( dirname( dirname( $coffee_widget_root_dir ) ) ) ) . '/wp-load.php';

if ( file_exists( $coffee_widget_wp_load ) ) {
	require_once $coffee_widget_wp_load;
} else {
	$coffee_widget_path = dirname( __FILE__ );
	for ( $coffee_widget_i = 0; $coffee_widget_i < 10; $coffee_widget_i++ ) {
		$coffee_widget_wp_load = dirname( $coffee_widget_path ) . '/wp-load.php';
		if ( file_exists( $coffee_widget_wp_load ) ) {
			require_once $coffee_widget_wp_load;
			break;
		}
		$coffee_widget_path = dirname( $coffee_widget_path );
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 500 Internal Server Error' );
	echo '<h1>Error</h1><p>WordPress environment not found.</p>';
	exit;
}

// Get plugin settings
$coffee_widget_options = get_option( 'coffee_widget_settings', array() );
$coffee_widget_plugin_version = defined( 'COFFEE_WIDGET_VERSION' ) ? COFFEE_WIDGET_VERSION : '1.0.0';

// Get enabled payment methods
$coffee_widget_payment_methods = isset( $coffee_widget_options['payment_methods'] ) && is_array( $coffee_widget_options['payment_methods'] )
	? $coffee_widget_options['payment_methods']
	: array( 'crypto' );

// Get donation tiers
$coffee_widget_donation_tiers = isset( $coffee_widget_options['donation_tiers'] ) && is_array( $coffee_widget_options['donation_tiers'] )
	? $coffee_widget_options['donation_tiers']
	: array( '5', '10', '20', '50' );

// Get crypto settings
$coffee_widget_crypto_address = isset( $coffee_widget_options['crypto_address'] ) ? $coffee_widget_options['crypto_address'] : '';
$coffee_widget_crypto_network = isset( $coffee_widget_options['crypto_network'] ) ? $coffee_widget_options['crypto_network'] : 'quai';

// Get payment method keys
$coffee_widget_stripe_key      = isset( $coffee_widget_options['stripe_publishable_key'] ) ? $coffee_widget_options['stripe_publishable_key'] : '';
$coffee_widget_paypal_client_id = isset( $coffee_widget_options['paypal_client_id'] ) ? $coffee_widget_options['paypal_client_id'] : '';
$coffee_widget_nowpayments_key  = isset( $coffee_widget_options['nowpayments_api_key'] ) ? $coffee_widget_options['nowpayments_api_key'] : '';
$coffee_widget_coingate_key     = isset( $coffee_widget_options['coingate_api_key'] ) ? $coffee_widget_options['coingate_api_key'] : '';
$coffee_widget_bitpay_key       = isset( $coffee_widget_options['bitpay_api_key'] ) ? $coffee_widget_options['bitpay_api_key'] : '';
$coffee_widget_moonpay_key      = isset( $coffee_widget_options['moonpay_api_key'] ) ? $coffee_widget_options['moonpay_api_key'] : '';

// Widget color for buttons only (not for floating button)
$coffee_widget_color = isset( $coffee_widget_options['color'] ) ? $coffee_widget_options['color'] : '#FFDD00';

// Proxy URL for scripts
$coffee_widget_plugin_url  = plugin_dir_url( __FILE__ );
$coffee_widget_proxy_url   = $coffee_widget_plugin_url . 'js/coffee-partner-scripts-proxy.php';

// Nonce for AJAX
$coffee_widget_nonce      = wp_create_nonce( 'coffee_widget_payment' );
$coffee_widget_ajax_url   = admin_url( 'admin-ajax.php' );

// OG Image - use docs folder images
$coffee_widget_docs_url   = plugin_dir_url( dirname( __FILE__ ) ) . 'docs/';
$coffee_widget_og_image   = $coffee_widget_docs_url . 'banner-1200x630.png';
if ( ! file_exists( dirname( dirname( __FILE__ ) ) . '/docs/banner-1200x630.png' ) ) {
	$coffee_widget_og_image = $coffee_widget_docs_url . 'let-girl-friend-enjoy-our-coffee.png';
}

// Translation strings (prefixed)
$coffee_widget_i18n = array(
	'support'               => __( 'Support', 'coffee-widget' ),
	'donate'                => __( 'Donate', 'coffee-widget' ),
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
	'donate_now'            => __( 'Donate Now', 'coffee-widget' ),
	'pay_with_card'         => __( 'Pay with Card', 'coffee-widget' ),
	'crypto_address'        => __( 'Crypto Address', 'coffee-widget' ),
	'copy_address'          => __( 'Copy Address', 'coffee-widget' ),
	'copied'                => __( 'Copied!', 'coffee-widget' ),
	'crypto_address_label'  => __( 'Send cryptocurrency directly to this address:', 'coffee-widget' ),
	'crypto_note'           => __( 'Funds go directly to your wallet. No fees.', 'coffee-widget' ),
	'crypto_network'        => __( 'Network', 'coffee-widget' ),
	'nowpayments_desc'      => __( 'Pay with credit card – crypto goes directly to your wallet.', 'coffee-widget' ),
	'coingate_desc'         => __( 'Pay with credit card – crypto to your wallet.', 'coffee-widget' ),
	'bitpay_desc'           => __( 'Enterprise-grade crypto payments. Best for US businesses.', 'coffee-widget' ),
	'moonpay_desc'          => __( 'Buy crypto with credit card – then send it to the address below.', 'coffee-widget' ),
	'moonpay_note'          => __( 'After purchasing crypto, please send it to the wallet address above.', 'coffee-widget' ),
	'your_crypto_address'   => __( 'Your wallet address:', 'coffee-widget' ),
	'processing'            => __( 'Processing...', 'coffee-widget' ),
	'payment_success'       => __( 'Payment successful!', 'coffee-widget' ),
	'payment_failed'        => __( 'Payment failed', 'coffee-widget' ),
	'buy_crypto'            => __( 'Buy Crypto', 'coffee-widget' ),
);

// Enqueue scripts via WordPress (if needed)
function coffee_widget_enqueue_github_scripts() {
	global $coffee_widget_payment_methods, $coffee_widget_stripe_key, $coffee_widget_paypal_client_id, $coffee_widget_proxy_url;

	if ( in_array( 'stripe', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_stripe_key ) ) {
		wp_enqueue_script(
			'coffee-widget-stripe-proxy',
			add_query_arg( array( 'script' => 'stripe', 'min' => 1 ), $coffee_widget_proxy_url ),
			array(),
			null,
			true
		);
	}
	if ( in_array( 'paypal', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_paypal_client_id ) ) {
		wp_enqueue_script(
			'coffee-widget-paypal-proxy',
			add_query_arg( array( 'script' => 'paypal', 'min' => 1 ), $coffee_widget_proxy_url ),
			array(),
			null,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'coffee_widget_enqueue_github_scripts' );

// Start output buffering so we can capture head and footer
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo esc_html( $coffee_widget_i18n['support'] ); ?> – <?php echo esc_html( get_bloginfo( 'name' ) ); ?></title>
    <meta name="description" content="<?php echo esc_attr( $coffee_widget_i18n['support'] ); ?> <?php echo esc_attr( get_bloginfo( 'name' ) ); ?> with credit card, PayPal, and crypto – all inline.">
    <meta property="og:title" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> - <?php echo esc_attr( $coffee_widget_i18n['support'] ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $coffee_widget_i18n['support'] ); ?> <?php echo esc_attr( get_bloginfo( 'name' ) ); ?> with credit card, PayPal, and crypto.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url( get_site_url() ); ?>">
    <meta property="og:image" content="<?php echo esc_url( $coffee_widget_og_image ); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', 'Poppins', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #fef9e6 0%, #fff5e6 100%);
            color: #2c1810;
            line-height: 1.6;
        }
        .banner {
            position: relative;
            width: 100%;
            height: calc(675 * calc(100vw / 1200));
            background: linear-gradient(135deg, #2c1810 0%, #3e2a1f 100%);
            overflow: hidden;
        }
        .banner-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.5;
        }
        .banner-content {
            position: relative;
            z-index: 2;
            height: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
            text-align: center;
            color: white;
        }
        .coffee-icon {
			height: 66%;
            font-size: 64px;
            margin-bottom: 20px;
            display: inline-block;
            animation: steam 3s ease-in-out infinite;
        }
        @keyframes steam {
            0%, 100% { transform: translateY(0); opacity: 0.8; }
            50% { transform: translateY(-8px); opacity: 1; }
        }
        .banner h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        }
        .banner .tagline {
            font-size: 1.2rem;
            opacity: 0.95;
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        .widget-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 40px;
        }
        .widget-header {
            background: linear-gradient(135deg, #2c1810 0%, #3e2a1f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .widget-header h2 {
            font-size: 1.6rem;
            margin-bottom: 8px;
        }
        .widget-header p {
            font-size: 1rem;
            opacity: 0.9;
        }
        .tabs {
            display: flex;
            flex-wrap: wrap;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 12px 0 12px;
            gap: 4px;
        }
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            color: #6c757d;
            border-radius: 20px 20px 0 0;
            background: none;
            border: none;
            transition: all 0.2s;
        }
        .tab.active {
            color: #2c3e50;
            background: white;
            border-bottom: 2px solid <?php echo esc_attr( $coffee_widget_color ); ?>;
        }
        .tab-panel {
            display: none;
            padding: 30px;
        }
        .tab-panel.active {
            display: block;
        }
        .donation-tiers {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        .tier {
            background: #f1f3f5;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .tier:hover {
            background: <?php echo esc_attr( $coffee_widget_color ); ?>;
            transform: scale(1.02);
        }
        .custom-amount-container {
            margin: 15px 0;
        }
        .custom-amount-container input {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 1px solid #dee2e6;
            border-radius: 40px;
            text-align: center;
            box-sizing: border-box;
        }
        .payment-button {
            background: <?php echo esc_attr( $coffee_widget_color ); ?>;
            border: none;
            padding: 14px 24px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .payment-button:hover {
            filter: brightness(0.95);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .payment-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .crypto-address {
            background: #f8f9fa;
            padding: 14px;
            border-radius: 12px;
            word-break: break-all;
            font-family: monospace;
            margin: 15px 0;
            border: 1px solid #e9ecef;
        }
        .copy-btn {
            background: <?php echo esc_attr( $coffee_widget_color ); ?>;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            margin-top: 8px;
        }
        .info-note {
            font-size: 12px;
            color: #6c757d;
            margin-top: 12px;
            text-align: center;
        }
        .loader {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 221, 0, 0.3);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px rgba(0,0,0,0.12);
            border-color: <?php echo esc_attr( $coffee_widget_color ); ?>;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: inline-block;
        }
        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: #2c1810;
        }
        .feature-card p {
            color: #5a3e2e;
            font-size: 0.9rem;
        }
        .footer {
            background: #2c1810;
            color: #f5e6d3;
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
        }
        .footer a {
            color: <?php echo esc_attr( $coffee_widget_color ); ?>;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .banner h1 {
                font-size: 1.8rem;
            }
            .banner .tagline {
                font-size: 1rem;
            }
            .widget-header h2 {
                font-size: 1.3rem;
            }
            .tab {
                padding: 8px 16px;
                font-size: 12px;
            }
            .tab-panel {
                padding: 20px;
            }
            .tier {
                padding: 10px 18px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="banner">
    <img src="<?php echo esc_url( $coffee_widget_docs_url . 'banner-1200x675.png' ); ?>" alt="Coffee Widget" class="banner-image" onerror="this.style.display='none'">
    <div class="banner-content">
        <div class="coffee-icon">☕</div>
        <h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
        <div class="tagline"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> 💛 <?php echo esc_html( $coffee_widget_i18n['support'] ); ?></div>
    </div>
</div>

<div class="container">
    <!-- Main Widget Card -->
    <div class="widget-card">
        <div class="widget-header">
            <h2><?php echo esc_html( $coffee_widget_i18n['donate'] ); ?></h2>
        </div>

        <div class="tabs" id="supportTabs">
            <?php if ( in_array( 'crypto', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_crypto_address ) ) : ?>
                <button class="tab" data-tab="crypto">🔗 <?php echo esc_html( $coffee_widget_i18n['crypto'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'nowpayments', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_nowpayments_key ) ) : ?>
                <button class="tab" data-tab="nowpayments">⚡ <?php echo esc_html( $coffee_widget_i18n['nowpayments'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'coingate', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_coingate_key ) ) : ?>
                <button class="tab" data-tab="coingate">🪙 <?php echo esc_html( $coffee_widget_i18n['coingate'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'bitpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_bitpay_key ) ) : ?>
                <button class="tab" data-tab="bitpay">🏦 <?php echo esc_html( $coffee_widget_i18n['bitpay'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'moonpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_moonpay_key ) ) : ?>
                <button class="tab" data-tab="moonpay">🌙 <?php echo esc_html( $coffee_widget_i18n['moonpay'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'stripe', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_stripe_key ) ) : ?>
                <button class="tab" data-tab="stripe">💳 <?php echo esc_html( $coffee_widget_i18n['stripe'] ); ?></button>
            <?php endif; ?>
            <?php if ( in_array( 'paypal', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_paypal_client_id ) ) : ?>
                <button class="tab" data-tab="paypal">🟡 <?php echo esc_html( $coffee_widget_i18n['paypal'] ); ?></button>
            <?php endif; ?>
        </div>

        <div class="tab-content">
            <!-- Crypto Panel -->
            <?php if ( in_array( 'crypto', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_crypto_address ) ) : ?>
            <div class="tab-panel" id="panel-crypto">
                <p><?php echo esc_html( $coffee_widget_i18n['crypto_address_label'] ); ?></p>
                <div class="crypto-address"><?php echo esc_html( $coffee_widget_crypto_address ); ?></div>
                <button class="copy-btn" data-address="<?php echo esc_attr( $coffee_widget_crypto_address ); ?>">📋 <?php echo esc_html( $coffee_widget_i18n['copy_address'] ); ?></button>
                <p class="info-note"><strong><?php echo esc_html( $coffee_widget_i18n['crypto_network'] ); ?>:</strong> <?php echo esc_html( strtoupper( $coffee_widget_crypto_network ) ); ?></p>
                <p class="info-note"><?php echo esc_html( $coffee_widget_i18n['crypto_note'] ); ?></p>
            </div>
            <?php endif; ?>

            <!-- NOWPayments Panel -->
            <?php if ( in_array( 'nowpayments', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_nowpayments_key ) ) : ?>
            <div class="tab-panel" id="panel-nowpayments">
                <p><?php echo esc_html( $coffee_widget_i18n['nowpayments_desc'] ); ?></p>
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="nowpayments-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <button id="nowpayments-pay" class="payment-button" disabled><?php echo esc_html( $coffee_widget_i18n['pay_with_card'] ); ?></button>
                <div id="nowpayments-loader" class="loader" style="display:none;">⏳ <?php echo esc_html( $coffee_widget_i18n['processing'] ); ?></div>
            </div>
            <?php endif; ?>

            <!-- CoinGate Panel -->
            <?php if ( in_array( 'coingate', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_coingate_key ) ) : ?>
            <div class="tab-panel" id="panel-coingate">
                <p><?php echo esc_html( $coffee_widget_i18n['coingate_desc'] ); ?></p>
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="coingate-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <button id="coingate-pay" class="payment-button" disabled><?php echo esc_html( $coffee_widget_i18n['pay_with_card'] ); ?></button>
                <div id="coingate-loader" class="loader" style="display:none;">⏳ <?php echo esc_html( $coffee_widget_i18n['processing'] ); ?></div>
            </div>
            <?php endif; ?>

            <!-- BitPay Panel -->
            <?php if ( in_array( 'bitpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_bitpay_key ) ) : ?>
            <div class="tab-panel" id="panel-bitpay">
                <p><?php echo esc_html( $coffee_widget_i18n['bitpay_desc'] ); ?></p>
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="bitpay-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <button id="bitpay-pay" class="payment-button" disabled><?php echo esc_html( $coffee_widget_i18n['pay_with_card'] ); ?></button>
                <div id="bitpay-loader" class="loader" style="display:none;">⏳ <?php echo esc_html( $coffee_widget_i18n['processing'] ); ?></div>
            </div>
            <?php endif; ?>

            <!-- MoonPay Panel -->
            <?php if ( in_array( 'moonpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_moonpay_key ) ) : ?>
            <div class="tab-panel" id="panel-moonpay">
                <p><?php echo esc_html( $coffee_widget_i18n['moonpay_desc'] ); ?></p>
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="moonpay-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <button id="moonpay-buy" class="payment-button" disabled><?php echo esc_html( $coffee_widget_i18n['buy_crypto'] ); ?></button>
                <div class="crypto-address" style="margin-top:15px;">
                    <strong><?php echo esc_html( $coffee_widget_i18n['your_crypto_address'] ); ?></strong><br>
                    <code><?php echo esc_html( $coffee_widget_crypto_address ); ?></code>
                    <button class="copy-btn" data-address="<?php echo esc_attr( $coffee_widget_crypto_address ); ?>" style="margin-top:8px;">📋 <?php echo esc_html( $coffee_widget_i18n['copy_address'] ); ?></button>
                </div>
                <p class="info-note"><?php echo esc_html( $coffee_widget_i18n['moonpay_note'] ); ?></p>
            </div>
            <?php endif; ?>

            <!-- Stripe Panel -->
            <?php if ( in_array( 'stripe', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_stripe_key ) ) : ?>
            <div class="tab-panel" id="panel-stripe">
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="stripe-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <div id="stripe-elements"></div>
                <button id="stripe-pay" class="payment-button" disabled><?php echo esc_html( $coffee_widget_i18n['pay_with_card'] ); ?></button>
                <div id="stripe-loader" class="loader" style="display:none;">⏳ <?php echo esc_html( $coffee_widget_i18n['processing'] ); ?></div>
            </div>
            <?php endif; ?>

            <!-- PayPal Panel -->
            <?php if ( in_array( 'paypal', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_paypal_client_id ) ) : ?>
            <div class="tab-panel" id="panel-paypal">
                <div class="donation-tiers">
                    <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                        <button class="tier" data-amount="<?php echo esc_attr( $coffee_widget_tier ); ?>">$<?php echo esc_html( $coffee_widget_tier ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="custom-amount-container">
                    <input type="number" id="paypal-amount" placeholder="<?php echo esc_attr( $coffee_widget_i18n['custom_amount'] ); ?> (USD)" step="0.01" min="1">
                </div>
                <div id="paypal-button-container"></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <h3><?php esc_html_e( 'Inline Payments', 'coffee-widget' ); ?></h3>
            <p><?php esc_html_e( 'Stripe Elements & PayPal Smart Buttons embedded directly – never leave the page.', 'coffee-widget' ); ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔗</div>
            <h3><?php esc_html_e( 'Crypto Ready', 'coffee-widget' ); ?></h3>
            <p><?php esc_html_e( 'Accept Quai, Bitcoin, Ethereum – zero fees for crypto donations.', 'coffee-widget' ); ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🌍</div>
            <h3><?php esc_html_e( 'Global Payments', 'coffee-widget' ); ?></h3>
            <p><?php esc_html_e( 'NOWPayments, CoinGate, BitPay, Stripe, PayPal – choose what works for you.', 'coffee-widget' ); ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🆓</div>
            <h3><?php esc_html_e( '100% Free', 'coffee-widget' ); ?></h3>
            <p><?php esc_html_e( 'No monthly fees, no premium versions. You only pay transaction fees.', 'coffee-widget' ); ?></p>
        </div>
    </div>
</div>

<div class="footer">
    <p>☕ <strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong> – 💛 <?php echo esc_html( $coffee_widget_i18n['support'] ); ?></p>
    <p style="margin-top: 15px;">
        <a href="<?php echo esc_url( get_site_url() ); ?>" target="_blank"><i class="fas fa-home"></i> <?php esc_html_e( 'Visit Website', 'coffee-widget' ); ?></a>
        <?php if ( ! empty( $coffee_widget_crypto_address ) ) : ?>
            &nbsp;|&nbsp;
            <a href="#" id="footer-copy-link"><i class="fas fa-copy"></i> <?php echo esc_html( $coffee_widget_i18n['copy_address'] ); ?></a>
        <?php endif; ?>
			&nbsp;|&nbsp;
            <a href="https://github.com/metatronslove/let-my-girl-friend-enjoy-her-coffee" target="_blank"><i class="fab fa-github"></i> GitHub</a> &nbsp;|&nbsp;
            <a href="https://buymeacoffee.com/invite/metatronslove" target="_blank"><i class="fas fa-mug-hot"></i> Buy Me a Coffee</a>
    </p>
    <p style="margin-top: 20px; font-size: 0.8rem;">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> – <?php esc_html_e( 'Free forever. No premium versions.', 'coffee-widget' ); ?></p>
</div>

<?php wp_footer(); ?>

<script>
var coffeeWidgetSupport = {
    ajaxUrl: '<?php echo esc_js( $coffee_widget_ajax_url ); ?>',
    nonce: '<?php echo esc_js( $coffee_widget_nonce ); ?>',
    cryptoNetwork: '<?php echo esc_js( strtoupper( $coffee_widget_crypto_network ) ); ?>',
    cryptoAddress: '<?php echo esc_js( $coffee_widget_crypto_address ); ?>',
    stripeKey: '<?php echo esc_js( $coffee_widget_stripe_key ); ?>',
    moonpayKey: '<?php echo esc_js( $coffee_widget_moonpay_key ); ?>',
    i18n: {
        copied: '<?php echo esc_js( $coffee_widget_i18n['copied'] ); ?>',
        paymentSuccess: '<?php echo esc_js( $coffee_widget_i18n['payment_success'] ); ?>',
        paymentFailed: '<?php echo esc_js( $coffee_widget_i18n['payment_failed'] ); ?>',
        processing: '<?php echo esc_js( $coffee_widget_i18n['processing'] ); ?>',
        moonpayNote: '<?php echo esc_js( $coffee_widget_i18n['moonpay_note'] ); ?>'
    }
};

(function() {
    // Tab switching
    const tabs = document.querySelectorAll('.tab');
    const panels = document.querySelectorAll('.tab-panel');

    function switchTab(tabId) {
        tabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('data-tab') === tabId) {
                tab.classList.add('active');
            }
        });
        panels.forEach(panel => {
            panel.classList.remove('active');
            if (panel.id === 'panel-' + tabId) {
                panel.classList.add('active');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            switchTab(tab.getAttribute('data-tab'));
        });
    });

    if (tabs.length > 0) {
        switchTab(tabs[0].getAttribute('data-tab'));
    }

    // Copy address
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const address = this.getAttribute('data-address');
            if (address) {
                navigator.clipboard.writeText(address).then(() => {
                    const original = this.textContent;
                    this.textContent = '✓ ' + coffeeWidgetSupport.i18n.copied;
                    setTimeout(() => {
                        this.textContent = original;
                    }, 2000);
                });
            }
        });
    });

    // Footer copy
    const footerCopy = document.getElementById('footer-copy-link');
    if (footerCopy && coffeeWidgetSupport.cryptoAddress) {
        footerCopy.addEventListener('click', (e) => {
            e.preventDefault();
            navigator.clipboard.writeText(coffeeWidgetSupport.cryptoAddress).then(() => {
                const original = footerCopy.innerHTML;
                footerCopy.innerHTML = '<i class="fas fa-check"></i> ' + coffeeWidgetSupport.i18n.copied;
                setTimeout(() => {
                    footerCopy.innerHTML = original;
                }, 2000);
            });
        });
    }

    // Donation tier helper
    function setupDonationTiers(panelId, amountInputId, buttonId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        const tiers = panel.querySelectorAll('.tier');
        const amountInput = document.getElementById(amountInputId);
        const payButton = document.getElementById(buttonId);

        let currentAmount = 0;

        function setAmount(val) {
            currentAmount = parseFloat(val);
            if (payButton) {
                payButton.disabled = currentAmount <= 0 || isNaN(currentAmount);
            }
        }

        tiers.forEach(tier => {
            tier.addEventListener('click', () => {
                const amount = tier.getAttribute('data-amount');
                setAmount(amount);
                if (amountInput) amountInput.value = amount;
            });
        });

        if (amountInput) {
            amountInput.addEventListener('input', () => setAmount(amountInput.value));
        }

        return { getAmount: () => currentAmount };
    }

    // NOWPayments
    <?php if ( in_array( 'nowpayments', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_nowpayments_key ) ) : ?>
    (function() {
        const { getAmount } = setupDonationTiers('panel-nowpayments', 'nowpayments-amount', 'nowpayments-pay');
        const payBtn = document.getElementById('nowpayments-pay');
        const loader = document.getElementById('nowpayments-loader');

        if (payBtn) {
            payBtn.addEventListener('click', () => {
                const amount = getAmount();
                if (amount <= 0) return;

                payBtn.style.display = 'none';
                loader.style.display = 'block';

                fetch(coffeeWidgetSupport.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'coffee_widget_create_nowpayments_invoice',
                        amount: amount,
                        currency: 'USD',
                        crypto_currency: coffeeWidgetSupport.cryptoNetwork.toLowerCase(),
                        nonce: coffeeWidgetSupport.nonce
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.data.invoice_url, '_blank');
                        alert(coffeeWidgetSupport.i18n.paymentSuccess);
                    } else {
                        alert('Error: ' + data.data);
                        payBtn.style.display = 'block';
                    }
                    loader.style.display = 'none';
                })
                .catch(() => {
                    alert(coffeeWidgetSupport.i18n.paymentFailed);
                    payBtn.style.display = 'block';
                    loader.style.display = 'none';
                });
            });
        }
    })();
    <?php endif; ?>

    // CoinGate
    <?php if ( in_array( 'coingate', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_coingate_key ) ) : ?>
    (function() {
        const { getAmount } = setupDonationTiers('panel-coingate', 'coingate-amount', 'coingate-pay');
        const payBtn = document.getElementById('coingate-pay');
        const loader = document.getElementById('coingate-loader');

        if (payBtn) {
            payBtn.addEventListener('click', () => {
                const amount = getAmount();
                if (amount <= 0) return;

                payBtn.style.display = 'none';
                loader.style.display = 'block';

                fetch(coffeeWidgetSupport.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'coffee_widget_create_coingate_invoice',
                        amount: amount,
                        nonce: coffeeWidgetSupport.nonce
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.data.invoice_url, '_blank');
                        alert(coffeeWidgetSupport.i18n.paymentSuccess);
                    } else {
                        alert('Error: ' + data.data);
                        payBtn.style.display = 'block';
                    }
                    loader.style.display = 'none';
                })
                .catch(() => {
                    alert(coffeeWidgetSupport.i18n.paymentFailed);
                    payBtn.style.display = 'block';
                    loader.style.display = 'none';
                });
            });
        }
    })();
    <?php endif; ?>

    // BitPay
    <?php if ( in_array( 'bitpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_bitpay_key ) ) : ?>
    (function() {
        const { getAmount } = setupDonationTiers('panel-bitpay', 'bitpay-amount', 'bitpay-pay');
        const payBtn = document.getElementById('bitpay-pay');
        const loader = document.getElementById('bitpay-loader');

        if (payBtn) {
            payBtn.addEventListener('click', () => {
                const amount = getAmount();
                if (amount <= 0) return;

                payBtn.style.display = 'none';
                loader.style.display = 'block';

                fetch(coffeeWidgetSupport.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'coffee_widget_create_bitpay_invoice',
                        amount: amount,
                        nonce: coffeeWidgetSupport.nonce
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.data.invoice_url, '_blank');
                        alert(coffeeWidgetSupport.i18n.paymentSuccess);
                    } else {
                        alert('Error: ' + data.data);
                        payBtn.style.display = 'block';
                    }
                    loader.style.display = 'none';
                })
                .catch(() => {
                    alert(coffeeWidgetSupport.i18n.paymentFailed);
                    payBtn.style.display = 'block';
                    loader.style.display = 'none';
                });
            });
        }
    })();
    <?php endif; ?>

    // MoonPay
    <?php if ( in_array( 'moonpay', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_moonpay_key ) ) : ?>
    (function() {
        const { getAmount } = setupDonationTiers('panel-moonpay', 'moonpay-amount', 'moonpay-buy');
        const buyBtn = document.getElementById('moonpay-buy');

        if (buyBtn) {
            buyBtn.addEventListener('click', () => {
                const amount = getAmount();
                if (amount <= 0) return;

                const moonpayUrl = 'https://buy.moonpay.com?apiKey=' + encodeURIComponent(coffeeWidgetSupport.moonpayKey) +
                    '&currencyCode=' + coffeeWidgetSupport.cryptoNetwork +
                    '&baseCurrencyCode=USD&baseCurrencyAmount=' + amount;
                window.open(moonpayUrl, '_blank');
                alert(coffeeWidgetSupport.i18n.moonpayNote);
            });
        }
    })();
    <?php endif; ?>

    // Stripe
    <?php if ( in_array( 'stripe', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_stripe_key ) ) : ?>
    (function() {
        const { getAmount } = setupDonationTiers('panel-stripe', 'stripe-amount', 'stripe-pay');
        const payBtn = document.getElementById('stripe-pay');
        const loader = document.getElementById('stripe-loader');
        const elementsDiv = document.getElementById('stripe-elements');

        let stripe = null;
        let elements = null;
        let cardElement = null;

        function initStripe() {
            if (typeof Stripe !== 'undefined' && !stripe) {
                stripe = Stripe(coffeeWidgetSupport.stripeKey);
                elements = stripe.elements();
                cardElement = elements.create('card');
                cardElement.mount(elementsDiv);
            }
        }

        if (payBtn) {
            payBtn.addEventListener('click', () => {
                const amount = getAmount();
                if (amount <= 0) return;

                if (!stripe) initStripe();
                if (!stripe || !cardElement) return;

                payBtn.disabled = true;
                loader.style.display = 'block';

                fetch(coffeeWidgetSupport.ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'coffee_widget_create_payment_intent',
                        amount: amount,
                        nonce: coffeeWidgetSupport.nonce
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.data);
                        payBtn.disabled = false;
                        loader.style.display = 'none';
                        return;
                    }

                    stripe.confirmCardPayment(data.data.client_secret, {
                        payment_method: { card: cardElement }
                    }).then(result => {
                        if (result.error) {
                            alert(result.error.message);
                            payBtn.disabled = false;
                            loader.style.display = 'none';
                        } else if (result.paymentIntent.status === 'succeeded') {
                            alert(coffeeWidgetSupport.i18n.paymentSuccess);
                            loader.style.display = 'none';
                        }
                    });
                })
                .catch(() => {
                    alert(coffeeWidgetSupport.i18n.paymentFailed);
                    payBtn.disabled = false;
                    loader.style.display = 'none';
                });
            });

            initStripe();
        }
    })();
    <?php endif; ?>

    // PayPal
    <?php if ( in_array( 'paypal', $coffee_widget_payment_methods ) && ! empty( $coffee_widget_paypal_client_id ) ) : ?>
    (function() {
        let currentAmount = 0;
        const panel = document.getElementById('panel-paypal');
        if (!panel) return;

        const tiers = panel.querySelectorAll('.tier');
        const amountInput = document.getElementById('paypal-amount');
        const container = document.getElementById('paypal-button-container');

        function setAmount(val) {
            currentAmount = parseFloat(val);
            if (currentAmount > 0 && !isNaN(currentAmount)) {
                renderPayPalButton();
            } else if (container) {
                container.innerHTML = '';
            }
        }

        function renderPayPalButton() {
            if (!container) return;
            container.innerHTML = '';
            if (typeof paypal === 'undefined') {
                container.innerHTML = '<p style="color:red;">PayPal SDK not loaded.</p>';
                return;
            }

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return fetch(coffeeWidgetSupport.ajaxUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'coffee_widget_create_paypal_order',
                            amount: currentAmount,
                            nonce: coffeeWidgetSupport.nonce
                        })
                    }).then(function(response) {
                        return response.json();
                    }).then(function(orderData) {
                        if (!orderData.success) throw new Error(orderData.data);
                        return orderData.data.order_id;
                    });
                },
                onApprove: function(data, actions) {
                    return fetch(coffeeWidgetSupport.ajaxUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'coffee_widget_capture_paypal_order',
                            order_id: data.orderID,
                            nonce: coffeeWidgetSupport.nonce
                        })
                    }).then(function(response) {
                        return response.json();
                    }).then(function(captureData) {
                        if (captureData.success) {
                            alert(coffeeWidgetSupport.i18n.paymentSuccess);
                        } else {
                            alert(coffeeWidgetSupport.i18n.paymentFailed + ': ' + captureData.data);
                        }
                    });
                },
                onError: function(err) {
                    alert(coffeeWidgetSupport.i18n.paymentFailed);
                }
            }).render('#paypal-button-container');
        }

        tiers.forEach(tier => {
            tier.addEventListener('click', () => {
                const amount = tier.getAttribute('data-amount');
                setAmount(amount);
                if (amountInput) amountInput.value = amount;
            });
        });

        if (amountInput) {
            amountInput.addEventListener('input', () => setAmount(amountInput.value));
        }
    })();
    <?php endif; ?>
})();
</script>
</body>
</html>
<?php
// Flush the output buffer
ob_end_flush();
