<?php
/**
 * Hooks to integrate with admin.
 *
 * @package PWA
 */

/**
 * Serve the error.php admin page template when requested.
 *
 * @since 0.2
 */
function pwaify_serve_admin_error_template() {
	add_filter( 'wp_doing_ajax', '__return_false' );
	require dirname( __FILE__ ) . '/error.php';
	exit;
}
add_action( 'wp_ajax_wp_error_template', 'pwaify_serve_admin_error_template' );
add_action( 'wp_ajax_nopriv_wp_error_template', 'pwaify_serve_admin_error_template' );
