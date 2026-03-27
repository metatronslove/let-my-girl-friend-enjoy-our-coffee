<?php
/**
 * Widget Settings page.
 *
 * @package CoffeeWidget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$coffee_widget_options = get_option( 'coffee_widget_settings', array() );
$coffee_widget_style_options = get_option( 'coffee_widget_style', array( 'custom_css' => '' ) );
$coffee_widget_code_options = get_option( 'coffee_widget_code', array( 'custom_js' => '' ) );

// Set defaults if not set
$coffee_widget_color      = isset( $coffee_widget_options['color'] ) ? $coffee_widget_options['color'] : '#FFDD00';
$coffee_widget_position   = isset( $coffee_widget_options['position'] ) ? $coffee_widget_options['position'] : 'right';
$coffee_widget_margin_x   = isset( $coffee_widget_options['margin_x'] ) ? $coffee_widget_options['margin_x'] : 18;
$coffee_widget_margin_y   = isset( $coffee_widget_options['margin_y'] ) ? $coffee_widget_options['margin_y'] : 18;
$coffee_widget_message    = isset( $coffee_widget_options['message'] ) ? $coffee_widget_options['message'] : 'Like my projects? Buy me a coffee!';
$coffee_widget_description= isset( $coffee_widget_options['description'] ) ? $coffee_widget_options['description'] : 'Support my work with a coffee';
$coffee_widget_enabled    = isset( $coffee_widget_options['enabled'] ) ? $coffee_widget_options['enabled'] : 1;
$coffee_widget_button_type = isset( $coffee_widget_options['button_type'] ) ? $coffee_widget_options['button_type'] : 'emoji';
$coffee_widget_button_emoji = isset( $coffee_widget_options['button_emoji'] ) ? $coffee_widget_options['button_emoji'] : '☕';
$coffee_widget_button_svg = isset( $coffee_widget_options['button_svg'] ) ? $coffee_widget_options['button_svg'] : '';
$coffee_widget_button_png_url = isset( $coffee_widget_options['button_png_url'] ) ? $coffee_widget_options['button_png_url'] : '';

// Proxy scripts
$coffee_widget_proxy_scripts = isset( $coffee_widget_options['proxy_scripts'] ) ? maybe_unserialize( $coffee_widget_options['proxy_scripts'] ) : array();
$coffee_widget_default_scripts = array(
    'stripe' => array(
        'url' => 'https://js.stripe.com/v3/',
        'type' => 'javascript',
        'cache_ttl' => 86400,
        'description' => 'Stripe.js for credit card payments'
    ),
    'paypal' => array(
        'url' => 'https://www.paypal.com/sdk/js',
        'type' => 'javascript',
        'cache_ttl' => 86400,
        'description' => 'PayPal SDK for PayPal payments'
    )
);
$coffee_widget_proxy_scripts = array_merge( $coffee_widget_default_scripts, $coffee_widget_proxy_scripts );
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Widget Settings', 'coffee-widget' ); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'coffee_widget_options' ); ?>
        
        <div class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', 'coffee-widget' ); ?></a>
            <a href="#button" class="nav-tab"><?php esc_html_e( 'Button', 'coffee-widget' ); ?></a>
            <a href="#advanced" class="nav-tab"><?php esc_html_e( 'Advanced', 'coffee-widget' ); ?></a>
            <a href="#proxy" class="nav-tab"><?php esc_html_e( 'Proxy Scripts', 'coffee-widget' ); ?></a>
        </div>

        <!-- General Tab -->
        <div id="tab-general" class="settings-tab-content">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Widget Status', 'coffee-widget' ); ?></th>
                    <td><label><input type="checkbox" name="coffee_widget_settings[enabled]" value="1" <?php checked( $coffee_widget_enabled, 1 ); ?> /> <?php esc_html_e( 'Widget active', 'coffee-widget' ); ?></label></td>
                </tr>
                <tr>
                    <th scope="row"><label for="coffee_widget_color"><?php esc_html_e( 'Button Color', 'coffee-widget' ); ?></label></th>
                    <td><input type="text" id="coffee_widget_color" name="coffee_widget_settings[color]" value="<?php echo esc_attr( $coffee_widget_color ); ?>" class="regular-text color-picker" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Button Position', 'coffee-widget' ); ?></th>
                    <td>
                        <label><input type="radio" name="coffee_widget_settings[position]" value="left" <?php checked( $coffee_widget_position, 'left' ); ?> /> <?php esc_html_e( 'Left', 'coffee-widget' ); ?></label><br>
                        <label><input type="radio" name="coffee_widget_settings[position]" value="right" <?php checked( $coffee_widget_position, 'right' ); ?> /> <?php esc_html_e( 'Right', 'coffee-widget' ); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="coffee_widget_margin_x"><?php esc_html_e( 'Horizontal Margin (px)', 'coffee-widget' ); ?></label></th>
                    <td><input type="number" id="coffee_widget_margin_x" name="coffee_widget_settings[margin_x]" value="<?php echo esc_attr( $coffee_widget_margin_x ); ?>" class="small-text" min="0" step="1" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="coffee_widget_margin_y"><?php esc_html_e( 'Vertical Margin (px)', 'coffee-widget' ); ?></label></th>
                    <td><input type="number" id="coffee_widget_margin_y" name="coffee_widget_settings[margin_y]" value="<?php echo esc_attr( $coffee_widget_margin_y ); ?>" class="small-text" min="0" step="1" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="coffee_widget_message"><?php esc_html_e( 'Button Message', 'coffee-widget' ); ?></label></th>
                    <td><input type="text" id="coffee_widget_message" name="coffee_widget_settings[message]" value="<?php echo esc_attr( $coffee_widget_message ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="coffee_widget_description"><?php esc_html_e( 'Description', 'coffee-widget' ); ?></label></th>
                    <td><input type="text" id="coffee_widget_description" name="coffee_widget_settings[description]" value="<?php echo esc_attr( $coffee_widget_description ); ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Button Tab -->
        <div id="tab-button" class="settings-tab-content" style="display:none;">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Button Type', 'coffee-widget' ); ?></th>
                    <td>
                        <label><input type="radio" name="coffee_widget_settings[button_type]" value="emoji" <?php checked( $coffee_widget_button_type, 'emoji' ); ?> /> <?php esc_html_e( 'Emoji', 'coffee-widget' ); ?></label><br>
                        <label><input type="radio" name="coffee_widget_settings[button_type]" value="svg" <?php checked( $coffee_widget_button_type, 'svg' ); ?> /> <?php esc_html_e( 'SVG Code', 'coffee-widget' ); ?></label><br>
                        <label><input type="radio" name="coffee_widget_settings[button_type]" value="png" <?php checked( $coffee_widget_button_type, 'png' ); ?> /> <?php esc_html_e( 'PNG Image URL', 'coffee-widget' ); ?></label>
                    </td>
                </tr>
                <tr class="button-option button-option-emoji">
                    <th scope="row"><label for="button_emoji"><?php esc_html_e( 'Button Emoji', 'coffee-widget' ); ?></label></th>
                    <td><input type="text" id="button_emoji" name="coffee_widget_settings[button_emoji]" value="<?php echo esc_attr( $coffee_widget_button_emoji ); ?>" class="regular-text" /></td>
                </tr>
                <tr class="button-option button-option-svg">
                    <th scope="row"><label for="button_svg"><?php esc_html_e( 'SVG Code', 'coffee-widget' ); ?></label></th>
                    <td><textarea id="button_svg" name="coffee_widget_settings[button_svg]" rows="5" class="large-text code"><?php echo esc_textarea( $coffee_widget_button_svg ); ?></textarea></td>
                </tr>
                <tr class="button-option button-option-png">
                    <th scope="row"><label for="button_png_url"><?php esc_html_e( 'PNG Image URL', 'coffee-widget' ); ?></label></th>
                    <td><input type="url" id="button_png_url" name="coffee_widget_settings[button_png_url]" value="<?php echo esc_url( $coffee_widget_button_png_url ); ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Advanced Tab -->
        <div id="tab-advanced" class="settings-tab-content" style="display:none;">
            <div class="help-box">
                <h3><?php esc_html_e( 'Custom CSS', 'coffee-widget' ); ?></h3>
                <p><?php esc_html_e( 'Add custom CSS to style the widget.', 'coffee-widget' ); ?></p>
                <textarea name="coffee_widget_style[custom_css]" rows="10" class="large-text code"><?php echo esc_textarea( $coffee_widget_style_options['custom_css'] ); ?></textarea>
            </div>
            <div class="help-box">
                <h3><?php esc_html_e( 'Custom JavaScript', 'coffee-widget' ); ?></h3>
                <p><?php esc_html_e( 'Add custom JavaScript to modify widget behavior.', 'coffee-widget' ); ?></p>
                <textarea name="coffee_widget_code[custom_js]" rows="10" class="large-text code"><?php echo esc_textarea( $coffee_widget_code_options['custom_js'] ); ?></textarea>
            </div>
        </div>

        <!-- Proxy Scripts Tab -->
        <div id="tab-proxy" class="settings-tab-content" style="display:none;">
            <div class="help-box">
                <h3><?php esc_html_e( 'External Script Proxy', 'coffee-widget' ); ?></h3>
                <p><?php esc_html_e( 'Some browsers block third-party scripts for privacy. This proxy loads scripts through your own server to bypass these blocks.', 'coffee-widget' ); ?></p>
                <p><?php esc_html_e( 'You can update the script URLs below if the original URLs change or if you want to use a different CDN.', 'coffee-widget' ); ?></p>
                
                <table class="widefat fixed striped" style="margin: 15px 0;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Script', 'coffee-widget' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'coffee-widget' ); ?></th>
                            <th><?php esc_html_e( 'URL', 'coffee-widget' ); ?></th>
                            <th><?php esc_html_e( 'Cache TTL', 'coffee-widget' ); ?></th>
                            <th><?php esc_html_e( 'Action', 'coffee-widget' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $coffee_widget_proxy_scripts as $coffee_widget_key => $coffee_widget_script ) : ?>
                        <tr id="script-row-<?php echo esc_attr( $coffee_widget_key ); ?>">
                            <td><strong><?php echo esc_html( $coffee_widget_key ); ?></strong></td>
                            <td><?php echo esc_html( $coffee_widget_script['description'] ?? '' ); ?></td>
                            <td>
                                <input type="text" 
                                       name="coffee_widget_settings[proxy_scripts][<?php echo esc_attr( $coffee_widget_key ); ?>][url]" 
                                       value="<?php echo esc_attr( $coffee_widget_script['url'] ); ?>" 
                                       class="regular-text" />
                            </td>
                            <td>
                                <input type="number" 
                                       name="coffee_widget_settings[proxy_scripts][<?php echo esc_attr( $coffee_widget_key ); ?>][cache_ttl]" 
                                       value="<?php echo esc_attr( $coffee_widget_script['cache_ttl'] ?? 86400 ); ?>" 
                                       class="small-text" />
                            </td>
                            <td>
                                <button type="button" class="button test-script" data-script="<?php echo esc_attr( $coffee_widget_key ); ?>" data-url="<?php echo esc_attr( $coffee_widget_script['url'] ); ?>">
                                    <?php esc_html_e( 'Test', 'coffee-widget' ); ?>
                                </button>
                                <button type="button" class="button refresh-cache" data-script="<?php echo esc_attr( $coffee_widget_key ); ?>">
                                    <?php esc_html_e( 'Refresh Cache', 'coffee-widget' ); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="notice notice-info inline">
                    <p><?php echo wp_kses_post( __( '<strong>Usage:</strong> <code>coffee-partner-scripts-proxy.php?script=stripe&min=1</code>', 'coffee-widget' ) ); ?></p>
                    <p><?php esc_html_e( 'Click "Test" to check if the script URL is accessible. The proxy will cache scripts locally for better performance.', 'coffee-widget' ); ?></p>
                </div>
            </div>
        </div>

        <!-- Preserve payment methods when saving from settings page -->
        <input type="hidden" name="coffee_widget_settings[payment_methods][]" value="">
        <?php 
        $coffee_widget_current_methods = isset( $coffee_widget_options['payment_methods'] ) && is_array( $coffee_widget_options['payment_methods'] ) ? $coffee_widget_options['payment_methods'] : array( 'crypto' );
        foreach ( $coffee_widget_current_methods as $coffee_widget_method ) : ?>
            <input type="hidden" name="coffee_widget_settings[payment_methods][]" value="<?php echo esc_attr( $coffee_widget_method ); ?>">
        <?php endforeach; ?>

        <!-- Preserve other critical data -->
        <input type="hidden" name="coffee_widget_settings[crypto_address]" value="<?php echo esc_attr( $coffee_widget_options['crypto_address'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[stripe_publishable_key]" value="<?php echo esc_attr( $coffee_widget_options['stripe_publishable_key'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[paypal_client_id]" value="<?php echo esc_attr( $coffee_widget_options['paypal_client_id'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[nowpayments_api_key]" value="<?php echo esc_attr( $coffee_widget_options['nowpayments_api_key'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[coingate_api_key]" value="<?php echo esc_attr( $coffee_widget_options['coingate_api_key'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[bitpay_api_key]" value="<?php echo esc_attr( $coffee_widget_options['bitpay_api_key'] ?? '' ); ?>">
        <input type="hidden" name="coffee_widget_settings[moonpay_api_key]" value="<?php echo esc_attr( $coffee_widget_options['moonpay_api_key'] ?? '' ); ?>">

        <?php submit_button(); ?>
    </form>
</div>

<style>
    .nav-tab-wrapper { margin-bottom: 20px; }
    .settings-tab-content { background: #fff; border: 1px solid #ccd0d4; border-top: none; padding: 20px; }
    .help-box { background: #f9f9f9; border-left: 4px solid #FFDD00; padding: 15px; margin: 20px 0; }
    .help-box h3 { margin-top: 0; }
    .button-option { display: table-row; }
    .script-status { white-space: nowrap; }
    .status-indicator { display: inline-block; width: 20px; text-align: center; }
</style>

<script>
jQuery(document).ready(function($) {
    if ($.fn.wpColorPicker) { $('.color-picker').wpColorPicker(); }

    $('.nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href').replace('#', '');
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.settings-tab-content').hide();
        $('#tab-' + target).show();
    });

    function toggleButtonOptions() {
        var selected = $('input[name="coffee_widget_settings[button_type]"]:checked').val();
        $('.button-option').hide();
        $('.button-option-' + selected).show();
    }
    $('input[name="coffee_widget_settings[button_type]"]').on('change', toggleButtonOptions);
    toggleButtonOptions();

    function testScript(scriptName, scriptUrl, $statusCell) {
        $statusCell.html('<span class="status-indicator">🟡</span> Testing...');
        
        $.ajax({
            url: scriptUrl,
            type: 'HEAD',
            timeout: 5000,
            crossDomain: true,
            success: function(data, textStatus, xhr) {
                var statusCode = xhr.status;
                if (statusCode === 200 || statusCode === 304) {
                    $statusCell.html('<span class="status-indicator" style="color:green;">✅</span> OK (HTTP ' + statusCode + ')');
                } else {
                    $statusCell.html('<span class="status-indicator" style="color:orange;">⚠️</span> HTTP ' + statusCode);
                }
            },
            error: function(xhr, status, error) {
                var testScript = document.createElement('script');
                testScript.src = scriptUrl;
                testScript.onload = function() {
                    $statusCell.html('<span class="status-indicator" style="color:green;">✅</span> Script loaded (bypass)');
                    document.head.removeChild(testScript);
                };
                testScript.onerror = function() {
                    $statusCell.html('<span class="status-indicator" style="color:red;">❌</span> Failed: ' + (xhr.statusText || error || 'unreachable'));
                };
                document.head.appendChild(testScript);
                
                setTimeout(function() {
                    if ($statusCell.find('.status-indicator').text() === '🟡') {
                        $statusCell.html('<span class="status-indicator" style="color:red;">❌</span> Timeout');
                        if (document.head.contains(testScript)) {
                            document.head.removeChild(testScript);
                        }
                    }
                }, 6000);
            }
        });
    }
    
    $('.test-script').on('click', function() {
        var scriptName = $(this).data('script');
        var scriptUrl = $(this).data('url');
        var $statusCell = $('#script-row-' + scriptName + ' .script-status');
        if ($statusCell.length === 0) {
            $statusCell = $('<td class="script-status"></td>');
            $(this).closest('tr').append($statusCell);
        }
        testScript(scriptName, scriptUrl, $statusCell);
    });
    
    $('.refresh-cache').on('click', function() {
        var script = $(this).data('script');
        var proxyUrl = '<?php echo esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'js/coffee-partner-scripts-proxy.php' ); ?>?script=' + script + '&refresh=1';
        var $statusCell = $('#script-row-' + script + ' .script-status');
        if ($statusCell.length === 0) {
            $statusCell = $('<td class="script-status"></td>');
            $(this).closest('tr').append($statusCell);
        }
        $statusCell.html('<span class="status-indicator">🟡</span> Refreshing cache...');
        
        $.ajax({
            url: proxyUrl,
            dataType: 'script',
            timeout: 10000,
            success: function() {
                $statusCell.html('<span class="status-indicator" style="color:green;">✅</span> Cache refreshed');
                setTimeout(function() {
                    if ($statusCell.find('.status-indicator').text() === '✅') {
                        $statusCell.html('<span class="status-indicator">⚪</span> Ready');
                    }
                }, 3000);
            },
            error: function(xhr) {
                $statusCell.html('<span class="status-indicator" style="color:red;">❌</span> Refresh failed: ' + (xhr.statusText || 'error'));
            }
        });
    });
});
</script>
