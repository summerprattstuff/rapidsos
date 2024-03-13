<?php

require_once('Lunawolf_Settings.php');
require_once('Lunawolf_Helpers.php');

use Timber\Site;

/**
 * Class Lunawolf
 */
class Lunawolf extends Site {
	protected $settings;
	protected $helpers;

	public function __construct() {
		$this->settings = Lunawolf_Settings::instance();
		$this->helpers = Lunawolf_Helpers::instance();

		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_filter( 'timber/locations', array( $this, 'timber_locations' ) );
		add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );
		add_filter( 'acfe/flexible/render/template', [$this, 'acfe_preview_override_timber'], 10, 4 );
//		add_filter( 'acfe/flexible/render/style', [$this, 'acfe_style_path'], 10, 4 );
		add_filter( 'acfe/flexible/enqueue', [$this, 'acfe_style_path_enqueue'], 10, 4 );

		parent::__construct();

		$this->load_components();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {

	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {

	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu']    = Timber::get_menu();
		$context['site']    = $this;
		$context['options'] = get_fields('options');
		$context['siteUrl'] = home_url('/');

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );

		add_theme_support('soil', [
			'clean-up',
//			'nav-walker', Not working with timber
//    'nice-search',
//    'disable-rest-api',
//    'disable-asset-versioning',
			'disable-trackbacks',
			'nice-search',
			'js-to-footer',
			'relative-urls',
		]);

		remove_theme_support('block-templates');

		/**
		 * Disable gutenberg style in header global-style
		 */
		add_action( 'wp_enqueue_scripts', function () {
			wp_dequeue_style( 'global-styles' );
			wp_dequeue_style( 'wp-block-columns' );
			wp_dequeue_style( 'wp-block-column' );
			wp_dequeue_style( 'classic-theme-styles' );

			global $wp_query;
			wp_enqueue_style( 'lunawolf-style', get_stylesheet_uri() );

			$asset_config_file = LUNAWOLF_BUILD_PATH . '/theme/main.asset.php';
			$asset_config_version = '';

			if (file_exists($asset_config_file))
			{
				$asset_config = include $asset_config_file;

				$asset_config_version = $asset_config['version'];
				$asset_config_dependency = $asset_config['dependencies'];
			}
//			$asset_config_dependency[] = 'jQuery';

			$style_url = LUNAWOLF_TEMPLATE_DIR_URI . '/build/theme/main.css';
			$script_url = LUNAWOLF_TEMPLATE_DIR_URI . '/build/theme/main.js';

			// Theme styles
			wp_enqueue_style('lunawolf-main-style', $style_url, [], $asset_config_version);
			// Theme JS
			wp_register_script( 'lunawolf-script-min', $script_url, $asset_config_dependency, $asset_config_version, true);
			wp_localize_script( 'lunawolf-script-min', 'wp_params', array(
				'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
				'posts' => json_encode( $wp_query->query_vars ),
				'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
				'max_page' => $wp_query->max_num_pages,
				'found_posts' => $wp_query->found_posts
			) );

			wp_enqueue_script( 'lunawolf-script-min' );
		}, 100 );
	}

	/**
	 * his would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	public function timber_locations($locs) {
		return $locs;
	}

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @param Twig\Environment $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		/**
		 * Required when you want to use Twig’s template_from_string.
		 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
		 */
		// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );

		$twig->addFilter( new Twig\TwigFilter( 'myfoo', [ $this, 'myfoo' ] ) );

		return $twig;
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @link https://twig.symfony.com/doc/245.x/api.html#environment-options
	 *
	 * \@param array $options An array of environment options.
	 *
	 * @return array
	 */
	function update_twig_environment_options( $options ) {
		// $options['autoescape'] = true;

		return $options;
	}

	private function load_components() {
		$setup_files = [
			'acf/fields',
			'acf/functions',
			'acf/options',

//			'gutenberg/gutenberg-setup',
		];

		foreach($setup_files as $file) {
			if (!locate_template($file = "lib/library/{$file}.php", true, true)) {
				wp_die(
					sprintf(__('Error locating <code>%s</code> for inclusion.', 'lunawolf'), $file)
				);
			}
		}
	}

	public function acfe_preview_override_timber($file, $field, $layout, $is_preview) {
		$context = Timber::context();
		$name = $layout['name'];
		$row = get_row(true);
		$context['block'] = get_row(true);
		$context['is_preview'] = $is_preview;
		// Settings configuration
		$settings = $row['layout_settings'] ?? null;

		$context['settings'] = $this->settings->settings($settings);

		Timber::render(sprintf('views/_blocks/%s.twig', $name), $context);
	}

	/**
	 * Not using this, since you cannot clear cache. using one under this
	 * @return string|void
	 */
	public function acfe_style_path() {
		if ( ! is_admin() ) return;

		return LUNAWOLF_BUILD_URI . '/blocks/blocks.css';
	}

	public function acfe_style_path_enqueue() {
		if ( ! is_admin() ) return;

		$asset_config_file = LUNAWOLF_BUILD_PATH . '/blocks/blocks.asset.php';
		$asset_config_version = '';

		if (file_exists($asset_config_file))
		{
			$asset_config = include $asset_config_file;

			$asset_config_version = $asset_config['version'];
		}

		wp_enqueue_style('acfe-blocks-styles', LUNAWOLF_BUILD_URI . '/blocks/blocks.css', [], $asset_config_version);
	}
}
