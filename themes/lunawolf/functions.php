<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

if ( ! defined( 'LUNAWOLF_TEMPLATE_URI' ) ) {
	define( 'LUNAWOLF_TEMPLATE_URI', untrailingslashit( get_stylesheet_directory() ) );
}

if ( ! defined( 'LUNAWOLF_TEMPLATE_DIR_URI' ) ) {
	define( 'LUNAWOLF_TEMPLATE_DIR_URI', untrailingslashit( get_stylesheet_directory_uri() ) );
}

if ( ! defined( 'LUNAWOLF_BUILD_PATH' ) ) {
	define( 'LUNAWOLF_BUILD_PATH', untrailingslashit( get_stylesheet_directory() ) . '/build' );
}

if ( ! defined( 'LUNAWOLF_BUILD_URI' ) ) {
	define( 'LUNAWOLF_BUILD_URI', untrailingslashit( LUNAWOLF_TEMPLATE_DIR_URI ) . '/build' );
}

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/lib/Lunawolf.php';

function custom_admin_page_order($query) {
    // Check if we are in the admin area, on the pages list screen, and the main query
    if (is_admin() && $query->is_main_query() && $query->get('post_type') == 'page') {
        $orderby = $query->get('orderby');
		
        // Only change the order if no specific order is set
        // if (!$orderby) {
		$query->set('orderby', 'date');
		$query->set('order', 'DESC');
        // }
    }
}

add_action('pre_get_posts', 'custom_admin_page_order');



Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new Lunawolf();


// Allow JSON file uploads
function allow_json_upload( $mimes ) {
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_json_upload' );
