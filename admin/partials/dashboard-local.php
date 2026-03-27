<?php
/**
 * Local dashboard content.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Dashboard', 'coffee-widget' ); ?></h1>
    
    <div class="help-box">
        <h2><?php esc_html_e( 'Welcome to the Coffee Widget!', 'coffee-widget' ); ?></h2>
        <p><?php esc_html_e( 'This widget helps you accept donations and payments from your visitors. You can receive funds via cryptocurrency (Quai), Stripe, PayPal, and more – all without monthly fees.', 'coffee-widget' ); ?></p>
        <p><?php esc_html_e( 'To get started, configure the widget in the <strong>Settings</strong> and <strong>Payment Methods</strong> tabs.', 'coffee-widget' ); ?></p>
        <p><?php esc_html_e( 'If you like this project, consider supporting the developer:', 'coffee-widget' ); ?> <a href="https://www.buymeacoffee.com/metatronslove" target="_blank"><?php esc_html_e( 'Buy Me a Coffee', 'coffee-widget' ); ?></a></p>
    </div>

    <div class="help-box">
        <h3><?php esc_html_e( 'Quick Stats', 'coffee-widget' ); ?></h3>
        <?php
        $coffee_widget_settings = get_option( 'coffee_widget_settings', array() );
        $coffee_widget_enabled = isset( $coffee_widget_settings['enabled'] ) && $coffee_widget_settings['enabled'];
        $coffee_widget_payment_methods = isset( $coffee_widget_settings['payment_methods'] ) ? $coffee_widget_settings['payment_methods'] : array();
        ?>
        <ul>
            <li><strong><?php esc_html_e( 'Widget Status:', 'coffee-widget' ); ?></strong> <?php echo $coffee_widget_enabled ? '<span style="color:green;">✓ ' . esc_html__( 'Active', 'coffee-widget' ) . '</span>' : '<span style="color:red;">✗ ' . esc_html__( 'Inactive', 'coffee-widget' ) . '</span>'; ?></li>
            <li><strong><?php esc_html_e( 'Enabled Payment Methods:', 'coffee-widget' ); ?></strong> <?php echo esc_html( ! empty( $coffee_widget_payment_methods ) ? implode( ', ', array_map( 'ucfirst', $coffee_widget_payment_methods ) ) : __( 'None', 'coffee-widget' ) ); ?></li>
            <li><strong><?php esc_html_e( 'Crypto Wallet:', 'coffee-widget' ); ?></strong> <?php echo ! empty( $coffee_widget_settings['crypto_address'] ) ? esc_html( substr( $coffee_widget_settings['crypto_address'], 0, 10 ) ) . '...' : esc_html__( 'Not set', 'coffee-widget' ); ?></li>
        </ul>
    </div>
</div>

<style>
    .help-box {
        background: #f9f9f9;
        border-left: 4px solid #FFDD00;
        padding: 15px 20px;
        margin: 20px 0;
        border-radius: 4px;
    }
    .help-box h2, .help-box h3 {
        margin-top: 0;
    }
    .help-box ul {
        margin-left: 20px;
    }
</style>
