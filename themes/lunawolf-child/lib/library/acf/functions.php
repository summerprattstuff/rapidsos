<?php
add_filter( 'acf/settings/load_json',                          'acf_json_load_paths' );
add_filter( 'acf/settings/save_json/type=acf-field-group',     'acf_json_save_path_for_field_groups' );
add_filter( 'acf/settings/save_json/type=acf-ui-options-page', 'acf_json_save_path_for_option_pages' );
add_filter( 'acf/settings/save_json/type=acf-post-type',       'acf_json_save_path_for_post_types' );
add_filter( 'acf/settings/save_json/type=acf-taxonomy',        'acf_json_save_path_for_taxonomies' );
add_filter( 'acf/json/save_file_name',                         'acf_json_filename', 10, 3 );
add_action( 'acf/input/admin_head',                            'acf_backend_assets', 10, 0 );

function acf_backend_assets() {
	$asset_config_file = LUNAWOLF_TEMPLATE_DIR_URI . '/build/acf/acf.asset.php';
	$asset_config_version = '';
	$asset_config_dependency = ['jQuery'];

	if (file_exists($asset_config_file))
	{
		$asset_config = include $asset_config_file;

		$asset_config_version = $asset_config['version'];
		$asset_config_dependency = $asset_config['dependencies'];

		// Force ACF input as dependency
		if (!isset($asset_config_dependency['acf-input']))
			$asset_config_dependency[] = 'acf-input';
	}

	$style_url = LUNAWOLF_TEMPLATE_DIR_URI . '/build/acf/acf.css';
	$script_url = LUNAWOLF_TEMPLATE_DIR_URI . '/build/acf/acf.js';

	// Theme styles
	wp_enqueue_style('lunawolf-acf-style', $style_url, [], $asset_config_version);
	wp_enqueue_style('lunawolf-acf-style', $style_url, [], $asset_config_version);
	// Theme JS
	wp_register_script( 'lunawolf-acf-script-min', $script_url, $asset_config_dependency, $asset_config_version, true);
	wp_localize_script( 'lunawolf-acf-script-min', 'wp_params', []);

	wp_enqueue_script( 'lunawolf-acf-script-min' );

	$magnific_popup_css = get_stylesheet_directory_uri() . '/magnific-popup/magnific-popup.css';
	$magnific_popup_js = get_stylesheet_directory_uri() . '/magnific-popup/jquery.magnific-popup.min.js';

	// Enqueue the Magnific Popup CSS and JS files.
	wp_enqueue_style('magnific-popup-css', $magnific_popup_css);
	wp_enqueue_script('magnific-popup-js', $magnific_popup_js, array('jquery'));
}

/**
 * Set a custom ACF JSON load path.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#loading-explained
 *
 * @param array $paths Existing, incoming paths.
 *
 * @return array $paths New, outgoing paths.
 *
 */
function acf_json_load_paths( $paths ) {
	// Remove the original path (optional).
	unset($paths[0]);

	$path = LUNAWOLF_TEMPLATE_URI . '/lib/library/acf/json';

	$paths[] = $path . '/field-groups';
	$paths[] = $path . '/options-pages';
	$paths[] = $path . '/post-types';
	$paths[] = $path . '/taxonomies';

	return $paths;
}

/**
 * Set custom ACF JSON save point for
 * ACF generated post types.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#saving-explained
 *
 * @return string $path New, outgoing path.
 *
 */
function acf_json_save_path_for_post_types() {
	$path = LUNAWOLF_TEMPLATE_URI . '/lib/library/acf/json';

	return $path . '/post-types';
}

/**
 * Set custom ACF JSON save point for
 * ACF generated field groups.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#saving-explained
 *
 * @return string $path New, outgoing path.
 *
 */
function acf_json_save_path_for_field_groups() {
	$path = LUNAWOLF_TEMPLATE_URI . '/lib/library/acf/json';

	return $path . '/field-groups';
}

/**
 * Set custom ACF JSON save point for
 * ACF generated taxonomies.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#saving-explained
 *
 * @return string $path New, outgoing path.
 *
 */
function acf_json_save_path_for_taxonomies() {
	$path = LUNAWOLF_TEMPLATE_URI . '/lib/library/acf/json';

	return $path . '/taxonomies';
}

/**
 * Set custom ACF JSON save point for
 * ACF generated Options Pages.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#saving-explained
 *
 * @return string $path New, outgoing path.
 *
 */
function acf_json_save_path_for_option_pages() {
	$path = LUNAWOLF_TEMPLATE_URI . '/lib/library/acf/json';

	return $path . '/options-pages';
}

/**
 * Customize the file names for each file.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#saving-explained
 *
 * @param string $filename  The default filename.
 * @param array  $post      The main post array for the item being saved.
 *
 * @return string $filename
 *
 * @since  0.1.1
 */
function acf_json_filename( $filename, $post, $load_path ) {
	$filename = str_replace(
		array(
			' ',
			'_',
		),
		array(
			'-',
			'-'
		),
		$post['title']
	);

	$filename = strtolower( $filename ) . '.json';

	return $filename;
}