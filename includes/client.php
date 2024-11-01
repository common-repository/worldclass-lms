<?php

/**
 * Enqueue client scripts
 */
function wcio_load_client_scripts() {
    wp_enqueue_script( 'wcio-client-main', WCIO_PLUGIN_URL . '/includes/js/scripts.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'wcio_load_client_scripts' );

// [worldclass]
function wc_shortcode_callback( $atts ) {
    $settings = get_option( 'wcio_settings' );
    $settings = unserialize( $settings );
    $protocol = isset($settings['wc_disable_https']) && $settings['wc_disable_https'] == 'yes' ? 'http' : 'https';
    $url = "{$protocol}://{$settings['wc_domain']}/client/dist/index.html?src=wordpress&type=embed";

    if (!$settings || !isset($settings['wc_domain'])) {
        return _e( "Worldclass Academy not configured correctly. Please add your Worldclass Academy domain in the Worldclass plugin settings page.", 'wcio' );
    }

    return "<iframe id='worldclass-embed' name='wcio-embed' data-embed='1.0' frameborder='0' width='100%' src='$url'></iframe>";
}
add_shortcode( 'worldclass', 'wc_shortcode_callback' );

?>