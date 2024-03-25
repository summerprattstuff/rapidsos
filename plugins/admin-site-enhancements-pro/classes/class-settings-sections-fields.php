<?php

namespace ASENHA\Classes;

/**
 * Class related to registration of settings fields
 *
 * @since 2.2.0
 */
class Settings_Sections_Fields {

	/**
	 * Register plugin settings and the corresponding fields
	 *
	 * @link https://wpshout.com/making-an-admin-options-page-with-the-wordpress-settings-api/
	 * @link https://rudrastyh.com/wordpress/creating-options-pages.html
	 * @since 1.0.0
	 */
	function register_sections_fields() {
		
		add_settings_section(
			'main-section', // Section ID
			'', // Section title. Can be blank.
			'', // Callback function to output section intro. Can be blank.
			ASENHA_SLUG // Settings page slug
		);

		$common_methods = new Common_Methods;

		// Register main setttings

		// Instantiate object for sanitization of settings fields values
		$sanitization = new Settings_Sanitization;

		// Instantiate object for rendering of settings fields for the admin page
		$render_field = new Settings_Fields_Render;

		register_setting( 
			ASENHA_ID, // Option group or option_page
			ASENHA_SLUG_U, // Option name in wp_options table
			array(
				'type'					=> 'array', // 'string', 'boolean', 'integer', 'number', 'array', or 'object'
				'description'			=> '', // A description of the data attached to this setting.
				'sanitize_callback'		=> [ $sanitization, 'sanitize_for_options' ],
				'show_in_rest'			=> false,
				'default'				=> array(), // When calling get_option()
			)
		);

		// =================================================================
		// Call WordPress globals and set new globals required for the fields
		// =================================================================

		global $wp_version, $wp_roles, $wpdb, $asenha_public_post_types, $asenha_gutenberg_post_types, $asenha_revisions_post_types, $active_plugin_slugs, $workable_nodes;

		$options = get_option( ASENHA_SLUG_U, array() );
		$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );

		$roles = $wp_roles->get_names();

		// Get array of slugs and plural labels for public post types, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_public_post_types = array();
		$public_post_type_names = get_post_types( array( 'public' => true ), 'names' );
		foreach( $public_post_type_names as $post_type_name ) {
			$post_type_object = get_post_type_object( $post_type_name );
			$asenha_public_post_types[$post_type_name] = $post_type_object->label;
		}

		// Get array of slugs and plural labels for post types that can be edited with the Gutenberg block editor, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_gutenberg_post_types = array();
		$gutenberg_not_applicable_types = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation' );
        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
        	$gutenberg_not_applicable_types[] = 'asenha_cpt';
        	$gutenberg_not_applicable_types[] = 'asenha_ctax';
        	$gutenberg_not_applicable_types[] = 'asenha_cfgroup';
        }

		$all_post_types = get_post_types( array(), 'objects' );
		foreach ( $all_post_types as $post_type_slug => $post_type_info ) {
			$asenha_gutenberg_post_types[$post_type_slug] = $post_type_info->label;
			if ( in_array( $post_type_slug, $gutenberg_not_applicable_types ) ) {
				unset( $asenha_gutenberg_post_types[$post_type_slug] );
			}
		}

		// Get array of slugs and plural labels for post types supporting revisions, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_revisions_post_types = array();
		foreach ( get_post_types( array(), 'names' ) as $post_type_slug ) { // post type slug/name
			if ( post_type_supports( $post_type_slug, 'revisions' ) ) {
				$post_type_object = get_post_type_object( $post_type_slug );
				if ( property_exists( $post_type_object, 'label' ) ) {
					$asenha_revisions_post_types[$post_type_slug] = $post_type_object->label;
				}
			}
		}

		// Get array of active plugins slugs
		$active_plugins = get_option( 'active_plugins', array() );
		$active_plugin_slugs = array();
		foreach( $active_plugins as $active_plugin ) {
			// e.g. debug-log-manager/debug-log-manager.php
			$active_plugin = explode( "/", $active_plugin );
			$active_plugin_slugs[] = $active_plugin[0];
		}
						
		// =================================================================
		// CONTENT MANAGEMENT
		// =================================================================

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Custom Content Types

			$field_id = 'custom_content_types';
			$field_slug = 'custom-content-types';

			add_settings_field(
				$field_id, // Field ID
				'Custom Content Types', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'			=> $field_id, // Custom argument
					'field_slug'		=> $field_slug, // Custom argument
					'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'	=> 'Conveniently register and edit custom post types and custom taxonomies. Enable the creation of custom field groups for your post types and options pages to store data for display on any part of your website.', // Custom argument
					'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
					'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
					'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);

			// $field_id = 'custom_field_groups';
			// $field_slug = 'custom-field-groups';

			// add_settings_field(
			// 	$field_id, // Field ID
			// 	'', // Field title
			// 	[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			// 	ASENHA_SLUG, // Settings page slug
			// 	'main-section', // Section ID
			// 	array(
			// 		'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
			// 		'field_id'				=> $field_id, // Custom argument
			// 		'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
			// 		'field_label'			=> 'Enable the creation of custom field groups for your post types', // Custom argument
			// 		'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			// 	)
			// );

			$field_id = 'custom_content_types_description';
			$field_slug = 'custom-content-types-description';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_description'		=> 'Please find the relevant menu items under Settings.', // Custom argument
					'class'					=> 'asenha-description top-border content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		// Enable Content Duplication

		$field_id = 'enable_duplication';
		$field_slug = 'enable-duplication';

		add_settings_field(
			$field_id, // Field ID
			'Content Duplication', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Enable one-click duplication of pages, posts and custom posts. The corresponding taxonomy terms and post meta will also be duplicated.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'duplication_redirect_destination';
		$field_slug = 'duplication-redirect-destination';

		add_settings_field(
			$field_id, // Field ID
			'After duplication, redirect to:', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Edit screen'	=> 'edit',
					'List view'		=> 'list',
				),
				'field_default'			=> 'edit',
				'class'					=> 'asenha-radio-buttons shift-up content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'enable_duplication_link_at';
			$field_slug = 'enable-duplication-link-at';
			
			$options = array(
				'List view post action row'		=> 'post-action',
				'Admin bar'						=> 'admin-bar',
				'Edit screen publish section'	=> 'publish-section',
			);

			add_settings_field(
				$field_id, // Field ID
				'Show duplication link on:', // Field title
				[ $render_field, 'render_checkboxes_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . '][]', // Custom argument
					'field_options'			=> $options,
					'field_default'			=> array( 'post-action', 'admin-bar', 'publish-section' ),
					'layout'				=> 'vertical', // 'horizontal' or 'vertical'
					'class'					=> 'asenha-checkboxes content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'heading_for_enable_duplication_for';
			$field_slug = 'heading-for-enable-duplication-for';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_subfields_heading' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'subfields_heading'		=> 'Enable duplication only for:', // Custom argument
					'class'					=> 'asenha-heading shift-more-up content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'enable_duplication_for';
			$field_slug = 'enable-duplication-for';

			if ( is_array( $roles ) ) {
				foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

					add_settings_field(
						$field_id . '_' . $role_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $role_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
							'field_label'			=> $role_label, // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half content-management ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
						)
					);

				}
			}


        }

		// Content Order

		$field_id = 'content_order';
		$field_slug = 'content-order';

		add_settings_field(
			$field_id, // Field ID
			'Content Order', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Enable custom ordering of various "hierarchical" content types or those supporting "page attributes". A new \'Order\' sub-menu will appear for enabled content type(s). The "All {Posts}" list page for enabled post types in wp-admin will automatically use the custom order. ', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// $field_id = 'content_order_subfields_heading';
		// $field_slug = 'content-order-subfields-heading';

		// add_settings_field(
		// 	$field_id, // Field ID
		// 	'', // Field title
		// 	[ $render_field, 'render_subfields_heading' ], // Callback to render field with custom arguments in the array below
		// 	ASENHA_SLUG, // Settings page slug
		// 	'main-section', // Section ID
		// 	array(
		// 		'subfields_heading'		=> 'Custom Post Types (Non-Hierarchical)', // Custom argument
		// 		'class'					=> 'asenha-heading shift-more-up content-management ' . $field_slug, // Custom class for the <tr> element
		// 	)
		// );

		$field_id = 'content_order_for';
		$field_slug = 'content-order-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				$is_hierarchical_label = ( is_post_type_hierarchical( $post_type_slug ) ) ? ' <span class="faded">- Hierarchical</span>' : '';
				if ( post_type_supports( $post_type_slug, 'page-attributes' ) || is_post_type_hierarchical( $post_type_slug ) ) {
					add_settings_field(
						$field_id . '_' . $post_type_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $post_type_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
							'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>' . $is_hierarchical_label, // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half content-management ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
						)
					);
				}
			}
		}

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'content_order_frontend';
			$field_slug = 'content-order-frontend';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
					'field_label'			=> 'Use custom order on frontend query and display of enabled post types', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th asenha-th-border-top content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Terms Order

			$field_id = 'terms_order';
			$field_slug = 'terms-order';

			add_settings_field(
				$field_id, // Field ID
				'Terms Order', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'					=> $field_id, // Custom argument
					'field_slug'				=> $field_slug, // Custom argument
					'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'			=> 'Enable custom ordering of terms from various "hierarchical" taxonomies. A new "Term Order" sub-menu will appear for enabled post type(s) with at least one such taxonomies. Terms listing and checkboxes in wp-admin will automatically use the custom order.', // Custom argument
					'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
					'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
					'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'terms_order_for';
			$field_slug = 'terms-order-for';

			if ( is_array( $asenha_public_post_types ) ) {
				foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts

					$post_type_taxonomies = get_object_taxonomies( $post_type_slug );
					$hierarchical_taxonomies = array();

					// Get the hierarchical taxonomies for the post type
					foreach ( $post_type_taxonomies as $key => $taxonomy_name ) {
		                $taxonomy_info = get_taxonomy( $taxonomy_name );

		                if ( empty( $taxonomy_info->hierarchical ) ||  $taxonomy_info->hierarchical !== TRUE ) {
		                    unset( $post_type_taxonomies[$key] );
		                } else {
		                	$hierarchical_taxonomies[] = $taxonomy_info->label;
		                }
		            }
		            
		            $hierarchical_taxonomies = implode( ', ', $hierarchical_taxonomies );
		            
		            // Only if there's at least 1 hierarchical taxonomy for the post type
		            if ( count( $post_type_taxonomies ) > 0 ) {

						add_settings_field(
							$field_id . '_' . $post_type_slug, // Field ID
							'', // Field title
							[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
							ASENHA_SLUG, // Settings page slug
							'main-section', // Section ID
							array(
								'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
								'parent_field_id'		=> $field_id, // Custom argument
								'field_id'				=> $post_type_slug, // Custom argument
								'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
								'field_label'			=> $post_type_label . '<span class="dashicons dashicons-arrow-right-alt2"></span> ' . $hierarchical_taxonomies, // Custom argument
								'class'					=> 'asenha-checkbox asenha-hide-th asenha-half content-management ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
							)
						);
		            
		            }

				}
			}

			$field_id = 'terms_order_frontend';
			$field_slug = 'terms-order-frontend';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
					'field_label'			=> 'Use custom order of terms on frontend query and display.', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th asenha-th-border-top content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }
		
        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Media Categories

			$field_id = 'enable_media_categories';
			$field_slug = 'enable-media-categories';

			add_settings_field(
				$field_id, // Field ID
				'Media Categories', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'			=> $field_id, // Custom argument
					'field_slug'		=> $field_slug, // Custom argument
					'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'	=> 'Add categories for the media library and enable drag-and-drop categorization of media items. Categories can then be used to filter media items during media insertion into content.', // Custom argument
					'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		// Media Replacement

		$field_id = 'enable_media_replacement';
		$field_slug = 'enable-media-replacement';

		add_settings_field(
			$field_id, // Field ID
			'Media Replacement', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Easily replace any type of media file with a new one while retaining the existing media ID, publish date and file name. So, no existing links will break.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Media Library Infinite Scrolling

		$field_id = 'media_library_infinite_scrolling';
		$field_slug = 'media-library-infinite-scrolling';

		add_settings_field(
			$field_id, // Field ID
			'Media Library Infinite Scrolling', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Re-enable infinite scrolling in the grid view of the media library. Useful for scrolling through a large library.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable SVG Upload

		$field_id = 'enable_svg_upload';
		$field_slug = 'enable-svg-upload';

		add_settings_field(
			$field_id, // Field ID
			'SVG Upload', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Allow some or all user roles to upload SVG files, which will then be sanitized to keep things secure.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_svg_upload_for';
		$field_slug = 'enable-svg-upload-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half admin-interface ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Enable AVIF Upload

			$field_id = 'enable_avif_upload';
			$field_slug = 'enable-avif-upload';

			add_settings_field(
				$field_id, // Field ID
				'AVIF Upload', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'					=> $field_id, // Custom argument
					'field_slug'				=> $field_slug, // Custom argument
					'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'			=> 'Enable uploading <a href="https://www.smashingmagazine.com/2021/09/modern-image-formats-avif-webp/" target="_blank">AVIF</a> files in the Media Library. You can convert your existing PNG, JPG and GIF files using a tool like <a href="https://squoosh.app/" target="_blank">Squoosh</a>.', // Custom argument
					'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
					'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
					'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'avif_support_status';
			$field_slug = 'avif-support-status';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_avif_support_status' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'class'					=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		// Enable External Permalinks

		$field_id = 'enable_external_permalinks';
		$field_slug = 'enable-external-permalinks';

		add_settings_field(
			$field_id, // Field ID
			'External Permalinks', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Enable pages, posts and/or custom post types to have permalinks that point to external URLs. The rel="noopener noreferrer nofollow" attribute will also be added for enhanced security and SEO benefits. Compatible with links added using <a href="https://wordpress.org/plugins/page-links-to/" target="_blank">Page Links To</a>.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_external_permalinks_for';
		$field_slug = 'enable-external-permalinks-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( 'attachment' != $post_type_slug ) {
					add_settings_field(
						$field_id . '_' . $post_type_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $post_type_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
							'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half content-management ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
						)
					);
				}
			}
		}

		// Open All External Links in New Tab

		$field_id = 'external_links_new_tab';
		$field_slug = 'external-links-new-tab';

		add_settings_field(
			$field_id, // Field ID
			'Open All External Links in New Tab', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Force all links to external sites in post content, where <a href="https://developer.wordpress.org/reference/hooks/the_content/" target="_blank">the_content</a> hook is used, to open in new browser tab via target="_blank" attribute. The rel="noopener noreferrer nofollow" attribute will also be added for enhanced security and SEO benefits.', // Custom argument
				'field_options_wrapper'		=> false, // Custom argument. Add container for additional options
				'field_options_moreless'	=> false,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Allow Custom Nav Menu Items to Open in New Tab

		$field_id = 'custom_nav_menu_items_new_tab';
		$field_slug = 'custom-nav-menu-items-new-tab';

		add_settings_field(
			$field_id, // Field ID
			'Allow Custom Navigation Menu Items to Open in New Tab', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Allow custom navigation menu items to have links that open in new browser tab via target="_blank" attribute. The rel="noopener noreferrer nofollow" attribute will also be added for enhanced security and SEO benefits. ', // Custom argument
				'field_options_wrapper'		=> false, // Custom argument. Add container for additional options
				'field_options_moreless'	=> false,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Auto-Publishing of Posts with Missed Schedules

		$field_id = 'enable_missed_schedule_posts_auto_publish';
		$field_slug = 'enable-missed-schedule-posts-auto-publish';

		add_settings_field(
			$field_id, // Field ID
			'Auto-Publish Posts with Missed Schedule', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Trigger publishing of scheduled posts of all types marked with "missed schedule", anytime the site is visited.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// ADMIN INTERFACE
		// =================================================================

		// Hide or Modify Elements / Clean Up Admin Bar

		$field_id = 'hide_modify_elements';
		$field_slug = 'hide-modify-elements';

		add_settings_field(
			$field_id, // Field ID
			'Clean Up Admin Bar', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Remove various elements from the admin bar.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_wp_logo_menu';
		$field_slug = 'hide-ab-wp-logo-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove WordPress logo/menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_customize_menu';
		$field_slug = 'hide-ab-customize-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove customize menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_updates_menu';
		$field_slug = 'hide-ab-updates-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove updates counter/link', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_comments_menu';
		$field_slug = 'hide-ab-comments-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove comments counter/link', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_new_content_menu';
		$field_slug = 'hide-ab-new-content-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove new content menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_howdy';
		$field_slug = 'hide-ab-howdy';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove \'Howdy\'', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_help_drawer';
		$field_slug = 'hide-help-drawer';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove the Help tab and drawer', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			
			// Get list of workable admin bar nodes from options

			$workable_nodes = ( isset( $options_extra['ab_nodes_workable'] ) ) ? $options_extra['ab_nodes_workable'] : array();

			ksort( $workable_nodes );
			
			if ( ! empty( $workable_nodes ) ) {

				$field_id = 'plugins_extra_admin_bar_items';
				$field_slug = 'plugins-extra-admin-bar-items';

				add_settings_field(
					$field_id, // Field ID
					'', // Field title
					[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'field_description'		=> 'Remove extra elements listed below, which most likely come from your theme or plugins.', // Custom argument
						'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
					)
				);

				$field_id = 'disabled_plugins_admin_bar_items';
				$field_slug = 'disabled-plugins-admin-bar-items';
				
				foreach( $workable_nodes as $node_id => $node ) {
					
					if ( ! empty( trim( $common_methods->strip_html_tags_and_content( $node['title'] ) ) ) ) {
						$node_title = $common_methods->strip_html_tags_and_content( $node['title'] );					
					} else {
						$node_title = $node_id;
					}

					add_settings_field(
						$field_id . '_' . $node_id, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $node_id, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . '][' . $node_id . ']', // Custom argument
							'field_label'			=> $node_title . ' <span class="faded">(' . $node_id . ')</span>', // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug . ' ' . $node_id, // Custom class for the <tr> element
						)
					);
				}

				$field_id = 'plugins_extra_admin_bar_items_description';
				$field_slug = 'plugins-extra-admin-bar-items-description';

				add_settings_field(
					$field_id, // Field ID
					'', // Field title
					[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'field_description'		=> 'Make sure to visit one of your frontend <a href="' . admin_url( 'edit.php?post_type=page' ) . '">pages</a> or <a href="' . admin_url( 'edit.php' ) . '">posts</a> while logged-in, to detect elements only visible in the frontend admin bar, and return here to see them on the list above.', // Custom argument
						'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
					)
				);

			}

		}

		// Hide Admin Notices

		$field_id = 'hide_admin_notices';
		$field_slug = 'hide-admin-notices';

		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_options_wrapper = true;
			$field_options_moreless = true;
		} else {
			$field_options_wrapper = false;
			$field_options_moreless = false;
		}

		add_settings_field(
			$field_id, // Field ID
			'Hide Admin Notices', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Clean up admin pages by moving notices into a separate panel easily accessible via the admin bar.', // Custom argument
				'field_options_wrapper'	=> $field_options_wrapper, // Custom argument. Add container for additional options
				'field_options_moreless'	=> $field_options_moreless,  // Custom argument. Add show more/less toggler.
				'class'				=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {

			$field_id = 'hide_admin_notices_for_nonadmins';
			$field_slug = 'hide-admin-notices-for-nonadmins';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
					'field_label'			=> 'Also hide admin notices for non-administrators.', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
				)
			);
		
		}	

		// Disable Dashboard Widgets

		$field_id = 'disable_dashboard_widgets';
		$field_slug = 'disable-dashboard-widgets';

		add_settings_field(
			$field_id, // Field ID
			'Disable Dashboard Widgets', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Clean up and speed up the dashboard by completely disabling some or all widgets. Disabled widgets won\'t load any assets nor show up under Screen Options. ', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disabled_dashboard_widgets';
		$field_slug = 'disabled-dashboard-widgets';

		if ( array_key_exists( 'dashboard_widgets', $options_extra ) ) {
			$dashboard_widgets = $options_extra['dashboard_widgets'];
		} else {
			$admin_interface = new Admin_Interface;
			$dashboard_widgets = $admin_interface->get_dashboard_widgets();
			$options_extra['dashboard_widgets'] = $dashboard_widgets;
			update_option( 'admin_site_enhancements_extra', $options_extra );
		}

		foreach ( $dashboard_widgets as $widget ) {
			add_settings_field(
				$field_id . '_' . $widget['id'], // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'parent_field_id'		=> $field_id, // Custom argument
					'field_id'				=> $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'], // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . '][' . $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'] . ']', // Custom argument
					'field_label'			=> $widget['title'] . ' <span class="faded">(' . $widget['id'] . ')</span>', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug . ' ' . $widget['id'], // Custom class for the <tr> element
				)
			);
		}

		// Hide Admin Bar

		$field_id = 'hide_admin_bar';
		$field_slug = 'hide-admin-bar';
		
		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_description = 'Hide admin bar for all or some user roles.';
		} else {
			$field_description = 'Hide admin bar on the frontend for all or some user roles.';			
		}

		add_settings_field(
			$field_id, // Field ID
			'Hide Admin Bar', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> $field_description, // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'heading_for_hide_admin_bar_on_frontend';
			$field_slug = 'heading-for-hide-admin-bar-on-frontend';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_subfields_heading' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'subfields_heading'		=> 'On the frontend:', // Custom argument
					'class'					=> 'asenha-heading shift-more-up admin-interface ' . $field_slug, // Custom class for the <tr> element
				)
			);
		}

		$field_id = 'hide_admin_bar_for';
		$field_slug = 'hide-admin-bar-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half admin-interface ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// On the backend
			$field_id = 'heading_for_hide_admin_bar_on_backend';
			$field_slug = 'heading-for-hide-admin-bar-on-backend';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_subfields_heading' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'subfields_heading'		=> 'On the backend:', // Custom argument
					'class'					=> 'asenha-heading asenha-hide-th asenha-th-border-top admin-interface ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'hide_admin_bar_on_backend_for';
			$field_slug = 'hide-admin-bar-on-backend-for';

			if ( is_array( $roles ) ) {
				foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

					add_settings_field(
						$field_id . '_' . $role_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $role_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
							'field_label'			=> $role_label, // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half admin-interface ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
						)
					);

				}
			}
		}
		
		// Wider Admin Menu

		$field_id = 'wider_admin_menu';
		$field_slug = 'wider-admin-menu';

		add_settings_field(
			$field_id, // Field ID
			'Wider Admin Menu', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Give the admin menu more room to better accommodate wider items.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'class'						=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'admin_menu_width';
		$field_slug = 'admin-menu-width';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set width to', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is 160px)</span>', // Custom argument
				'field_select_options'	=> array(
					'180px'	=> '180px',
					'200px'	=> '200px',
					'220px'	=> '220px',
					'240px'	=> '240px',
					'260px'	=> '260px',
					'280px'	=> '280px',
					'300px'	=> '300px',
				),
				'field_select_default'	=> 200,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up admin-interface ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		// Admin Menu Organizer

		$field_id = 'customize_admin_menu';
		$field_slug = 'customize-admin-menu';

		add_settings_field(
			$field_id, // Field ID
			'Admin Menu Organizer', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Customize the order of the admin menu and optionally change menu item title or hide some items.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_menu_order';
		$field_slug = 'custom-menu-order';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_sortable_menu' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'sortable-menu', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-sortable asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Admin Columns Manager

			$field_id = 'admin_columns_manager';
			$field_slug = 'admin-columns-manager';

			add_settings_field(
				$field_id, // Field ID
				'Admin Columns Manager', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'			=> $field_id, // Custom argument
					'field_slug'		=> $field_slug, // Custom argument
					'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'	=> 'Manage and organize columns in the admin listing for pages, posts and custom post types.', // Custom argument
					'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
					'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
					'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		// Enhance List Tables

		$field_id = 'enhance_list_tables';
		$field_slug = 'enhance-list-tables';

		add_settings_field(
			$field_id, // Field ID
			'Enhance List Tables', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'		=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Improve the usefulness of listing pages for various post types and taxonomies, media, comments and users by adding / removing columns and elements.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Featured Image Column

		$field_id = 'show_featured_image_column';
		$field_slug = 'show-featured-image-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show featured image column', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Excerpt Column

		$field_id = 'show_excerpt_column';
		$field_slug = 'show-excerpt-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show excerpt column', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show ID Column

		$field_id = 'show_id_column';
		$field_slug = 'show-id-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show ID column', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show ID in Action Row

		$field_id = 'show_id_in_action_row';
		$field_slug = 'show-id-in-action_row';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show ID in action rows along with links for Edit, View, etc.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Custom Taxonomy Filters

		$field_id = 'show_custom_taxonomy_filters';
		$field_slug = 'show-custom-taxonomy-filters';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show additional filter(s) for hierarchical, custom taxonomies', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Hide Comments Column

		$field_id = 'hide_comments_column';
		$field_slug = 'hide-comments-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove comments column', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Hide Post Tags Column

		$field_id = 'hide_post_tags_column';
		$field_slug = 'hide-post-tags-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove tags column (for posts)', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Display Active Plugins First

		$field_id = 'display_active_plugins_first';
		$field_slug = 'display-active-plugins-first';

		add_settings_field(
			$field_id, // Field ID
			'Display Active Plugins First', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Display active / activated plugins at the top of the Installed Plugins list. Useful when your site has many deactivated plugins for testing or development purposes.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Custom Admin Footer Text

		$field_id = 'custom_admin_footer_text';
		$field_slug = 'custom-admin-footer-text';

		add_settings_field(
			$field_id, // Field ID
			'Custom Admin Footer Text', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Customize the text you see on the footer of wp-admin pages other than this ASE settings page.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);
		
		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$media_buttons = true;
			$quicktags = true;
			$toolbar1 = 'bold,italic,underline,separator,link,unlink,undo,redo';
		} else {
			$media_buttons = false;
			$quicktags = false;
			$toolbar1 = 'bold,italic,underline';
		}

		$field_id = 'custom_admin_footer_left';
		$field_slug = 'custom-admin-footer-left';

		// https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
		// https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/
		$editor_settings = array(
			'media_buttons'		=> $media_buttons,
			'textarea_name'		=> ASENHA_SLUG_U . '['. $field_id .']',
			'textarea_rows'		=> 3,
			// 'teeny'				=> true,
			'tiny_mce'			=> true,
			'tinymce'			=> array(
				'toolbar1'		=> $toolbar1,
				'content_css'	=> ASENHA_URL . 'assets/css/settings-wpeditor.css',
			),
			'editor_css'		=> '',
			'quicktags'			=> $quicktags,
			'default_editor'	=> 'tinymce', // 'tinymce' or 'html'
		);

		add_settings_field(
			$field_id, // Field ID
			'Left Side', // Field title
			[ $render_field, 'render_wpeditor_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default text is: <em>Thank you for creating with <a href="https://wordpress.org/">WordPress</a></em>.', // Custom argument
				'field_placeholder'		=> '',
				'editor_settings'		=> $editor_settings,
				'class'					=> 'asenha-textarea admin-interface has-wpeditor ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_admin_footer_right';
		$field_slug = 'custom-admin-footer-right';

		// https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
		// https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/
		$editor_settings = array(
			'media_buttons'		=> $media_buttons,
			'textarea_name'		=> ASENHA_SLUG_U . '['. $field_id .']',
			'textarea_rows'		=> 3,
			// 'teeny'				=> true,
			'tiny_mce'			=> true,
			'tinymce'			=> array(
				'toolbar1'		=> $toolbar1,
				'content_css'	=> ASENHA_URL . 'assets/css/settings-wpeditor.css',
			),
			'editor_css'		=> '',
			'quicktags'			=> $quicktags,
			'default_editor'	=> 'tinymce', // 'tinymce' or 'html'
		);

		add_settings_field(
			$field_id, // Field ID
			'Right Side', // Field title
			[ $render_field, 'render_wpeditor_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 1,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default text is: <em>Version ' . $wp_version . '</em>', // Custom argument
				'field_placeholder'		=> '',
				'editor_settings'		=> $editor_settings,
				'class'					=> 'asenha-textarea admin-interface has-wpeditor ' . $field_slug, // Custom class for the <tr> element
			)
		);
		// =================================================================
		// LOG IN | LOG OUT
		// =================================================================

		// Change Login URL

		$field_id = 'change_login_url';
		$field_slug = 'change-login-url';

		add_settings_field(
			$field_id, // Field ID
			'Change Login URL', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Default is ' . get_site_url() . '/wp-login.php', // Custom argument
				'field_options_moreless'=> true,  // Custom argument. Add show more/less toggler.
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_login_slug';
		$field_slug = 'custom-login-slug';

		add_settings_field(
			$field_id, // Field ID
			'New login URL:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'default_login_redirect_slug';
			$field_slug = 'default-login-redirect-slug';

			add_settings_field(
				$field_id, // Field ID
				'Redirect default URLs to:', // Field title
				[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'with-prefix-suffix', // Custom argument
					'field_prefix'			=> get_site_url() . '/', // Custom argument
					'field_suffix'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'class'					=> 'asenha-text with-prefix-suffix margin-top-8 login-logout ' . $field_slug, // Custom class for the <tr> element
				)
			);        	
        }

		$field_id = 'change_login_url_description';
		$field_slug = 'change-login-url-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> '<div class="asenha-warning">This feature <strong>only works for/with the default WordPress login page</strong>. It does not support using custom login page you manually created with a page builder or with another plugin.<br /><br />It\'s also <strong>not yet compatible with two-factor authentication (2FA) methods</strong>. If you use a 2FA plugin, please use the change login URL feature bundled in that plugin, or use another plugin that is compatible with it.<br /><br />And obviously, to improve security, please <strong>use something other than \'login\'</strong> for the custom login slug.</div>', // Custom argument
				'class'					=> 'asenha-description login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Login ID Type

		$field_id = 'login_id_type_restriction';
		$field_slug = 'login-id-type-restriction';

		add_settings_field(
			$field_id, // Field ID
			'Login ID Type', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Restrict login ID to username or email address only.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'class'						=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_id_type';
		$field_slug = 'login-id-type';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Username only'			=> 'username',
					'Email address only'	=> 'email',
				),
				'field_default'			=> 'username',
				'class'					=> 'asenha-radio-buttons shift-up login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Use Site Identity on the Login Page

		$field_id = 'site_identity_on_login';
		$field_slug = 'site-identity-on-login';

		add_settings_field(
			$field_id, // Field ID
			'Site Identity on Login Page', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Use the site icon and URL to replace the default WordPress logo with link to wordpress.org on the login page. Go to the <a href="' . get_admin_url() . 'customize.php">customizer</a> to set or change your site icon.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Log In/Out Menu

		$field_id = 'enable_login_logout_menu';
		$field_slug = 'enable-login-logout-menu';

		add_settings_field(
			$field_id, // Field ID
			'Log In/Out Menu', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Enable log in, log out and dynamic log in/out menu item for addition to any menu.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Last Login Column

		$field_id = 'enable_last_login_column';
		$field_slug = 'enable-last-login-column';

		add_settings_field(
			$field_id, // Field ID
			'Last Login Column', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Log when users on the site last logged in and display the date and time in the users list table.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Redirect After Login

		$field_id = 'redirect_after_login';
		$field_slug = 'redirect-after-login';

		add_settings_field(
			$field_id, // Field ID
			'Redirect After Login', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Set custom redirect URL for all or some user roles after login.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_login_to_slug';
		$field_slug = 'redirect-after-login-to-slug';

		add_settings_field(
			$field_id, // Field ID
			'Redirect to:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> ' for:', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_login_for';
		$field_slug = 'redirect-after-login-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half login-logout ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// Redirect After Logout

		$field_id = 'redirect_after_logout';
		$field_slug = 'redirect-after-logout';

		add_settings_field(
			$field_id, // Field ID
			'Redirect After Logout', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Set custom redirect URL for all or some user roles after logout.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_logout_to_slug';
		$field_slug = 'redirect-after-logout-to-slug';

		add_settings_field(
			$field_id, // Field ID
			'Redirect to:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> ' for:', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_logout_for';
		$field_slug = 'redirect-after-logout-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half login-logout ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// =================================================================
		// CUSTOM CODE
		// =================================================================

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Code Snippets Manager

			$field_id = 'enable_code_snippets_manager';
			$field_slug = 'enable-code-snippets-manager';

			add_settings_field(
				$field_id, // Field ID
				'Code Snippets Manager', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'					=> $field_id, // Custom argument
					'field_slug'				=> $field_slug, // Custom argument
					'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'			=> 'Conveniently add and manage CSS, JS, HTML and PHP code snippets to modify your site\'s content, design, behaviour and functionalities', // Custom argument
					'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
					'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
					'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
				)
			);        	
        }

		// Enable Custom Admin CSS

		$field_id = 'enable_custom_admin_css';
		$field_slug = 'enable-custom-admin-css';

		add_settings_field(
			$field_id, // Field ID
			'Custom Admin CSS', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom CSS on all admin pages for all user roles.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_admin_css';
		$field_slug = 'custom-admin-css';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 30,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Custom Frontend CSS

		$field_id = 'enable_custom_frontend_css';
		$field_slug = 'enable-custom-frontend-css';

		add_settings_field(
			$field_id, // Field ID
			'Custom Frontend CSS', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom CSS on all frontend pages for all user roles.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'custom_frontend_css_priority';
			$field_slug = 'custom-frontend-css-priority';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'with-prefix-suffix', // Custom argument
					'field_prefix'			=> '<strong>Insert CSS with the priority of</strong>', // Custom argument
					'field_suffix'			=> '', // Custom argument
					'field_intro'			=> '', // Custom argument
					'field_description'		=> 'Default is 10. Larger number inserts CSS closer to &lt;/head&gt; and allows it to better override other CSS loaded earlier in &lt;head&gt;.', // Custom argument
					'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		$field_id = 'custom_frontend_css';
		$field_slug = 'custom-frontend-css';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 30,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Custom Body Class

		$field_id = 'enable_custom_body_class';
		$field_slug = 'enable-custom-body-class';

		add_settings_field(
			$field_id, // Field ID
			'Custom Body Class', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom &lt;body&gt; class(es) on the singular view of some or all public post types. Compatible with classes already added using <a href="https://wordpress.org/plugins/wp-custom-body-class" target="_blank">Custom Body Class plugin</a>.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_custom_body_class_for';
		$field_slug = 'enable-custom-body-class-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( 'attachment' != $post_type_slug ) {
					add_settings_field(
						$field_id . '_' . $post_type_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $post_type_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
							'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half custom-code ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
						)
					);
				}
			}
		}

		// Manage ads.txt and app-ads.txt

		$field_id = 'manage_ads_appads_txt';
		$field_slug = 'manage-ads-appads-txt';

		add_settings_field(
			$field_id, // Field ID
			'Manage ads.txt and app-ads.txt', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily edit and validate your <a href="/ads.txt" target="_blank">ads.txt</a> and <a href="/app-ads.txt" target="_blank">app-ads.txt</a> content.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'ads_txt_content';
		$field_slug = 'ads-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '<strong>Your ads.txt content:</strong>', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://adstxt.guru/validator/url/?url=' . urlencode( get_site_url( null, 'ads.txt' ) ) . '" target="_blank">adstxt.guru</a> | <a href="https://www.adstxtvalidator.com/ads_txt/' . esc_attr( str_replace( '.', '-', $_SERVER['SERVER_NAME'] ) ) . '" target="_blank">adstxtvalidator.com</a><div class="vspacer"></div>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'app_ads_txt_content';
		$field_slug = 'app-ads-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '<strong>Your app-ads.txt content:</strong>', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://adstxt.guru/validator/url/?url=' . urlencode( get_site_url( null, 'app-ads.txt' ) ) . '" target="_blank">adstxt.guru</a>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Manage robots.txt

		$field_id = 'manage_robots_txt';
		$field_slug = 'manage-robots-txt';

		add_settings_field(
			$field_id, // Field ID
			'Manage robots.txt', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily edit and validate your <a href="/robots.txt" target="_blank">robots.txt</a> content.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'robots_txt_content';
		$field_slug = 'robots-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 20,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://en.ryte.com/free-tools/robots-txt/?refresh=1&url=' . urlencode( get_site_url( null, 'robots.txt' ) ) . '&useragent=Googlebot&submit=Evaluate" target="_blank">ryte.com</a> | <a href="https://serp.tools/tools/robots-txt" target="_blank">serp.tools</a><div class="vspacer"></div>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Insert <head>, <body> and <footer> code

		$field_id = 'insert_head_body_footer_code';
		$field_slug = 'insert-head-body-footer-code';

		add_settings_field(
			$field_id, // Field ID
			'Insert &lt;head&gt;, &lt;body&gt; and &lt;footer&gt; Code', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily insert &lt;meta&gt;, &lt;link&gt;, &lt;script&gt; and &lt;style&gt; tags, Google Analytics, Tag Manager, AdSense, Ads Conversion and Optimize code, Facebook, TikTok and Twitter pixels, etc.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'head_code_priority';
		$field_slug = 'head-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert before &lt;/head&gt; with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Larger number insert code closer to &lt;/head&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'head_code';
		$field_slug = 'head-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'body_code_priority';
		$field_slug = 'body-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert after &lt;body&gt; with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Smaller number insert code closer to &lt;body&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'body_code';
		$field_slug = 'body-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'footer_code_priority';
		$field_slug = 'footer-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert in footer section before &lt;/body&gt;: with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Larger number insert code closer to &lt;/body&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'footer_code';
		$field_slug = 'footer-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// DISABLE COMPONENTS
		// =================================================================

		// Disable Gutenberg

		$field_id = 'disable_gutenberg';
		$field_slug = 'disable-gutenberg';

		add_settings_field(
			$field_id, // Field ID
			'Disable Gutenberg', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable the Gutenberg block editor for some or all applicable post types.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_gutenberg_for';
		$field_slug = 'disable-gutenberg-for';

		if ( is_array( $asenha_gutenberg_post_types ) ) {
			foreach ( $asenha_gutenberg_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half disable-components ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		$field_id = 'disable_gutenberg_frontend_styles';
		$field_slug = 'disable-gutenberg-frontend-styles';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Also disable frontend block styles / CSS files for the selected post types.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th asenha-th-border-top disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Block-Based Widgets Screen

		$field_id = 'disable_block_widgets';
		$field_slug = 'disable-block-widgets';

		add_settings_field(
			$field_id, // Field ID
			'Disable Block-Based Widgets Settings Screen', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Restores the classic widgets settings screen when using a classic (non-block) theme. This has no effect on block themes.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Comments

		$field_id = 'disable_comments';
		$field_slug = 'disable-comments';

		add_settings_field(
			$field_id, // Field ID
			'Disable Comments', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable comments for some or all public post types. When disabled, existing comments will also be hidden on the frontend.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_comments_for';
		$field_slug = 'disable-comments-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half disable-components ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		// Disable REST API

		$field_id = 'disable_rest_api';
		$field_slug = 'disable-rest-api';

		add_settings_field(
			$field_id, // Field ID
			'Disable REST API', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable REST API access for non-authenticated users and remove URL traces from &lt;head&gt;, HTTP headers and WP RSD endpoint.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Feeds

		$field_id = 'disable_feeds';
		$field_slug = 'disable-feeds';

		add_settings_field(
			$field_id, // Field ID
			'Disable Feeds', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable all RSS, Atom and RDF feeds. This includes feeds for posts, categories, tags, comments, authors and search. Also removes traces of feed URLs from &lt;head&gt;.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Auto Updates

		$field_id = 'disable_all_updates';
		$field_slug = 'disable-all-updates';

		add_settings_field(
			$field_id, // Field ID
			'Disable All Updates', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Completely disable core, theme and plugin updates and auto-updates. Will also disable update checks, notices and emails.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Smaller Components

		$field_id = 'disable_smaller_components';
		$field_slug = 'disable-smaller-components';

		add_settings_field(
			$field_id, // Field ID
			'Disable Smaller Components', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Prevent smaller components from running or loading. Make the site more secure, load slightly faster and be more optimized for crawling by search engines.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_generator_tag';
		$field_slug = 'disable-head-generator-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>generator &lt;meta&gt; tag</strong> in &lt;head&gt;, which discloses the WordPress version number. Older versions(s) might contain unpatched security loophole(s).', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_resource_version_number';
		$field_slug = 'disable-resource-version-number';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable <strong>version number</strong> on static resource URLs referenced in &lt;head&gt;, which can disclose WordPress version number. Older versions(s) might contain unpatched security loophole(s). Applies to non-logged-in view of pages. This will also increase cacheability of static assets, but may have unintended consequences. Make sure you know what you are doing.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);
		$field_id = 'disable_head_wlwmanifest_tag';
		$field_slug = 'disable-head-wlwmanifest-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>Windows Live Writer (WLW) manifest &lt;link&gt; tag</strong> in &lt;head&gt;. The WLW app was discontinued in 2017.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_rsd_tag';
		$field_slug = 'disable-head-rsd-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>Really Simple Discovery (RSD) &lt;link&gt; tag</strong> in &lt;head&gt;. It\'s not needed if your site is not using pingback or remote (XML-RPC) client to manage posts.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_shortlink_tag';
		$field_slug = 'disable-head-shortlink-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the default <strong>WordPress shortlink &lt;link&gt; tag</strong> in &lt;head&gt;. Ignored by search engines and has minimal practical use case. Usually, a dedicated shortlink plugin or service is preferred that allows for nice names in the short links and tracking of clicks when sharing the link on social media.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_frontend_dashicons';
		$field_slug = 'disable-frontend-dashicons';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable loading of <strong>Dashicons CSS and JS files</strong> on the front-end for public site visitors. This might break the layout or design of custom forms, including custom login forms, if they depend on Dashicons. Make sure to check those forms after disabling.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_emoji_support';
		$field_slug = 'disable-emoji-support';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable <strong>emoji support for pages, posts and custom post types</strong> on the admin and frontend. The support is primarily useful for older browsers that do not have native support for it. Most modern browsers across different OSes and devices now have native support for it.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_jquery_migrate';
		$field_slug = 'disable-jquery-migrate';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable <strong>jQuery Migrate</strong> script on the frontend, which should no longer be needed if your site uses modern theme and plugins.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// SECURITY
		// =================================================================

		// Limit Login Attempts

		$field_id = 'limit_login_attempts';
		$field_slug = 'limit-login-attempts';

		add_settings_field(
			$field_id, // Field ID
			'Limit Login Attempts', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Prevent brute force attacks by limiting the number of failed login attempts allowed per IP address.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_fails_allowed';
		$field_slug = 'login-fails-allowed';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> 'failed login attempts allowed before 15 minutes lockout', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow no-margin security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_lockout_maxcount';
		$field_slug = 'login-lockout-maxcount';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> 'lockout(s) will block further login attempts for 24 hours', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow no-margin security ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'limit_login_attempts_ip_whitelist';
			$field_slug = 'limit-login-attempts-ip-whitelist';

			add_settings_field(
				$field_id, // Field ID
				'Never block the following IP addresses:', // Field title
				[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_rows'			=> 3,
					'field_intro'			=> '', // Custom argument
					'field_description'		=> 'Enter one IPv4 address per line', // Custom argument
					'field_placeholder'		=> 'e.g. 202.73.201.157',
					'class'					=> 'asenha-textarea margin-top-16 security ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		$field_id = 'login_attempts_log_table';
		$field_slug = 'login-attempts-log-table';

		add_settings_field(
			$field_id, // Field ID
			'Failed login attempts:', // Field title
			[ $render_field, 'render_datatable' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'datatable', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text datatable margin-top-16 security ' . $field_slug, // Custom class for the <tr> element
				'table_title'			=> 'Failed Login Attempts Log',
				'table_name'			=> $wpdb->prefix . 'asenha_failed_logins',
			)
		);

		// Obfuscate Author Slugs

		$field_id = 'obfuscate_author_slugs';
		$field_slug = 'obfuscate-author-slugs';

		add_settings_field(
			$field_id, // Field ID
			'Obfuscate Author Slugs', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Obfuscate publicly exposed author page URLs that shows the user slugs / usernames, e.g. <em>sitename.com/author/username1/</em> into <em>sitename.com/author/a6r5b8ytu9gp34bv/</em>, and output 404 errors for the original URLs. Also obfuscates in /wp-json/wp/v2/users/ REST API endpoint.', // Custom argument
				'field_options_wrapper'	=> false, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Obfuscate Email Address

		$field_id = 'obfuscate_email_address';
		$field_slug = 'obfuscate-email-address';

		add_settings_field(
			$field_id, // Field ID
			'Email Address Obfuscator', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Obfuscate email address to prevent spam bots from harvesting them, but make it readable like a regular email address for human visitors.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless' => true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'obfuscate_email_address_description';
		$field_slug = 'obfuscate-email-address-description';

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
        	$field_description = 'Use a shortcode like the following examples to display an email address on the frontend of your site: 
        		<ul>
        			<li><strong>[obfuscate email="john@example.com"]</strong> to display the email on it\'s own line</li>
        			<li><strong>[obfuscate email="john@example.com" display="inline"]</strong> to show the email inline</li>
        			<li><strong>[obfuscate email="john@example.com" display="inline" link="yes"]</strong> to show the email inline and linked with <strong>mailto:</strong></li>
        			<li><strong>[obfuscate email="john@example.com" display="inline" link="yes" subject="I\'m interested to learn about your services..."]</strong> to show the email inline and linked with <strong>mailto:</strong> with a pre-defined subject line.</li>
        			<li><strong>[obfuscate email="john@example.com" display="inline" link="yes" class="custom-class-name"]</strong> to show the email inline, linked with <strong>mailto:</strong> and has a custom CSS class to more easily customize the style.</li>
        		</ul>';

        } else {
        	$field_description = 'Use a shortcode like the following examples to display an email address on the frontend of your site: 
        		<ul>
        			<li><strong>[obfuscate email="john@example.com"]</strong> to display the email on it\'s own line</li>
        			<li><strong>[obfuscate email="john@example.com" display="inline"]</strong> to show the email inline</li>
        		</ul>';
        }

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> $field_description, // Custom argument
				'class'					=> 'asenha-description security ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'obfuscate_email_address_in_content';
			$field_slug = 'obfuscate-email-address-in-content';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
					'field_label'			=> 'Automatically obfuscate email addresses in post content.', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th asenha-th-border-top security ' . $field_slug, // Custom class for the <tr> element
				)
			);        	
        }

		// Disable XML-RPC

		$field_id = 'disable_xmlrpc';
		$field_slug = 'disable-xmlrpc';

		add_settings_field(
			$field_id, // Field ID
			'Disable XML-RPC', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Protect your site from brute force, DOS and DDOS attacks via <a href="https://kinsta.com/blog/xmlrpc-php/#what-is-xmlrpcphp" target="_blank">XML-RPC</a>. Also disables trackbacks and pingbacks. ', // Custom argument
				'field_options_wrapper'	=> false, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// OPTIMIZATIONS
		// =================================================================

		// Image Upload Control

		$field_id = 'image_upload_control';
		$field_slug = 'image-upload-control';

		add_settings_field(
			$field_id, // Field ID
			'Image Upload Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Resize newly uploaded, large images to a smaller dimension and delete originally uploaded files. BMPs and non-transparent PNGs will be converted to JPGs and resized.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'image_max_width';
		$field_slug = 'image-max-width';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Max width:', // Custom argument
				'field_suffix'			=> 'pixels. <span class="faded">(Default is 1920 pixels)</span>', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'image_max_height';
		$field_slug = 'image-max-height';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Max height:', // Custom argument
				'field_suffix'			=> 'pixels <span class="faded">(Default is 1920 pixels)</span>', // Custom argument
				'field_intro'			=> '', // Custom argument
				// 'field_description'		=> 'To exclude an image from conversion and resizing, append \'-nr\' suffix to the file name, e.g. bird-photo-4k-nr.jpg', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow margin-bottom-4 optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'convert_to_webp';
			$field_slug = 'convert-to-webp';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
					'field_label'			=> 'Convert BMP, PNG and JPG uploads to WebP instead of JPG.', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th optimizations ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'convert_to_webp_quality';
			$field_slug = 'convert-to-webp-quality';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'with-prefix-suffix', // Custom argument
					'field_prefix'			=> 'WebP quality:', // Custom argument
					'field_suffix'			=> '', // Custom argument
					'field_intro'			=> '', // Custom argument
					'field_description'		=> 'Default is 82 from a range between 1 to 100. The higher the number, the higher the quality and the file size.', // Custom argument
					'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		$field_id = 'image_upload_control_description';
		$field_slug = 'image-upload-control-description';
    	$field_description = 'To exclude an image from conversion and resizing, append \'-nr\' suffix to the file name, e.g. bird-photo-4k-nr.jpg';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> $field_description, // Custom argument
				'class'					=> 'asenha-description top-border optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Revisions Control

		$field_id = 'enable_revisions_control';
		$field_slug = 'enable-revisions-control';

		add_settings_field(
			$field_id, // Field ID
			'Revisions Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Prevent bloating the database by limiting the number of revisions to keep for some or all post types supporting revisions.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'revisions_max_number';
		$field_slug = 'revisions-max-number';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Limit to', // Custom argument
				'field_suffix'			=> 'revisions for:', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_revisions_control_for';
		$field_slug = 'enable-revisions-control-for';

		if ( is_array( $asenha_revisions_post_types ) ) {
			// Exclude Bricks builder template CPT as revisions are handled via a constant
			// Ref: https://academy.bricksbuilder.io/article/revisions/
			unset( $asenha_revisions_post_types['bricks_template'] );
			foreach ( $asenha_revisions_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half optimizations ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		// Enable Heartbeat Control

		$field_id = 'enable_heartbeat_control';
		$field_slug = 'enable-heartbeat-control';

		add_settings_field(
			$field_id, // Field ID
			'Heartbeat Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Modify the interval of the WordPress heartbeat API or disable it on admin pages, post creation/edit screens and/or the frontend. This will help reduce CPU load on the server.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'heartbeat_control_for_admin_pages';
		$field_slug = 'heartbeat-control-for-admin-pages';

		add_settings_field(
			$field_id, // Field ID
			'On admin pages', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'heartbeat_interval_for_admin_pages';
		$field_slug = 'heartbeat-interval-for-admin-pages';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is 1 minute)</span>', // Custom argument
				'field_select_options'	=> array(
					'15 seconds'	=> 15,
					'30 seconds'	=> 30,
					'1 minute'		=> 60,
					'2 minutes'		=> 120,
					'3 minutes'		=> 180,
					'5 minutes'		=> 300,
					'10 minutes'	=> 600,
				),
				'field_select_default'	=> 60,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		$field_id = 'heartbeat_control_for_post_edit';
		$field_slug = 'heartbeat-control-for-post-edit';

		add_settings_field(
			$field_id, // Field ID
			'On post creation and edit screens', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations top-border ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'heartbeat_interval_for_post_edit';
		$field_slug = 'heartbeat-interval-for-post-edit';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is 15 seconds)</span>', // Custom argument
				'field_select_options'	=> array( 
					'15 seconds'	=> 15,
					'30 seconds'	=> 30, 
					'45 seconds'	=> 45, 
					'60 seconds'	=> 60, 
					'90 seconds'	=> 90, 
					'120 seconds'	=> 120 
				),
				'field_select_default'	=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		$field_id = 'heartbeat_control_for_frontend';
		$field_slug = 'heartbeat-control-for-frontend';

		add_settings_field(
			$field_id, // Field ID
			'On the frontend', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations top-border ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'heartbeat_interval_for_frontend';
		$field_slug = 'heartbeat-interval-for-frontend';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_select_options'	=> array( 
					'15 seconds'	=> 15,
					'30 seconds'	=> 30,
					'1 minute'		=> 60,
					'2 minutes'		=> 120,
					'3 minutes'		=> 180,
					'5 minutes'		=> 300,
					'10 minutes'	=> 600,
				),
				'field_select_default'	=> 60,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		// =================================================================
		// UTILITIES
		// =================================================================

		// SMTP Email Delivery

		$field_id = 'smtp_email_delivery';
		$field_slug = 'smtp-email-delivery';

		add_settings_field(
			$field_id, // Field ID
			'Email Delivery', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Set custom sender name and email. Optionally use external SMTP service to ensure notification and transactional emails from your site are being delivered to inboxes.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_default_from_description';
		$field_slug = 'smtp-default-from-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> 'If set, the following sender name/email overrides WordPress core defaults but can still be overridden by other plugins that enables custom sender name/email, e.g. form plugins.', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);
		
		$field_id = 'smtp_default_from_name';
		$field_slug = 'smtp-default-from-name';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Sender name</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_default_from_email';
		$field_slug = 'smtp-default-from-email';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Sender email</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_force_from';
		$field_slug = 'smtp-force-from';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Force the usage of the sender name/email defined above. It will override those set by other plugins.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th bottom-border utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_description';
		$field_slug = 'smtp--description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> 'If set, the following SMTP service/account wil be used to deliver your emails.', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_host';
		$field_slug = 'smtp-host';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Host</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_port';
		$field_slug = 'smtp-port';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Port</span>', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_security';
		$field_slug = 'smtp-security';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Security</span>', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'None'		=> 'none',
					'SSL'		=> 'ssl',
					'TLS'		=> 'tls',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_username';
		$field_slug = 'smtp-username';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Username</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_password';
		$field_slug = 'smtp-password';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Password</span>', // Field title
			[ $render_field, 'render_password_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);
		
		$field_id = 'smtp_bypass_ssl_verification';
		$field_slug = 'smtp-bypass-ssl-verification';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Bypass verification of SSL certificate. This would be insecure if mail is delivered across the internet but could help in certain local and/or containerized WordPress scenarios.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_debug';
		$field_slug = 'smtp-debug';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Enable debug mode and output the debug info into WordPress debug.log file.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th bottom-border utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_send_test_email_description';
		$field_slug = 'smtp-send-test-email-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> 'After saving the settings above, check if everything is configured properly below.', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_send_test_email_to';
		$field_slug = 'smtp-send-test-email-to';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Send a test email to</span>', // Field title
			[ $render_field, 'render_custom_html' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'html'		=> '<input type="text" id="test-email-to" class="asenha-subfield-text" name="" placeholder="" value=""><a id="send-test-email" class="button send-test-email">Send Now</a>',
				'class'		=> 'asenha-html wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_send_test_email_result';
		$field_slug = 'smtp-send-test-email-result';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> '<div id="ajax-result" class="ajax-result-div" style="display:none;">
				<div class="sending-test-email"><img src="' . ASENHA_URL . 'assets/img/oval.svg" id="sending-test-email-spinner" class="spinner-img">Sending test email...</div>
				<div id="test-email-success" class="test-email-success" style="display:none;"><span class="dashicons dashicons-yes"></span> <span>Test email was successfully processed</span>.<br />Please check the destination email\'s inbox to verify successful delivery.</div>
				<div id="test-email-failed" class="test-email-failed" style="display:none;"><span class="dashicons dashicons-no-alt"></span> <span>Oops, something went wrong</span>.<br />Please double check your settings and the destination email address.</div></div>', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			// Local User Avatar
			$field_id = 'local_user_avatar';
			$field_slug = 'local-user-avatar';

			add_settings_field(
				$field_id, // Field ID
				'Local User Avatar', // Field title
				[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_description'		=> 'Enable usage of any image from WordPress Media Library as user avatars.', // Custom argument
					'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
					'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);
		}

		// Multiple User Roles

		$field_id = 'multiple_user_roles';
		$field_slug = 'multiple-user-roles';

		add_settings_field(
			$field_id, // Field ID
			'Multiple User Roles', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Enable assignment of multiple roles during user account creation and editing. This maybe useful for working with roles not defined in WordPress core, e.g. from e-commerce or LMS plugins.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);
		
		// Image Sizes Panel

		$field_id = 'image_sizes_panel';
		$field_slug = 'image-sizes-panel';

		add_settings_field(
			$field_id, // Field ID
			'Image Sizes Panel', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Display a panel showing and linking to all available sizes when viewing an image in the media library. Especially useful to quickly get the URL of a particular image size.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// View Admin as Role

		$field_id = 'view_admin_as_role';
		$field_slug = 'view-admin-as-role';

		add_settings_field(
			$field_id, // Field ID
			'View Admin as Role', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'View admin pages and the site (logged-in) as one of the non-administrator user roles.', // Custom argument
				'field_options_moreless'=> true,  // Custom argument. Add show more/less toggler.
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$current_user = wp_get_current_user();
		$current_user_username = $current_user->user_login;

		$field_id = 'view_admin_as_role_description';
		$field_slug = 'view-admin-as-role-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> '<div class="asenha-warning"><strong>If something goes wrong</strong> and you need to regain access to your account as an administrator, please visit the following URL: <br /><strong>' . get_site_url( null, '/?reset-for=' ) . $current_user_username . '</strong><br /><br />If you use <strong>Ninja Firewall</strong>, please uncheck "Block attempts to gain administrative privileges" in the Firewall Policies settings before you try to view as a non-admin user role to <strong>prevent being locked out</strong> of your admin account.</div>', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Password Protection

		$field_id = 'enable_password_protection';
		$field_slug = 'enable-password-protection';

		add_settings_field(
			$field_id, // Field ID
			'Password Protection', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Password-protect the entire site to hide the content from public view and search engine bots / crawlers. Logged-in administrators can still access the site as usual.', // Custom argument
				'field_options_moreless'=> true,  // Custom argument. Add show more/less toggler.
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'password_protection_password';
		$field_slug = 'password-protection-password';

		add_settings_field(
			$field_id, // Field ID
			'Set the password:', // Field title
			[ $render_field, 'render_password_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is \'secret\')</span>', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'password_protection_ip_whitelist';
			$field_slug = 'password-protection-ip-whitelist';

			add_settings_field(
				$field_id, // Field ID
				'Allow visitors with the following IP addresses to view the site directly:', // Field title
				[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_rows'			=> 3,
					'field_intro'			=> '', // Custom argument
					'field_description'		=> 'Enter one IPv4 address per line', // Custom argument
					'field_placeholder'		=> 'e.g. 202.73.201.157',
					'class'					=> 'asenha-textarea margin-top-16 utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);

			// Determine which password to show in the description section for bypass via URL param below
			$stored_password = ( isset( $options['password_protection_password'] ) ) ? $options['password_protection_password'] : '';
			if ( ! empty( $stored_password ) ) {
				$displayed_password = $stored_password;
			} else {
				$displayed_password = 'yourpassword';				
			}

			$field_id = 'password_protection_description_url_parameter';
			$field_slug = 'password-protection-description-url-parameter';
	    	$field_description = 'You can also append <strong>?bypass=' . $displayed_password . '</strong> in the URL to bypass the password entry form. e.g. ' . get_site_url() . '/?bypass=' . $displayed_password . '. This can be useful when you need to quickly share a dev site with another person, e.g. a client.';

			add_settings_field(
				$field_id, // Field ID
				'Bypass password protection with URL parameter', // Field title
				[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_description'		=> $field_description, // Custom argument
					'class'					=> 'asenha-description margin-top-16 utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		// Maintenance Mode

		$field_id = 'maintenance_mode';
		$field_slug = 'maintenance-mode';

		add_settings_field(
			$field_id, // Field ID
			'Maintenance Mode', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'				=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Show a customizable maintenance page on the frontend while performing a brief maintenance to your site. Logged-in administrators can still view the site as usual.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'maintenance_page_title';
			$field_slug = 'maintenance-page-title';

			add_settings_field(
				$field_id, // Field ID
				'Page Title <span class="faded">(shown in the browser tab)</span>', // Field title
				[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> '', // Custom argument
					'field_prefix'			=> '', // Custom argument
					'field_suffix'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'field_placeholder'		=> 'Under maintenance',
					'class'					=> 'asenha-text utilities full-width ' . $field_slug, // Custom class for the <tr> element
				)
			);    
		}

		$field_id = 'maintenance_page_heading';
		$field_slug = 'maintenance-page-heading';

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {

			// https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
			// https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/
			$editor_settings = array(
				'media_buttons'		=> true,
				'textarea_name'		=> ASENHA_SLUG_U . '['. $field_id .']',
				'textarea_rows'		=> 3,
				'tiny_mce'			=> true,
				'tinymce'			=> array(
					// 'toolbar1'		=> 'bold,italic,underline,separator,link,unlink,undo,redo',
					'toolbar1'		=> 'bold,italic,underline,strikethrough,superscript,subscript,blockquote,bullist,numlist,alignleft,aligncenter,alignjustify,alignright,alignnone,link,unlink,fontsizeselect,forecolor,undo,redo,removeformat',
					'content_css'	=> ASENHA_URL . 'assets/css/settings-wpeditor.css',
				),
				'editor_css'		=> '',
				'wpautop'			=> true,
				'quicktags'			=> true,
				'default_editor'	=> 'tinymce', // 'tinymce' or 'html'
			);

			add_settings_field(
				$field_id, // Field ID
				'Heading', // Field title
				[ $render_field, 'render_wpeditor_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_intro'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'field_placeholder'		=> 'We\'ll be back soon.',
					'editor_settings'		=> $editor_settings,
					'class'					=> 'asenha-textarea utilities has-wpeditor ' . $field_slug, // Custom class for the <tr> element
				)
			);
        } else {
			add_settings_field(
				$field_id, // Field ID
				'Heading', // Field title
				[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> '', // Custom argument
					'field_prefix'			=> '', // Custom argument
					'field_suffix'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'field_placeholder'		=> 'We\'ll be back soon.',
					'class'					=> 'asenha-text utilities full-width ' . $field_slug, // Custom class for the <tr> element
				)
			);        	
        }
		
		$field_id = 'maintenance_page_description';
		$field_slug = 'maintenance-page-description';

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$editor_settings = array(
				'media_buttons'		=> true,
				'textarea_name'		=> ASENHA_SLUG_U . '['. $field_id .']',
				'textarea_rows'		=> 3,
				'tiny_mce'			=> true,
				'tinymce'			=> array(
					// 'toolbar1'		=> 'bold,italic,underline,separator,link,unlink,undo,redo',
					'toolbar1'		=> 'bold,italic,underline,strikethrough,superscript,subscript,blockquote,bullist,numlist,alignleft,aligncenter,alignjustify,alignright,alignnone,link,unlink,fontsizeselect,forecolor,undo,redo,removeformat',
					'content_css'	=> ASENHA_URL . 'assets/css/settings-wpeditor.css',
				),
				'editor_css'		=> '',
				'wpautop'			=> true,
				'quicktags'			=> true,
				'default_editor'	=> 'tinymce', // 'tinymce' or 'html'
			);

			add_settings_field(
				$field_id, // Field ID
				'Description', // Field title
				[ $render_field, 'render_wpeditor_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_intro'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'field_placeholder'		=> 'This site is undergoing maintenance for an extended period today. Thanks for your patience.',
					'editor_settings'		=> $editor_settings,
					'class'					=> 'asenha-textarea utilities has-wpeditor ' . $field_slug, // Custom class for the <tr> element
				)
			);
        } else {
			add_settings_field(
				$field_id, // Field ID
				'Description', // Field title
				[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_rows'			=> 5,
					'field_intro'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'field_placeholder'		=> 'This site is undergoing maintenance for an extended period today. Thanks for your patience.',
					'class'					=> 'asenha-textarea utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		$field_id = 'maintenance_page_background';
		$field_slug = 'maintenance-page-background';

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
        	$field_radios = array(
					'Stripes'	=> 'stripes',
					'Curves'	=> 'curves',
					'Lines'		=> 'lines',
					'Image'		=> 'image',
					'Color'		=> 'solid_color',
				);
        } else {
        	$field_radios = array(
					'Stripes'	=> 'stripes',
					'Curves'	=> 'curves',
					'Lines'		=> 'lines',
				);        	
        }

		add_settings_field(
			$field_id, // Field ID
			'Background', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> $field_radios,
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
			$field_id = 'maintenance_page_background_image';
			$field_slug = 'maintenance-page-background-image';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_media_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'					=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'						=> $field_id, // Custom argument
					'field_slug'					=> $field_slug, // Custom argument
					'field_name'					=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_media_frame_title'		=> 'Select an Image', // Media frame title
					'field_media_frame_multiple' 	=> false, // Allow multiple selection?
					'field_media_frame_library_type' => 'image', // Which media types to show
					'field_media_frame_button_text'	=> 'Use Selected Image', // Insertion button text
					'field_intro'					=> '', // Custom argument
					'field_description'				=> '', // Custom argument
					'class'							=> 'asenha-textarea utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);

			$field_id = 'maintenance_page_background_color';
			$field_slug = 'maintenance-page-background-color';

			add_settings_field(
				$field_id, // Field ID
				'', // Field title
				[ $render_field, 'render_color_picker_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'					=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'						=> $field_id, // Custom argument
					'field_slug'					=> $field_slug, // Custom argument
					'field_name'					=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_intro'					=> '', // Custom argument
					'field_description'				=> '', // Custom argument
					'field_default_value'			=> 'eeeeee', // Show or hide on page load
					'class'							=> 'asenha-textarea utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);	

			$field_id = 'maintenance_page_custom_css';
			$field_slug = 'maintenance-page-custom-css';

			add_settings_field(
				$field_id, // Field ID
				'Custom CSS', // Field title
				[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
					'field_id'				=> $field_id, // Custom argument
					'field_slug'			=> $field_slug, // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
					'field_type'			=> 'textarea', // Custom argument
					'field_rows'			=> 20,
					'field_intro'			=> '', // Custom argument
					'field_description'		=> '', // Custom argument
					'class'					=> 'asenha-textarea syntax-highlighted utilities ' . $field_slug, // Custom class for the <tr> element
				)
			);
        }

		$field_id = 'maintenance_mode_description';
		$field_slug = 'maintenance-mode-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_description'		=> '<div class="asenha-warning"><strong>Please clear your cache</strong> after enabling or disabling maintenance mode. This ensures site visitors see either the maintenance page or the actual content of each page.</div>', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Redirect 404 to Homepage

		$field_id = 'redirect_404_to_homepage';
		$field_slug = 'redirect-404-to-homepage';

		add_settings_field(
			$field_id, // Field ID
			'Redirect 404 to Homepage', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Perform 301 (permanent) redirect to the homepage for all 404 (not found) pages.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Display System Summary

		$field_id = 'display_system_summary';
		$field_slug = 'display-system-summary';

		add_settings_field(
			$field_id, // Field ID
			'Display System Summary', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Show quick summary of the system the site is running on to admins, in the "At a Glance" dashboard widget. This includes the web server software, the PHP version, the database software and server IP address.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Search Engines Visibility Status

		$field_id = 'search_engine_visibility_status';
		$field_slug = 'search-engine-visibility-status';

		add_settings_field(
			$field_id, // Field ID
			'Search Engines Visibility Status', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'option_name'			=> ASENHA_SLUG_U, // Option name in wp_options table
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Show admin bar status and admin notice when search engines are set to be discouraged from indexing the site. This is set through a "Search engine visibility" checkbox in Settings >> Reading.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

	}

}