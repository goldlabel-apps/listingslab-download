<?php
/**
 * PWA
 *
 * @package      PWAify
 * @license      GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: PWAify
 * Plugin URI:  https://github.com/listingslab-software/pwaify
 * Description: Brings Progressive WordPress App (PWA) capabilities to WordPress
 * Version:     1.1.0
 * Text Domain: pwaify
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'PWAIFY_VERSION', '1.1.0' );
define( 'PWAIFY_PLUGIN_FILE', __FILE__ );
define( 'PWAIFY_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'PWAIFY_WORKBOX_VERSION', '5.1.4' );
define( 'PWAIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Print admin notice regarding having an old version of PHP.
 *
 * @since 0.2
 */
function _pwaify_print_php_version_admin_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: required PHP version */
				esc_html__( 'The pwaify plugin requires PHP %s. Please contact your host to update your PHP version.', 'pwaify' ),
				'5.6+'
			);
			?>
		</p>
	</div>
	<?php
}
if ( version_compare( phpversion(), '5.6', '<' ) ) {
	add_action( 'admin_notices', '_pwaify_print_php_version_admin_notice' );
	return;
}

/**
 * Print admin notice if plugin installed with incorrect slug (which impacts WordPress's auto-update system).
 *
 * @since 0.2
 */
function _pwaify_incorrect_plugin_slug_admin_notice() {
	$actual_slug = basename( PWAIFY_PLUGIN_DIR );
	?>
	<div class="notice notice-warning">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: %1$s is the current directory name, and %2$s is the required directory name */
					__( 'You appear to have installed the PWA plugin incorrectly. It is currently installed in the <code>%1$s</code> directory, but it needs to be placed in a directory named <code>%2$s</code>. Please rename the directory. This is important for WordPress plugin auto-updates.', 'pwaify' ),
					$actual_slug,
					'pwaify'
				)
			);
			?>
		</p>
	</div>
	<?php
}
if ( 'pwaify' !== basename( PWAIFY_PLUGIN_DIR ) ) {
	add_action( 'admin_notices', '_pwaify_incorrect_plugin_slug_admin_notice' );
}

/**
 * Print admin notice when a build has not been been performed.
 *
 * @since 0.2
 */
function _pwaify_print_build_needed_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: composer install && npm install && npm run build */
				__( 'You appear to be running the PWA plugin from source. Please do %s to finish installation.', 'pwaify' ), // phpcs:ignore WordPress.Security.EscapeOutput
				'<code>composer install &amp;&amp; npm install &amp;&amp; npm run build</code>'
			);
			?>
		</p>
	</div>
	<?php
}
if ( ! file_exists( PWAIFY_PLUGIN_DIR . '/wp-includes/js/workbox-v' . PWAIFY_WORKBOX_VERSION ) || ! file_exists( PWAIFY_PLUGIN_DIR . '/wp-includes/js/workbox-v' . PWAIFY_WORKBOX_VERSION . '/workbox-sw.js' ) ) {
	//add_action( 'admin_notices', '_pwaify_print_build_needed_notice' );
	return;
}

/**
 * Register test for navigation preload being erroneously disabled.
 *
 * @since 0.3
 *
 * @param array $tests Tests.
 * @return array Tests.
 */
function _pwaify_add_disabled_navigation_preload_site_status_test( $tests ) {
	$tests['direct']['navigation_preload_enabled'] = array(
		'label' => __( 'Navigation Preload Enabled', 'pwaify' ),
		'test'  => '_pwaify_check_disabled_navigation_preload',
	);
	return $tests;
}
add_filter( 'site_status_tests', '_pwaify_add_disabled_navigation_preload_site_status_test' );

/**
 * Flag navigation preload incorrectly being disabled.
 *
 * This is temporary measure to correct a mistake in the example for how navigation request caching strategies.
 *
 * @todo Eventually add a test for enabling a navigation caching strategy.
 * @since 0.3
 *
 * @return array|null Test results.
 */
function _pwaify_check_disabled_navigation_preload() {

	/** This filter is documented in wp-includes/components/class-wp-service-worker-navigation-routing-component.php */
	$navigation_route_precache_entry = apply_filters(
		'wp_service_worker_navigation_route',
		array(
			'url'      => null,
			'revision' => '',
		)
	);

	// Skip adding the navigation-preload test when using app shell since navigation preload is forcibly-disabled.
	if ( ! empty( $navigation_route_precache_entry['url'] ) ) {
		return null;
	}

	/** This filter is documented in wp-includes/components/class-wp-service-worker-navigation-routing-component.php */
	$navigation_preload_enabled = apply_filters( 'wp_service_worker_navigation_preload', true, WP_Service_Workers::SCOPE_FRONT );

	if ( $navigation_preload_enabled ) {
		$result = array(
			'label'       => __( 'Navigation preload is enabled in service worker', 'pwaify' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Performance', 'pwaify' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				esc_html__( 'Navigation preload speeds up performance for return visitors when the service worker has been suspended.', 'pwaify' )
			),
		);
	} else {
		$result = array(
			'label'       => __( 'Navigation preload is being disabled in service worker', 'pwaify' ),
			'status'      => 'recommended',
			'badge'       => array(
				'label' => __( 'Performance', 'pwaify' ),
				'color' => 'orange',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					/* translators: %s: the wp_service_worker_navigation_preload filter call */
					esc_html__( 'A theme or a plugin appears to have disabled navigation preload in order to enable a navigation caching strategy. This was a workaround that is now no longer needed, and it is actually being ignored. Remove the following code from your theme/plugin to improve performance: %s.', 'pwaify' ),
					'<code>add_filter( \'wp_service_worker_navigation_preload\', \'__return_false\' </code>'
				)
			),
			'actions'     => sprintf(
				'<a href="https://developers.google.com/web/tools/workbox/modules/workbox-navigation-preload#who_should_enable_navigation_preloads">%s</a>',
				esc_html__( 'Learn about enabling navigation preload.', 'pwaify' )
			),
		);
	}

	$result['test'] = 'navigation_preload_enabled';

	return $result;
}

/** WP_Web_App_Manifest Class */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-web-app-manifest.php';

/** WP_HTTPS_Detection Class */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-https-detection.php';

/** WP_HTTPS_UI Class */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-https-ui.php';

/** WP_Service_Workers Class */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-service-workers.php';

/** WP_Service_Worker_Scripts Class */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-service-worker-scripts.php';

/** WP_Service_Worker_Component Interface */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/interface-wp-service-worker-component.php';

/** WP_Service_Worker_Component Implementation Classes */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-configuration-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-navigation-routing-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-core-asset-caching-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-theme-asset-caching-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-plugin-asset-caching-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-uploaded-image-caching-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-precaching-routes-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-precaching-routes.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-caching-routes-component.php';
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/components/class-wp-service-worker-caching-routes.php';

/** WordPress Service Worker Functions */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/service-workers.php';

/** WordPress Service Worker Deprecated */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/deprecated.php';

/** Amend default filters */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/default-filters.php';

/** Functions to add to query.php file. */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/query.php';

/** Functions to add to template.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/template.php';

/** Functions to add to general-template.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/general-template.php';

/** Function to add to post-template.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/post-template.php';

/** Patch behavior in template-loader.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/template-loader.php';

/** Patch behavior in class-wp.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp.php';

/** Patch behavior in class-wp-query.php */
require_once PWAIFY_PLUGIN_DIR . '/wp-includes/class-wp-query.php';

/** Hooks to add for when accessing admin. */
require_once PWAIFY_PLUGIN_DIR . '/wp-admin/admin.php';

/**
 * Plugin activation hook.
 */
function _pwaify_activate_plugin() {
	pwaify_add_rewrite_rules();
	flush_rewrite_rules( false );
}

register_activation_hook( PWAIFY_PLUGIN_FILE, '_pwaify_activate_plugin' );

/**
 * Plugin deactivation hook.
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 */
function _pwaify_deactivate_plugin() {
	global $wp_rewrite;
	unset( $wp_rewrite->extra_rules_top['^wp\.serviceworker$'] );
	flush_rewrite_rules( false );
}

register_deactivation_hook( PWAIFY_PLUGIN_FILE, '_pwaify_deactivate_plugin' );

/**
 * Load service worker integrations.
 *
 * @since 0.2.0
 *
 * @param WP_Service_Worker_Scripts $scripts Instance to register service worker behavior with.
 */
function pwaify_load_service_worker_integrations( WP_Service_Worker_Scripts $scripts ) {
	if ( ! current_theme_supports( 'service_worker' ) ) {
		return;
	}

	/** WordPress Service Worker Integration Functions */
	require_once PWAIFY_PLUGIN_DIR . '/integrations/functions.php';

	pwaify_register_service_worker_integrations( $scripts );
}
add_action( 'wp_default_service_workers', 'pwaify_load_service_worker_integrations', -1 );

$wp_web_app_manifest = new WP_Web_App_Manifest();
$wp_web_app_manifest->init();

$wp_https_detection = new WP_HTTPS_Detection();
$wp_https_detection->init();

require_once PWAIFY_PLUGIN_DIR . '/wp-admin/options-reading-offline-browsing.php';
