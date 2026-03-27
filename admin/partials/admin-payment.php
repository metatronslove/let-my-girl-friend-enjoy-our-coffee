<?php
/**
 * Payment Methods settings page.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$coffee_widget_options = get_option( 'coffee_widget_settings', array() );

// Set defaults
$coffee_widget_donation_tiers   = isset( $coffee_widget_options['donation_tiers'] ) && is_array( $coffee_widget_options['donation_tiers'] ) ? $coffee_widget_options['donation_tiers'] : array( '5', '10', '20', '50' );
$coffee_widget_payment_methods  = isset( $coffee_widget_options['payment_methods'] ) && is_array( $coffee_widget_options['payment_methods'] ) ? $coffee_widget_options['payment_methods'] : array( 'crypto', 'nowpayments', 'stripe', 'paypal' );

// Crypto
$coffee_widget_crypto_address   = isset( $coffee_widget_options['crypto_address'] ) ? $coffee_widget_options['crypto_address'] : '';
$coffee_widget_crypto_network   = isset( $coffee_widget_options['crypto_network'] ) ? $coffee_widget_options['crypto_network'] : 'quai';

// NOWPayments
$coffee_widget_nowpayments_api_key = isset( $coffee_widget_options['nowpayments_api_key'] ) ? $coffee_widget_options['nowpayments_api_key'] : '';

// CoinGate
$coffee_widget_coingate_api_key    = isset( $coffee_widget_options['coingate_api_key'] ) ? $coffee_widget_options['coingate_api_key'] : '';

// BitPay
$coffee_widget_bitpay_api_key      = isset( $coffee_widget_options['bitpay_api_key'] ) ? $coffee_widget_options['bitpay_api_key'] : '';

// MoonPay
$coffee_widget_moonpay_api_key     = isset( $coffee_widget_options['moonpay_api_key'] ) ? $coffee_widget_options['moonpay_api_key'] : '';

// Stripe
$coffee_widget_stripe_pub_key      = isset( $coffee_widget_options['stripe_publishable_key'] ) ? $coffee_widget_options['stripe_publishable_key'] : '';
$coffee_widget_stripe_secret       = isset( $coffee_widget_options['stripe_secret_key'] ) ? $coffee_widget_options['stripe_secret_key'] : '';

// PayPal
$coffee_widget_paypal_client_id    = isset( $coffee_widget_options['paypal_client_id'] ) ? $coffee_widget_options['paypal_client_id'] : '';
$coffee_widget_paypal_secret       = isset( $coffee_widget_options['paypal_secret'] ) ? $coffee_widget_options['paypal_secret'] : '';
$coffee_widget_paypal_email        = isset( $coffee_widget_options['paypal_email'] ) ? $coffee_widget_options['paypal_email'] : '';

$coffee_widget_membership_enabled  = isset( $coffee_widget_options['membership_enabled'] ) ? $coffee_widget_options['membership_enabled'] : 0;
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Payment Methods', 'coffee-widget' ); ?></h1>

    <!-- Comparison Table -->
    <div class="help-box service-comparison">
        <h3><?php esc_html_e( 'Which payment service should you choose?', 'coffee-widget' ); ?></h3>
        <p><?php esc_html_e( 'We offer multiple services to accept credit card payments to your crypto wallet. Here\'s how they compare:', 'coffee-widget' ); ?></p>
        
        <table class="widefat fixed striped" style="margin: 15px 0;">
            <thead>
                 <tr>
                    <th width="15%"><?php esc_html_e( 'Service', 'coffee-widget' ); ?></th>
                    <th width="10%"><?php esc_html_e( 'Fee', 'coffee-widget' ); ?></th>
                    <th width="25%"><?php esc_html_e( 'Where does crypto go?', 'coffee-widget' ); ?></th>
                    <th width="25%"><?php esc_html_e( 'Best for', 'coffee-widget' ); ?></th>
                    <th width="15%"><?php esc_html_e( 'US Only?', 'coffee-widget' ); ?></th>
                 </tr>
            </thead>
            <tbody>
                <tr style="background: #e8f5e9;">
                    <td><strong><?php esc_html_e( 'Direct Crypto', 'coffee-widget' ); ?></strong></td>
                    <td><?php esc_html_e( 'Free', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( '✅ Your wallet directly', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Crypto-savvy users', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Anywhere', 'coffee-widget' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'NOWPayments', 'coffee-widget' ); ?></strong></td>
                    <td><strong>0.5% - 1%</strong></td>
                    <td><?php esc_html_e( '✅ Your wallet directly', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Beginners, lowest fees', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Global (incl. Turkey)', 'coffee-widget' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'CoinGate', 'coffee-widget' ); ?></strong></td>
                    <td><strong>1%</strong></td>
                    <td><?php esc_html_e( '✅ Your wallet directly', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Good support, official plugin', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Global', 'coffee-widget' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'BitPay', 'coffee-widget' ); ?></strong></td>
                    <td><strong>1%</strong></td>
                    <td><?php esc_html_e( '✅ Your wallet directly', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'US businesses, enterprise', 'coffee-widget' ); ?></td>
                    <td><span class="badge badge-warning"><?php esc_html_e( 'US-friendly', 'coffee-widget' ); ?></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'MoonPay', 'coffee-widget' ); ?></strong></td>
                    <td><strong>~3.5%</strong></td>
                    <td><?php esc_html_e( '⚠️ User\'s wallet first', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Users who want to buy crypto', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Global', 'coffee-widget' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Stripe', 'coffee-widget' ); ?></strong></td>
                    <td><strong>2.9% + $0.30</strong></td>
                    <td><?php esc_html_e( '⚠️ Your Stripe balance (not direct crypto)', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'US businesses only', 'coffee-widget' ); ?></td>
                    <td><strong class="badge badge-danger"><?php esc_html_e( 'US ONLY', 'coffee-widget' ); ?></strong></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'PayPal', 'coffee-widget' ); ?></strong></td>
                    <td><?php esc_html_e( 'Standard fees', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( '⚠️ PayPal balance', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'PayPal users', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Global', 'coffee-widget' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Alchemy Pay', 'coffee-widget' ); ?></strong></td>
                    <td><?php esc_html_e( 'Variable', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( '✅ Your wallet directly', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Asia markets', 'coffee-widget' ); ?></td>
                    <td><?php esc_html_e( 'Global', 'coffee-widget' ); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="notice notice-info inline" style="margin: 15px 0;">
            <p><strong>💡 <?php esc_html_e( 'Tip:', 'coffee-widget' ); ?></strong> 
            <?php esc_html_e( 'If you are outside the US, we recommend NOWPayments (lowest fees, direct to your wallet). If you are in the US, Stripe is also an option. You can enable multiple services and let your visitors choose!', 'coffee-widget' ); ?></p>
        </div>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( 'coffee_widget_options' ); ?>
        
        <!-- ==================== ENABLED PAYMENT METHODS ==================== -->
        <h2><?php esc_html_e( 'Enabled Payment Methods', 'coffee-widget' ); ?></h2>
        <p><?php esc_html_e( 'Select which payment options will appear in your widget. Your visitors can choose their preferred method.', 'coffee-widget' ); ?></p>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'Show these methods', 'coffee-widget' ); ?></th>
                <td>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="crypto" <?php checked( in_array( 'crypto', $coffee_widget_payment_methods ) ); ?> /> 🔗 <?php esc_html_e( 'Direct Crypto (Wallet address)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="nowpayments" <?php checked( in_array( 'nowpayments', $coffee_widget_payment_methods ) ); ?> /> ⚡ <?php esc_html_e( 'NOWPayments (Credit Card → Crypto)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="coingate" <?php checked( in_array( 'coingate', $coffee_widget_payment_methods ) ); ?> /> 🪙 <?php esc_html_e( 'CoinGate (Credit Card → Crypto)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="bitpay" <?php checked( in_array( 'bitpay', $coffee_widget_payment_methods ) ); ?> /> 🏦 <?php esc_html_e( 'BitPay (Credit Card → Crypto)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="moonpay" <?php checked( in_array( 'moonpay', $coffee_widget_payment_methods ) ); ?> /> 🌙 <?php esc_html_e( 'MoonPay (User buys crypto first)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="stripe" <?php checked( in_array( 'stripe', $coffee_widget_payment_methods ) ); ?> /> 💳 <?php esc_html_e( 'Stripe (Credit Card)', 'coffee-widget' ); ?></label><br>
                    <label><input type="checkbox" name="coffee_widget_settings[payment_methods][]" value="paypal" <?php checked( in_array( 'paypal', $coffee_widget_payment_methods ) ); ?> /> 🟡 <?php esc_html_e( 'PayPal', 'coffee-widget' ); ?></label><br>
                </td>
            </tr>
        </table>

        <!-- ==================== DIRECT CRYPTO ==================== -->
        <h2>🔗 <?php esc_html_e( 'Direct Crypto', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><?php esc_html_e( 'Display your wallet address so users can send crypto directly. Zero fees, but requires users to already have crypto.', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-crypto">
                <th scope="row"><label for="crypto_address"><?php esc_html_e( 'Wallet Address', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="crypto_address" name="coffee_widget_settings[crypto_address]" value="<?php echo esc_attr( $coffee_widget_crypto_address ); ?>" class="regular-text" /></td>
            </tr>
            <tr class="payment-settings-crypto">
                <th scope="row"><label for="crypto_network"><?php esc_html_e( 'Network', 'coffee-widget' ); ?></label></th>
                <td>
                    <select id="crypto_network" name="coffee_widget_settings[crypto_network]">
                        <option value="quai" <?php selected( $coffee_widget_crypto_network, 'quai' ); ?>><?php esc_html_e( 'Quai Network (Recommended - free)', 'coffee-widget' ); ?></option>
                        <option value="ethereum" <?php selected( $coffee_widget_crypto_network, 'ethereum' ); ?>><?php esc_html_e( 'Ethereum', 'coffee-widget' ); ?></option>
                        <option value="bitcoin" <?php selected( $coffee_widget_crypto_network, 'bitcoin' ); ?>><?php esc_html_e( 'Bitcoin', 'coffee-widget' ); ?></option>
                        <option value="solana" <?php selected( $coffee_widget_crypto_network, 'solana' ); ?>><?php esc_html_e( 'Solana', 'coffee-widget' ); ?></option>
                        <option value="polygon" <?php selected( $coffee_widget_crypto_network, 'polygon' ); ?>><?php esc_html_e( 'Polygon', 'coffee-widget' ); ?></option>
                        <option value="dogecoin" <?php selected( $coffee_widget_crypto_network, 'dogecoin' ); ?>><?php esc_html_e( 'Dogecoin', 'coffee-widget' ); ?></option>
                        <option value="litecoin" <?php selected( $coffee_widget_crypto_network, 'litecoin' ); ?>><?php esc_html_e( 'Litecoin', 'coffee-widget' ); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Select the network for your wallet address.', 'coffee-widget' ); ?></p>
                </td>
            </tr>
        </table>

        <!-- ==================== NOWPAYMENTS ==================== -->
        <h2>⚡ <?php esc_html_e( 'NOWPayments', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><strong><?php esc_html_e( 'Best for beginners & lowest fees', 'coffee-widget' ); ?></strong></p>
            <p><?php esc_html_e( 'NOWPayments allows you to accept credit card payments directly to your crypto wallet. Funds go straight to your wallet – no middleman.', 'coffee-widget' ); ?></p>
            <p><strong><?php esc_html_e( 'Fee:', 'coffee-widget' ); ?></strong> 0.5% - 1% <?php esc_html_e( 'per transaction', 'coffee-widget' ); ?> | <strong><?php esc_html_e( 'Payout:', 'coffee-widget' ); ?></strong> <?php esc_html_e( 'Direct to your wallet', 'coffee-widget' ); ?></p>
            <p><strong><?php esc_html_e( 'Setup:', 'coffee-widget' ); ?></strong> <a href="https://nowpayments.io" target="_blank">nowpayments.io</a> → <?php esc_html_e( 'Create free account → API Keys → Copy API Key', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-nowpayments">
                <th scope="row"><label for="nowpayments_api_key"><?php esc_html_e( 'NOWPayments API Key', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="nowpayments_api_key" name="coffee_widget_settings[nowpayments_api_key]" value="<?php echo esc_attr( $coffee_widget_nowpayments_api_key ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== COINGATE ==================== -->
        <h2>🪙 <?php esc_html_e( 'CoinGate', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><strong><?php esc_html_e( 'Great support, slightly higher fee', 'coffee-widget' ); ?></strong></p>
            <p><?php esc_html_e( 'CoinGate is a well-established crypto payment processor with an official WordPress plugin.', 'coffee-widget' ); ?></p>
            <p><strong><?php esc_html_e( 'Fee:', 'coffee-widget' ); ?></strong> 1% <?php esc_html_e( 'per transaction', 'coffee-widget' ); ?> | <strong><?php esc_html_e( 'Payout:', 'coffee-widget' ); ?></strong> <?php esc_html_e( 'Direct to your wallet', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-coingate">
                <th scope="row"><label for="coingate_api_key"><?php esc_html_e( 'CoinGate API Key', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="coingate_api_key" name="coffee_widget_settings[coingate_api_key]" value="<?php echo esc_attr( $coffee_widget_coingate_api_key ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== BITPAY ==================== -->
        <h2>🏦 <?php esc_html_e( 'BitPay', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><strong><?php esc_html_e( 'Enterprise-grade, US-friendly', 'coffee-widget' ); ?></strong></p>
            <p><?php esc_html_e( 'BitPay is trusted by major companies. Works well for US-based businesses.', 'coffee-widget' ); ?></p>
            <p><strong><?php esc_html_e( 'Fee:', 'coffee-widget' ); ?></strong> 1% <?php esc_html_e( 'per transaction', 'coffee-widget' ); ?> | <strong><?php esc_html_e( 'Payout:', 'coffee-widget' ); ?></strong> <?php esc_html_e( 'Direct to your wallet', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-bitpay">
                <th scope="row"><label for="bitpay_api_key"><?php esc_html_e( 'BitPay API Key', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="bitpay_api_key" name="coffee_widget_settings[bitpay_api_key]" value="<?php echo esc_attr( $coffee_widget_bitpay_api_key ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== MOONPAY ==================== -->
        <h2>🌙 <?php esc_html_e( 'MoonPay', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><strong><?php esc_html_e( 'Users buy crypto first, then send to you', 'coffee-widget' ); ?></strong></p>
            <p><?php esc_html_e( 'MoonPay lets users buy crypto with credit card directly to THEIR wallet, then they send it to you. Higher fees but very simple for users.', 'coffee-widget' ); ?></p>
            <p><strong><?php esc_html_e( 'Fee:', 'coffee-widget' ); ?></strong> ~3.5% <?php esc_html_e( 'per transaction', 'coffee-widget' ); ?> | <strong><?php esc_html_e( 'Payout:', 'coffee-widget' ); ?></strong> <?php esc_html_e( 'User\'s wallet first', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-moonpay">
                <th scope="row"><label for="moonpay_api_key"><?php esc_html_e( 'MoonPay API Key', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="moonpay_api_key" name="coffee_widget_settings[moonpay_api_key]" value="<?php echo esc_attr( $coffee_widget_moonpay_api_key ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== STRIPE ==================== -->
        <h2>💳 <?php esc_html_e( 'Stripe', 'coffee-widget' ); ?></h2>
        <div class="help-box notice notice-warning inline">
            <p><strong>⚠️ <?php esc_html_e( 'Important:', 'coffee-widget' ); ?></strong> 
            <?php esc_html_e( 'Stripe Crypto payments (USDC) are ONLY available for US-based businesses. If you are outside the US, Stripe will only accept credit card payments to your Stripe balance (not directly to crypto).', 'coffee-widget' ); ?></p>
            <p><?php esc_html_e( 'If you are in the US, you can use Stripe to receive crypto directly. If outside, consider NOWPayments or CoinGate for direct crypto payouts.', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-stripe">
                <th scope="row"><label for="stripe_publishable_key"><?php esc_html_e( 'Stripe Publishable Key', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="stripe_publishable_key" name="coffee_widget_settings[stripe_publishable_key]" value="<?php echo esc_attr( $coffee_widget_stripe_pub_key ); ?>" class="regular-text" /></td>
            </tr>
            <tr class="payment-settings-stripe">
                <th scope="row"><label for="stripe_secret_key"><?php esc_html_e( 'Stripe Secret Key', 'coffee-widget' ); ?></label></th>
                <td><input type="password" id="stripe_secret_key" name="coffee_widget_settings[stripe_secret_key]" value="<?php echo esc_attr( $coffee_widget_stripe_secret ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== PAYPAL ==================== -->
        <h2>🟡 <?php esc_html_e( 'PayPal', 'coffee-widget' ); ?></h2>
        <div class="help-box">
            <p><?php esc_html_e( 'Classic PayPal payments. Users pay with PayPal balance or credit card. Funds go to your PayPal account.', 'coffee-widget' ); ?></p>
        </div>
        <table class="form-table">
            <tr class="payment-settings-paypal">
                <th scope="row"><label for="paypal_client_id"><?php esc_html_e( 'PayPal Client ID', 'coffee-widget' ); ?></label></th>
                <td><input type="text" id="paypal_client_id" name="coffee_widget_settings[paypal_client_id]" value="<?php echo esc_attr( $coffee_widget_paypal_client_id ); ?>" class="regular-text" /></td>
            </tr>
            <tr class="payment-settings-paypal">
                <th scope="row"><label for="paypal_secret"><?php esc_html_e( 'PayPal Secret', 'coffee-widget' ); ?></label></th>
                <td><input type="password" id="paypal_secret" name="coffee_widget_settings[paypal_secret]" value="<?php echo esc_attr( $coffee_widget_paypal_secret ); ?>" class="regular-text" /></td>
            </tr>
            <tr class="payment-settings-paypal">
                <th scope="row"><label for="paypal_email"><?php esc_html_e( 'PayPal Email (Fallback)', 'coffee-widget' ); ?></label></th>
                <td><input type="email" id="paypal_email" name="coffee_widget_settings[paypal_email]" value="<?php echo esc_attr( $coffee_widget_paypal_email ); ?>" class="regular-text" /></td>
            </tr>
        </table>

        <!-- ==================== DONATION TIERS ==================== -->
        <h2>💰 <?php esc_html_e( 'Donation Tiers', 'coffee-widget' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'Suggested Amounts', 'coffee-widget' ); ?></th>
                <td>
                    <div id="tiers-list">
                        <?php foreach ( $coffee_widget_donation_tiers as $coffee_widget_tier ) : ?>
                            <div class="tier-item">
                                <input type="number" name="coffee_widget_settings[donation_tiers][]" value="<?php echo esc_attr( $coffee_widget_tier ); ?>" step="0.01" min="1" style="width: 100px;" />
                                <button type="button" class="remove-tier button"><?php esc_html_e( 'Remove', 'coffee-widget' ); ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-tier" class="button"><?php esc_html_e( 'Add Tier', 'coffee-widget' ); ?></button>
                    <p class="description"><?php esc_html_e( 'Predefined donation amounts (USD). Leave empty for custom amount only.', 'coffee-widget' ); ?></p>
                </td>
            </tr>
        </table>

        <!-- ==================== MEMBERSHIP ==================== -->
        <h2>👥 <?php esc_html_e( 'Membership', 'coffee-widget' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="membership_enabled"><?php esc_html_e( 'Enable Membership', 'coffee-widget' ); ?></label></th>
                <td>
                    <input type="checkbox" id="membership_enabled" name="coffee_widget_settings[membership_enabled]" value="1" <?php checked( $coffee_widget_membership_enabled, 1 ); ?> />
                    <p class="description"><?php esc_html_e( 'Allow recurring subscriptions (requires Stripe Subscriptions).', 'coffee-widget' ); ?></p>
                </td>
            </tr>
        </table>

        <!-- Preserve button settings when saving from payment page -->
        <input type="hidden" name="coffee_widget_settings[color]" value="<?php echo esc_attr( $coffee_widget_options['color'] ?? '#FFDD00' ); ?>">
        <input type="hidden" name="coffee_widget_settings[position]" value="<?php echo esc_attr( $coffee_widget_options['position'] ?? 'right' ); ?>">
        <input type="hidden" name="coffee_widget_settings[margin_x]" value="<?php echo esc_attr( $coffee_widget_options['margin_x'] ?? 18 ); ?>">
        <input type="hidden" name="coffee_widget_settings[margin_y]" value="<?php echo esc_attr( $coffee_widget_options['margin_y'] ?? 18 ); ?>">
        <input type="hidden" name="coffee_widget_settings[message]" value="<?php echo esc_attr( $coffee_widget_options['message'] ?? 'Support my work' ); ?>">
        <input type="hidden" name="coffee_widget_settings[description]" value="<?php echo esc_attr( $coffee_widget_options['description'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[enabled]" value="<?php echo esc_attr( $coffee_widget_options['enabled'] ?? 1 ); ?>">
        <input type="hidden" name="coffee_widget_settings[button_type]" value="<?php echo esc_attr( $coffee_widget_options['button_type'] ?? 'emoji' ); ?>">
        <input type="hidden" name="coffee_widget_settings[button_emoji]" value="<?php echo esc_attr( $coffee_widget_options['button_emoji'] ?? '☕' ); ?>">

        <?php submit_button(); ?>
    </form>
</div>

<style>
    .help-box {
        background: #f9f9f9;
        border-left: 4px solid #FFDD00;
        padding: 15px 20px;
        margin: 20px 0;
        border-radius: 4px;
    }
    .help-box h3 {
        margin-top: 0;
        margin-bottom: 10px;
    }
    .help-box p {
        margin: 5px 0;
    }
    .service-comparison table {
        margin: 15px 0;
        border-collapse: collapse;
    }
    .service-comparison th,
    .service-comparison td {
        padding: 10px 12px;
        vertical-align: top;
    }
    .tier-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .badge {
        display: inline-block;
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
    }
    .badge-warning {
        background: #ffc107;
        color: #212529;
    }
    .badge-danger {
        background: #dc3545;
        color: white;
    }
    .notice-warning {
        background: #fff3cd;
        border-left-color: #ffc107;
    }
    .form-table th {
        width: 220px;
    }
    @media (max-width: 782px) {
        .form-table th {
            width: auto;
        }
        .service-comparison table {
            font-size: 12px;
        }
        .service-comparison th,
        .service-comparison td {
            padding: 6px 8px;
        }
    }
</style>

<script>
jQuery(document).ready(function($) {
    $('#add-tier').on('click', function(e) {
        e.preventDefault();
        var newTier = $(
            '<div class="tier-item">' +
            '<input type="number" name="coffee_widget_settings[donation_tiers][]" value="" step="0.01" min="1" style="width: 100px;" />' +
            '<button type="button" class="remove-tier button"><?php echo esc_js( __( 'Remove', 'coffee-widget' ) ); ?></button>' +
            '</div>'
        );
        $('#tiers-list').append(newTier);
        $('.remove-tier').off('click').on('click', function() {
            $(this).parent().remove();
        });
    });
    $('.remove-tier').on('click', function() {
        $(this).parent().remove();
    });
});
</script>
