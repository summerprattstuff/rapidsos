<?php

namespace ASENHA\Classes;

use WP_Admin_Bar;
use WC_Admin_Duplicate_Product; 

/**
 * Class for Content Duplication module
 *
 * @since 6.9.5
 */
class Content_Duplication {
    
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

                    'comment_status'    => $original_post->comment_status,
                    'ping_status'       => $original_post->ping_status,
                    'post_author'       => $new_post_author_id,
                    // We replace single backslash with double backslash, so that upon saving, it becomes single backslash again
                    // This is to compensate for the default behaviour that removes single/unescaped backslashes upon saving content
                    // This ensures CSS styles using var(--varname) in the Block Editor, which is saved as var(\u002d\u002varname)
                    // Will not become var(u002du002dsecondary) in the duplicated post (not the missing backslash)
                    'post_content'      => str_replace( '\\', "\\\\", $original_post->post_content ),
                    'post_excerpt'      => $original_post->post_excerpt,
                    'post_parent'       => $original_post->post_parent,
                    'post_password'     => $original_post->post_password,
                    'post_status'       => $new_post_status,
                    'post_title'        => $original_post->post_title . $new_post_title_suffix,
                    'post_type'         => $original_post->post_type,
                    'to_ping'           => $original_post->to_ping,
                    'menu_order'        => $original_post->menu_order,

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
                        'ID'            => $duplicate_product_post->ID,
                        'post_title'    => str_replace( '(Copy)', '(DUPLICATE)', $duplicate_product_post->post_title ),
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
                    if ( 'post' == $post_type ) {
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
                    'cd_post_id'        => intval($post->ID),
                    'cd_nonce'          => wp_create_nonce( 'asenha-duplicate-' . $post->ID ),
                    'cd_post_text'      => 'Duplicate ' . $post_type_singular_label,
                    'cd_post_title'     => 'Duplicate this as draft',
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
    
}