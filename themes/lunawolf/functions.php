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

Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new Lunawolf();
