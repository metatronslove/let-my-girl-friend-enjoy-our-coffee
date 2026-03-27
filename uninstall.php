<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up proxy cache directory using WP_Filesystem
$coffee_widget_cache_dir = plugin_dir_path( __FILE__ ) . 'public/js/cache/';

global $wp_filesystem;
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}
if ( WP_Filesystem() ) {
    if ( $wp_filesystem->exists( $coffee_widget_cache_dir ) ) {
        $coffee_widget_files = $wp_filesystem->dirlist( $coffee_widget_cache_dir );
        if ( ! empty( $coffee_widget_files ) ) {
            foreach ( $coffee_widget_files as $coffee_widget_file ) {
                if ( ! $coffee_widget_file['is_dir'] ) {
                    $wp_filesystem->delete( $coffee_widget_cache_dir . $coffee_widget_file['name'] );
                }
            }
        }
        $wp_filesystem->rmdir( $coffee_widget_cache_dir );
    }
}

delete_option( 'coffee_widget_settings' );
delete_option( 'coffee_widget_style' );
delete_option( 'coffee_widget_code' );
