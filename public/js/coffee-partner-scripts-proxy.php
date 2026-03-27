<?php
/**
 * Coffee Widget - Partner Scripts Proxy
 * 
 * This file proxies external scripts to bypass tracking prevention.
 * URLs can be updated via admin panel if needed.
 * 
 * Usage: coffee-partner-scripts-proxy.php?script=stripe&min=1
 *        coffee-partner-scripts-proxy.php?test=stripe (for testing)
 */

if ( ! defined( 'ABSPATH' ) ) {
    header( 'Content-Type: text/plain' );
    echo '// Direct access not allowed';
    exit;
}

// Security: Only allow requests from your site
$coffee_widget_site_url = get_site_url();
$coffee_widget_site_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
$coffee_widget_allowed_origins = array(
    $coffee_widget_site_url,
    'https://' . $coffee_widget_site_host,
    'http://' . $coffee_widget_site_host
);

if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
    $coffee_widget_referer_raw = sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
    $coffee_widget_referer = wp_parse_url( $coffee_widget_referer_raw, PHP_URL_HOST );
    $coffee_widget_allowed = false;
    foreach ( $coffee_widget_allowed_origins as $coffee_widget_origin ) {
        if ( strpos( $coffee_widget_referer_raw, $coffee_widget_origin ) === 0 ) {
            $coffee_widget_allowed = true;
            break;
        }
    }
    if ( ! $coffee_widget_allowed ) {
        header( 'HTTP/1.0 403 Forbidden' );
        echo '// Access denied';
        exit;
    }
}

// Get script name from query string with nonce verification for state-changing operations
$coffee_widget_script_name = isset( $_GET['script'] ) ? sanitize_text_field( wp_unslash( $_GET['script'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$coffee_widget_test_mode = isset( $_GET['test'] ) ? sanitize_text_field( wp_unslash( $_GET['test'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$coffee_widget_force_refresh = isset( $_GET['refresh'] ) && sanitize_text_field( wp_unslash( $_GET['refresh'] ) ) === '1'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$coffee_widget_minify = isset( $_GET['min'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['min'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// Nonce kontrolü (sadece refresh=1 için)
if ( $coffee_widget_force_refresh ) {
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'coffee_widget_proxy_refresh' ) ) {
        header( 'HTTP/1.0 403 Forbidden' );
        echo '// Invalid nonce for cache refresh';
        exit;
    }
}

// =============================================================
// TEST MODE - Just check if script is accessible
// =============================================================
if ( ! empty( $coffee_widget_test_mode ) ) {
    header( 'Content-Type: application/json' );
    
    $coffee_widget_options = get_option( 'coffee_widget_settings', array() );
    $coffee_widget_custom_scripts = isset( $coffee_widget_options['proxy_scripts'] ) ? maybe_unserialize( $coffee_widget_options['proxy_scripts'] ) : array();
    
    $coffee_widget_default_scripts = array(
        'stripe' => array( 'url' => 'https://js.stripe.com/v3/', 'description' => 'Stripe.js' ),
        'paypal' => array( 'url' => 'https://www.paypal.com/sdk/js', 'description' => 'PayPal SDK' )
    );
    
    $coffee_widget_scripts = array_merge( $coffee_widget_default_scripts, $coffee_widget_custom_scripts );
    
    if ( ! isset( $coffee_widget_scripts[ $coffee_widget_test_mode ] ) ) {
        echo wp_json_encode( array( 'success' => false, 'error' => 'Script not found' ) );
        exit;
    }
    
    $coffee_widget_test_url = $coffee_widget_scripts[ $coffee_widget_test_mode ]['url'];
    
    // Try to fetch headers only
    $coffee_widget_response = wp_remote_head( $coffee_widget_test_url, array( 'timeout' => 10 ) );
    
    if ( is_wp_error( $coffee_widget_response ) ) {
        echo wp_json_encode( array( 
            'success' => false, 
            'error' => $coffee_widget_response->get_error_message(),
            'url' => $coffee_widget_test_url
        ) );
        exit;
    }
    
    $coffee_widget_status_code = wp_remote_retrieve_response_code( $coffee_widget_response );
    
    if ( $coffee_widget_status_code === 200 || $coffee_widget_status_code === 304 ) {
        echo wp_json_encode( array( 
            'success' => true, 
            'status' => $coffee_widget_status_code,
            'url' => $coffee_widget_test_url,
            'message' => 'OK'
        ) );
    } else {
        echo wp_json_encode( array( 
            'success' => false, 
            'status' => $coffee_widget_status_code,
            'url' => $coffee_widget_test_url,
            'error' => 'HTTP ' . $coffee_widget_status_code
        ) );
    }
    exit;
}

// =============================================================
// NORMAL PROXY MODE
// =============================================================

if ( empty( $coffee_widget_script_name ) ) {
    header( 'HTTP/1.0 400 Bad Request' );
    echo '// No script specified';
    exit;
}

// Get script URLs from WordPress options
$coffee_widget_options = get_option( 'coffee_widget_settings', array() );

// Default script URLs (working)
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

// Allow custom URLs from admin
$coffee_widget_custom_scripts = isset( $coffee_widget_options['proxy_scripts'] ) ? maybe_unserialize( $coffee_widget_options['proxy_scripts'] ) : array();

// Merge with defaults (custom overrides default)
$coffee_widget_scripts = array_merge( $coffee_widget_default_scripts, $coffee_widget_custom_scripts );

if ( ! isset( $coffee_widget_scripts[ $coffee_widget_script_name ] ) ) {
    header( 'HTTP/1.0 404 Not Found' );
    echo '// Script not found: ' . esc_js( $coffee_widget_script_name );
    exit;
}

$coffee_widget_script = $coffee_widget_scripts[ $coffee_widget_script_name ];
$coffee_widget_script_url = $coffee_widget_script['url'];
$coffee_widget_script_type = isset( $coffee_widget_script['type'] ) ? $coffee_widget_script['type'] : 'javascript';
$coffee_widget_cache_ttl = isset( $coffee_widget_script['cache_ttl'] ) ? $coffee_widget_script['cache_ttl'] : 86400;

// Build query parameters from current request
$coffee_widget_query_params = array();
if ( $coffee_widget_script_name === 'paypal' ) {
    $coffee_widget_client_id = isset( $coffee_widget_options['paypal_client_id'] ) ? $coffee_widget_options['paypal_client_id'] : '';
    if ( ! empty( $coffee_widget_client_id ) ) {
        $coffee_widget_query_params['client-id'] = $coffee_widget_client_id;
    }
    $coffee_widget_query_params['currency'] = 'USD';
    $coffee_widget_query_params['components'] = 'buttons';
    $coffee_widget_query_params['enable-funding'] = 'card';
}

if ( $coffee_widget_script_name === 'stripe' ) {
    $coffee_widget_query_params['advancedFraudSignals'] = 'true';
}

// Build final URL with query parameters
$coffee_widget_final_url = $coffee_widget_script_url;
if ( ! empty( $coffee_widget_query_params ) ) {
    $coffee_widget_final_url .= ( strpos( $coffee_widget_script_url, '?' ) === false ? '?' : '&' ) . http_build_query( $coffee_widget_query_params );
}

// Set cache key
$coffee_widget_cache_key = 'coffee_widget_script_' . $coffee_widget_script_name;
$coffee_widget_cache_key .= '_' . md5( $coffee_widget_final_url );
$coffee_widget_cache_dir = dirname( __FILE__ ) . '/cache/';

// Create cache directory if it doesn't exist using WP_Filesystem
global $wp_filesystem;
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}
if ( ! WP_Filesystem() ) {
    header( 'HTTP/1.0 500 Internal Server Error' );
    echo '// Could not initialize filesystem';
    exit;
}

if ( ! $wp_filesystem->exists( $coffee_widget_cache_dir ) ) {
    $wp_filesystem->mkdir( $coffee_widget_cache_dir, 0755 );
}

$coffee_widget_cache_file = $coffee_widget_cache_dir . $coffee_widget_cache_key . '.' . ( $coffee_widget_script_type === 'css' ? 'css' : 'js' );

// Serve from cache if available and not forcing refresh
if ( ! $coffee_widget_force_refresh && $wp_filesystem->exists( $coffee_widget_cache_file ) && ( time() - $wp_filesystem->mtime( $coffee_widget_cache_file ) ) < $coffee_widget_cache_ttl ) {
    header( 'Content-Type: text/' . $coffee_widget_script_type );
    header( 'X-Coffee-Widget-Cache: HIT' );
    header( 'Cache-Control: public, max-age=' . $coffee_widget_cache_ttl );
    // Output file content
    $coffee_widget_cache_content = $wp_filesystem->get_contents( $coffee_widget_cache_file );
    if ( false !== $coffee_widget_cache_content ) {
        echo $coffee_widget_cache_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    exit;
}

// Fetch the script from remote URL
$coffee_widget_response = wp_remote_get( $coffee_widget_final_url, array(
    'timeout' => 15,
    'headers' => array(
        'User-Agent' => 'Mozilla/5.0 (compatible; WordPress Coffee Widget)',
        'Accept' => $coffee_widget_script_type === 'css' ? 'text/css' : 'application/javascript'
    )
) );

if ( is_wp_error( $coffee_widget_response ) ) {
    header( 'HTTP/1.0 502 Bad Gateway' );
    echo '// Error fetching script: ' . esc_js( $coffee_widget_response->get_error_message() );
    exit;
}

$coffee_widget_status_code = wp_remote_retrieve_response_code( $coffee_widget_response );
if ( $coffee_widget_status_code !== 200 ) {
    header( 'HTTP/1.0 ' . $coffee_widget_status_code . ' Service Unavailable' );
    echo '// Script unavailable (HTTP ' . esc_html( $coffee_widget_status_code ) . ')';
    exit;
}

$coffee_widget_content = wp_remote_retrieve_body( $coffee_widget_response );

// Process content to replace internal CDN references
if ( $coffee_widget_script_name === 'stripe' ) {
    $coffee_widget_content = str_replace( 'https://js.stripe.com', '', $coffee_widget_content );
    $coffee_widget_content = str_replace( 'https://m.stripe.network', '', $coffee_widget_content );
    $coffee_widget_content = str_replace( 'https://m.stripe.com', '', $coffee_widget_content );
    $coffee_widget_content = str_replace( 'https://stripe.com', '', $coffee_widget_content );
    
    $coffee_widget_content = preg_replace(
        '/(["\'])\/(v3\/[^"\']+\.js)/',
        '$1' . plugin_dir_url( __FILE__ ) . 'coffee-partner-scripts-proxy.php?script=stripe&url=$2',
        $coffee_widget_content
    );
}

if ( $coffee_widget_script_name === 'paypal' ) {
    $coffee_widget_content = str_replace( 'https://www.paypal.com', '', $coffee_widget_content );
    $coffee_widget_content = str_replace( 'https://paypal.com', '', $coffee_widget_content );
}

// Minify if requested
if ( $coffee_widget_minify && $coffee_widget_script_type === 'javascript' ) {
    $coffee_widget_content = preg_replace( '!/\*.*?\*/!s', '', $coffee_widget_content );
    $coffee_widget_content = preg_replace( '/\n\s*\n/', "\n", $coffee_widget_content );
    $coffee_widget_content = preg_replace( '/\s+/', ' ', $coffee_widget_content );
}

// Save to cache
$wp_filesystem->put_contents( $coffee_widget_cache_file, $coffee_widget_content, FS_CHMOD_FILE );

// Output
header( 'Content-Type: text/' . $coffee_widget_script_type );
header( 'X-Coffee-Widget-Cache: MISS' );
header( 'Cache-Control: public, max-age=' . $coffee_widget_cache_ttl );
header( 'Access-Control-Allow-Origin: *' );

// Output script content (escaped in context? no, it's raw script)
echo $coffee_widget_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
