<?php

namespace ASENHA\Classes;
use enshrined\svgSanitize\Sanitizer; // For Enable SVG Upload
use WP_Query;
use WC_Admin_Duplicate_Product;	
use WP_Admin_Bar;

/**
 * Class related to Content Management features
 *
 * @since 1.0.0
 */
class Content_Management {

	/**
	 * Register CPTs for registering additional custom post types and custom taxonomies
	 * 
	 * @since 5.1.0
	 */
	public function register_asenha_cpts__premium_only() {
		// Custom Post Types
		register_post_type(
			'asenha_cpt',
			array(
				'labels'				=> array(
					'name'					=> 'Custom Post Types',
					'singular_name'			=> 'Custom Post Type',
					'add_new'				=> 'Add New',
					'add_new_item'			=> 'Add New Post Type',
					'new_item'				=> 'New Post Type',
					'edit_item'				=> 'Edit Post Type',
					'update_item'			=> 'Update Post Type',
					'view_item'				=> 'View Post Type',
					'search_items'			=> 'Search Post Types',
					'not_found'				=> 'No Custom Post Types found',
					'not_found_in_trash'	=> 'No Custom Post Types found in Trash',
				),
				'public'			=> false,
				'show_ui'			=> true,
				'show_in_menu'		=> 'options-general.php',
				'hierarchical'		=> false,
				// Hide 'title' and 'editor'
				'supports'			=> false,
				'rewrite'			=> false,
				'query_var'			=> false,
				'can_export'		=> true,
				// Allow administrator to edit/read/delete without adding custom capabilites
				'map_meta_cap'		=> true,
				// Limit CRUD operations to administrators
				'capabilities'		=> array(
					// Meta capabilities.
					'edit_post'              => 'edit_asenha_cpt',
					'read_post'              => 'read_asenha_cpt',
					'delete_post'            => 'delete_asenha_cpt',

					// Primitive capabilities used outside of map_meta_cap():
					'edit_posts'             => 'manage_options',
					'edit_others_posts'      => 'manage_options',
					'publish_posts'          => 'manage_options',
					'read_private_posts'     => 'manage_options',

					// Primitive capabilities used within map_meta_cap():
					'read'                   => 'read',
					'delete_posts'           => 'manage_options',
					'delete_private_posts'   => 'manage_options',
					'delete_published_posts' => 'manage_options',
					'delete_others_posts'    => 'manage_options',
					'edit_private_posts'     => 'manage_options',
					'edit_published_posts'   => 'manage_options',
					'create_posts'           => 'manage_options',				
				),
			)
		);

		// Custom Taxonomies
		register_post_type(
			'asenha_ctax',
			array(
				'labels'				=> array(
					'name'					=> 'Custom Taxonomies',
					'singular_name'			=> 'Custom Taxonomy',
					'add_new'				=> 'Add New',
					'add_new_item'			=> 'Add New Taxonomy',
					'edit_item'				=> 'Edit Taxonomy',
					'new_item'				=> 'New Taxonomy',
					'view_item'				=> 'View Taxonomy',
					'search_items'			=> 'Search Taxonomies',
					'not_found'				=> 'No Custom Taxonomies found',
					'not_found_in_trash'	=> 'No Custom Taxonomies found in Trash',
				),
				'public'			=> false,
				'show_ui'			=> true,
				'show_in_menu'		=> 'options-general.php',
				'hierarchical'		=> false,
				// Hide 'title' and 'editor'
				'supports'			=> false,
				'rewrite'			=> false,
				'query_var'			=> false,
				'can_export'		=> true,
				// Allow administrator to edit/read/delete without adding custom capabilites
				'map_meta_cap'		=> true,
				// Limit CRUD operations to administrators
				'capabilities'		=> array(
					// Meta capabilities.
					'edit_post'              => 'edit_asenha_ctax',
					'read_post'              => 'read_asenha_ctax',
					'delete_post'            => 'delete_asenha_ctax',

					// Primitive capabilities used outside of map_meta_cap():
					'edit_posts'             => 'manage_options',
					'edit_others_posts'      => 'manage_options',
					'publish_posts'          => 'manage_options',
					'read_private_posts'     => 'manage_options',

					// Primitive capabilities used within map_meta_cap():
					'read'                   => 'read',
					'delete_posts'           => 'manage_options',
					'delete_private_posts'   => 'manage_options',
					'delete_published_posts' => 'manage_options',
					'delete_others_posts'    => 'manage_options',
					'edit_private_posts'     => 'manage_options',
					'edit_published_posts'   => 'manage_options',
					'create_posts'           => 'manage_options',				
				),
			)
		);

	}
	
	/**
	 * Add meta boxes to the creation screen of CPTs and custom taxonomies
	 * 
	 * @since 5.1.0
	 */
	public function add_meta_boxes_for_asenha_cpts__premium_only() {
		
		add_meta_box(
			'asenha_cpt_meta_box',
			'Post Type Options',
			[ $this, 'render_cpt_meta_box__premium_only' ],
			'asenha_cpt',
			'advanced',
			'high'
		);
		
		add_meta_box(
			'asenha_ctax_meta_box',
			'Taxonomy Options',
			[ $this, 'render_ctax_meta_box__premium_only' ],
			'asenha_ctax',
			'advanced',
			'high'
		);

		// add_meta_box(
		// 	'asenha_cpt_references_meta_box',
		// 	'References',
		// 	[ $this, 'render_cpt_references_meta_box__premium_only' ],
		// 	'asenha_cpt',
		// 	'side',
		// 	'low'
		// );
		
		// add_meta_box(
		// 	'asenha_ctax_references_meta_box',
		// 	'References',
		// 	[ $this, 'render_ctax_references_meta_box__premium_only' ],
		// 	'asenha_ctax',
		// 	'side',
		// 	'low'
		// );
		
	}
	
	/**
	 * Render options for custom post type
	 * 
	 * @since 5.1.0
	 */
	public function render_cpt_meta_box__premium_only() {

		// Get or set the values of CPT fields
		require_once ASENHA_PATH . 'includes/premium/custom-content/cpt/cpt-fields-values.php';

		// Render metabox to input / update CPT fields
		require_once ASENHA_PATH . 'includes/premium/custom-content/cpt/cpt-form-fields.php';
		
	}

	/**
	 * Render options for taxonomies
	 * 
	 * @since 5.1.0
	 */
	public function render_ctax_meta_box__premium_only() {

		// Get or set the values of custom taxonomy fields
		require_once ASENHA_PATH . 'includes/premium/custom-content/ctax/ctax-fields-values.php';

		// Render metabox to input / update custom taxonomy fields
		require_once ASENHA_PATH . 'includes/premium/custom-content/ctax/ctax-form-fields.php';

	}

	/**
	 * Render documentation metabox for the create CPT screen
	 * 
	 * @since 5.1.0
	 */
	public function render_cpt_references_meta_box__premium_only() {
		?>
		<div class="tips-wrapper">
			Message about upgrading to ASENHA PRO
			<ul>
				<li><span class="dashicons dashicons-yes"></span> Benefit #1</li>
			</ul>
			<a href="#" class="button">Upgrade Now</a>
		</div>
		<?php
	}
	
	/**
	 * Render documentation metabox for the create CPT screen
	 * 
	 * @since 5.1.0
	 */
	public function render_ctax_references_meta_box__premium_only() {
		?>
		<div class="references-wrapper">
			Message about upgrading to ASENHA PRO
			<ul>
				<li><span class="dashicons dashicons-yes"></span> Benefit #1</li>
			</ul>
			<a href="#" class="button">Upgrade Now</a>
		</div>
		<?php
	}
	
	/**
	 * Save fields for custom post types and taxonomies
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/save_post/
	 * @since 5.1.0
	 */
	public function save_asenha_cpt_ctax_optionp_fields__premium_only( $post_id, $post, $update ) {

		$common_methods = new Common_Methods;

		// Sanitize and save custom post type, custom taxonomy or options page fields values in post meta
		require_once ASENHA_PATH . 'includes/premium/custom-content/save-cpt-ctax-optionp-fields.php';
		
	}
	
	/**
	 * Flush rewrite rules after creating or updating CPTs and custom taxonomies
	 * 
	 * @since 5.1.0
	 */
	public function asenha_cpts_flush_rewrite_rules__premium_only() {
		$options = get_option( ASENHA_SLUG_U );
		
		if ( true === $options['custom_content_types_flush_rewrite_rules_needed'] ) {
			flush_rewrite_rules();
			$options['custom_content_types_flush_rewrite_rules_needed'] = false;
			update_option( ASENHA_SLUG_U, $options );
		}
	}
	
	/**
	 * Register CPTs and custom taxonomies created by custom content module
	 * 
	 * @since 5.1.0
	 */
	public function register_cpts_and_ctaxs_created_by_asenha__premium_only() {
		
		// ====== REGISTER CPTS ======
		
		// Get CPT posts in wp_posts table added via Settings >> Custom Post Types
		$asenha_cpts_posts = get_posts( array(
			'numberposts'		=> -1,
			'post_type'			=> 'asenha_cpt',
			'post_status'		=> 'publish',
			'suppress_filters'	=> false,		
		) );

		// Get CPT posts meta and use them to register the CPTs
		if ( $asenha_cpts_posts ) {
			foreach( $asenha_cpts_posts as $asenha_cpt ) {
				
				$post_type_key = get_post_meta( $asenha_cpt->ID, 'cpt_key', true );
				
				// --- Set LABELS ---

				$labels = array(
					'name'						=> get_post_meta( $asenha_cpt->ID, 'cpt_plural_name', true ),
					'singular_name'				=> get_post_meta( $asenha_cpt->ID, 'cpt_singular_name', true ),
					'add_new'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_add_new', true ),
					'add_new_item'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_add_new_item', true ),
					'edit_item'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_edit_item', true ),
					'new_item'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_new_item', true ),
					'view_item'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_view_item', true ),
					'view_items'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_view_items', true ),
					'search_items'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_search_items', true ),
					'not_found'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_not_found', true ),
					'not_found_in_trash'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_not_found_in_trash', true ),
					'parent_item_colon'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_parent_item_colon', true ),
					'all_items'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_all_items', true ),
					'archives'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_archives', true ),
					'attributes'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_attributes', true ),
					'insert_into_item'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_insert_into_item', true ),
					'uploaded_to_this_item'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_uploaded_to_this_item', true ),
					'featured_image'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_featured_image', true ),
					'set_featured_image'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_set_featured_image', true ),
					'remove_featured_image'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_remove_featured_image', true ),
					'use_featured_image'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_use_featured_image', true ),
					'menu_name'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_menu_name', true ),
					'filter_items_list'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_filter_items_list', true ),
					'filter_by_date'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_filter_by_date', true ),
					'items_list_navigation'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_items_list_navigation', true ),
					'items_list'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_items_list', true ),
					'item_published'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_published', true ),
					'item_published_privately'	=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_published_privately', true ),
					'item_reverted_to_draft'	=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_reverted_to_draft', true ),
					'item_scheduled'			=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_scheduled', true ),
					'item_updated'				=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_updated', true ),
					'item_link'					=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_link', true ),
					'item_link_description'		=> get_post_meta( $asenha_cpt->ID, 'cpt_label_item_link_description', true ),
				);

				// --- Set ARGS ---
				
				// Get capability type

				$cpt_capability_type = get_post_meta( $asenha_cpt->ID, 'cpt_capability_type', true );
				$cpt_custom_cap_slug_singular = get_post_meta( $asenha_cpt->ID, 'cpt_capability_type_custom_slug_singular', true );
				$cpt_custom_cap_slug_plural = get_post_meta( $asenha_cpt->ID, 'cpt_capability_type_custom_slug_plural', true );

				if ( $cpt_capability_type == 'post' || $cpt_capability_type == 'page' ) {
					$capability_type = $cpt_capability_type;
				} elseif ( $cpt_capability_type == 'custom' ) {
					if ( ! empty( $cpt_custom_cap_slug_singular ) && ! empty( $cpt_custom_cap_slug_plural ) ) {
						$capability_type = array( $cpt_custom_cap_slug_singular, $cpt_custom_cap_slug_plural );					
					} else if ( ! empty( $cpt_custom_cap_slug_singular )  && empty( $cpt_custom_cap_slug_plural ) ) {
						$capability_type = $cpt_custom_cap_slug_singular;
					} else {
						$capability_type = 'post';
					}
				}
				
				// Define query_var value to use
				
				$cpt_query_var = get_post_meta( $asenha_cpt->ID, 'cpt_query_var', true );
				$cpt_use_custom_query_var_string = get_post_meta( $asenha_cpt->ID, 'cpt_use_custom_query_var_string', true );
				$cpt_query_var_string = get_post_meta( $asenha_cpt->ID, 'cpt_query_var_string', true );

				if ( $cpt_query_var ) {
					if ( $cpt_use_custom_query_var_string ) {
						if ( ! empty( $cpt_query_var_string ) ) {
							$query_var = $cpt_query_var_string;
						} else {
							$query_var = true;
						}
					} else {
						$query_var = true;
					}
				} else {
					$query_var = false;
				}

				// Define has_archive value to use
				
				$cpt_has_archive = get_post_meta( $asenha_cpt->ID, 'cpt_has_archive', true );
				$cpt_use_custom_archive_slug = get_post_meta( $asenha_cpt->ID, 'cpt_use_custom_archive_slug', true );
				$cpt_has_archive_custom_slug = get_post_meta( $asenha_cpt->ID, 'cpt_has_archive_custom_slug', true );

				if ( $cpt_has_archive ) {
					if ( $cpt_use_custom_archive_slug ) {
						if ( ! empty( $cpt_has_archive_custom_slug ) ) {
							$has_archive = $cpt_has_archive_custom_slug;
						} else {
							$has_archive = true;
						}
					} else {
						$has_archive = true;
					}
				} else {
					$has_archive = false;
				}
				
				// Define rewrite value to use

				$cpt_rewrite = get_post_meta( $asenha_cpt->ID, 'cpt_rewrite', true );
				$cpt_use_custom_rewrite_slug = get_post_meta( $asenha_cpt->ID, 'cpt_use_custom_rewrite_slug', true );
				$cpt_rewrite_custom_slug = get_post_meta( $asenha_cpt->ID, 'cpt_rewrite_custom_slug', true );
				$cpt_with_front = ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_with_front', true ) ) ? true : false;
				$cpt_feeds = ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_feeds', true ) ) ? true : false;
				$cpt_pages = ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_pages', true ) ) ? true : false;
				$cpt_ep_mask = get_post_meta( $asenha_cpt->ID, 'cpt_ep_mask', true );
				$cpt_ep_mask_custom = get_post_meta( $asenha_cpt->ID, 'cpt_ep_mask_custom', true );

				if ( $cpt_rewrite ) {
					if ( $cpt_use_custom_rewrite_slug || $cpt_with_front || $cpt_pages || $cpt_ep_mask ) {
						$rewrite = array(
							'slug'			=> ( $cpt_use_custom_rewrite_slug ) ? $cpt_rewrite_custom_slug : $post_type_key,
							'with_front'	=> $cpt_with_front,
							'feeds'			=> $cpt_feeds,
							'pages'			=> $cpt_pages,
						);
						if ( $cpt_ep_mask && ! empty( $cpt_ep_mask_custom ) ) {
							$rewrite['ep_mask'] = $cpt_ep_mask_custom;
						}
					} else {
						$rewrite = true;
					}
				} else {
					$rewrite = false;
				}
				
				// Supports
				
				if ( in_array( 'none', get_post_meta( $asenha_cpt->ID, 'cpt_supports', true ) ) ) {
					$supports = false;
				} else {
					$supports = get_post_meta( $asenha_cpt->ID, 'cpt_supports', true );
				}

				$args = array(
					'labels'				=> $labels,
					'public'				=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_public', true ) ) ? true : false,
					'hierarchical'			=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_hierarchical', true ) ) ? true : false,
					'capability_type'		=> $capability_type,
					'map_meta_cap'			=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_map_meta_cap', true ) ) ? true : NULL,
					'supports'				=> $supports,
					'taxonomies'			=> get_post_meta( $asenha_cpt->ID, 'cpt_taxonomies', true ),
					'show_ui'				=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_show_ui', true ) ) ? true : false,
					'show_in_menu'			=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_show_in_menu', true ) ) ? true : false,
					'menu_position'			=> 5,
					'menu_icon'				=> get_post_meta( $asenha_cpt->ID, 'cpt_menu_icon', true ),
					'show_in_admin_bar'		=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_show_in_admin_bar', true ) ) ? true : false,
					'show_in_nav_menus'		=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_show_in_nav_menus', true ) ) ? true : false,
					'can_export'			=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_can_export', true ) ) ? true : false,
					'delete_with_user'		=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_delete_with_user', true ) ) ? true : false,
					'publicly_queryable'	=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_publicly_queryable', true ) ) ? true : false,
					'query_var'				=> $query_var,
					'has_archive'			=> $has_archive,
					'rewrite'				=> $rewrite,
					'show_in_rest'			=> ( '1' == get_post_meta( $asenha_cpt->ID, 'cpt_show_in_rest', true ) ) ? true : false,
				);
				
				if ( ! empty( get_post_meta( $asenha_cpt->ID, 'cpt_rest_base', true ) ) ) {
					$args['rest_base'] = get_post_meta( $asenha_cpt->ID, 'cpt_rest_base', true );
				}

				if ( ! empty( get_post_meta( $asenha_cpt->ID, 'cpt_rest_base', true ) ) ) {
					$args['rest_namespace'] = get_post_meta( $asenha_cpt->ID, 'cpt_rest_namespace', true );
				}

				if ( ! empty( get_post_meta( $asenha_cpt->ID, 'cpt_rest_base', true ) ) ) {
					$args['rest_controller_class'] = get_post_meta( $asenha_cpt->ID, 'cpt_rest_controller_class', true );
				}
												
				register_post_type( $post_type_key, $args );
			}
		}

		// ====== REGISTER CUSTOM TAXONOMIES ======
		
		// Get Taxonomy posts in wp_posts table added via Settings >> Custom Taxonomies
		$asenha_ctaxs_posts = get_posts( array(
			'numberposts'		=> -1,
			'post_type'			=> 'asenha_ctax',
			'post_status'		=> 'publish',
			'suppress_filters'	=> false,		
		) );
		
		// Get CPT posts meta and use them to register the CPTs
		if ( $asenha_ctaxs_posts ) {
			foreach( $asenha_ctaxs_posts as $asenha_ctax ) {
				
				$taxonomy_key = get_post_meta( $asenha_ctax->ID, 'ctax_key', true );

				$labels = array(
					'name'						=> get_post_meta( $asenha_ctax->ID, 'ctax_plural_name', true ),
					'singular_name'				=> get_post_meta( $asenha_ctax->ID, 'ctax_singular_name', true ),
					'menu_name'					=> get_post_meta( $asenha_ctax->ID, 'ctax_plural_name', true ),
					'search_items'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_search_items', true ),
					'popular_items'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_popular_items', true ),
					'all_items'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_all_items', true ),
					'parent_item'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_parent_item', true ),
					'parent_item_colon'			=> get_post_meta( $asenha_ctax->ID, 'ctax_label_parent_item_colon', true ),
					'name_field_description'	=> get_post_meta( $asenha_ctax->ID, 'ctax_label_name_field_description', true ),
					'slug_field_description'	=> get_post_meta( $asenha_ctax->ID, 'ctax_label_slug_field_description', true ),
					'parent_field_description'	=> get_post_meta( $asenha_ctax->ID, 'ctax_label_parent_field_description', true ),
					'desc_field_description'	=> get_post_meta( $asenha_ctax->ID, 'ctax_label_desc_field_description', true ),
					'edit_item'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_edit_item', true ),
					'view_item'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_view_item', true ),
					'update_item'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_update_item', true ),
					'add_new_item'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_add_new_item', true ),
					'new_item_name'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_new_item_name', true ),
					'separate_items_with_commas' => get_post_meta( $asenha_ctax->ID, 'ctax_label_separate_items_with_commas', true ),
					'add_or_remove_items'		=> get_post_meta( $asenha_ctax->ID, 'ctax_label_add_or_remove_items', true ),
					'choose_from_most_used'		=> get_post_meta( $asenha_ctax->ID, 'ctax_label_choose_from_most_used', true ),
					'not_found'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_not_found', true ),
					'no_terms'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_no_terms', true ),
					'filter_by_item'			=> get_post_meta( $asenha_ctax->ID, 'ctax_label_filter_by_item', true ),
					'items_list_navigation'		=> get_post_meta( $asenha_ctax->ID, 'ctax_label_items_list_navigation', true ),
					'items_list'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_items_list', true ),
					'most_used'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_most_used', true ),
					'back_to_items'				=> get_post_meta( $asenha_ctax->ID, 'ctax_label_back_to_items', true ),
					'item_link'					=> get_post_meta( $asenha_ctax->ID, 'ctax_label_item_link', true ),
					'item_link_description'		=> get_post_meta( $asenha_ctax->ID, 'ctax_label_item_link_description', true ),
				);
				
				// --- Set ARGS ---

				// Define query_var value to use
				
				$ctax_query_var = get_post_meta( $asenha_ctax->ID, 'ctax_query_var', true );
				$ctax_use_custom_query_var_string = get_post_meta( $asenha_ctax->ID, 'ctax_use_custom_query_var_string', true );
				$ctax_query_var_string = get_post_meta( $asenha_ctax->ID, 'ctax_query_var_string', true );

				if ( $ctax_query_var ) {
					if ( $ctax_use_custom_query_var_string ) {
						if ( ! empty( $ctax_query_var_string ) ) {
							$query_var = $ctax_query_var_string;
						} else {
							$query_var = true;
						}
					} else {
						$query_var = true;
					}
				} else {
					$query_var = false;
				}

				// Define rewrite value to use

				$ctax_rewrite = get_post_meta( $asenha_ctax->ID, 'ctax_rewrite', true );
				$ctax_use_custom_rewrite_slug = get_post_meta( $asenha_ctax->ID, 'ctax_use_custom_rewrite_slug', true );
				$ctax_rewrite_custom_slug = get_post_meta( $asenha_ctax->ID, 'ctax_rewrite_custom_slug', true );
				$ctax_with_front = ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_with_front', true ) ) ? true : false;
				$ctax_hierarchical_urls = ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_hierarchical_urls', true ) ) ? true : false;
				$ctax_ep_mask = get_post_meta( $asenha_ctax->ID, 'ctax_ep_mask', true );
				$ctax_ep_mask_custom = get_post_meta( $asenha_ctax->ID, 'ctax_ep_mask_custom', true );

				if ( $ctax_rewrite ) {
					if ( $ctax_use_custom_rewrite_slug || $ctax_with_front || $ctax_hierarchical_urls || $ctax_ep_mask ) {
						$rewrite = array(
							'slug'			=> ( $ctax_use_custom_rewrite_slug ) ? $ctax_rewrite_custom_slug : '',
							'with_front'	=> $ctax_with_front,
							'hierarchical'	=> $ctax_hierarchical_urls,
						);
						if ( $ctax_ep_mask && ! empty( $ctax_ep_mask_custom ) ) {
							$rewrite['ep_mask'] = $ctax_ep_mask_custom;
						}
					} else {
						$rewrite = true;
					}
				} else {
					$rewrite = false;
				}

				$args = array(
					'labels'					=> $labels,
					'public'					=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_public', true ) ) ? true : false,
					'publicly_queryable'		=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_publicly_queryable', true ) ) ? true : false,
					'hierarchical'				=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_hierarchical', true ) ) ? true : false,
					'show_ui'					=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_ui', true ) ) ? true : false,
					'show_in_menu'				=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_in_menu', true ) ) ? true : false,
					'show_in_nav_menus'			=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_in_nav_menus', true ) ) ? true : false,
					'show_tagcloud'				=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_tagcloud', true ) ) ? true : false,
					'show_in_quick_edit'		=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_in_quick_edit', true ) ) ? true : false,
					'show_admin_column'			=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_admin_column', true ) ) ? true : false,
					'capabilities'				=> array(
														'manage_terms'	=> 'manage_categories',
														'edit_terms'	=> 'manage_categories',
														'delete_terms'	=> 'manage_categories',
														'assign_terms'	=> 'edit_posts',
													),
					'query_var'					=> $query_var,
					'rewrite'					=> $rewrite,
					'update_count_callback'		=> '_update_post_term_count',
					'sort'						=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_sort', true ) ) ? true : false,
					'show_in_rest'				=> ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_show_in_rest', true ) ) ? true : false,
				);
				
				// Add default term for hierarchical taxonomies
				
				if ( ( '1' == get_post_meta( $asenha_ctax->ID, 'ctax_hierarchical', true ) ) ) {
					$args['default_term'] = array(
											'name'			=> 'Uncategorized',
											'slug'			=> 'uncategorized',
											'description'	=> 'Not yet categorized',
										);
				}
				
				// Add optional arguments related to REST API
				
				if ( ! empty( get_post_meta( $asenha_ctax->ID, 'ctax_rest_base', true ) ) ) {
					$args['ctax_rest_base'] = get_post_meta( $asenha_ctax->ID, 'ctax_rest_base', true );
				}

				if ( ! empty( get_post_meta( $asenha_ctax->ID, 'ctax_rest_namespace', true ) ) ) {
					$args['ctax_rest_namespace'] = get_post_meta( $asenha_ctax->ID, 'ctax_rest_namespace', true );
				}

				if ( ! empty( get_post_meta( $asenha_ctax->ID, 'ctax_rest_controller_class', true ) ) ) {
					$args['ctax_rest_controller_class'] = get_post_meta( $asenha_ctax->ID, 'ctax_rest_controller_class', true );
				}
				
				// Register custom taxonomy with their supported post types
				
				$supported_post_types = get_post_meta( $asenha_ctax->ID, 'ctax_post_types', true );
				
				register_taxonomy( $taxonomy_key, $supported_post_types, $args );
			}
		}

	}
	
	/**
	 * Use plural name as CPT and Custom Taxonomy title in admin table
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/wp_insert_post_data/
	 * @since 5.1.0
	 */
	public function use_plural_name_as_cpt_ctax_title__premium_only( $data ) {
		if ( ( $data['post_type'] == 'asenha_cpt' && isset( $_POST['cpt_plural_name'] ) ) 
			|| ( $data['post_type'] == 'asenha_ctax' && isset( $_POST['ctax_plural_name'] ) )
		) {
			if ( isset( $_POST['cpt_plural_name'] ) ) {
				$data['post_title'] = $_POST['cpt_plural_name'];
			} elseif ( isset( $_POST['ctax_plural_name'] ) ) {
				$data['post_title'] = $_POST['ctax_plural_name'];				
			}
		}
		
		if ( $data['post_type'] == 'options_page_config' && isset( $_POST['options_page_title'] ) ) {
				$data['post_title'] = $_POST['options_page_title'];
		}
		
		return $data;
	}

	/**
	 * Define columns to show for CPT admin table
	 *
	 * @since  5.1.0
	 */
	public function cpt_posts_define_columns__premium_only( $columns ) {

		$columns = array(
			'cb' => $columns['cb'], // Checkbox for bulk actions
			'basic' => 'Basic Info',
			'supports' => 'Supported Features',
			'taxonomies' => 'Supported Taxonomies',
			'author' => 'Author',
			'date' => 'Date',
		);
		return $columns;
	}

	/**
	 * Render custom columns content for CPT admin table
	 *
	 * @since  5.1.0
	 */
	public function cpt_posts_custom_columns_content__premium_only( $column_name, $id ) {
		
		$edit_link = get_edit_post_link();
		$singular_name = get_post_meta( get_the_ID(), 'cpt_singular_name', true );
		$plural_name = get_post_meta( get_the_ID(), 'cpt_plural_name', true );
		$description = get_post_meta( get_the_ID(), 'cpt_description', true );
		$description_separator = ( ! empty( get_post_meta( get_the_ID(), 'cpt_description', true ) ) ) ? '<br />' : '';
		$key = get_post_meta( get_the_ID(), 'cpt_key', true );
		$public = ( get_post_meta( get_the_ID(), 'cpt_public', true ) ) ? 'Public' : 'Not public';
		$hierarchical = ( get_post_meta( get_the_ID(), 'cpt_hierarchical', true ) ) ? ' | Hierarchical' : ' | Not hierarchical';

		$supports_raw = get_post_meta( get_the_ID(), 'cpt_supports', true );
		$supports = '';
		$separator = ', ';
		if ( in_array( 'none', $supports_raw ) ) {
			$supports = 'None';
		} else {
			foreach( $supports_raw as $support_raw ) {
				if ( $support_raw == 'title' ) {
					$supports .= 'Title' . $separator;
				}
				if ( $support_raw == 'editor' ) {
					$supports .= 'Editor' . $separator;
				}
				if ( $support_raw == 'author' ) {
					$supports .= 'Author' . $separator;
				}
				if ( $support_raw == 'thumbnail' ) {
					$supports .= 'Featured Image' . $separator;
				}
				if ( $support_raw == 'excerpt' ) {
					$supports .= 'Excerpt' . $separator;
				}
				if ( $support_raw == 'trackbacks' ) {
					$supports .= 'Trackback' . $separator;
				}
				if ( $support_raw == 'custom-fields' ) {
					$supports .= 'Custom Fields' . $separator;
				}
				if ( $support_raw == 'comments' ) {
					$supports .= 'Comments' . $separator;
				}
				if ( $support_raw == 'revisions' ) {
					$supports .= 'Revisions' . $separator;
				}
				if ( $support_raw == 'page-attributes' ) {
					$supports .= 'Page Attributes' . $separator;
				}
				if ( $support_raw == 'post-formats' ) {
					$supports .= 'Post Formats' . $separator;
				}
			}
		}
		$supports = trim( trim( $supports ), ',' );
		
		$taxonomies = get_taxonomies( array(), 'objects' );
		$supported_taxonomies_slugs = array();
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy_object  ) {
			foreach( $taxonomy_object->object_type as $object_type_slug ) {
				if ( $key == $object_type_slug ) {
					$supported_taxonomies_slugs[] = $taxonomy_slug;
				}
			}
		}
		
		$taxonomies = '';
		$taxonomy_page_link_suffix = ( ! in_array( $key, array( 'post', 'page' ) ) ) ? '&post_type=' . $key : '' ;
		if ( ! empty( $supported_taxonomies_slugs ) ) {
			foreach( $supported_taxonomies_slugs as $taxonomy_slug ) {
				$taxonomy = get_taxonomy( $taxonomy_slug );
				$taxonomies .= '<a href="/wp-admin/edit-tags.php?taxonomy=' . $taxonomy_slug . $taxonomy_page_link_suffix . '">' . $taxonomy->labels->name . '</a>' . $separator;
			}
			$taxonomies = trim( trim( $taxonomies ), ',' );
		} else {
			$taxonomies = 'None supported yet.';
		}

		if ( $column_name === 'basic' ) {
			echo '<a href="' . esc_attr( $edit_link ) . '"><strong>' . esc_html( $plural_name ) . '</strong></a> (' . esc_html( $key ) . ')<br />' . esc_html( $description ) . $description_separator . esc_html( $public ) . esc_html( $hierarchical ) ;
		}
		if ( $column_name === 'description' ) {
			echo get_post_meta( get_the_ID(), 'cpt_description', true );
		}
		if ( $column_name === 'supports' ) {
			echo esc_html( $supports );
		}
		if ( $column_name === 'taxonomies' ) {
			echo $taxonomies;
		}
	}

	/**
	 * Define columns to show for custom taxonomies admin table
	 *
	 * @since  5.1.0
	 */
	public function ctax_posts_define_columns__premium_only( $columns ) {

		$columns = array(
			'cb' => $columns['cb'], // Checkbox for bulk actions
			'basic' => 'Basic Info',
			'post_types' => 'Supported Post Types',
			'author' => 'Author',
			'date' => 'Date',
		);
		return $columns;
	}

	/**
	 * Render custom columns content for CPT admin table
	 *
	 * @since  5.1.0
	 */
	public function ctax_posts_custom_columns_content__premium_only( $column_name, $id ) {
		
		$edit_link = get_edit_post_link();
		$singular_name = get_post_meta( get_the_ID(), 'ctax_singular_name', true );
		$plural_name = get_post_meta( get_the_ID(), 'ctax_plural_name', true );
		$description = get_post_meta( get_the_ID(), 'ctax_description', true );
		$description_separator = ( ! empty( get_post_meta( get_the_ID(), 'ctax_description', true ) ) ) ? '<br />' : '';
		$key = get_post_meta( get_the_ID(), 'ctax_key', true );
		$public = ( get_post_meta( get_the_ID(), 'ctax_public', true ) ) ? 'Public' : 'Not public';
		$hierarchical = ( get_post_meta( get_the_ID(), 'ctax_hierarchical', true ) ) ? ' | Hierarchical' : ' | Not hierarchical';
		
		$post_types_raw = get_post_meta( get_the_ID(), 'ctax_post_types', true );
		$post_types = '';
		$separator = ', ';
		if ( ! empty( $post_types_raw ) ) {
			foreach( $post_types_raw as $post_type_slug ) {
				$post_type_page_link_suffix = ( ! in_array( $post_type_slug, array( 'post', 'page' ) ) ) ? '?post_type=' . $post_type_slug : '' ;
				$post_type = get_post_type_object( $post_type_slug );
				if ( is_object( $post_type ) && property_exists( $post_type, 'label' ) ) {
					$post_type_label = $post_type->label;
				} elseif ( is_object( $post_type ) && ! is_null( $post_type->labels ) ) {
					$post_type_label = $post_type->label->name;					
				} else {
					$post_type_label = $post_type_slug;
				}
				$post_types .= '<a href="/wp-admin/edit.php' . $post_type_page_link_suffix . '">' . $post_type->labels->name . '</a>' . $separator;
			}
			$post_types = trim( trim( $post_types ), ',' );
		} else {
			$post_types = 'None supported yet.';
		}

		if ( $column_name === 'basic' ) {
			echo '<a href="' . esc_attr( $edit_link ) . '"><strong>' . esc_html( $plural_name ) . '</strong></a> (' . esc_html( $key ) . ')<br />' . esc_html( $description ) . $description_separator  . esc_html( $public ) . esc_html( $hierarchical ) ;
		}
		if ( $column_name === 'post_types' ) {
			echo $post_types;
		}
	}

	/**
	 * Remove Custom Post Types and Custom Taxonomies submenus from Settings menu
	 * 
	 * @since 5.1.0
	 */
	public function remove_cpt_ctax_submenus_for_nonadmins__premium_only() {
		global $current_user;
		$roles = array();
		foreach( $current_user->roles as $key => $role ) {
			$roles[] = $role;
		}
		if ( ! in_array( 'administrator', $roles ) ) {
			remove_submenu_page( 'options-general.php', 'edit.php?post_type=asenha_cpt' );			
			remove_submenu_page( 'options-general.php', 'edit.php?post_type=asenha_ctax' );			
		}
	}

	/**
	 * Enable duplication of pages, posts and custom posts
	 *
	 * @since 1.0.0
	 */
	public function duplicate_content() {
		$allow_duplication = false;

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			global $roles_duplication_enabled;
			if ( is_null( $roles_duplication_enabled ) ) {
				$roles_duplication_enabled = array();
			}

			$current_user = wp_get_current_user();
			$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

			if ( count( $roles_duplication_enabled ) > 0 ) {

				// Add mime type for user roles set to enable SVG upload
				foreach ( $current_user_roles as $role ) {
					if ( in_array( $role, $roles_duplication_enabled ) ) {
						// Do something here
						$allow_duplication = true;
					}
				}	

			}
        } else {
        	if ( current_user_can( 'edit_posts' ) ) {
        		$allow_duplication = true;
        	}
        }

		$original_post_id = intval( sanitize_text_field( $_REQUEST['post'] ) );
		$nonce = sanitize_text_field( $_REQUEST['nonce'] );

		if ( wp_verify_nonce( $nonce, 'asenha-duplicate-' . $original_post_id ) && $allow_duplication ) {

			$original_post = get_post( $original_post_id );
			
			$post_type = $original_post->post_type;

			// Not WooCommerce product
			
			if ( 'product' != $post_type ) {

				// Set some attributes for the duplicate post

				$new_post_title_suffix = ' (DUPLICATE)';
				$new_post_status = 'draft';
				$current_user = wp_get_current_user();
				$new_post_author_id = $current_user->ID;

				// Create the duplicate post and store the ID
				
				$args = array(

					'comment_status'	=> $original_post->comment_status,
					'ping_status'		=> $original_post->ping_status,
					'post_author'		=> $new_post_author_id,
					// We replace single backslash with double backslash, so that upon saving, it becomes single backslash again
					// This is to compensate for the default behaviour that removes single/unescaped backslashes upon saving content
					// This ensures CSS styles using var(--varname) in the Block Editor, which is saved as var(\u002d\u002varname)
					// Will not become var(u002du002dsecondary) in the duplicated post (not the missing backslash)
					'post_content'		=> str_replace( '\\', "\\\\", $original_post->post_content ),
					'post_excerpt'		=> $original_post->post_excerpt,
					'post_parent'		=> $original_post->post_parent,
					'post_password'		=> $original_post->post_password,
					'post_status'		=> $new_post_status,
					'post_title'		=> $original_post->post_title . $new_post_title_suffix,
					'post_type'			=> $original_post->post_type,
					'to_ping'			=> $original_post->to_ping,
					'menu_order'		=> $original_post->menu_order,

				);

				$new_post_id = wp_insert_post( $args );

				// Copy over the taxonomies

				$original_taxonomies = get_object_taxonomies( $original_post->post_type );

				if ( ! empty( $original_taxonomies ) && is_array( $original_taxonomies ) ) {

					foreach( $original_taxonomies as $taxonomy ) {

						$original_post_terms = wp_get_object_terms( $original_post_id, $taxonomy, array( 'fields' => 'slugs' ) );

						wp_set_object_terms( $new_post_id, $original_post_terms, $taxonomy, false );

					}

				}

				// Copy over the post meta
				
				$original_post_metas = get_post_meta( $original_post_id ); // all meta keys and the corresponding values

				if ( ! empty( $original_post_metas ) ) {

					foreach( $original_post_metas as $meta_key => $meta_values ) {

						foreach( $meta_values as $meta_value ) {

							update_post_meta( $new_post_id, $meta_key, wp_slash( maybe_unserialize( $meta_value ) ) );

						}

					}

				}
				
			}

	        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {

				// WooCommerce product

				if ( 'product' == $post_type ) {

					// WooCommerce product
					
					$WC_Duplicate = new WC_Admin_Duplicate_Product;
					$product = wc_get_product( $original_post_id );				
					$duplicate_product = $WC_Duplicate->product_duplicate( $product );

					// Update duplicated product title for consistency

					$duplicate_product_post = get_post( $duplicate_product->get_id() );

					$args = array(
						'ID'			=> $duplicate_product_post->ID,
						'post_title'	=> str_replace( '(Copy)', '(DUPLICATE)', $duplicate_product_post->post_title ),
					);

					wp_update_post( $args );

				}
				
			}

			$options = get_option( ASENHA_SLUG_U, array() );
			$duplication_redirect_destination = isset( $options['duplication_redirect_destination'] ) ? $options['duplication_redirect_destination'] : 'edit';

			switch ( $duplication_redirect_destination ) {
				case 'edit':
					// Redirect to edit screen of the duplicate post
					wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );				
					break;

				case 'list':
					// Redirect to list table of the corresponding post type of original post
					if ( 'post'	== $post_type ) {
						wp_redirect( admin_url( 'edit.php' ) );
					} else {
						wp_redirect( admin_url( 'edit.php?post_type=' . $post_type ) );
					}				
					break;
			}			
			
		} else {

			wp_die( 'You do not have permission to perform this action.' );

		}

	}

	/** 
	 * Add row action link to perform duplication in page/post list tables
	 *
	 * @since 1.0.0
	 */
	public function add_duplication_action_link( $actions, $post ) {		
		$duplication_link_locations = $this->get_duplication_link_locations();
		
		$allow_duplication = $this->is_user_allowed_to_duplicate_content();
        
		$post_type = $post->post_type;

		if ( $allow_duplication ) {
			// Not WooCommerce product
			if ( in_array( 'post-action', $duplication_link_locations ) && 'product' != $post_type ) {
				$actions['asenha-duplicate'] = '<a href="admin.php?action=duplicate_content&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'asenha-duplicate-' . $post->ID ) . '" title="Duplicate this as draft">Duplicate</a>';
			}
		}

		return $actions;
	}
	
	/**
	 * Add admin bar duplicate link
	 * 
	 * @since 6.3.0
	 */
	public function add_admin_bar_duplication_link( WP_Admin_Bar $wp_admin_bar ) {		
		$duplication_link_locations = $this->get_duplication_link_locations();

		$allow_duplication = $this->is_user_allowed_to_duplicate_content();
        
		global $pagenow, $typenow, $post;
		$inapplicable_post_types = array( 'attachment' );

		if ( $allow_duplication ) {
			if ( ( 'post.php' == $pagenow && ! in_array( $typenow, $inapplicable_post_types ) ) || is_singular() ) {
				if ( in_array( 'admin-bar', $duplication_link_locations ) ) {
					if ( is_object( $post ) ) {
						$common_methods = new Common_Methods;
						$post_type_singular_label = $common_methods->get_post_type_singular_label( $post );

	                    if ( property_exists( $post, 'ID' ) ) {
							$wp_admin_bar->add_menu( array(
								'id'    => 'duplicate-content',
								'parent' => null,
								'group'  => null,
								'title' => 'Duplicate ' . $post_type_singular_label, 
								'href'  => admin_url( 'admin.php?action=duplicate_content&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'asenha-duplicate-' . $post->ID ) ),
							) );                    	
	                    }
					}					
				}
			}
		}
		
	}

    /**
     * Add duplication link in post submit/update box
     * 
     * @since 6.9.3
     */
    public function add_submitbox_duplication_link__premium_only() {
		$duplication_link_locations = $this->get_duplication_link_locations();

		$allow_duplication = $this->is_user_allowed_to_duplicate_content();

        global $post, $pagenow;

		if ( $allow_duplication && is_object( $post ) && 'post.php' == $pagenow && in_array( 'publish-section', $duplication_link_locations ) ) {
			$common_methods = new Common_Methods;
			$post_type_singular_label = $common_methods->get_post_type_singular_label( $post );

	        $duplication_link_section = '<div class="additional-actions"><span id="duplication"><a href="admin.php?action=duplicate_content&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'asenha-duplicate-' . $post->ID ) . '" title="Duplicate this as draft">Duplicate ' . $post_type_singular_label . '</a></span></div>';
	        echo wp_kses_post( $duplication_link_section );
		}
    }
    
    /**
     * Add duplication button in the block editor
     * 
     * @since 6.9.3
     */
    public function add_gutenberg_duplication_link__premium_only() {
        global $post, $pagenow;
		$common_methods = new Common_Methods;
		$duplication_link_locations = $this->get_duplication_link_locations();

		$allow_duplication = $this->is_user_allowed_to_duplicate_content();

		if ( $allow_duplication && is_object( $post ) && 'post.php' == $pagenow && in_array( 'publish-section', $duplication_link_locations ) ) {
			// Check if we're inside the block editor. Ref: https://wordpress.stackexchange.com/a/309955.
		    if ( $common_methods->is_in_block_editor() ) {
				$post_type_singular_label = $common_methods->get_post_type_singular_label( $post );

				// Ref: https://plugins.trac.wordpress.org/browser/duplicate-page/tags/4.5/duplicatepage.php#L286
	            wp_enqueue_style( 'asenha-gutenberg-content-duplication', ASENHA_URL . 'assets/css/gutenberg-content-duplication.css' );

	            wp_register_script( 'asenha-gutenberg-content-duplication', ASENHA_URL . 'assets/js/gutenberg-content-duplication.js', array( 'wp-edit-post', 'wp-plugins', 'wp-i18n', 'wp-element' ), ASENHA_VERSION);

	            wp_localize_script( 'asenha-gutenberg-content-duplication', 'cd_params', array(
	                'cd_post_id' 		=> intval($post->ID),
	                'cd_nonce' 			=> wp_create_nonce( 'asenha-duplicate-' . $post->ID ),
	                'cd_post_text' 		=> 'Duplicate ' . $post_type_singular_label,
	                'cd_post_title'		=> 'Duplicate this as draft',
	                'cd_duplicate_link' => "admin.php?action=duplicate_content"
	                )
	            );

	            wp_enqueue_script( 'asenha-gutenberg-content-duplication' );
		    }
        }
    }

	/**
	 * Check at which locations duplication link should enabled
	 * 
	 * @since 6.9.3
	 */
	public function get_duplication_link_locations() {
		$options = get_option( ASENHA_SLUG_U, array() );
		
		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$duplication_link_locations = isset( $options['enable_duplication_link_at'] ) ? $options['enable_duplication_link_at'] : array( 'post-action', 'admin-bar', 'publish-section' );		
		} else {
			$duplication_link_locations = array( 'post-action', 'admin-bar' );
		}

		return $duplication_link_locations;
	}

    /**
     * Check if a user role is allowed to duplicate content
     * 
     * @since 6.9.3
     */
    public function is_user_allowed_to_duplicate_content() {
		$allow_duplication = false;

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			global $roles_duplication_enabled;
			if ( is_null( $roles_duplication_enabled ) ) {
				$roles_duplication_enabled = array();
			}

			$current_user = wp_get_current_user();
			$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

			if ( count( $roles_duplication_enabled ) > 0 ) {

				// Add mime type for user roles set to enable SVG upload
				foreach ( $current_user_roles as $role ) {
					if ( in_array( $role, $roles_duplication_enabled ) ) {
						// Do something here
						$allow_duplication = true;
					}
				}	

			}
        } else {
        	if ( current_user_can( 'edit_posts' ) ) {
        		$allow_duplication = true;
        	}
        }
        
        return $allow_duplication;
    }
    
	/**
	 * Modify the 'Edit' link to be 'Edit or Replace'
	 * 
	 */
	public function modify_media_list_table_edit_link( $actions, $post ) {

		$new_actions = array();

		foreach( $actions as $key => $value ) {

			if ( $key == 'edit' ) {

				$new_actions['edit'] = '<a href="' . get_edit_post_link( $post ) . '" aria-label="Edit or Replace">Edit or Replace</a>';

			} else {

				$new_actions[$key] = $value;

			}

		}

		return $new_actions;

	}
	
	/** 
	 * Add "Custom Order" sub-menu for post types
	 * 
	 * @since 5.0.0
	 */
	public function add_content_order_submenu( $context ) {
		$options = get_option( ASENHA_SLUG_U, array() );
		$content_order_for = $options['content_order_for'];
		$content_order_enabled_post_types = array();
		
		foreach ( $options['content_order_for'] as $post_type_slug => $is_custom_order_enabled ) {
			if ( $is_custom_order_enabled ) {
				$post_type_object = get_post_type_object( $post_type_slug );

				if ( is_object( $post_type_object ) && property_exists( $post_type_object, 'labels' ) ) {
					$post_type_name_plural = $post_type_object->labels->name;
					if ( 'post' == $post_type_slug ) {
						$hook_suffix = add_posts_page(
							$post_type_name_plural . ' Order', // Page title
							'Order', // Menu title
							'edit_pages', // Capability required
							'custom-order-posts', // Menu and page slug
							[ $this, 'custom_order_page_output' ] // Callback function that outputs page content
						);
					} else {
						$hook_suffix = add_submenu_page(
							'edit.php?post_type=' . $post_type_slug, // Parent (menu) slug. Ref: https://developer.wordpress.org/reference/functions/add_submenu_page/#comment-1404
							$post_type_name_plural . ' Order', // Page title
							'Order', // Menu title
							'edit_pages', // Capability required
							'custom-order-' . $post_type_slug, // Menu and page slug
							[ $this, 'custom_order_page_output' ],  // Callback function that outputs page content
							9999 // position
						);
					}

					add_action( 'admin_print_styles-' . $hook_suffix, [ $this, 'enqueue_content_order_styles' ] );
					add_action( 'admin_print_scripts-' . $hook_suffix, [ $this, 'enqueue_content_order_scripts' ] );					
				}
			}
		}		
	}
	
	/**
	 * Output content for the custom order page for each enabled post types
	 * Not using settings API because all done via AJAX
	 * 
	 * @since 5.0.0
	 */
	public function custom_order_page_output() {

		$parent_slug = get_admin_page_parent();
		if ( 'edit.php' == $parent_slug ) {
			$post_type_slug = 'post';
		} else {
			$post_type_slug = str_replace( 'edit.php?post_type=', '', $parent_slug );
		}

		// Object with properties for each post status and the count of posts for each status
		// $post_count_object = wp_count_posts( $post_type_slug );

		// Number of items with the status 'publish(ed)', 'future' (scheduled), 'draft', 'pending' and 'private'
		// $post_count = absint( $post_count_object->publish )
		// 			  + absint( $post_count_object->future )
		// 			  + absint( $post_count_object->draft )
		// 			  + absint( $post_count_object->pending )
		// 			  + absint( $post_count_object->private );
		?>
		<div class="wrap">
			<div class="page-header">
				<h2>
					<?php
						echo get_admin_page_title();
					?>
				</h2>
				<div id="toggles" style="display:none;">
					<input type="checkbox" id="toggle-taxonomy-terms" name="terms" value="" /><label for="toggle-taxonomy-terms">Show taxonomy terms</label>
					<input type="checkbox" id="toggle-excerpt" name="excerpt" value="" /><label for="toggle-excerpt">Show excerpt</label>
				</div>
			</div>
		<?php
		// Get posts
		$query = new WP_Query( array(
				'post_type'			=> $post_type_slug,
				'posts_per_page'	=> -1, // Get all posts
				'orderby'			=> 'menu_order title', // By menu order then by title
				'order'				=> 'ASC',
				'post_status'		=> array( 'publish', 'future', 'draft', 'pending', 'private' ),
				'post_parent'		=> 0, // In hierarchical post types, only return top-level / parent posts
		) );

		if ( $query->have_posts() ) {
			?>
			<ul id="item-list">
				<?php
				while( $query->have_posts() ) {
					$query->the_post();
					$post = get_post( get_the_ID() );
					$this->custom_order_single_item_output( $post );
				}
				?>
			</ul>
			<div id="updating-order-notice" class="updating-order-notice" style="display: none;"><img src="<?php echo ASENHA_URL . 'assets/img/oval.svg'; ?>" id="spinner-img" class="spinner-img" /><span class="dashicons dashicons-saved" style="display:none;"></span>Updating order...</div>
			<?php
		} else {
			?>
			<h3>There is nothing to sort for this post type.</h3>
			<?php
		}
		?>
		</div> <!-- End of div.wrap -->
		<?php
		wp_reset_postdata();
	}
	
	/**
	 * Output single item sortable for custom content order
	 * 
	 * @since 5.0.0
	 */
	private function custom_order_single_item_output( $post ) {
		if ( is_post_type_hierarchical( $post->post_type ) ) {
			$post_type_object = get_post_type_object( $post->post_type );

			$children = get_pages( array( 
				'child_of'	=> $post->ID, 
				'post_type'	=> $post->post_type,
			) );

			if ( count( $children ) > 0 ) {
				$has_child_label = '<span class="has-child-label"> <span class="dashicons dashicons-arrow-right"></span> Has child ' . strtolower( $post_type_object->label ) . '</span>';
				$has_child = 'true';
			} else {
				$has_child_label = '';						
				$has_child = 'false';
			}						
		} else {
			$has_child_label = '';
			$has_child = 'false';
		}

		$post_status_label_class = ( $post->post_status == 'publish' ) ? ' item-status-hidden' : '';
		$post_status_object = get_post_status_object( $post->post_status );

		if ( empty( wp_trim_excerpt( '', $post ) ) ) {
			$short_excerpt = '';
		} else {
			$excerpt_trimmed = implode(" ", array_slice( explode( " ", wp_trim_excerpt( '', $post ) ), 0, 30 ) );
			$short_excerpt = '<span class="item-excerpt"> | ' . $excerpt_trimmed . '</span>';			
		}

		$taxonomies = get_object_taxonomies( $post->post_type, 'objects' );
		// vi( $taxonomies );
		$taxonomies_and_terms = '';
		foreach( $taxonomies as $taxonomy ) {
			$terms = array();
			if ( $taxonomy->hierarchical ) {
				$taxonomy_terms = get_the_terms( $post->ID, $taxonomy->name );
				if ( is_array( $taxonomy_terms ) && ! empty( $taxonomy_terms ) ) {
					foreach( $taxonomy_terms as $term ) {
						$terms[] = $term->name;
					}					
				}
			}
			$terms = implode( ', ', $terms );
			$taxonomies_and_terms .= ' | ' . $taxonomy->label . ': ' . $terms;								
		}
		if ( ! empty( $taxonomies_and_terms ) ) {
			$taxonomies_and_terms = '<span class="item-taxonomy-terms">' . $taxonomies_and_terms . '</span>';
		}
		
		?>
		<li id="list_<?php echo $post->ID; ?>" data-id="<?php echo $post->ID; ?>" data-menu-order="<?php echo $post->menu_order; ?>" data-parent="<?php echo $post->post_parent; ?>" data-has-child="<?php echo $has_child; ?>" data-post-type="<?php echo $post->post_type; ?>">
			<div class="row">
				<div class="row-content">
					<?php 
					echo    '<div class="content-main">
								<span class="dashicons dashicons-menu"></span><a href="' . get_edit_post_link( $post->ID ) . '" class="item-title">' . $post->post_title . '</a><span class="item-status' . $post_status_label_class . '"> — ' . $post_status_object->label . '</span>' . $has_child_label . wp_kses_post( $taxonomies_and_terms ) . wp_kses_post( $short_excerpt ) . '<div class="fader"></div>
							</div>
							<div class="content-additional">
								<a href="' . get_the_permalink( $post->ID ) . '" target="_blank" class="button item-view-link">View</a>
							</div>';
					?>
				</div>
			</div>
		</li>
		<?php
	}
	
	/**
	 * Enqueue styles for content order pages
	 * 
	 * @since 5.0.0
	 */
	public function enqueue_content_order_styles() {
		wp_enqueue_style( 
			'content-order-style', 
			ASENHA_URL . 'assets/css/content-order.css', 
			array(), 
			ASENHA_VERSION 
		);
	}

	/**
	 * Enqueue scripts for content order pages
	 * 
	 * @since 5.0.0
	 */
	public function enqueue_content_order_scripts() {
		global $typenow;
		wp_enqueue_script( 
			'content-order-jquery-ui-touch-punch', 
			ASENHA_URL . 'assets/js/jquery.ui.touch-punch.min.js', 
			array( 'jquery-ui-sortable' ), 
			'0.2.3', 
			true 
		);
		wp_register_script( 
			'content-order-nested-sortable', 
			ASENHA_URL . 'assets/js/jquery.mjs.nestedSortable.js', 
			array( 'content-order-jquery-ui-touch-punch' ), 
			'2.0.0', 
			true 
		);
		wp_enqueue_script( 
			'content-order-sort', 
			ASENHA_URL . 'assets/js/content-order-sort.js', 
			array( 'content-order-nested-sortable' ), 
			ASENHA_VERSION, 
			true 
		);
		wp_localize_script(
			'content-order-sort',
			'contentOrderSort',
			array(
				'action'		=> 'save_custom_order',
				'nonce'			=> wp_create_nonce( 'order_sorting_nonce' ),
				'hirarchical'	=> is_post_type_hierarchical( $typenow ) ? 'true' : 'false',
			)
		);
	}
	
	/**
	 * Save custom content order coming from ajax call
	 * 
	 * @since 5.0.0
	 */
	public function save_custom_content_order() {
		global $wpdb;
		
		// Check user capabilities
		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json( 'Something went wrong.' );
		}
		
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'order_sorting_nonce' ) ) {
			wp_send_json( 'Something went wrong.' );
		}
		
		// Get ajax variables
		$action = isset( $_POST['action'] ) ? $_POST['action'] : '' ;
		$item_parent = isset( $_POST['item_parent'] ) ? absint( $_POST['item_parent'] ) : 0 ;
		$menu_order_start = isset( $_POST['start'] ) ? absint( $_POST['start'] ) : 0 ;
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0 ;
		$item_menu_order = isset( $_POST['menu_order'] ) ? absint( $_POST['menu_order'] ) : 0 ;
		$items_to_exclude = isset( $_POST['excluded_items'] ) ? absint( $_POST['excluded_items'] ) : array();
		$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : false ;
		
		// Make processing faster by removing certain actions
		remove_action( 'pre_post_update', 'wp_save_post_revision' );
		
		// $response array for ajax response
		$response = array();

		// Update the item whose order/position was moved
		if ( $post_id > 0 && ! isset( $_POST['more_posts'] ) ) {
			// https://developer.wordpress.org/reference/classes/wpdb/update/
			$wpdb->update(
				$wpdb->posts, // The table
				array( // The data
					'menu_order' 	=> $item_menu_order,
				),
				array( // The post ID
					'ID'			=> $post_id
				)
			);
			clean_post_cache( $post_id );
			$items_to_exclude[] = $post_id;
		}
		
		// Get all posts from the post type related to ajax request
		$query_args = array(
			'post_type'					=> $post_type,
			'orderby'					=> 'menu_order title',
			'order'						=> 'ASC',
			'posts_per_page'			=> -1, // Get all posts
			'suppress_filters'			=> true,
			'ignore_sticky_posts'		=> true,
			'post_status'				=> array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'post_parent'				=> $item_parent,
			'post__not_in'				=> $items_to_exclude,
			'update_post_term_cache'	=> false, // Speed up processing by not updating term cache
			'update_post_meta_cache'	=> false, // Speed up processing by not updating meta cache
		);
		
		$posts = new WP_Query( $query_args );
						
		if ( $posts->have_posts() ) {
			// Iterate through posts and update menu order and post parent
			foreach ( $posts->posts as $post ) {
				// If the $post is the one being displaced (shited downward) by the moved item, increment it's menu_order by one
				if ( $menu_order_start == $item_menu_order && $post_id > 0 ) {
					$menu_order_start++;
				}
				
				// Only process posts other than the moved item, which has been processed earlier outside this loop
				if ( $post_id != $post->ID ) {
					// Update menu_order
					$wpdb->update(
						$wpdb->posts,
						array(
							'menu_order'	=> $menu_order_start,
						),
						array(
							'ID'			=> $post->ID
						)
					);
					clean_post_cache( $post->ID );
				}
				
				$items_to_exclude[] = $post->ID;
				$menu_order_start++;
			}
			die( json_encode( $response ) );
		} else {
			die( json_encode( $response ) );
		}
	}

	/**
	 * Set default ordering of list tables of sortable post types by 'menu_order'
	 * 
	 * @link https://developer.wordpress.org/reference/classes/wp_query/#methods
	 * @since 5.0.0
	 */
	public function orderby_menu_order( $query ) {
		global $pagenow, $typenow;

		$options = get_option( ASENHA_SLUG_U, array() );
		$content_order_for = $options['content_order_for'];
		$content_order_enabled_post_types = array();
		foreach ( $options['content_order_for'] as $post_type_slug => $is_custom_order_enabled ) {
			if ( $is_custom_order_enabled ) {
				$content_order_enabled_post_types[] = $post_type_slug;
			}
		}
		
		// Use custom order in wp-admin listing pages/tables for enabled post types
		if ( is_admin() && 'edit.php' == $pagenow && ! isset( $_GET['orderby'] ) ) {
			if ( in_array( $typenow, $content_order_enabled_post_types ) 
				&& ( post_type_supports( $typenow, 'page-attributes' ) || is_post_type_hierarchical( $typenow ) ) 
			) {
			    $query->set( 'orderby', 'menu_order title' );
			    $query->set( 'order', 'ASC' );				
			}
		}
		
		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Use custom order in the frontend for enabled post types
			$content_order_frontend = isset( $options['content_order_frontend'] ) ? $options['content_order_frontend'] : false;
			if ( $content_order_frontend && ! is_admin() && ! $query->is_search() ) {
				if ( $query->is_main_query() ) {
					// On post types archive pages
					if ( $query->is_post_type_archive()
						&& in_array( $query->get('post_type'), $content_order_enabled_post_types ) 
					) {
					    $query->set( 'orderby', 'menu_order title' );
					    $query->set( 'order', 'ASC' );
					}
				} else {
					// On secondary queries
					if ( in_array( $query->get('post_type'), $content_order_enabled_post_types ) ) {
					    $query->set( 'orderby', 'menu_order title' );
					    $query->set( 'order', 'ASC' );						
					}
				}
			}				 
		}
	}

	/**
	 * Make sure newly created posts are assigned the highest menu_order so it's added at the bottom of the existing order
	 * 
	 * @since 6.2.1
	 */
	public function set_menu_order_for_new_posts( $post_id, $post, $update ) {
		$options = get_option( ASENHA_SLUG_U, array() );
		$content_order_for = $options['content_order_for'];
		$content_order_enabled_post_types = array();
		foreach ( $options['content_order_for'] as $post_type_slug => $is_custom_order_enabled ) {
			if ( $is_custom_order_enabled ) {
				$content_order_enabled_post_types[] = $post_type_slug;
			}
		}

		// Only assign menu_order if there are none assigned when creating the post, i.e. menu_order is 0
		if ( in_array( $post->post_type, $content_order_enabled_post_types ) 
			// New posts most likely are immediately assigned the auto-draft status
			&& ( 'auto-draft' == $post->post_status || 'publish' == $post->post_status )
			&& $post->menu_order == '0'
			&& false === $update
		) {
			$post_with_highest_menu_order = get_posts( array(
				'post_type'			=> $post->post_type,
				'posts_per_page'	=> 1,
				'orderby'			=> 'menu_order',
				'order'				=> 'DESC',
				// 'fields'			=> 'ids', // return post IDs instead of objects
			) );
		
			if ( $post_with_highest_menu_order ) {
				$new_menu_order = (int) $post_with_highest_menu_order[0]->menu_order + 1;
				
				// Assign the one higher menu_order to the new post
				$args = array(
					'ID'			=> $post_id,
					'menu_order'	=> $new_menu_order,
				);
				wp_update_post( $args );				
			}
		}
		
	}
		
	/**
	 * Add media replacement button in the edit screen of media/attachment
	 *
	 * @since 1.1.0
	 */
	public function add_media_replacement_button( $fields, $post ) {
		global $post, $pagenow, $typenow;

		// Do not do this on post creation and editing screen
		// May cause media frame layout / display issues
		if ( 'attachment' == $typenow ||
			( 'attachment' != $typenow && 'post-new.php' != $pagenow && 'post.php' != $pagenow )		
			) {
			$image_mime_type = '';
			if ( is_object( $post ) ) {
				if ( property_exists( $post, 'post_mime_type' ) ) {
					$image_mime_type = $post->post_mime_type;		
				}
			}
					
			// Enqueues all scripts, styles, settings, and templates necessary to use all media JS APIs.
			// Reference: https://codex.wordpress.org/Javascript_Reference/wp.media
			wp_enqueue_media();

			// Add new field to attachment fields for the media replace functionality
			$fields['asenha-media-replace'] = array();
			$fields['asenha-media-replace']['label'] = '';
			$fields['asenha-media-replace']['input'] = 'html';
			$fields['asenha-media-replace']['html'] = '
				<div id="media-replace-div" class="postbox">
					<div class="postbox-header">
						<h2 class="hndle ui-sortable-handle">Replace Media</h2>
					</div>
					<div class="inside">
					<button type="button" id="asenha-media-replace" class="button-secondary button-large asenha-media-replace-button" data-old-image-mime-type="' . $image_mime_type . '" onclick="replaceMedia(\'' . $image_mime_type . '\');">Select New Media File</button>
					<input type="hidden" id="new-attachment-id" name="new-attachment-id" />
					<div class="asenha-media-replace-notes"><p>The current file will be replaced with the uploaded / selected file (of the same type) while retaining the current ID, publish date and file name. Thus, no existing links will break.</p></div>
					</div>
				</div>
			';
		}

		return $fields;

	}
	
	public function attachment_for_js( $image_url, $attachment_id ) {
		// vi( $image_url );
		// vi( $attachment_id );
	}

	/**
	 * Replace existing media with the newly updated file
	 *
	 * @link https://plugins.trac.wordpress.org/browser/replace-image/tags/1.1.7/hm-replace-image.php#L55
	 * @since 1.1.0
	 */
	public function replace_media( $old_attachment_id ) {

		$old_post_meta = get_post( $old_attachment_id, ARRAY_A );
		$old_post_mime = $old_post_meta['post_mime_type']; // e.g. 'image/jpeg'

		// Get the new attachment/media ID, meta and mime type
		if ( isset( $_POST['new-attachment-id'] ) && ! empty( $_POST['new-attachment-id'] ) ) {
			$new_attachment_id = intval( sanitize_text_field( $_POST['new-attachment-id'] ) );
			$new_post_meta = get_post( $new_attachment_id, ARRAY_A );
			$new_post_mime = $new_post_meta['post_mime_type']; // e.g. 'image/jpeg'
		}

		// Check if the media file ID selected via the media frame and passed on to the #new-attachment-id hidden field
		// Ensure the mime type matches too
		if ( ( ! empty( $new_attachment_id ) ) && is_numeric( $new_attachment_id ) && ( $old_post_mime == $new_post_mime ) ) {

			$new_attachment_meta = wp_get_attachment_metadata( $new_attachment_id );

			// If original file is larger than 2560 pixel
			// https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
			if ( array_key_exists( 'original_image', $new_attachment_meta ) ) {

				// Get the original media file path
				$new_media_file_path = wp_get_original_image_path( $new_attachment_id );

			} else {

				// Get the path to newly uploaded media file. An image file name may end with '-scaled'.
				$new_attachment_file = get_post_meta( $new_attachment_id, '_wp_attached_file', true );
				$upload_dir = wp_upload_dir();
				$new_media_file_path = $upload_dir['basedir'] . '/' . $new_attachment_file;

			}

			// Check if the new media file exist / was successfully uploaded
			if ( ! is_file( $new_media_file_path ) ) {
				return false;
			}

			// Delete existing/old media files. Post and post meta entries for it are still there in the database.
			$this->delete_media_files( $old_attachment_id );

			// If original file is larger than 2560 pixel
			// https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
			if ( array_key_exists( 'original_image', $new_attachment_meta ) ) {

				// Get the original media file path
				$old_media_file_path = wp_get_original_image_path( $old_attachment_id );

			} else {

				// Get the path to the old/existing media file that will be replaced and deleted. An image file name may end with '-scaled'.
				$old_attachment_file = get_post_meta( $old_attachment_id, '_wp_attached_file', true );
				$old_media_file_path = $upload_dir['basedir'] . '/' . $old_attachment_file;

			}

			// Check if the directory path to the old media file is still intact
			if ( ! file_exists( dirname( $old_media_file_path ) ) ) {

				// Recreate the directory path
				mkdir( dirname( $old_media_file_path ), 0755, true );

			}

			// Copy the new media file into the old media file's path
			copy( $new_media_file_path, $old_media_file_path );

			// Regenerate attachment meta data and image sub-sizes from the new media file that was just copied to the old path
			$old_media_post_meta_updated = wp_generate_attachment_metadata( $old_attachment_id, $old_media_file_path );

			// Update new media file's meta data with the ones from the old media. i.e. new media file will carry over the post ID and post meta of the old media file. i.e. only the files are replaced for the old media's ID and post meta in the database.
			wp_update_attachment_metadata( $old_attachment_id, $old_media_post_meta_updated );

			// Delete the newly uploaded media file and it's sub-sizes, and also delete post and post meta entries for it in the database.
			wp_delete_attachment( $new_attachment_id, true );
			
			// Add old attachment ID to recently replaced media option. This will be used for cache busting to ensure the new replacement images are immediately loaded in the browser / wp-admin
			$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
			$recently_replaced_media = isset( $options_extra['recently_replaced_media'] ) ? $options_extra['recently_replaced_media'] : array();
			$max_media_number_to_cache_bust = 5;
			if ( count( $recently_replaced_media ) >= $max_media_number_to_cache_bust ) {
				// Remove first/oldest attachment ID
				array_shift( $recently_replaced_media );
			}
			$recently_replaced_media[] = $old_attachment_id;
			$recently_replaced_media = array_unique( $recently_replaced_media );
			$options_extra['recently_replaced_media'] = $recently_replaced_media;
			update_option( 'admin_site_enhancements_extra', $options_extra );

		}

	}

	/**
	 * Delete the existing/old media files when performing media replacement
	 *
	 * @link https://plugins.trac.wordpress.org/browser/replace-image/tags/1.1.7/hm-replace-image.php#L80
	 * @since 1.1.0
	 */
	public function delete_media_files( $post_id ) {

		$attachment_meta = wp_get_attachment_metadata( $post_id );

		// Will get '-scaled' version if it exists, e.g. /path/to/uploads/year/month/file-name.jpg
		$attachment_file_path = get_attached_file( $post_id ); 

		// e.g. file-name.jpg
		$attachment_file_basename = basename( $attachment_file_path );

		// Delete intermediate images if there are any
		
		if ( isset( $attachment_meta['sizes'] ) && is_array( $attachment_meta['sizes'] ) ) {

			foreach( $attachment_meta['sizes'] as $size => $size_info) {

				// /path/to/uploads/year/month/file-name.jpg --> /path/to/uploads/year/month/file-name-1024x768.jpg
				$intermediate_file_path = str_replace( $attachment_file_basename, $size_info['file'], $attachment_file_path );
				wp_delete_file( $intermediate_file_path );

			}

		}

		// Delete the attachment file, which maybe the '-scaled' version
		wp_delete_file( $attachment_file_path );

		// If original file is larger than 2560 pixel
		// https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/
		if ( array_key_exists( 'original_image', $attachment_meta ) ) {

			$attachment_original_file_path = wp_get_original_image_path( $post_id );

			// Delete the original file
			wp_delete_file( $attachment_original_file_path );

		}

	}

	/**
	 * Customize the attachment updated message
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/6.0.2/src/wp-admin/edit-form-advanced.php#L180
	 * @since 1.1.0
	 */
	public function attachment_updated_custom_message( $messages ) {

		$new_messages = array();

		foreach( $messages as $post_type => $messages_array ) {

			if ( $post_type == 'attachment' ) {

				// Message ID for successful edit/update of an attachment is 4. e.g. /wp-admin/post.php?post=a&action=edit&classic-editor&message=4 Customize it here.
				$messages_array[4] = 'Media file updated. You may need to <a href="https://fabricdigital.co.nz/blog/how-to-hard-refresh-your-browser-and-clear-cache" target="_blank">hard refresh</a> your browser to see the updated media preview image below.';

			}

			$new_messages[$post_type] = $messages_array;

		}

		return $new_messages;

	}
	
	/**
	 * Append cache busting parameter to the end of image srcset
	 * 
	 * @since 5.7.0
	 */
	public function append_cache_busting_param_to_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
		$recently_replaced_media = isset( $options_extra['recently_replaced_media'] ) ? $options_extra['recently_replaced_media'] : array();

		if ( in_array( $attachment_id, $recently_replaced_media ) ) {
			foreach ( $sources as $size => $source ) {
				$source['url'] .= ( false === strpos($source['url'], '?' ) ? '?' : '&' ) . 't=' . time();
				$sources[$size] = $source;
			}
		}
		return $sources;
	}

	/**
	 * Append cache busting parameter to the end of image src
	 * 
	 * @since 5.7.0
	 */
	public function append_cache_busting_param_to_attachment_image_src( $image, $attachment_id ) {
		$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
		$recently_replaced_media = isset( $options_extra['recently_replaced_media'] ) ? $options_extra['recently_replaced_media'] : array();

		if ( ! empty( $image[0] ) && in_array( $attachment_id, $recently_replaced_media ) ) {
			$image[0] .= ( false === strpos($image[0], '?') ? '?' : '&' ) . 't=' . time();
		}

		return $image;
	}

	/**
	 * Append cache busting parameter to image src for js
	 * 
	 * @since 5.7.0
	 */
	public function append_cache_busting_param_to_attachment_for_js( $response, $attachment ) {
		$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
		$recently_replaced_media = isset( $options_extra['recently_replaced_media'] ) ? $options_extra['recently_replaced_media'] : array();

		if ( in_array( $attachment->ID, $recently_replaced_media ) ) {
			if ( false !== strpos( $response['url'], '?' ) ) {
				$response['url'] .= ( false === strpos( $response['url'], '?' ) ? '?' : '&' ) . 't=' . time();
			}
			if ( isset( $response['sizes'] ) ) {
				foreach ( $response['sizes'] as $size_name => $size ) {
					$response['sizes'][$size_name]['url'] .= ( false === strpos( $size['url'], '?' ) ? '?' : '&' ) . 't=' . time();
				}
			}
		}

		return $response;		
	}
	
	/**
	 * Append cache busting parameter to attachment URL
	 * 
	 * @since 6.8.2
	 */
	public function append_cache_busting_param_to_attachment_url( $url, $attachment_id ) {
		$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
		$recently_replaced_media = isset( $options_extra['recently_replaced_media'] ) ? $options_extra['recently_replaced_media'] : array();

		if ( in_array( $attachment_id, $recently_replaced_media ) ) {
			// if ( false !== strpos( $url, '?' ) ) {
				$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . 't=' . time();
			// }
		}

		return $url;
	}
	
	/**
	 * Add SVG mime type for media library uploads
	 *
	 * @link https://developer.wordpress.org/reference/hooks/upload_mimes/
	 * @since 2.6.0
	 */
	public function add_svg_mime( $mimes ) {

		global $roles_svg_upload_enabled;

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		if ( count( $roles_svg_upload_enabled ) > 0 ) {

			// Add mime type for user roles set to enable SVG upload
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_svg_upload_enabled ) ) {
					$mimes['svg'] = 'image/svg+xml';
				}
			}	

		}

		return $mimes;

	}

	/**
	 * Check and confirm if the real file type is indeed SVG
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_check_filetype_and_ext/
	 * @since 2.6.0
	 */
	public function confirm_file_type_is_svg( $filetypes_extensions, $file, $filename, $mimes ) {

		global $roles_svg_upload_enabled;

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		if ( count( $roles_svg_upload_enabled ) > 0 ) {

			// Check file extension
			if ( substr( $filename, -4 ) == '.svg' ) {

				// Add mime type for user roles set to enable SVG upload
				foreach ( $current_user_roles as $role ) {
					if ( in_array( $role, $roles_svg_upload_enabled ) ) {
						$filetypes_extensions['type'] = 'image/svg+xml';
						$filetypes_extensions['ext'] = 'svg';
					}
				}	

			}

		}

		return $filetypes_extensions;

	}

	/** 
	 * Sanitize the SVG file and maybe allow upload based on the result
	 *
	 * @since 2.6.0
	 */
	public function sanitize_and_maybe_allow_svg_upload( $file ) {

		if ( ! isset( $file['tmp_name'] ) ) {
			return $file;
		}

		$file_tmp_name = $file['tmp_name']; // full path
		$file_name = isset( $file['name'] ) ? $file['name'] : '';
		$file_type_ext = wp_check_filetype_and_ext( $file_tmp_name, $file_name );
		$file_type = ! empty( $file_type_ext['type'] ) ? $file_type_ext['type'] : '';

		// Load sanitizer library - https://github.com/darylldoyle/svg-sanitizer
		$sanitizer = new Sanitizer();

		if ( 'image/svg+xml' === $file_type ) {

			$original_svg = file_get_contents( $file_tmp_name );
			$sanitized_svg = $sanitizer->sanitize( $original_svg ); // boolean

			if ( false === $sanitized_svg ) {

				$file['error'] = 'This SVG file could not be sanitized, so, was not uploaded for security reasons.';

			}

			file_put_contents( $file_tmp_name, $sanitized_svg );

		}

        return $file;

	}

	/**
	 * Generate metadata for the svg attachment
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_generate_attachment_metadata/
	 * @since 2.6.0
	 */
	public function generate_svg_metadata( $metadata, $attachment_id, $context ) {

		if ( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ) {

			// Get SVG dimensions
			$svg_path = get_attached_file( $attachment_id );
			$svg = simplexml_load_file( $svg_path );
			$width = 0;
			$height = 0;

			if ( $svg ) {

				$attributes = $svg->attributes();
				if ( isset( $attributes->width, $attributes->height ) ) {
					$width = intval( floatval( $attributes->width ) );
					$height = intval( floatval( $attributes->height ) );
				} elseif ( isset( $attributes->viewBox ) ) {
					$sizes = explode( ' ', $attributes->viewBox );
					if ( isset( $sizes[2], $sizes[3] ) ) {
						$width = intval( floatval( $sizes[2] ) );
						$height = intval( floatval( $sizes[3] ) );
					}
				}

			}

			$metadata['width'] = $width;
			$metadata['height'] = $height;

			// Get SVG filename
			$svg_url = wp_get_original_image_url( $attachment_id );
			$svg_url_path = str_replace( wp_upload_dir()['baseurl'] .'/' , '', $svg_url );
			$metadata['file'] = $svg_url_path;

		}

		return $metadata;

	}

	/**
	 * Return svg file URL to show preview in media library
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wp_ajax_action/
	 * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_url/
	 * @since 2.6.0
	 */
	public function get_svg_attachment_url() {

		$attachment_url = '';
		$attachment_id = isset( $_REQUEST['attachmentID'] ) ? $_REQUEST['attachmentID'] : '';

		// Check response mime type
		if ( $attachment_id ) {

			$attachment_url = wp_get_attachment_url( $attachment_id );

			echo $attachment_url;

			die();

		}

	}

	/**
	 * Return svg file URL to show preview in media library
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_prepare_attachment_for_js/
	 * @since 2.6.0
	 */
	public function get_svg_url_in_media_library( $response ) {

		// Check response mime type
		if ( $response['mime'] === 'image/svg+xml' ) {

			$response['image'] = array(
				'src'	=> $response['url'],
			);

		}

		return $response;

	}

	/**
	 * Add AVIF mime type to list of mime types
	 *
	 * @since 5.7.0
	 */
	public function add_avif_mime_type__premium_only( $wp_get_mime_types ) {

		$wp_get_mime_types['avif'] = 'image/avif';
		return $wp_get_mime_types;

	}

	/**
	 * Add AVIF mime type to allowed mime types
	 *
	 * @since 5.7.0
	 */
	public function allow_avif_mime_type_upload__premium_only( $mimes ) {

		$mimes['avif'] = 'image/avif';
		return $mimes;

	}

	/**
	 * Add AVIF to mapping of mime types to their respective extensions
	 *
	 * @since 5.7.0
	 */
	public function add_avif_mime_type_to_exts__premium_only( $mime_to_ext ) {

		$mime_to_ext['image/avif'] = 'avif';
		return $mime_to_ext;

	}
	
	/**
	 * Add correct dimension for AVIF images
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/avif-support/trunk/includes/AvifSupport.php#L104
	 * @since 5.7.0
	 */
	public function add_avif_image_dimension__premium_only( $metadata, $attachment_id, $context ) {
				
		if ( empty( $metadata ) ) {
			return $metadata;
		}
		
		$attachment_post = get_post( $attachment_id );
		
		if ( ! $attachment_post || is_wp_error( $attachment_post ) ) {
			return $metadata;
		}
		
		if ( 'image/avif' !== $attachment_post->post_mime_type ) {
			return $metadata;
		}
		
		// Fix width and height

		if ( 
			( ! empty( $metadata['width'] ) 
			  && ( 0 !== $metadata['width'] ) ) 
			  && ( ! empty( $metadata['height'] ) 
			  && 0 !== $metadata['height'] ) 
			) {
			return $metadata;
		}
		
		$file = get_attached_file( $attachment_id );
		
		if ( ! $file ) {
			return $metadata;	
		}
		
		if ( empty( $metadata['width'] ) ) {
			$metadata['width'] = 0;
		}

		if ( empty( $metadata['height'] ) ) {
			$metadata['height'] = 0;
		}
		
		if ( empty( $metadata['file'] ) ) {
			$metadata['file'] = _wp_relative_upload_path( $file );
		}
		
		if ( empty( $metadata['sizes'] ) ) {
			$metadata['sizes'] = array();
		}
		
		$img_size = wp_getimagesize( $file );

		// Legacy PHP Version, return false, fake it till manual.
		if ( empty( $img_size ) ) {
			$img_size = array(
				0      => 0,
				1      => 0,
				2      => 19,
				3      => 'width="0" height="0"',
				'mime' => 'image/avif',
			);
		}

		if ( is_array( $img_size ) && ( 0 !== $img_size[0] ) && ( 0 !== $img_size[1] ) ) {
			// Do nothing, we have what we need
		} else {
			
			// Manually get width and height
			$binary_string = file_get_contents( $file );
			$ispe_pos      = strpos( $binary_string, 'ispe' );

			if ( false === $ispe_pos ) {
				// Corrupted Image.
				return false;
			}

			$dim_start_pos = $ispe_pos + 8;
			$dim_bin       = substr( $binary_string, $dim_start_pos, 8 );
			$width         = hexdec( bin2hex( substr( $dim_bin, 0, 4 ) ) );
			$height        = hexdec( bin2hex( substr( $dim_bin, 4, 8 ) ) );

			if ( $width && $height && is_numeric( $width ) && is_numeric( $height ) ) {
				$img_size[0] = absint( $width );
				$img_size[1] = absint( $height );
			}

			// wp_getimagesize() failed, try with Imagick
			// if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			// 	try {
			// 		$imagick      = new \Imagick( $file );
			// 		$img_dim     = $imagick->getImageGeometry();
			// 		$img_size[0] = $img_dim['width'];
			// 		$img_size[1] = $img_dim['height'];

			// 		$imagick->clear();
			// 	} catch ( \Exception $e ) {
			// 		// Do nothing for now.
			// 	}
			// }

		}
		
		if ( ! $img_size ) {
			$avif_specs = false;
		} else {
			$file_size = filesize( $file );
			$avif_specs = array(
				'width'       => $img_size[0],
				'height'      => $img_size[1],
				'mime'        => $img_size['mime'],
				'dimension'   => $img_size[0] . 'x' . $img_size[1],
				'ext'         => str_replace( 'image/', '', $img_size['mime'] ),
				'size'        => $file_size,
				'size_format' => size_format( $file_size ),
			);
		}
		
		if ( is_wp_error( $avif_specs ) || ! $avif_specs ) {
			return $metadata;
		}
		
		$metadata['width'] = $avif_specs['width'];
		$metadata['height'] = $avif_specs['height'];
		
		return $metadata;

		// Fix scaled version of the image
				
	}
	
	/**
	 * Make sure AVIF files are displayable in the browser
	 * 
	 * @since 5.7.0
	 */
	public function make_avif_displayable__premium_only( $result, $path ) {
		if ( str_ends_with( $path, '.avif' ) ) {
			return true;
		}
		
		return $result;
	}

	/**
	 * Handle rare scenarios where exif and fileinfo fail to detect AVIF
	 * 
	 * @since 5.7.0
	 */
	public function handle_exif_and_fileinfo_fail__premium_only( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

		// AVIF is properly handled, no need to do anything else
		if ( $wp_check_filetype_and_ext['ext'] && $wp_check_filetype_and_ext['type'] ) {
			return $wp_check_filetype_and_ext;
		}

		// Not an .avif file, no need to do anything else
		if ( ! str_ends_with( $filename, '.avif' ) ) {
			return $wp_check_filetype_and_ext;
		} else {
			$binary_string = file_get_contents( $file );
			$ispe_pos      = strpos( $binary_string, 'ispe' );

			if ( false === $ispe_pos ) {
				// Corrupted Image.
				return false;
			}

			$dim_start_pos = $ispe_pos + 8;
			$dim_bin       = substr( $binary_string, $dim_start_pos, 8 );
			$width         = hexdec( bin2hex( substr( $dim_bin, 0, 4 ) ) );
			$height        = hexdec( bin2hex( substr( $dim_bin, 4, 8 ) ) );

			// If this is a valid image with proper width and height, set filetype and ext to AVIF
			if ( $width && $height && is_numeric( $width ) && is_numeric( $height ) ) {
				$wp_check_filetype_and_ext['type'] = 'image/avif';
				$wp_check_filetype_and_ext['ext']  = 'avif';				
			}
			
			return $wp_check_filetype_and_ext;
		}

	}

	/**
	 * Add external permalink meta box for enabled post types
	 * 
	 * @since 3.9.0
	 */
	public function add_external_permalink_meta_box( $post_type, $post ) {

		$options = get_option( ASENHA_SLUG_U, array() );
		$enable_external_permalinks_for = $options['enable_external_permalinks_for'];

		foreach ( $enable_external_permalinks_for as $post_type_slug => $is_external_permalink_enabled ) {
			if ( ( get_post_type() == $post_type_slug ) && $is_external_permalink_enabled ) {

				// Skip adding meta box for post types where Gutenberg is enabled
				// if ( 
				// 	function_exists( 'use_block_editor_for_post_type' ) 
				// 	&& use_block_editor_for_post_type( $post_type_slug ) 
				// ) {
				// 	continue; // go to the beginning of next iteration
				// }

				add_meta_box(
					'asenha-external-permalink', // ID of meta box
					'External Permalink', // Title of meta box
					[ $this, 'output_external_permalink_meta_box' ], // Callback function
					$post_type_slug, // The screen on which the meta box should be output to
					'normal', // context
					'high' // priority
					// array(), // $args to pass to callback function. Ref: https://developer.wordpress.org/reference/functions/add_meta_box/#comment-342
				);

			}
		}

	}

	/**
	 * Render External Permalink meta box
	 *
	 * @since 3.9.0
	 */
	public function output_external_permalink_meta_box( $post ) {
		?>
		<div class="external-permalink-input">
			<input name="<?php echo esc_attr( 'external_permalink' ); ?>" class="large-text" id="<?php echo esc_attr( 'external_permalink' ); ?>" type="text" value="<?php echo esc_url( get_post_meta( $post->ID, '_links_to', true ) ); ?>" placeholder="https://" />
			<div class="external-permalink-input-description">Keep empty to use the default WordPress permalink. External permalink will open in a new browser tab.</div>
			<?php wp_nonce_field( 'external_permalink_' . $post->ID, 'external_permalink_nonce', false, true ); ?>
		</div>
		<?php
	}

	/**
	 * Save external permalink input
	 *
	 * @since 3.9.0
	 */
	public function save_external_permalink( $post_id ) {

		// Only proceed if nonce is verified
		if ( isset( $_POST['external_permalink_nonce'] ) && wp_verify_nonce( $_POST['external_permalink_nonce'], 'external_permalink_' . $post_id ) ) {

			// Get the value of external permalink from input field
			$external_permalink = isset( $_POST['external_permalink'] ) ? esc_url_raw( trim( $_POST['external_permalink'] ) ) : '';

			// Update or delete external permalink post meta
			if ( ! empty( $external_permalink ) ) {
				update_post_meta( $post_id, '_links_to', $external_permalink );
			} else {
				delete_post_meta( $post_id, '_links_to' );
			}

		}

	}

	/**
	 * Change WordPress default permalink into external permalink for pages
	 *
	 * @since 3.9.0
	 */
	public function use_external_permalink_for_pages( $permalink, $post_id ) {

		$external_permalink = get_post_meta( $post_id, '_links_to', true );

		if ( ! empty( $external_permalink ) ) {
			$permalink = $external_permalink;
		}

		return $permalink;

	}

	/**
	 * Change WordPress default permalink into external permalink for posts and custom post types
	 *
	 * @since 3.9.0
	 */
	public function use_external_permalink_for_posts( $permalink, $post ) {

		$external_permalink = get_post_meta( $post->ID, '_links_to', true );

		if ( ! empty( $external_permalink ) ) {
			$permalink = $external_permalink;

			if ( ! is_admin() ) { 
				$permalink = $permalink . '#new_tab';
			}
		}

		return $permalink;

	}

	/** 
	 * Redirect page/post to external permalink if it's loaded directly from the WP default permalink
	 *
	 * @since 3.9.0
	 */
	public function redirect_to_external_permalink() {

		global $post;

		// If not on/loading the single page/post URL, do nothing
		if ( ! is_singular() ) {
			return;
		}

		$external_permalink = get_post_meta( $post->ID, '_links_to', true );

		if ( ! empty( $external_permalink ) ) {
			wp_redirect( $external_permalink, 302 ); // temporary redirect
			exit;
		}

	}
	
	/**
	 * Parse links in content to add target="_blank" rel="noopener noreferrer nofollow" attributes
	 * 
	 * @since 4.9.0
	 */
	public function add_target_and_rel_atts_to_content_links( $content ) {
		if ( ! empty( $content ) ) {

			// regex pattern for "a href"
			$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";

			if ( preg_match_all( "/$regexp/siU", $content, $matches, PREG_SET_ORDER ) ) {

				// $matches might contain parts of $content that has links (a href)
				preg_match_all( "/$regexp/siU", $content, $matches, PREG_SET_ORDER );
				
				if ( is_array( $matches ) ) {					
					$i = 0;

					foreach ( $matches as $match ) {

						$original_tag = $match[0]; // e.g. <a title="Link Title" href="http://www.example.com/sit-quaerat">
						$tag = $match[0]; // Same value as $original_tag but for further processing
						$url = $match[2]; // e.g. http://www.example.com/sit-quaerat
						
						if ( false !== strpos( $url, get_site_url() ) ) {
							// Internal link. Do nothing.
						} elseif ( false === strpos( $url, 'http' ) ) {
							// Relative link to internal URL. Do nothing.
						} else {
							// External link. Let's do something.
							// Regex pattern for target="_blank|parent|self|top"
							$pattern = '/target\s*=\s*"\s*_(blank|parent|self|top)\s*"/';
							// If there's no 'target="_blank|parent|self|top"' in $tag, add target="blank"
							if ( 0 === preg_match( $pattern, $tag ) ) {
								// Replace closing > with ' target="_blank">'
								$tag = substr_replace( $tag, ' target="_blank">', -1 );
							}							

							// If there's no 'rel' attribute in $tag, add rel="noopener noreferrer nofollow"
							$pattern = '/rel\s*=\s*\"[a-zA-Z0-9_\s]*\"/';
							if ( 0 === preg_match( $pattern, $tag ) ) {
								// Replace closing > with ' rel="noopener noreferrer nofollow">'
								$tag = substr_replace( $tag, ' rel="noopener noreferrer nofollow">', -1 );
							} else {
								// replace rel="noopener" with rel="noopener noreferrer nofollow"
								if ( false !== strpos( $tag, 'noopener' ) 
									&& false === strpos( $tag, 'noreferrer' ) 
									&& false === strpos( $tag, 'nofollow' ) 
									) {
									$tag = str_replace( 'noopener', 'noopener noreferrer nofollow', $tag );
								}
							}
							
							// Replace original a href tag with one containing target and rel attributes above
							$content = str_replace( $original_tag, $tag, $content );
						}
						$i++;
					}
				}
			}
		}

		return $content;
	}
	
	/**
	 * Add "open in new tab" checkbox in custom nav menu item settings
	 * 
	 * @since 5.4.0
	 */
	public function add_custom_nav_menu_open_in_new_tab_field( $item_id, $menu_item, $depth, $args ) {
				
		$target_blank = get_post_meta( $item_id, '_menu_item_target_blank', true );
		
		if ( 'custom' == $menu_item->object ) {			
		?>
			<p class="field-target_blank description-wide">
				<label for="edit-menu-item-target-blank-<?php echo esc_attr( $item_id ); ?>">
					<input type="checkbox" id="edit-menu-item-target-blank-<?php echo esc_attr( $item_id ); ?>" name="menu-item-target-blank[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $target_blank, '1' ); ?> />
					Open link in new tab and add rel="noopener noreferrer nofollow" attribute.
				</label>
			</p>
		<?php
		}
		
	}
	
	/**
	 * Save status of "open in new tab" checkbox in custom nav menu item settings
	 * 
	 * @since 5.4.0
	 */
	public function save_custom_nav_menu_open_in_new_tab_status( $menu_id, $menu_item_db_id, $args ) {
		
		if ( isset( $_POST['menu-item-target-blank'][$menu_item_db_id] ) ) {
			update_post_meta( $menu_item_db_id, '_menu_item_target_blank', '1'  );
		} else {
			delete_post_meta( $menu_item_db_id, '_menu_item_target_blank' );
		}

	}
	
	/**
	 * Add attributes to custom nav menu item on the frontend
	 * 
	 * @since 5.4.0
	 */
	public function add_attributes_to_custom_nav_menu_item( $atts, $menu_item, $args ) {

		$target_blank = get_post_meta( $menu_item->ID, '_menu_item_target_blank', true );
		
		if ( $target_blank ) {
			$atts['target'] = '_blank';
			$atts['rel']	= 'noopener noreferrer nofollow';
		}
		
		return $atts;
		
	}

	/**
	 * Publish posts of any type with missed schedule. 
	 * We use the Transients API to reduce straining the site with DB queries on busy sites.
	 * So, this function will only query the DB once every 15 minutes at most.
	 *
	 * @since 3.1.0
	 */
	public function publish_missed_schedule_posts() {

		if ( is_front_page() || is_home() || is_page() || is_single() || is_singular() || is_archive() || is_admin() || is_blog_admin() || is_robots() || is_ssl() ) {

			// Get missed schedule posts data from cache
			$missed_schedule_posts = get_transient( 'asenha_missed_schedule_posts' );

			// Nothing found in cache
			if ( false === $missed_schedule_posts ) {

				global $wpdb;

				$current_gmt_datetime = gmdate( 'Y-m-d H:i:00' );

				$args = array(
					'public'	=> true,
					'_builtin'	=> false, // not post, page, attachment, revision or nav_menu_item
				);

				$custom_post_types = get_post_types( $args, 'names' ); // array, e.g. array( 'project', 'book', 'staff' )

				if ( count( $custom_post_types ) > 0 ) {
					$custom_post_types = "'" . implode( "','", $custom_post_types ) . "'"; // string, e.g. 'project','book','staff'
					$post_types = "'page','post'," . $custom_post_types; // 'page','post','project','book','staff'
				} else {
					$post_types = "'page','post'";
				}

				$sql = "SELECT ID FROM $wpdb->posts WHERE post_type IN ($post_types) AND post_status='future' AND post_date_gmt<'$current_gmt_datetime'";

				// The following does not work as backslashes are inserted before single quotes in $post_types
				// $sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type IN (%s) AND post_status='future' AND post_date_gmt<'%s'", array( $post_types, $current_gmt_datetime ) );

				$missed_schedule_posts = $wpdb->get_results( $sql, ARRAY_A );

				// Save query results as a transient with expiry of 15 minutes
				set_transient( 'asenha_missed_schedule_posts', $missed_schedule_posts, 15 * MINUTE_IN_SECONDS );

			}

			if ( empty( $missed_schedule_posts ) || ! is_array( $missed_schedule_posts ) ) {
				return;
			}

			foreach( $missed_schedule_posts as $post ) {
				wp_publish_post( $post['ID'] );
			}

		}

	}

}