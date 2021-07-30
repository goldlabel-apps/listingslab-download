<?php
/**
 * Contains the offline base template
 *
 * When the client's internet connection goes down, this template is served as the response
 * instead of the service worker. This template can be overridden by including an offline.php
 * in the theme.
 *
 * @package PWA
 * @since 0.2.0
 */

pwaify_get_header( 'error' );

?>
<main>
	<h1><?php esc_html_e( 'Offline', 'pwaify' ); ?></h1>
	<?php wp_service_worker_error_message_placeholder(); ?>
</main>
<?php

pwaify_get_footer( 'error' );
