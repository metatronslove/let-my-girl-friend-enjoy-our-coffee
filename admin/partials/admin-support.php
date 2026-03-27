<?php
/**
 * Help & Support page.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$coffee_widget_support_url = plugins_url( 'public/github-support.php', dirname( __DIR__ ) );
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Help & Support', 'coffee-widget' ); ?></h1>

    <div class="help-box">
        <h3><?php esc_html_e( 'How to set up everything for free', 'coffee-widget' ); ?></h3>
        <ol>
            <li><?php echo wp_kses_post( __( '<strong>Get a free website</strong>: Use InfinityFree (https://infinityfree.net) to get free hosting and a subdomain. Install WordPress on it.', 'coffee-widget' ) ); ?></li>
            <li><?php echo wp_kses_post( __( '<strong>Install this plugin</strong>: Upload and activate the "Let My Girl Friend Enjoy Our Coffee" plugin.', 'coffee-widget' ) ); ?></li>
            <li><?php echo wp_kses_post( __( '<strong>Get a crypto wallet</strong>: Visit https://quai.network to create a free Quai wallet. Copy your wallet address.', 'coffee-widget' ) ); ?></li>
            <li><?php echo wp_kses_post( __( '<strong>Set up payment methods</strong>: Go to Payment Methods in the plugin settings. Enter your wallet address. For credit card, sign up for Stripe (free) or NOWPayments (free) and paste your API keys.', 'coffee-widget' ) ); ?></li>
            <li><?php echo wp_kses_post( __( '<strong>Customize widget</strong>: Adjust button appearance and donation tiers.', 'coffee-widget' ) ); ?></li>
            <li><?php echo wp_kses_post( __( '<strong>Promote your support page</strong>: The widget will appear on your site. You can also link to the GitHub support page:', 'coffee-widget' ) ); ?> <a href="<?php echo esc_url( $coffee_widget_support_url ); ?>" target="_blank"><?php echo esc_url( $coffee_widget_support_url ); ?></a></li>
        </ol>
    </div>

    <div class="help-box">
        <h3><?php esc_html_e( 'How to use the generated GitHub support page', 'coffee-widget' ); ?></h3>
        <p><?php esc_html_e( 'This plugin automatically creates a page at:', 'coffee-widget' ); ?> <code><?php echo esc_url( $coffee_widget_support_url ); ?></code></p>
        <p><?php esc_html_e( 'You can use this URL in your GitHub repository\'s FUNDING.yml under the "custom" field to receive donations via the widget.', 'coffee-widget' ); ?></p>
        <p><?php esc_html_e( 'Example FUNDING.yml entry:', 'coffee-widget' ); ?></p>
        <pre>custom: ['<?php echo esc_url( $coffee_widget_support_url ); ?>']</pre>
        <p><?php esc_html_e( 'When visitors click the "Sponsor" button on GitHub, they will be redirected to your site\'s support page, where they can donate via your widget.', 'coffee-widget' ); ?></p>
    </div>

    <div class="help-box">
        <h3><?php esc_html_e( 'Need help?', 'coffee-widget' ); ?></h3>
        <p><?php echo wp_kses_post( __( 'For support, feature requests, or bug reports, please visit the WordPress support forum or our GitHub repository.', 'coffee-widget' ) ); ?></p>
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
    .help-box h3 {
        margin-top: 0;
    }
    .help-box ol, .help-box ul {
        margin-left: 20px;
    }
    .help-box code {
        background: #eaeaea;
        padding: 2px 5px;
        border-radius: 3px;
    }
</style>
