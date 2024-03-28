<?php

namespace ASENHA\Classes;

/**
 * Class for Admin Menu Organizer module
 *
 * @since 6.9.5
 */
class Admin_Menu_Organizer {
    
    /**
     * Render custom menu order
     *
     * @param $menu_order array an ordered array of menu items
     * @link https://developer.wordpress.org/reference/hooks/menu_order/
     * @since 2.0.0
     */
    public function render_custom_menu_order( $menu_order ) {

        global $menu;

        $options = get_option( ASENHA_SLUG_U );

        // Get current menu order. We're not using the default $menu_order which uses index.php, edit.php as array values.
        $current_menu_order = array();

        foreach ( $menu as $menu_key => $menu_info ) {

            if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
                $menu_item_id = $menu_info[2];
            } else {
                $menu_item_id = $menu_info[5];
            }

            $current_menu_order[] = array( $menu_item_id, $menu_info[2] );

        }

        // Get custom menu order
        $custom_menu_order = $options['custom_menu_order']; // comma separated
        $custom_menu_order = explode( ",", $custom_menu_order ); // array of menu ID, e.g. menu-dashboard

        // Return menu order for rendering

        $rendered_menu_order = array();

        // Render menu based on items saved in custom menu order

        foreach ( $custom_menu_order as $custom_menu_item_id ) {

            foreach ( $current_menu_order as $current_menu_item_id => $current_menu_item ) {

                if ( $custom_menu_item_id == $current_menu_item[0] ) {

                    $rendered_menu_order[] = $current_menu_item[1];

                }

            }

        }

        // Add items from current menu not already part of custom menu order, e.g. new plugin activated and adds new menu item

        foreach ( $current_menu_order as $current_menu_item_id => $current_menu_item ) {

            if ( ! in_array( $current_menu_item[0], $custom_menu_order ) ) {

                $rendered_menu_order[] = $current_menu_item[1];

            }

        }
        
        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
            global $submenu;

            // Rearrange the sequence / positioning of sub-menu items
    
            $common_methods = new Common_Methods;

            // Get custom submenus order
            $custom_submenus_order = ( array_key_exists( 'custom_submenus_order', $options ) ) ? $options['custom_submenus_order'] : ''; // comma separated
            $custom_submenus_order = ( is_array( json_decode( $custom_submenus_order, true ) ) ) ? json_decode( $custom_submenus_order, true ) : array();
            $custom_submenus_order_transformed = array();

            foreach ( $custom_submenus_order as $submenu_id => $submenu_order ) {
                // Transform e.g. edit__php___post_type____page ==> edit.php?post_type=page
                $submenu_id = $common_methods->restore_menu_item_id( $submenu_id );
                $submenu_order = explode( ",", $submenu_order );
                $custom_submenus_order_transformed[$submenu_id] = $submenu_order;
            }
            
            // Change the order of submenu items for $submenu global
            foreach ( $submenu as $submenu_global_id => $submenu_global_items ) {

                // There's an empty $submenu_global_id for items that are 'hidden'
                // if ( empty( $submenu_global_id ) ) {
                //  $submenu_global_id = 'unassigned';
                // }

                $custom_ordered_items = array();

                foreach ( $custom_submenus_order_transformed as $submenu_custom_id => $submenu_custom_order ) {

                    if ( $submenu_global_id == $submenu_custom_id ) {
                        
                        // Assign new index / position for submenu items as saved in $submenu_custom_order

                        foreach ( $submenu_custom_order as $new_index => $item_id ) {
                            // Convert special HTML character into it's entity equivalent, e.g. '&' into '&amp;'
                            $item_id_htmlentities = htmlentities( $item_id );
                            foreach ( $submenu_global_items as $index => $info ) {
                                // Most of the time, this is true
                                if ( $item_id_htmlentities == $info[2] ) {
                                    $custom_ordered_items[$new_index] = $info;
                                } else {
                                    // Sometimes, $info[2] does not contain HTML entities, so they are teh same as $item_id
                                    if ( $item_id == $info[2] ) {
                                        $custom_ordered_items[$new_index] = $info;                                  
                                    }
                                }                           
                            }                       
                        }

                        // Here we append submenu items that are newly added and not part of the saved $submenu_custom_order yet
                        
                        $existing_submenu_items_count = count( $submenu_custom_order );
                        $counter = $existing_submenu_items_count;

                        foreach( $submenu_global_items as $index => $info  ) {
                            $info2 = $info[2];
                            // $info2 = str_replace( '&amp;', '&', $info2 ); // This has caused some submenu items to go missing
                            $info2 = html_entity_decode( $info2 );                          
                            if ( ! in_array( $info2, $submenu_custom_order ) ) {
                                $custom_ordered_items[$counter] = $info;
                                $counter++;
                            }
                        }

                        $submenu[$submenu_global_id] = $custom_ordered_items;
                    }
                }
            }
        }

        return $rendered_menu_order;

    }

    /**
     * Apply custom menu item titles
     *
     * @since 2.9.0
     */
    public function apply_custom_menu_item_titles() {

        global $menu;

        $options = get_option( ASENHA_SLUG_U );

        // Get custom menu item titles
        $custom_menu_titles = $options['custom_menu_titles'];
        $custom_menu_titles = explode( ',', $custom_menu_titles );

        foreach ( $menu as $menu_key => $menu_info ) {

            if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
                $menu_item_id = $menu_info[2];
            } else {
                $menu_item_id = $menu_info[5];
            }

            // Get defaul/custom menu item title
            foreach ( $custom_menu_titles as $custom_menu_title ) {

                // At this point, $custom_menu_title value looks like toplevel_page_snippets__Code Snippets

                $custom_menu_title = explode( '__', $custom_menu_title );

                if ( $custom_menu_title[0] == $menu_item_id ) {
                    $menu_item_title = $custom_menu_title[1]; // e.g. Code Snippets
                    break; // stop foreach loop so $menu_item_title is not overwritten in the next iteration
                } else {
                    $menu_item_title = $menu_info[0];
                }

            }

            $menu[$menu_key][0] = $menu_item_title;

        }
    }

    /**
     * Hide menu items by adding a class to hide them (part of WP Core's common.css)
     *
     * @since 2.0.0
     */
    public function hide_menu_items() {

        global $menu;

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
            $options = get_option( ASENHA_SLUG_U, array() );

            // Get data on menu items to be hidden by user roles
            if ( isset( $options['custom_menu_always_hidden'] ) ) {
                $menu_always_hidden = $options['custom_menu_always_hidden'];
                $menu_always_hidden = json_decode( $menu_always_hidden, true );
            } else {
                $menu_always_hidden = array();
            }

            global $current_user;
            $current_user_roles = $current_user->roles; // indexed array of role slugs
        }

        $common_methods = new Common_Methods;
        $menu_hidden_by_toggle = $common_methods->get_menu_hidden_by_toggle(); // indexed array

        foreach ( $menu as $menu_key => $menu_info ) {

            if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
                $menu_item_id = $menu_info[2];
            } else {
                $menu_item_id = $menu_info[5];
            }

            // Append 'hidden' class to hide menu item until toggled
            if ( in_array( $menu_item_id, $menu_hidden_by_toggle ) ) {
                $menu[$menu_key][4] = $menu_info[4] . ' hidden asenha_hidden_menu';
            }

            if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
                if ( is_array( $menu_always_hidden ) && ! empty( $menu_always_hidden ) ) {
                    foreach( $menu_always_hidden as $hidden_menu_item_id => $info ) {
                        $hidden_menu_item_id = $common_methods->restore_menu_item_id( $hidden_menu_item_id );
                        if ( $hidden_menu_item_id == $menu_item_id 
                            // Special case where Yoast SEO is showing Yoast SEO menu only to editors using a different $menu_item_id
                            || ( 'toplevel_page_wpseo_dashboard' == $hidden_menu_item_id && 'toplevel_page_wpseo_workouts' == $menu_item_id  )
                        ) {

                            // Append 'always-hidden' class to always hide menu item
                            if ( isset( $info['always_hide'] )
                                && $info['always_hide'] 
                                && ( $info['always_hide_for'] == 'all-roles' ) 
                                ) { // for all roles

                                $menu[$menu_key][4] = $menu[$menu_key][4] . ' always-hidden';

                            } elseif ( isset( $info['always_hide'] ) 
                                && $info['always_hide']
                                && $info['always_hide_for'] == 'all-roles-except'
                                && ! empty( $info['which_roles'] )
                                ) { // for all roles except

                                foreach ( $current_user_roles as $current_user_role ) {
                                    if ( ! in_array( $current_user_role, $info['which_roles'] ) ) {

                                        $menu[$menu_key][4] = $menu[$menu_key][4] . ' always-hidden';

                                    }
                                }
                                
                            } elseif ( isset( $info['always_hide'] )
                                && $info['always_hide'] 
                                && $info['always_hide_for'] == 'selected-roles'
                                && ! empty( $info['which_roles'] )
                                ) { // for selected roles
                                
                                foreach ( $current_user_roles as $current_user_role ) {
                                    foreach( $info['which_roles'] as $role_menu_is_hidden_for ) {
                                        if ( $current_user_role == $role_menu_is_hidden_for ) {

                                            $menu[$menu_key][4] = $menu[$menu_key][4] . ' always-hidden';
                                            
                                        }
                                    }
                                }
                                
                            }
                        }
                    }                   
                }
            }

        }

    }

    /**
     * Add toggle to show hidden menu items
     *
     * @since 2.0.0
     */
    public function add_hidden_menu_toggle() {
        
        global $current_user;

        // Get menu items hidden by toggle
        $common_methods = new Common_Methods;       
        $menu_hidden_by_toggle = $common_methods->get_menu_hidden_by_toggle();

        // Get user capabilities the "Show All/Less" toggle should be shown for
        $user_capabilities_to_show_menu_toggle_for = $common_methods->get_user_capabilities_to_show_menu_toggle_for();

        // Get current user's capabilities from the user's role(s)
        $current_user_capabilities = '';
        $current_user_roles = $current_user->roles; // indexed array of role slugs
        foreach( $current_user_roles as $current_user_role ) {
            $current_user_role_capabilities = get_role( $current_user_role )->capabilities;
            $current_user_role_capabilities = array_keys( $current_user_role_capabilities ); // indexed array
            $current_user_role_capabilities = implode( ",", $current_user_role_capabilities );
            $current_user_capabilities .= $current_user_role_capabilities;
        }
        $current_user_capabilities = array_unique( explode(",", $current_user_capabilities) );

        // Maybe show "Show All/Less" toggle
        $show_toggle_menu = false;
        foreach( $user_capabilities_to_show_menu_toggle_for as $user_capability_to_show_menu_toggle_for ) {
            if ( in_array( $user_capability_to_show_menu_toggle_for, $current_user_capabilities ) ) {
                $show_toggle_menu = true;
                break;
            }
        }

        if ( ! empty( $menu_hidden_by_toggle ) && $show_toggle_menu ) {

            add_menu_page(
                'Show All',
                'Show All',
                'read',
                'asenha_show_hidden_menu',
                function () {  return false;  },
                "dashicons-arrow-down-alt2",
                300 // position
            );

            add_menu_page(
                'Show Less',
                'Show Less',
                'read',
                'asenha_hide_hidden_menu',
                function () {  return false;  },
                "dashicons-arrow-up-alt2",
                301 // position
            );
        }

    }

    /**
     * Script to toggle hidden menu itesm
     *
     * @since 2.0.0
     */
    public function enqueue_toggle_hidden_menu_script() {

        // Get menu items hidden by toggle
        $common_methods = new Common_Methods;
        $menu_hidden_by_toggle = $common_methods->get_menu_hidden_by_toggle();

        if ( ! empty( $menu_hidden_by_toggle ) ) {

            // Script to set behaviour and actions of the sortable menu
            wp_enqueue_script( 'asenha-toggle-hidden-menu', ASENHA_URL . 'assets/js/toggle-hidden-menu.js', array(), ASENHA_VERSION, false );

        }

    }
    
    /**
     * Maybe restrict access to current screen based on 'hide', 'hide always', 'for all/some roles' settings for each menu item
     * 
     * @since 5.1.0
     */
    public function maybe_restrict_access_to_current_screen__premium_only() {
        global $wp, $menu, $submenu;

        // $current_screen = get_current_screen(); // WP_Screen object
        // $current_screen_id = $current_screen->id;
        // $current_screen_parent_file = $current_screen->parent_file;

        $current_user = wp_get_current_user();
        $current_user_roles = array_values( $current_user->roles ); // indexed array

        $common_methods = new Common_Methods;
        $always_hidden_menu_url_fragments = $common_methods->get_url_fragments_of_always_hidden_menu_pages__premium_only();
                
        // Get the page url id for the current admin page
        $current_admin_page_url_fragment = add_query_arg( $wp->query_vars ); // e.g. /wp-admin/admin.php?page=some-admin-page-slug

        if ( '/wp-admin/' == $current_admin_page_url_fragment && ! in_array( 'index.php', $always_hidden_menu_url_fragments ) ) {
            // do nothing, this is the dashboard at /wp-admin/ without index.php and it's not restricted for access
            $page_url_id = $current_admin_page_url_fragment; 
        } elseif ( '/wp-admin/' == $current_admin_page_url_fragment && in_array( 'index.php', $always_hidden_menu_url_fragments ) ) {
            // we're on the dashboard at /wp-admin/ and it (index.php) is restricted for access, so, change url ID to 'index.php'
            $page_url_id = 'index.php';
        } elseif ( false !== strpos( $current_admin_page_url_fragment, '?page=' ) ) {
            $current_admin_page_url_fragments = explode( '?page=', $current_admin_page_url_fragment );
            $page_url_id = $current_admin_page_url_fragments[1]; // e.g. some-admin-page-slug
        } elseif ( false !== strpos( $current_admin_page_url_fragment, '?' ) ) {
            $page_url_id = str_replace( '/wp-admin/', '', $current_admin_page_url_fragment ); // e.g. post-new.php?post_type=todos
        } else {
            $page_url_id = str_replace( '/wp-admin/', '', $current_admin_page_url_fragment ); // e.g. edit-comments.php or plugins.php  
        }

        $restrict_access = false;
        
        if ( in_array( $page_url_id, $always_hidden_menu_url_fragments ) ) {
            
            // Maybe restrict access for (parent) admin page

            // Get data for which roles should (parent) menu page be hidden for
            $always_hide_for_raw = $common_methods->get_always_hide_for__premium_only( $page_url_id );

            if ( isset( $always_hide_for_raw[$page_url_id]['always_hide'] ) && $always_hide_for_raw[$page_url_id]['always_hide'] ) {
                $always_hide = true;
            } else {
                $always_hide = false;           
            }
            if ( isset( $always_hide_for_raw[$page_url_id]['always_hide_for'] ) ) {
                $always_hide_for = $always_hide_for_raw[$page_url_id]['always_hide_for'];           
            } else {
                $always_hide_for = '';
            }
            if ( isset( $always_hide_for_raw[$page_url_id]['which_roles'] ) ) {
                $which_roles = $always_hide_for_raw[$page_url_id]['which_roles'];           
            } else {
                $which_roles = array();
            }

            // Check if current user's role(s) should be restricted from access
            
            if ( $always_hide && ( $always_hide_for == 'all-roles' ) ) {
                $restrict_access = true;
            } elseif ( $always_hide && ( $always_hide_for == 'all-roles-except' ) && ! empty( $which_roles ) ) { 
                $intersecting_roles = array_intersect( $current_user_roles, $which_roles );
                if ( empty( $intersecting_roles ) ) {
                    $restrict_access = true;
                }
            } elseif ( $always_hide && ( $always_hide_for == 'selected-roles' ) && ! empty( $which_roles ) ) {
                $intersecting_roles = array_intersect( $current_user_roles, $which_roles );
                if ( ! empty( $intersecting_roles ) ) {
                    $restrict_access = true;
                }               
            }
            
        } else {
            
            // Maybe restrict access for (child) admin pages whose parent has been restricted

            if ( is_array( $submenu ) ) {               
                foreach( $submenu as $menu_id => $submenu_items ) {
                    if ( in_array( $menu_id, $always_hidden_menu_url_fragments ) ) {
                        foreach( $submenu_items as $submenu_item_priority => $submenu_item_info ) {
                            if ( $page_url_id == $submenu_item_info[2] ) {

                                // Get roles for which parent menu page should be hidden for
                                $always_hide_for_raw = $common_methods->get_always_hide_for__premium_only( $menu_id );

                                if ( isset( $always_hide_for_raw[$menu_id]['always_hide'] ) && $always_hide_for_raw[$menu_id]['always_hide'] ) {
                                    $always_hide = true;
                                } else {
                                    $always_hide = false;           
                                }
                                if ( isset( $always_hide_for_raw[$menu_id]['always_hide_for'] ) ) {
                                    $always_hide_for = $always_hide_for_raw[$menu_id]['always_hide_for'];           
                                } else {
                                    $always_hide_for = '';
                                }
                                if ( isset( $always_hide_for_raw[$menu_id]['which_roles'] ) ) {
                                    $which_roles = $always_hide_for_raw[$menu_id]['which_roles'];           
                                } else {
                                    $which_roles = array();
                                }
                                
                                // Check if current user's role(s) should be restricted from access

                                if ( $always_hide && ( $always_hide_for == 'all-roles' ) ) {
                                    $restrict_access = true;
                                } elseif ( $always_hide && ( $always_hide_for == 'all-roles-except' ) && ! empty( $which_roles ) ) { 
                                    $intersecting_roles = array_intersect( $current_user_roles, $which_roles );
                                    if ( empty( $intersecting_roles ) ) {
                                        $restrict_access = true;
                                    }
                                } elseif ( $always_hide && ( $always_hide_for == 'selected-roles' ) && ! empty( $which_roles ) ) {
                                    $intersecting_roles = array_intersect( $current_user_roles, $which_roles );
                                    if ( ! empty( $intersecting_roles ) ) {
                                        $restrict_access = true;
                                    }               
                                }
                                                        
                            }
                        }
                    }
                }
            }
            
        }
        
        if ( $restrict_access ) {
            wp_die( 'Sorry, you are not allowed to access this page.' );        
        }
                
    }
    
    /**
     * Reset admin menu via AJAX
     * 
     * @since 6.3.1
     */
    public function reset_admin_menu__premium_only() {

        if ( isset( $_REQUEST ) ) {
            if ( check_ajax_referer( 'reset-menu-nonce', 'nonce', false ) ) {

                $options = get_option( ASENHA_SLUG_U, array() );
                
                unset( $options['custom_menu_order'] );
                unset( $options['custom_menu_titles'] );
                unset( $options['custom_submenus_order'] );
                unset( $options['custom_menu_always_hidden'] );
                unset( $options['custom_menu_hidden'] );

                $updated = update_option( ASENHA_SLUG_U, $options );
                
                if ( $updated ) {
                    $response = array(
                        'status'    => 'success',
                    );
                } else {
                    $response = array(
                        'status'    => 'failed',
                    );                  
                }
                
                echo json_encode( $response );
            }
        }
        
    }
    
}