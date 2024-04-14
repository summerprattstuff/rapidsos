<?php

namespace ASENHA\Classes;

use ArrayObject;
use NumberFormatter;

/**
 * Class for Admin Columns Manager module
 *
 * @since 6.9.5
 */
class Admin_Columns_Manager {
    
    /**
     * Register admin menu for Admin Columns Organizer
     * 
     * @since 5.3.0
     */
    public function add_admin_columns_organizer_menu__premium_only() {

        $hook_suffix = add_submenu_page(
            'options-general.php', // Parent page/menu
            'Admin Columns Manager', // Browser tab/window title
            'Admin Columns Manager', // Sub menu title
            'manage_options', // Minimal user capabililty
            'admin-columns', // Page slug. Shows up in URL.
            array( $this, 'render_admin_columns_organizer_settings_page__premium_only' )
        );

        add_action( 'admin_print_styles-' . $hook_suffix, [ $this, 'enqueue_admin_columns_styles__premium_only' ] );
        add_action( 'admin_print_scripts-' . $hook_suffix, [ $this, 'enqueue_admin_columns_scripts__premium_only' ] );

    }

    /**
     * Enqueue styles for content order pages
     * 
     * @since 5.0.0
     */
    public function enqueue_admin_columns_styles__premium_only() {
        wp_enqueue_style( 
            'admin-columns', 
            ASENHA_URL . 'assets/premium/css/admin-columns.css', 
            array(), 
            ASENHA_VERSION 
        );
    }

    /**
     * Enqueue scripts for content order pages
     * 
     * @since 5.0.0
     */
    public function enqueue_admin_columns_scripts__premium_only() {
        global $typenow;
        wp_enqueue_script( 
            'admin-columns', 
            ASENHA_URL . 'assets/premium/js/admin-columns.js', 
            array( 'jquery', 'jquery-ui-sortable', 'jquery-touch-punch' ), 
            ASENHA_VERSION 
        );
        wp_localize_script(
            'admin-columns',
            'adminColumns',
            array(
                'nonce'         => wp_create_nonce( 'admin-columns-nonce' ),
            )
        );
    }
    
    /**
     * Add scripts for content list tables
     * 
     * @since 5.3.0
     */
    public function add_list_tables_scripts__premium_only( $hook_suffix ) {
        
        // List tables of pages, posts and CPTs
        if ( 'edit.php' == $hook_suffix ) {
            wp_enqueue_style( 'asenha-list-tables-organize-columns', ASENHA_URL . 'assets/premium/css/list-tables-organize-columns.css', array(), ASENHA_VERSION );
            wp_enqueue_script( 'asenha-list-tables-organize-columns', ASENHA_URL . 'assets/premium/js/list-tables-organize-columns.js', array( 'jquery' ), ASENHA_VERSION, false );
        }
        
    }
    
    /**
     * Render settings page for Admin Columns Organizer
     * 
     * @since 5.3.0
     */
    public function render_admin_columns_organizer_settings_page__premium_only() {
        global $taxonomy_slugs; // indexed array of taxonomy slugs
        
        // Render post type selection and set it to the value of post_type URL parameter if it exist
                
        $post_types = get_post_types( array(
            'show_ui'   => true,
        ), 'objects' );
        
        $post_types_sorted = array();

        foreach ( $post_types as $post_type => $post_type_object ) {
            $post_types_sorted[$post_type] = $post_type_object->label;
        }

        $post_types_sorted = new ArrayObject( $post_types_sorted );
        $post_types_sorted->asort();
                
        $excluded_post_types = array(
            'attachment',
            'wp_navigation',
        );

        // Get current and default columns

        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $selected_post_type = isset( $_GET['for'] ) ? $_GET['for'] : 'post';
        
        // Get the URL to view listing page for the selected post type
        
        if ( 'post' != $selected_post_type && 'attachment' != $selected_post_type ) {
            $view_all_posts_url_string = 'edit.php?post_type=' . $selected_post_type;           
        } else {
            if ( 'post' == $selected_post_type ) {
                $view_all_posts_url_string = 'edit.php';                    
            // } elseif ( 'attachment'  == $selected_post_type ) {
            //  $view_all_posts_url_string = 'upload.php';                  
            } else {}
        }

        // Get the default columns, which differs by selected post type
        $available_columns = $this->get_default_columns( $selected_post_type );
        $available_columns_keys = array_keys( $available_columns );
        
        // Get extra columns, e.g. from plugins
        $extra_columns = ( isset( $options['admin_columns_extra'][$selected_post_type] ) ) ? $options['admin_columns_extra'][$selected_post_type] : array();
        $extra_columns_keys = array_keys( $extra_columns );

        $can_use_original_title = array(
            'default' => array(
                'comments'
            ),
        );
        
        // Output of the admin columns settings page
        ?>
        <div class="wrap">
            <div class="asenha-heading-inline-wrap">
                <h1 class="wp-heading-inline">Admin Columns Manager for </h1>
                <select name="organize-admin-columns-for" class="asenha-select-inline">
                <?php foreach( $post_types_sorted as $post_type => $post_type_label ): ?>
                    <?php if ( ! in_array( $post_type, $excluded_post_types ) ): ?>
                    
                        <option value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $post_type_label ); ?> (<?php echo esc_html( $post_type ); ?>)</option>
                        
                    <?php endif; ?>                 
                <?php endforeach; ?>
                </select>
                <a class="button button-secondary view-button" href="<?php echo esc_url( admin_url( $view_all_posts_url_string ) ); ?>" target="_blank">View</a>
                <img src="https://wpplugins-build.dev.bowo.io/wp-content/plugins/admin-site-enhancements-freemium/assets/img/oval.svg" id="spinner-img" class="spinner-img" style="display:none;" />
            </div>
        <?php

        // Get the currently active columns

        $current_columns = isset( $options['admin_columns'][$selected_post_type] ) ? $options['admin_columns'][$selected_post_type] : array() ;
        $current_columns_keys = array_keys( $current_columns );
                
        if ( ! empty( $current_columns ) ) {

            // Remove column for bulk editing checkbox. This will be added back via AJAX action in PHP when saving changes.
            unset( $current_columns['cb'] ); 

            // Get custom fields data
            $custom_fields = $this->get_custom_field_data__premium_only( $selected_post_type, 10, 'comprehensive' );
            $custom_fields_keys = array_keys( $custom_fields );

            ?>
            <div class="admin-columns-data sticky-container">

                <div class="admin-columns-current"><!-- Start of ACTIVE columns -->
                    <div class="columns-heading-container">
                        <div class="columns-heading-first">
                            <h2>Active</h2>
                            <a href="#" class="button button-secondary expand-collapse-all">Expand all</a>
                        </div>
                        <div class="columns-heading-second">
                            <span class="set-all-to">Set all to:</span>
                            <a href="#" id="set-width-pixel" class="button button-secondary set-width-pixel">100 px</a>
                            <a href="#" id="set-width-percent" class="button button-secondary set-width-percent">%</a>
                            <a href="#" id="set-width-auto" class="button button-secondary set-width-auto">Auto</a>
                        </div>
                    </div>
                    <div class="active-columns-container">
                        <div id="current-columns" class="sortable-columns">
                        <?php foreach ( $current_columns as $column_key => $column_data ): ?>
                            <?php
                            $handler = '';
                            $handler_title = '';
                            $type = '';
                            $original_title = $column_data['title'];
                            $custom_title = ( isset( $column_data['custom_title'] ) ) ? $column_data['custom_title'] : $column_data['title'];
                            $use_original_title = ( isset( $column_data['use_original_title'] ) && $column_data['use_original_title'] ) ? 'yes' : 'no';
                            $width_number = ( isset( $column_data['width'] ) ) ? $column_data['width'] : '';
                            $width_type = ( isset( $column_data['width_type'] ) ) ? $column_data['width_type'] : '';
                            $is_taxonomy = ( is_array( $taxonomy_slugs ) && in_array( $column_key, $taxonomy_slugs ) ) ? true : false;

                            if ( empty( $width_number ) || empty( $width_type ) ) {
                                $width_type = 'auto';
                            }
                            if ( $width_type == 'auto' ) {
                                $width_type_label = 'Auto';
                            } else {
                                $width_type_label = $width_type;
                            }
                            
                            // Figure out if current column is an extra column, e.g. from plugin, or from a custom field
                            if ( in_array( $column_key, $custom_fields_keys ) ) {
                                $is_extra_column = 'no';
                                $is_custom_field = 'yes';
                                $may_use_original_title = false;
                            } elseif ( in_array( $column_key, $available_columns_keys ) ) {
                                $is_extra_column = 'no';
                                $is_custom_field = 'no';
                                $may_use_original_title = false;
                                if ( in_array( $column_key, $can_use_original_title['default'] ) ) {
                                    $may_use_original_title = true;
                                }
                            } else {
                                $is_extra_column = 'yes';
                                $is_custom_field = 'no';
                                $may_use_original_title = true;
                            }

                            $is_sortable = $this->check_column_sortability__premium_only( $selected_post_type, $column_key, $column_data );

                            // For custom field columns, figure out which plugin is the handler and what is the field type
                            // $is_custom_field = ( in_array( $column_key, $custom_fields_keys ) ) ? 'yes' : 'no';
                            foreach ( $custom_fields as $custom_field_key => $custom_field_data ) {
                                if ( $custom_field_key == $column_key ) {
                                    $handler = $custom_field_data['handler'];
                                    $type = $custom_field_data['type'];                         
                                }
                            }
                            $column_key_class = '';

                            // Output sortables for active columns
                            ?>
                            <div class="sortable-item active" data-search-filter data-column-key="<?php echo esc_attr( $column_key ); ?>" data-column-title="<?php echo esc_attr( $column_data['title'] ); ?>" data-use-original-title="<?php echo esc_attr( $use_original_title ); ?>" data-is-extra-field="<?php echo esc_attr( $is_extra_column ); ?>" data-is-custom-field="<?php echo esc_attr( $is_custom_field ); ?>" data-is-sortable="<?php echo esc_attr( $is_sortable ); ?>">
                                <?php
                                require ASENHA_PATH . 'includes/premium/admin-columns/sortable-item-bar.php';
                                ?>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="save-changes">
                        <a class="button button-primary save-button">Save Changes</a>
                        <div class="saving-progress" style="display:none;"><img src="https://wpplugins-build.dev.bowo.io/wp-content/plugins/admin-site-enhancements-freemium/assets/img/oval.svg" id="saving-spinner-img" class="spinner-img" /><div class="changes-saved" style="display:none;"><span class="dashicons dashicons-yes"></span>Changes have been saved</div></div>
                        <div class="reset-columns-wrapper"><img src="https://wpplugins-build.dev.bowo.io/wp-content/plugins/admin-site-enhancements-freemium/assets/img/oval.svg" id="reset-columns-spinner-img" class="spinner-img" style="display:none" />
                        <a id="reset-columns" class="button button-secondary">Reset Columns</a></div>
                    </div>

                    <div class="admin-columns-discarded" style="display:none;"><!-- Start of DISCARDED columns -->
                        <div class="columns-heading-container">
                            <h2 class="discarded-columns-heading">Discarded</h2>
                        </div>
                        <div class="discarded-columns-container">
                            <div id="discarded-columns" class="sortable-columns">
                            </div>
                        </div>
                    </div><!-- End of DISCARDED columns -->
                </div><!-- End of ACTIVE columns -->

                <div class="admin-columns-available"><!-- Start of AVAILABLE columns -->

                    <div class="columns-heading-container"><!-- Start of DEFAULT columns -->
                        <h2 class="default-columns-heading">Default</h2>
                        <input id="column-search-input" type="search" placeholder="Search..." />
                    </div>
                    <div class="available-columns-container">
                        <div id="default-columns" class="sortable-columns">
                        <?php foreach ( $available_columns as $column_key => $column_title ): ?>
                            <?php
                            $handler = '';
                            $handler_title = '';
                            $original_title = $column_title;
                            $custom_title = $column_title;
                            if ( in_array( $column_key, $can_use_original_title['default'] ) ) {
                                $may_use_original_title = true;
                                $use_original_title = 'yes';
                            } else {
                                $may_use_original_title = false;
                                $use_original_title = 'no';
                            }
                            $width_number = '';
                            $width_type = 'Auto';
                            $is_taxonomy = ( is_array( $taxonomy_slugs ) && in_array( $column_key, $taxonomy_slugs ) ) ? true : false;
                            $is_extra_column = 'no';
                            $is_custom_field = 'no';
                            $is_sortable = $this->check_column_sortability__premium_only( $selected_post_type, $column_key, $column_title );
                            ?>
                            <?php if ( ! in_array( $column_key, $current_columns_keys ) ): ?>
                                <div class="sortable-item default" data-search-filter data-column-key="<?php echo esc_attr( $column_key ); ?>" data-column-title="<?php echo esc_attr( $column_title ); ?>" data-use-original-title="<?php echo esc_attr( $use_original_title ); ?>" data-is-extra-field="<?php echo esc_attr( $is_extra_column ); ?>" data-is-custom-field="<?php echo esc_attr( $is_custom_field ); ?>" data-is-sortable="<?php echo esc_attr( $is_sortable ); ?>">
                                    <?php
                                    require ASENHA_PATH . 'includes/premium/admin-columns/sortable-item-bar.php';
                                    ?>                                  
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                    </div><!-- End of AVAILABLE columns -->

                    <div class="columns-heading-container"><!-- Start of EXTRA columns -->
                        <h2 class="extra-columns-heading">Extra<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 20 20"><path fill="currentColor" d="M13.11 4.36L9.87 7.6L8 5.73l3.24-3.24c.35-.34 1.05-.2 1.56.32c.52.51.66 1.21.31 1.55zm-8 1.77l.91-1.12l9.01 9.01l-1.19.84c-.71.71-2.63 1.16-3.82 1.16H6.14L4.9 17.26c-.59.59-1.54.59-2.12 0a1.49 1.49 0 0 1 0-2.12l1.24-1.24v-3.88c0-1.13.4-3.19 1.09-3.89zm7.26 3.97l3.24-3.24c.34-.35 1.04-.21 1.55.31c.52.51.66 1.21.31 1.55l-3.24 3.25z"/></svg></h2>
                    </div>
                    <div class="extra-columns-container<?php if ( empty( $extra_columns ) ) : ?> no-columns<?php endif; ?>">
                        <div id="extra-columns" class="sortable-columns">
                        <?php if ( ! empty( $extra_columns ) ) : ?>
                            <?php foreach ( $extra_columns as $column_key => $column_data ): ?>
                                <?php
                                $handler = '';
                                $handler_title = '';
                                $original_title = $column_data['title'];
                                $custom_title = ( isset( $column_data['custom_title'] ) ) ? $column_data['custom_title'] : $column_data['title'];
                                $may_use_original_title = true;
                                $use_original_title = 'no';
                                $width_number = '';
                                $width_type = 'Auto';
                                $is_taxonomy = false;
                                $is_extra_column = 'yes';
                                $is_custom_field = 'no';
                                $is_sortable = 'maybe';
                                ?>
                                <?php if ( ! in_array( $column_key, $current_columns_keys ) ): ?>
                                    <div class="sortable-item default" data-search-filter data-column-key="<?php echo esc_attr( $column_key ); ?>" data-column-title="<?php echo esc_attr( $column_title ); ?>" data-use-original-title="<?php echo esc_attr( $use_original_title ); ?>" data-is-extra-field="<?php echo esc_attr( $is_extra_column ); ?>" data-is-custom-field="<?php echo esc_attr( $is_custom_field ); ?>" data-is-sortable="<?php echo esc_attr( $is_sortable ); ?>">
                                        <?php
                                        require ASENHA_PATH . 'includes/premium/admin-columns/sortable-item-bar.php';
                                        ?>                                  
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="extra-columns-empty">No extra columns, e.g. from plugins, available at the moment.</div>
                        <?php endif; ?>
                        <?php if ( empty( $extra_columns ) ) : ?>
                        <?php endif; ?>
                        </div>
                    </div><!-- End of EXTRA columns -->

                    <div class="columns-heading-container"><!-- Start of CUSTOM FIELD columns -->
                        <!-- https://icon-sets.iconify.design/pixelarticons/card-plus/ -->
                        <h2 class="custom-fields-columns-heading">Custom Fields<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"><path fill="#1d2327" d="M22 4H2v16h10v-2H4V6h16v4h2V4zm-3 13h3v-2h-3v-3h-2v3h-3v2h3v3h2v-3z"/></svg><a id="reindex-custom-fields" class="button button-secondary">Re-Index</a><img src="https://wpplugins-build.dev.bowo.io/wp-content/plugins/admin-site-enhancements-freemium/assets/img/oval.svg" id="reindex-custom-fields-spinner-img" class="spinner-img" style="display:none" /></h2>
                        <div class="heading-action-wrapper"><input type="checkbox" id="toggle-private-fields" name="private-field" value="" /><label for="toggle-private-fields">Show private fields</label></div>
                    </div>
                    <div class="custom-fields-columns-container">
                        <?php
                        $custom_fields = $this->get_custom_field_data__premium_only( $selected_post_type, 10, 'comprehensive' );
                        // sort( $custom_fields );
                        ?>
                        <div id="custom-field-columns" class="sortable-columns">
                        <?php foreach ( $custom_fields as $custom_field_key => $custom_field_data ): ?>
                            <?php
                            $handler = $custom_field_data['handler'];
                            if ( $handler == 'ASE' ) {
                                $handler_title = 'Admin and Site Enhancements';
                            } elseif ( $handler == 'ACF' ) {
                                $handler_title = 'Advanced Custom Fields';
                            }
                            $type = $custom_field_data['type'];
                            $column_key = $custom_field_key;
                            $original_title = $column_title;
                            $custom_title = ( ! empty( $custom_field_data['label'] ) ) ? $custom_field_data['label'] : $custom_field_key;
                            if ( $custom_title == $custom_field_key ) {
                                $column_key_class = ' hide-key';
                            } else {
                                $column_key_class = '';
                            }
                            $may_use_original_title = false;
                            $use_original_title = 'no';
                            $width_number = '';
                            $width_type = 'Auto';
                            $is_taxonomy = false;
                            $is_extra_column = 'no';
                            $is_custom_field = 'yes';
                            $is_private_field = $custom_field_data['is_private'];
                            $is_sortable = $this->check_column_sortability__premium_only( $selected_post_type, $custom_field_key, $custom_field_data );
                            ?>
                            <?php if ( 
                                ! in_array( $custom_field_key, $current_columns_keys ) 
                                // && ( '_' != substr( $custom_field_key, 0, 1 ) ) 
                                // ASE - tab
                                && ( $custom_field_data['type'] != 'tab' )
                                // Sub-field in ASE
                                && ( ! $custom_field_data['is_sub_field'] )
                            ): ?>
                                <div class="sortable-item custom-field<?php if ( $is_private_field ): ?> private-field<?php else : ?> public-field<?php endif; ?>" data-search-filter data-column-key="<?php echo esc_attr( $custom_field_key ); ?>" data-column-title="<?php echo esc_attr( $custom_title ); ?>" data-use-original-title="<?php echo esc_attr( $use_original_title ); ?>" data-is-extra-field="<?php echo esc_attr( $is_extra_column ); ?>" data-is-custom-field="<?php echo esc_attr( $is_custom_field ); ?>" data-is-sortable="<?php echo esc_attr( $is_sortable ); ?>">
                                    <?php
                                    require ASENHA_PATH . 'includes/premium/admin-columns/sortable-item-bar.php';
                                    ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                    </div><!-- End of CUSTOM FIELD columns -->

                </div><!-- End of AVAILABLE columns -->
            
            <!-- <form action="options.php" method="post"> -->
                <?php
                // settings_fields( ASENHA_ID . '_admin_columns' ); // Group ID
                // do_settings_sections( 'admin-columns' ); // Settings page slug

                // foreach( $post_types as $post_type => $post_type_object ) {
                //  if ( ! in_array( $post_type, $excluded_post_types ) ) {
                //      $columns = $options['admin_columns_current_for_' . $post_type];
                //      $columns = json_encode( $columns, true );
                //      echo '<input type="hidden" id="admin_site_enhancements_admin_columns[admin_columns_current_for_' . $post_type . ']" class="asenha-subfield-text" name="admin_site_enhancements_admin_columns[admin_columns_current_for_' . $post_type . ']" value="' . $columns . '">';
                //  }
                // }

                // submit_button(
                //  'Save Changes', // Button copy
                //  'primary', // Type: 'primary', 'small', or 'large'
                //  'submit', // The 'name' attribute
                //  true, // Whether to wrap in <p> tag
                //  array( 'id' => 'asenha-submit' ) // additional attributes
                // );
                ?>
            <!-- </form> -->

            </div><!-- end of .wrap -->

            <?php

        } else {
            ?>
                <div class="instructions">Please <a href="<?php echo esc_url( admin_url( $view_all_posts_url_string ) ); ?>">visit the listing page</a> for <?php echo esc_html( $selected_post_type ); ?> first and then click on the "Manage Columns" button there to start managing admin columns for it.</div>
            <?php
        }
        
    }
    
    /** 
     * Check if a column is sortable
     * 
     * @since 6.4.1
     */
    public function check_column_sortability__premium_only( $selected_post_type, $column_key, $column_info ) {

        $common_methods = new Common_Methods;
        $most_recent_post_id = $common_methods->get_most_recent_post_id( $selected_post_type );

        // Define columns that are sortable (ASC / DESC)

        $sortable_columns = array(
            'default'       => array(
                'title',
                'slug',
                'permalink',
                'date',
                'postid',
                'post_parent',
                'author',
                'status',
                'password_protected',
                'menu_order', 
                'date_published', 
                'modified',
                'sticky',
                'post_formats',
                'comments',
                'comment_count',
                'comment_status',
                'ping_status',
                'post_formats',
                'wp_pattern_sync_status',
            ),
            'custom_field'  => array(
                'ase_field_types'   => array(
                    'text',
                    'true_false',
                    'select', // except multiple
                    'file', // except image
                    'hyperlink',
                    'date',
                    'color',
                ),
                'acf_field_types'   => array(
                    'text',
                    'number',
                    'url',
                    'file',
                    'range',
                    'email',
                    'oembed',
                    'select', // except multiple
                    'radio',
                    'button_group',
                    'true_false',
                    'link',
                    'post_object', // except multiple
                    'page_link', // except multiple
                    'date_picker',
                    'date_time_picker',
                    'time_picker',
                    'color_picker',
                ),
                'mb_field_types'    => array(
                    'checkbox',
                    'text',
                    'radio',
                    'select',
                    'select_advanced',
                    'button_group',
                    'email',
                    'range',
                    'number',
                    'url',
                    'color',
                    'switch',                   
                    'slider',
                    'oembed',
                    'map',
                    'osm',
                    'datetime',
                    'date',
                    'time',
                    'post',
                    'taxonomy',
                    'taxonomy_advanced',
                    'user',
                    'sidebar',
                ),
            ),
        );

        $options = get_option( ASENHA_SLUG_U . '_extra', array() );

        // $current_columns = isset( $options['admin_columns'][$selected_post_type] ) ? $options['admin_columns'][$selected_post_type] : array() ;
        // $current_columns_keys = array_keys( $current_columns );

        $available_columns = $this->get_default_columns( $selected_post_type );
        $available_columns_keys = array_keys( $available_columns );

        // $extra_columns = ( isset( $options['admin_columns_extra'][$selected_post_type] ) ) ? $options['admin_columns_extra'][$selected_post_type] : array();
        // $extra_columns_keys = array_keys( $extra_columns );
        
        $custom_fields = $this->get_custom_field_data__premium_only( $selected_post_type, 10, 'comprehensive' );
        $custom_fields_keys = array_keys( $custom_fields );

        // Determine sortable status: yes, no, or maybe
        if ( in_array( $column_key, $custom_fields_keys ) ) {
            $is_sortable = 'maybe';

            if ( 'ASE' == $custom_fields[$column_key]['handler'] && class_exists( 'Custom_Field_Group' ) ) {
                if ( in_array( $custom_fields[$column_key]['type'], $sortable_columns['custom_field']['ase_field_types'] ) ) {
                    $is_sortable = 'yes';

                    $field_info = CFG()->get_field_info( $column_key, $most_recent_post_id );

                    // Prevent multiple 'select' field from being sortable
                    if ( 'select' == $field_info['type'] && true == $field_info['options']['multiple'] ) {
                        $is_sortable = 'no';
                    }

                    // Prevent image 'file' field from being sortable
                    if ( 'file' == $field_info['type'] && 'image' == $field_info['options']['file_type'] ) {
                        $is_sortable = 'no';
                    }

                }
            }

            if ( 'ACF' == $custom_fields[$column_key]['handler'] && class_exists( 'ACF' ) ) {
                if ( in_array( $custom_fields[$column_key]['type'], $sortable_columns['custom_field']['acf_field_types'] ) ) {
                    $is_sortable = 'yes';

                    $field_info = get_field_object( $column_key, $most_recent_post_id );
                    
                    // Prevent multiple 'select' field from being sortable
                    if ( ( 'select' == $field_info['type'] || 'post_object' == $field_info['type'] || 'page_link' == $field_info['type'] ) 
                        && true == $field_info['multiple'] 
                    ) {
                        $is_sortable = 'no';
                    }

                }
            }

            if ( 'MB' == $custom_fields[$column_key]['handler'] && class_exists( 'RWMB_Core' ) ) {
                if ( in_array( $custom_fields[$column_key]['type'], $sortable_columns['custom_field']['mb_field_types'] ) ) {
                    $is_sortable = 'yes';
                    
                    $field_info = rwmb_get_field_settings( $column_key, '', $most_recent_post_id );
                    $is_cloneable = ( isset( $field_info['clone'] ) && true == $field_info['clone'] ) ? true : false;
                    $is_multiple = ( isset( $field_info['multiple'] ) && true == $field_info['multiple'] ) ? true : false;
                    
                    if ( $is_cloneable ) {
                        // Prevent a clonable field from being sortable
                        $is_sortable = 'no';
                    } else {
                        if ( $is_multiple ) {
                            // Prevent field with multiple selections/values from being sortable
                            $is_sortable = 'no';
                        }
                    }
                }
            }           

        } elseif ( in_array( $column_key, $available_columns_keys ) ) {
            $is_sortable = 'no';

            if ( in_array( $column_key, $sortable_columns['default'] ) ) {
                $is_sortable = 'yes';
            }
        } else {
            $is_sortable = 'maybe'; // extra columns            
        }
        
        return $is_sortable;
    }
    
    /**
     * Register settings field for Admin Columns options page
     * Currently unused but kept for reference. Saving is done via AJAX instead.
     * 
     * @since 5.3.0
     */
    public function register_admin_columns_sections_fields__premium_only() {
        
        add_settings_section(
            'main-section', // Section ID
            '', // Section title. Can be blank.
            '', // Callback function to output section intro. Can be blank.
            'admin-columns' // Settings page slug
        );

        // Register main setttings

        // Instantiate object for sanitization of settings fields values
        // $sanitization = new Settings_Sanitization;

        // Instantiate object for rendering of settings fields for the admin page
        $render_field = new Settings_Fields_Render;

        register_setting( 
            ASENHA_ID . '_admin_columns' , // Option group ID or option_page
            ASENHA_SLUG_U . '_admin_columns', // Option name in wp_options table
            array(
                'type'                  => 'array', // 'string', 'boolean', 'integer', 'number', 'array', or 'object'
                'description'           => '', // A description of the data attached to this setting.
                'sanitize_callback'     => [ $this, 'sanitize_admin_columns_options__premium_only' ],
                'show_in_rest'          => false,
                'default'               => array(), // When calling get_option()
            )
        );

        // $field_id = 'admin_columns_checkbox_field_test';
        // $field_slug = 'admin-columns-checkbox-field-test';

        // add_settings_field(
        //  $field_id, // Field ID
        //  '', // Field title
        //  [ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
        //  'admin-columns', // Settings page slug
        //  'main-section', // Section ID
        //  array(
        //      'option_name'           => ASENHA_SLUG_U . '_admin_columns', // Option name in wp_options table
        //      'field_id'              => $field_id, // Custom argument
        //      'field_name'            => ASENHA_SLUG_U . '_admin_columns[' . $field_id . ']', // Custom argument
        //      // 'field_name'         => $field_id, // Custom argument
        //      'field_label'           => 'For testing if this checkbox input is saved correctly.', // Custom argument
        //      'class'                 => 'asenha-checkbox asenha-hide-th ' . $field_slug, // Custom class for the <tr> element
        //  )
        // );
        
    }
    
    /**
     * Sanitize options for admin columns
     * Currently unused but kept for reference. Saving is done via AJAX instead.
     * 
     * @since 5.3.0
     */
    public function sanitize_admin_columns_options__premium_only( $options ) {

        if ( ! isset( $options['admin_columns_checkbox_field_test'] ) ) $options['admin_columns_checkbox_field_test'] = false;
        $options['admin_columns_checkbox_field_test'] = ( 'on' == $options['admin_columns_checkbox_field_test'] ? true : false );

        return $options;

    }

    /**
     * Add additinal HTML elements on list tables
     * 
     * @since 5.3.0
     */
    public function add_additional_elements__premium_only() {
        global $pagenow, $typenow;

        // List tables of pages, posts and CPTs. Administrators only.
        if ( 'edit.php' == $pagenow && current_user_can( 'manage_options' ) ) {
            // Add "Manage Columns" button
            ?>
            <div id="organize-columns">
                <a class="button" href="<?php echo esc_url( get_admin_url() ); ?>admin.php?page=admin-columns&for=<?php echo esc_attr( $typenow ); ?>">Manage Columns</a>
            </div>
            <?php           
        }
        
        // List tables of pages, posts and CPTs. Administrators only.
        if ( 'edit.php' == $pagenow ) {
            // Add list table wrapper to enable horizontal scrolling
            ?>
            <div id="list-table-wrapper"></div>
            <?php           
        }
    }
    
    /**
     * Render current admin columns
     * 
     * @link https://developer.wordpress.org/reference/hooks/manage_posts_columns/
     * @link https://developer.wordpress.org/reference/hooks/manage_pages_columns/
     * @since 5.3.0
     */
    public function render_columns__premium_only() {
        global $pagenow;
        
        // Since we're hooking into wp_loaded, we need to limit this only to listing pages of pages, posts and custom post types
        if ( 'edit.php' == $pagenow ) {
            
            // Get post types with admin management UI enabled
            $post_types = get_post_types( array(
                'show_ui'   => true,
            ), 'names' );
            
            // Exclude certain default core post types
            $excluded_post_types = array(
                'attachment',
                'wp_navigation',
            );

            foreach ( $post_types as $post_type => $post_type_key ) {
                if ( ! in_array( $post_type_key, $excluded_post_types ) ) {
                    // The manage_{$screen->id}_columns hook is more effective than manage_{$post_type}_posts_columns hook
                    // at handling custom columns from plugins, so we use that instead. It also works with Pages.
                    add_filter( 'manage_edit-' . $post_type . '_columns', [ $this, 'custom_columns_order__premium_only' ], PHP_INT_MAX );
                    add_action( 'manage_' . $post_type . '_posts_custom_column', [ $this, 'custom_columns_content__premium_only' ], PHP_INT_MAX, 2 );
                    add_action( 'manage_edit-' . $post_type . '_sortable_columns', [ $this, 'sortable_columns__premium_only' ], PHP_INT_MAX );
                }
            }

        }       
    }
    
    /**
     * Define the columns to show, it's order and each column title for the post type selected
     * 
     * @since 5.3.0
     */
    public function custom_columns_order__premium_only( $columns ) {
        global $typenow;
                
        $columns_keys = array_keys( $columns );

        $options = get_option( ASENHA_SLUG_U . '_extra', array() );

        $available_columns = $this->get_default_columns( $typenow );
        $available_columns_keys = array_keys( $available_columns );
                
        // Prepare columns data for storage in options
        $columns_for_options = array();
        foreach( $columns as $column_key => $column_title ) {
            $columns_for_options[$column_key] = array(
                'key'   => $column_key,
                'title' => $column_title,
            );
        }
        
        if ( ! isset( $options['admin_columns'] ) ) {
            $options['admin_columns'] = array();
        }

        if ( ! isset( $options['admin_columns'][$typenow] ) ) {
            // Store default columns order if it's not been done before
            // i.e. when visiting the post type's listing page for the first time
            $options['admin_columns'][$typenow] = $columns_for_options;
            update_option( ASENHA_SLUG_U . '_extra', $options );
            return $columns;
        } else {
            $custom_columns = $options['admin_columns'][$typenow];
            
            // We detect columns that are not default WP core, not from custom fields, 
            // and not already part of active custom columns
            $columns_count = count( $columns );
            $custom_columns_count = count( $custom_columns );
            if ( $columns_count > $custom_columns_count ) {
                $extra_columns = array();
                if ( ! isset( $options['admin_columns_extra'][$typenow] ) ) {
                    $custom_columns_keys = array_keys( $custom_columns );
                    foreach( $columns_for_options as $column_key => $column_data ) {
                        if ( ! in_array( $column_key, $custom_columns_keys ) ) {
                            $extra_columns[$column_key] = $column_data;                     
                        }
                    }
                } else {
                    // At this stage, $columns transformed to $columns_for_options does not contain custom field columns
                    // So, we add non-default columns to extra columns
                    foreach( $columns_for_options as $column_key => $column_data ) {
                        if ( ! in_array( $column_key, $available_columns_keys ) && 'cb' != $column_key ) {
                            $extra_columns[$column_key] = $column_data;
                        }
                    }
                    // Vice versa, we remove stored extra columns that are no longer in $columns / $columns_for_options
                    // This can caused by the plugin that originally added the extra columns have been deactivated
                    foreach( $extra_columns as $column_key => $column_data ) {
                        if ( ! in_array( $column_key, $columns_keys ) ) {
                            unset( $extra_columns[$column_key] );
                        }
                    }
                }
                $options['admin_columns_extra'][$typenow] = $extra_columns;
                update_option( ASENHA_SLUG_U . '_extra', $options );
            } else {
                // Vice versa, we remove stored extra columns that are no longer in $columns / $columns_for_options
                // This can caused by the plugin that originally added the extra columns have been deactivated
                if ( isset( $options['admin_columns_extra'][$typenow] ) ) {
                    $extra_columns = $options['admin_columns_extra'][$typenow];
                    foreach( $extra_columns as $column_key => $column_data ) {
                        if ( ! in_array( $column_key, $columns_keys ) ) {
                            unset( $extra_columns[$column_key] );
                        }
                    }
                    $options['admin_columns_extra'][$typenow] = $extra_columns;
                    update_option( ASENHA_SLUG_U . '_extra', $options );
                }
            }

            // We have custom columns order stored in options, so, we render that
            $new_columns = array();
            foreach( $custom_columns as $custom_column_key => $custom_column_data ) {
                if ( isset( $custom_column_data['use_original_title'] ) && $custom_column_data['use_original_title'] ) {
                    $title = $custom_column_data['title'];
                } else {
                    $title = isset( $custom_column_data['custom_title'] ) ? $custom_column_data['custom_title'] : $custom_column_data['title'];
                }
                $new_columns[$custom_column_key] = $title;
            }
            return $new_columns;
        }
    }
    
    /**
     * Output column values for each row / post
     * 
     * @since 5.3.0
     */
    public function custom_columns_content__premium_only( $column_name, $post_id ) {

        global $typenow;

        // Get custom fields data for the current list table's post type
        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $custom_fields = isset( $options['custom_fields'][$typenow] ) ? $options['custom_fields'][$typenow] : array();
        $custom_field_keys = array_keys( $custom_fields );

        if ( ! in_array( $column_name, $custom_field_keys ) ) {

            // Render default columns

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->render_default_columns__premium_only( $column_name, $post_id, $typenow );
                
        } else {

            // Render custom field columns

            $cf_handler = $custom_fields[$column_name]['handler'];
            $cf_type = $custom_fields[$column_name]['type'];

            // Render custom field handled by Admin and Site Enhancements Custom Field Groups module

            if ( 'ASE' == $cf_handler ) {

                // Check if the Custom Field Group module is enabled or not
                if ( class_exists( 'Custom_Field_Group' ) ) {

                    $cf_value = get_cf( $column_name, 'default', $post_id );

                    $output = $this->render_ase_cf_columns__premium_only( $cf_type, $cf_value, $column_name, $post_id, $typenow );
                    
                    if ( is_array( $output ) || is_null( $output ) ) {
                        $output = '';
                    } else {
                        echo wp_kses_post( $output );
                    }
                    
                } else {

                    // Custom Field Group module is NOT enabled, so we render with default post meta value
                    echo wp_kses_post( get_post_meta( $post_id, $column_name, true ) );

                }

            } elseif ( 'ACF' == $cf_handler ) {

                // Check if the ACF plugin is active or not
                if ( class_exists( 'ACF' ) ) {
                    
                    // Get raw value
                    $cf_value = get_field( $column_name, $post_id, false );

                    $output = $this->render_acf_cf_columns__premium_only( $cf_type, $cf_value, $column_name, $post_id, $typenow );
                    
                    if ( is_array( $output ) || is_null( $output ) ) {
                        $output = '';
                    } else {
                        echo wp_kses_post( $output );
                    }
                    
                } else {

                    // ACF is NOT active, so we render with default post meta value
                    echo wp_kses_post( get_post_meta( $post_id, $column_name, true ) );

                }
                
            } elseif ( 'MB' == $cf_handler ) {

                // Check if the Meta Box plugin is active or not
                if ( class_exists( 'RWMB_Core' ) ) {
                    
                    // Get raw value
                    $cf_value = rwmb_get_value( $column_name, array(), $post_id );

                    $output = $this->render_mb_cf_columns__premium_only( $cf_type, $cf_value, $column_name, $post_id, $typenow );
                    
                    if ( is_array( $output ) || is_null( $output ) ) {
                        $output = '';
                    } else {
                        echo wp_kses_post( $output );
                    }
                    
                } else {

                    // Meta Box is NOT active, so we render with default post meta value
                    echo wp_kses_post( get_post_meta( $post_id, $column_name, true ) );

                }
                
            } else {
                
                $excluded_columns = array(
                    // Rank Math columns are excluded to prevent duplicated values being shown
                    'rank_math_title',
                    'rank_math_description',
                );
                
                if ( ! in_array( $column_name, $excluded_columns ) ) {
                    // There's no handler defined, so, render with default post meta value
                    $output = get_post_meta( $post_id, $column_name, true );
                    if ( is_array( $output ) ) {
                        $output = json_encode( $output );
                    }
                    echo wp_kses_post( $output );
                }
                
            }

        }
    }

    /**
     * Make columns sortable
     * 
     * @since 6.2.7
     */
    public function sortable_columns__premium_only( $sortable_columns ) {
        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $all_post_type_columns = isset( $options['admin_columns'] ) ? $options['admin_columns'] : array();
        
        $sortable_column_keys = array();
        
        if ( is_array( $all_post_type_columns ) && count( $all_post_type_columns ) > 0 ) {
            foreach( $all_post_type_columns as $post_type => $post_type_columns ) {
                foreach( $post_type_columns as $column_key => $column_data ) {
                    if ( isset( $column_data['is_sortable'] ) && true === $column_data['is_sortable'] ) {
                        $sortable_column_keys[] = $column_key;
                    }
                }
            }
        }
        
        if ( is_array( $sortable_column_keys ) && count( $sortable_column_keys ) > 0 ) {
            foreach( $sortable_column_keys as $column_key ) {
                $sortable_columns[$column_key] = $column_key;
            }           
        }
                
        return $sortable_columns;
    }
    
    /**
     * Get and set default columns
     * 
     * @since 6.0.9
     */
    public function get_default_columns( $post_type ) {
        global $taxonomy_slugs; // indexed array of taxonomy slugs

        $available_columns = array(
            'date_published'            => 'Published',
            'postid'                    => 'ID',
            'modified'                  => 'Last Modified',
            'password_protected'        => 'Password Protected',
            'permalink'                 => 'Permalink',
            'slug'                      => 'Slug',
            'status'                    => 'Status',
            // 'actions'                => 'Actions',
            // 'attachment'             => 'Attachments',
            'date'                      => 'Date',
            'post_parent'               => 'Post Parent',
            'menu_order'                => 'Menu Order',
            // 'used_by_menu'           => 'Menu',
            // 'before_moretag'         => 'More Tag',
            // 'path'                   => 'Path',
            // 'estimated_reading_time' => 'Read Time',
            // 'shortcode'              => 'Shortcodes',
            // 'shortlink'              => 'Shortlink',
            // 'word_count'             => 'Word Count',
        );
        
        if ( 'post' == $post_type ) {
            $available_columns['sticky'] = 'Sticky';
        }

        if ( post_type_supports( $post_type, 'title' ) ) {
            $available_columns['title'] = 'Title';
            // $available_columns['title_raw'] = 'Title Only';
        }

        if ( post_type_supports( $post_type, 'editor' ) || post_type_supports( $post_type, 'excerpt' ) ) {
            $available_columns['excerpt'] = 'Excerpt';          
        }

        if ( post_type_supports( $post_type, 'thumbnail' ) ) {
            $available_columns['featured_image'] = 'Featured Image';            
        }

        if ( post_type_supports( $post_type, 'author' ) ) {
            $available_columns['author'] = 'Author';
            // $available_columns['author_name'] = 'Author';
            // $available_columns['last_modified_author'] = 'Last Modified Author';
        }
        
        if ( post_type_supports( $post_type, 'post-formats' ) ) {
            $available_columns['post_formats'] = 'Post Format';         
        }

        if ( post_type_supports( $post_type, 'comments' ) ) {
            $available_columns['comments'] = 'Comments';
            $available_columns['comment_count'] = 'Comment Count';
            $available_columns['comment_status'] = 'Allow Comment';
        }

        if ( post_type_supports( $post_type, 'trackbacks' ) ) {
            $available_columns['ping_status'] = 'Ping Status';
        }
        
        // Already included by default
        // if ( post_type_supports( $post_type, 'page-attributes' ) || is_post_type_hierarchical( $post_type ) ) {
        //     $available_columns['post_parent'] = 'Post Parent';
        //     $available_columns['menu_order'] = 'Menu Order';
        // }
        
        if ( 'wp_block' == $post_type ) {
            $available_columns['wp_pattern_sync_status'] = 'Sync Status';
        }

        if ( 'shop_order' == $post_type ) {
            $available_columns['products_ordered'] = 'Products';
        }
        
        // Add taxonomy columns according to the custom taxonomies assigned to each post type
        $attached_taxonomies = get_object_taxonomies( $post_type );
        if ( ! empty( $attached_taxonomies ) ) {
            $taxonomy_slugs = array();
            foreach( $attached_taxonomies as $taxonomy_slug ) {
                $taxonomy_object = get_taxonomy( $taxonomy_slug );
                $taxonomy_label = $taxonomy_object->labels->name;
                if ( 'category' == $taxonomy_slug ) {
                    $taxonomy_slug = 'categories';
                } elseif ( 'post_tag' == $taxonomy_slug ) {
                    $taxonomy_slug = 'tags';
                }
                $taxonomy_slugs[] = $taxonomy_slug;
                $available_columns[$taxonomy_slug] = $taxonomy_label;
            }
        }

        // Sort by array value in ascending order, so they are displayed in that order in the 'Default' pane
        asort( $available_columns );
        
        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $options['admin_columns_available'][$post_type] = $available_columns;
        update_option( ASENHA_SLUG_U . '_extra', $options );
        
        return $available_columns;
        
    }
    
    /**
     * Render default columns in list tables
     * 
     * @since 5.3.0
     */
    public function render_default_columns__premium_only( $column_name, $post_id, $typenow ) {

        if ( 'postid' === $column_name ) {
            return $post_id;
        }

        if ( 'featured_image' === $column_name ) {
            if ( has_post_thumbnail( $post_id ) ) {
                return '<img src="' . get_the_post_thumbnail_url( $post_id, 'medium' ) . '" class="post-featured-image" style="width:100px;">';
            } else {
                return '<img src="' . esc_url( plugins_url( 'assets/img/default_featured_image.jpg', __DIR__ ) ) . '" class="post-featured-image" style="width:100px;" />';
            }
        }
        
        if ( 'modified' === $column_name ) {
            $last_modified_date = wp_date( get_option( 'date_format' ), strtotime( get_post_field( 'post_modified_gmt', $post_id ) ) );
            $last_modified_time = wp_date( get_option( 'time_format' ), strtotime( get_post_field( 'post_modified_gmt', $post_id ) ) );
            
            return $last_modified_date . ' at ' . $last_modified_time;
        }

        if ( 'date_published' === $column_name ) {
            $last_modified_date = wp_date( get_option( 'date_format' ), strtotime( get_post_field( 'post_date_gmt', $post_id ) ) );
            $last_modified_time = wp_date( get_option( 'time_format' ), strtotime( get_post_field( 'post_date_gmt', $post_id ) ) );
            
            return $last_modified_date . ' at ' . $last_modified_time;
        }

        if ( 'author_name' === $column_name ) {
            $user_id = get_post_field( 'post_author', $post_id );
            $user = get_userdata( $user_id );
            return '<a href="' . admin_url( 'edit.php?post_type=' . $typenow . '&author' . $user_id ) . '">' . $user->display_name . '</a>';
        }

        if ( 'comment_status' === $column_name ) {
            return ucfirst( get_post_field( 'comment_status', $post_id ) );
        }

        if ( 'comment_count' === $column_name ) {
            return get_comments_number( $post_id );
        }

        if ( 'excerpt' === $column_name ) {
            $raw_excerpt = get_the_excerpt( $post_id );
            $word_limit = 25;
            $excerpt = implode(" ", array_slice( explode(" ", $raw_excerpt), 0, $word_limit ) ) . '...';
            return $excerpt;
        }

        if ( 'slug' === $column_name ) {
            return get_post_field( 'post_name', $post_id );
        }

        if ( 'permalink' === $column_name ) {
            // return get_the_permalink( $post_id );
            return str_replace( get_site_url(), '', get_the_permalink( $post_id ) );
        }

        if ( 'password_protected' === $column_name ) {
            if ( ! empty( get_post_field( 'post_password', $post_id ) ) ) {
                return 'Yes';   
            } else {
                return 'No';
            }
        }

        if ( 'ping_status' === $column_name ) {
            return ucfirst( get_post_field( 'ping_status', $post_id ) );
        }

        if ( 'status' === $column_name ) {
            return ucfirst( get_post_field( 'post_status', $post_id ) );
        }

        if ( 'post_formats' === $column_name ) {
            $post_formats = get_post_format_strings();
            $post_format_slug = ( get_post_format( $post_id ) ) ? get_post_format( $post_id ) : 'standard';
            return $post_formats[$post_format_slug];
        }

        if ( 'sticky' === $column_name ) {
            if ( is_sticky( $post_id ) ) {
                return 'Yes';   
            } else {
                return 'No';
            }
        }

        if ( 'menu_order' === $column_name ) {
            return get_post_field( 'menu_order', $post_id );
        }

        if ( 'post_parent' === $column_name ) {
            if ( ! empty( get_post_field( 'post_parent', $post_id ) ) ) {
                return '<a href="' . get_edit_post_link( get_post_field( 'post_parent', $post_id ) ) . '">' . get_the_title( get_post_field( 'post_parent', $post_id ) ) . '</a>';
            } else {
                return 'None';
            }
        }

        if ( 'wp_pattern_sync_status' === $column_name ) {
            return get_post_field( 'wp_pattern_sync_status', $post_id );
        }
        
        if ( 'products_ordered' === $column_name ) {
            $order = wc_get_order( $post_id );
            $products = '<div class="collection-items-wrapper products-ordered">
                        <a class="show-more-less show-more" href="#">Expand ▼</a>
                        <div class="collection-items">';
            foreach ( $order->get_items() as $item_id => $item ) {
                $product = wc_get_product( $item->get_product_id() );
                if ( is_object( $product ) ) {
                    $product_sku = ( $product->get_sku() ) ? $product->get_sku() : '';                          
                }
                $products .= '<div class="collection-item"><a href="' . get_edit_post_link( $item->get_product_id() ) . '">' . $item->get_name() . '</a>';
                if ( ! empty( $product_sku ) ) {
                    $products .= ' <br />SKU: ' . $product_sku;
                }
                $products .= '</div>';
            }
            $products .= '</div></div>';
            return $products;
        }
        
        // Render taxonomy terms
        
        // Already rendered by WP core, WooCommerce, etc. No need to re-render to prevent duplication
        $already_rendered = array( 
            'categories', 
            'tags', 
            'product_cat', 
            'product_tag',
            'elementor_library_type',
        );

        $attached_taxonomies = get_object_taxonomies( $typenow );
        $tax_terms = '';

        if ( in_array( $column_name, $attached_taxonomies ) && ! in_array( $column_name, $already_rendered ) ) {
            $terms = get_the_terms( $post_id, $column_name );

            if ( ! empty( $terms ) ) {
                foreach ( $terms as $term ) {
                    $tax_terms .= '<a href="' . admin_url( 'term.php?taxonomy=' . $column_name . '&tag_ID=' . $term->term_id . '&post_type=' . $typenow) . '" class="acm-rendered-value">' . $term->name . '</a>, ';
                }

                $tax_terms = rtrim( $tax_terms, ', ' );
            }

            return $tax_terms;
        }
        

    }
    
    /**
     * Render ASE CFG custom fields in list tables
     * 
     * @since 5.3.0
     */
    public function render_ase_cf_columns__premium_only( $cf_type = 'text', $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $custom_fields = $options['custom_fields'][$typenow];

        if ( 'text' == $cf_type ) {
            return $this->render_text_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'number' == $cf_type ) {
            return $this->render_number_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'textarea' == $cf_type || 'wysiwyg' == $cf_type ) {
            return $this->render_longtext_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        } 
        
        if ( 'true_false' == $cf_type ) {
            return $this->render_truefalse_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        } 
        
        if ( 'date' == $cf_type ) {
            return $this->render_date_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'color' == $cf_type ) {
            return $this->render_color_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'select' == $cf_type || 'radio' == $cf_type || 'checkbox' == $cf_type ) {
            return $this->render_choice_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'file' == $cf_type ) {
            $custom_field_info = CFG()->get_field_info( $column_name, $post_id );

            if ( 'image' == $custom_field_info['options']['file_type'] ) {
                if ( 'url' == $custom_field_info['options']['return_value'] ) {
                    return '<div style="background-image:url(' . $cf_value . ');width:100px;height:100px;" class="custom-field-file-image-as-url"></div>';
                }
                
                if ( 'id' == $custom_field_info['options']['return_value'] ) {
                    $image_url = wp_get_attachment_image_url( $cf_value, 'medium' );
                    return '<img src="' . $image_url . '" class="custom-field-file-image-as-id" style="width:100px;height:auto;">';
                }
            } else {
                $attachment_url = '';
                $url_parts = array();

                if ( 'url' == $custom_field_info['options']['return_value'] ) {
                    if ( ! is_null( $cf_value ) ) {
                        $attachment_url = $cf_value;
                        $url_parts = explode( '/', $cf_value);                  
                    }
                }
                
                if ( 'id' == $custom_field_info['options']['return_value'] ) {
                    if ( ! empty( $cf_value ) ) {
                        $attachment_url = wp_get_attachment_url( $cf_value );
                        $url_parts = explode( '/', wp_get_attachment_url( $cf_value ) );                    
                    }
                }

                if ( is_array( $url_parts ) && ! empty( $url_parts ) ) {
                    $file_name = $url_parts[count($url_parts)-1];               
                } else {
                    $file_name = '';
                }
                // $file_name_length = strlen( $file_name );
                // $max_chars = 7;
                // $file_name_truncated = substr_replace( $file_name, '..', $max_chars - 2, $file_name_length - $max_chars - 2 );

                // return '<a href="' . $attachment_url . '" class="custom-field-file-link">' . $file_name_truncated . '</a>';
                return '<a href="' . $attachment_url . '" class="custom-field-file-link">' . $file_name . '</a>';
            }
        }

        if ( 'gallery' == $cf_type ) {
            return $this->render_gallery_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'hyperlink' == $cf_type ) {
            if ( is_array( $cf_value ) && isset( $cf_value['url'] ) ) {
                return '<a href="'. $cf_value['url'] .'" target="_blank">'. $cf_value['text'] .'</a>';              
            } else {
                $cf_value_raw = CFG()->get( $column_name, $post_id, array( 'format' => 'raw' ) );
                if ( is_array( $cf_value_raw ) && isset( $cf_value_raw['url'] ) ) {
                    return '<a href="'. $cf_value_raw['url'] .'" target="_blank">'. $cf_value_raw['text'] .'</a>';
                } else {
                    return;
                }
            }
        }

        if ( 'term' == $cf_type ) {
            return $this->render_term_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'user' == $cf_type ) {
            return $this->render_user_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );           
        }
        
        if ( 'relationship' == $cf_type ) {
            return $this->render_relationship_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        } 

        if ( 'repeater' == $cf_type ) {
            ?>
            <div class="collection-items-wrapper">
                <?php
                if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                    ?>
                    <a class="show-more-less show-more" href="#">Expand ▼</a>
                    <?php
                }
                ?>
                <div class="collection-items">
                <?php
                if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                    foreach ( $cf_value as $repeater_item ) {
                        ?>
                        <div class="collection-item">
                        <?php
                        foreach ( $repeater_item as $scf_key => $scf_value ) {
                            $scf_type = $custom_fields[$scf_key]['type'];
                            ?>
                            <div class="collection-sub-item">
                                <div class="sub-field-label"><?php echo esc_html( $custom_fields[$scf_key]['label'] ); ?>:</div>
                                <div><?php echo wp_kses_post( $this->render_ase_cf_columns__premium_only( $scf_type, $scf_value, $scf_key, $post_id, $typenow ) ); ?></div>
                            </div>
                            <?php
                        }
                        ?>
                        </div>
                        <?php
                    }
                } else {
                    return;
                }
                ?>
                </div>
            </div>
            <?php
        }
                        
    }

    /**
     * Render ACF custom fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_acf_cf_columns__premium_only( $cf_type = 'text', $cf_value = '', $column_name = '', $post_id = false, $typenow = '', $in_collection = false, $parent_field = '', $parent_type = '' ) {

        if ( 'text' == $cf_type || 'url' == $cf_type || 'email' == $cf_type || 'range' == $cf_type || 'oembed' == $cf_type || 'time_picker' == $cf_type ) {
            return $this->render_text_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'number' == $cf_type ) {
            return $this->render_number_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'textarea' == $cf_type || 'wysiwyg' == $cf_type ) {
            return $this->render_longtext_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'true_false' == $cf_type ) {
            return $this->render_truefalse_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'date_picker' == $cf_type ) {
            return $this->render_date_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'date_time_picker' == $cf_type ) {
            return $this->render_datetime_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'color_picker' == $cf_type ) {
            return $this->render_color_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'link' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );
    
            if ( is_array( $field_data ) && isset( $field_data['value'] ) ) {
                $link = $field_data['value'];

                if ( is_array( $link ) && isset( $link['url'] ) ) {
                    return '<a href="'. $link['url'] .'" target="_blank">'. $link['url'] .'</a>';               
                } else {
                    return'<a href="'. $link .'" target="_blank">'. $link .'</a>';
                }
            } else {
                return;
            }
        }
                
        if ( 'image' == $cf_type ) {
            if ( is_numeric(  $cf_value ) ) {
                $image_url = wp_get_attachment_image_url( $cf_value, 'medium' ); // $cf_value is attachment ID
                return '<img src="' . $image_url . '" class="custom-field-file-image-as-id" style="width:100px;height:auto;">';
            } else {
                return;
            }
        }

        if ( 'gallery' == $cf_type ) {
            return $this->render_gallery_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'file' == $cf_type ) {
            if ( is_numeric(  $cf_value ) ) {
                $url_parts = explode( '/', wp_get_attachment_url( $cf_value ) );
                $file_name = $url_parts[count($url_parts)-1];
                // $file_name_length = strlen( $file_name );
                // $max_chars = 7;
                // $file_name_truncated = substr_replace( $file_name, '..', $max_chars - 2, $file_name_length - $max_chars - 2 );

                // return '<a href="' . wp_get_attachment_url( $cf_value ) . '" class="custom-field-file-link">' . $file_name_truncated . '</a>';               
                return '<a href="' . wp_get_attachment_url( $cf_value ) . '" class="custom-field-file-link">' . $file_name . '</a>';
            } else {
                return;
            }
        }
        
        if ( 'select' == $cf_type || 'checkbox' == $cf_type || 'radio' == $cf_type || 'button_group' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );
            $return_format = isset( $field_data['return_format'] ) ? $field_data['return_format'] : '';

            if ( is_array( $field_data ) && isset( $field_data['choices'] ) ) {
                $choices = $field_data['choices'];
            } else {
                $choices = array();
            }
            
            $choice_values = '';

            // Single choice
            if ( is_string( $cf_value ) ) {
                foreach( $choices as $choice_value => $choice_label ) {
                    if ( ( 'value' == $return_format && $cf_value == $choice_value )
                        || ( 'label' == $return_format && $cf_value == $choice_value )
                        || ( 'array' == $return_format && isset( $cf_value['value'] ) && $cf_value['value'] == $choice_value )
                    ) {
                        $choice_values = $choice_label;
                    }
                }
            }

            // Multiple choice
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                $choice_values = array();
                foreach( $choices as $choice_value => $choice_label ) {
                    if ( ( 'value' == $return_format ) ) {
                        foreach( $cf_value as $item_value ) {
                            if ( $item_value == $choice_value ) {
                                $choice_values[] = $choice_label;
                            }   
                        }
                    }
                    if ( ( 'label' == $return_format ) ) {
                        foreach( $cf_value as $item_value ) {
                            if ( $item_value == $choice_value ) {
                                $choice_values[] = $choice_label;
                            }   
                        }
                    }
                    if ( ( 'array' == $return_format ) ) {
                        foreach( $cf_value as $cf_item  ) {
                            if ( is_string( $cf_item ) && $cf_item == $choice_value ) {
                                $choice_values[] = $choice_label;
                            }
                            if ( is_array( $cf_item ) && isset( $cf_item['value'] ) && $cf_item['value'] == $choice_value ) {
                                $choice_values[] = $choice_label;                               
                            }
                        }
                    }
                }
            }
            
            return $this->render_choice_cf_column__premium_only( $choice_values, $column_name, $post_id, $typenow );            
        }

        if ( 'relationship' == $cf_type ) {
            return $this->render_relationship_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        } 

        if ( 'post_object' == $cf_type || 'page_link' == $cf_type ) {
            if ( ! $in_collection ) {
                $field_values = get_field( $column_name, $post_id, false ); // Could be string of single ID, or array of IDs
            } else {
                $parent_field_data = get_field_object( $parent_field, $post_id );
                $parent_field_values = $parent_field_data['value'];
                foreach( $parent_field_values as $parent_field_value ) {
                    foreach( $parent_field_value as $item_key => $item_value ) {
                        if ( $item_key == $column_name ) {
                            $field_values = $item_value;
                        }
                    }
                }
            }

            return $this->render_relationship_cf_column__premium_only( $field_values, $column_name, $post_id, $typenow );
        }
        
        if ( 'taxonomy' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );

            if ( is_array( $field_data ) && isset( $field_data['taxonomy'] ) ) {
                $taxonomy_slug = $field_data['taxonomy'];
                $term_ids = $cf_value; // array
                $term_links = '';
                
                if ( is_array( $term_ids ) && ! empty( $term_ids ) ) {
                    foreach( $term_ids as $term_id ) {
                        $term = get_term( $term_id, $taxonomy_slug );
                        $term_links .= '<a href="' . admin_url( 'term.php?taxonomy=' . $taxonomy_slug . '&tag_ID=' . $term_id . '&post_type=' . $typenow ) . '" class="acm-rendered-value">' . $term->name . '</a>, ';
                    }                   
                }

                $term_links = rtrim( $term_links, ', ' );
                return $term_links; 
            } else {
                return;
            }
        }
        
        if ( 'user' == $cf_type ) {
            $user_ids = get_field( $column_name, $post_id, false ); // array
            if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
                $user_links = '';
                
                foreach( $user_ids as $user_id ) {
                    $user = get_user_by( 'id', $user_id );
                    $user_links .= '<a href="' . admin_url( 'user-edit.php?user_id=' . $user_id ) . '" class="acm-rendered-value">' . $user->display_name . '</a>, ';                               
                }

                $user_links = rtrim( $user_links, ', ' );
                return $user_links;
            } else {
                return;
            }
        }

        if ( 'google_map' == $cf_type ) {
            if ( ! empty( $cf_value ) ) {
                return '<a href="http://maps.google.com/maps?z=12&t=m&q=loc:' . $cf_value['lat'] . '+' . $cf_value['lng'] . '" target="_blank">' . $cf_value['address'] . '</a>';
            } else {
                return;
            }
        }

        if ( 'group' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );

            if ( is_array( $field_data ) && ! empty( $field_data ) ) {
                $cf_values = $field_data['value'];

                if ( is_array( $cf_values ) && ! empty( $cf_values ) ) {
                    $cf_values_array = array_values( $cf_values );
                    $cf_values_not_empty = false;

                    foreach( $cf_values_array as $cf_value_item ) {
                        if ( ! empty( $cf_value_item ) ) {
                            $cf_values_not_empty = true;
                        }
                    }
                    
                    $cf_subfields = $field_data['sub_fields'];

                    if ( $cf_values_not_empty ) {
                    ?>
                        <div class="collection-items-wrapper">
                        <a class="show-more-less show-more" href="#">Expand ▼</a>
                        <div class="collection-items">
                        <div class="collection-item">
                    <?php
                    }

                    foreach( $cf_values as $item_key => $item_value ) {
                        if ( ! empty( $item_value ) ) {
                            foreach( $cf_subfields as $cf_subfield ) {
                                if ( 'repeater' != $cf_subfield['type'] ) {
                                    if ( $cf_subfield['name'] == $item_key ) {
                                        $item_type = $cf_subfield['type'];
                                        ?>
                                        <div class="collection-sub-item">
                                        <div class="sub-field-label"><?php echo esc_html( $cf_subfield['label'] ); ?>:</div>
                                        <div><?php echo $this->render_acf_cf_columns__premium_only( $item_type, $item_value, $item_key, $post_id, $typenow, true, $column_name, 'group' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                        </div>
                                        <?php
                                    }
                                }
                            }                           
                        }
                    }

                    if ( $cf_values_not_empty ) {
                    ?>
                        </div>
                        </div>
                        </div>
                    <?php
                    }                   
                } else {
                    return;
                }
            } else {
                return;
            }
        }

        if ( 'repeater' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );
            $cf_values = $field_data['value'];
            $cf_subfields = $field_data['sub_fields'];
        
            ?>
            <div class="collection-items-wrapper">
            <?php

            if ( is_array( $cf_values ) && ! empty( $cf_values ) ) {
                ?>
                <a class="show-more-less show-more" href="#">Expand ▼</a>
                <?php
            }
            
            ?>
            <div class="collection-items">
            <?php
            
            if ( is_array( $cf_values ) && ! empty( $cf_values ) ) {
                foreach( $cf_values as $cf_value ) {
                    ?>
                    <div class="collection-item">
                    <?php
                    foreach( $cf_value as $item_key => $item_value ) {
                        foreach( $cf_subfields as $cf_subfield ) {
                            if ( 'repeater' != $cf_subfield['type'] ) {
                                if ( $cf_subfield['name'] == $item_key ) {
                                    $item_type = $cf_subfield['type'];
                                    ?>
                                    <div class="collection-sub-item">
                                        <div class="sub-field-label"><?php echo esc_html( $cf_subfield['label'] ); ?>:</div>
                                        <div><?php echo $this->render_acf_cf_columns__premium_only( $item_type, $item_value, $item_key, $post_id, $typenow, true, $column_name, 'repeater' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    </div>
                    <?php
                }
            }
            
            ?>
            </div>
            </div>
            <?php       
            
        }

        if ( 'flexible_content' == $cf_type ) {
            $field_data = $this->get_acf_field_object( $column_name, $post_id, $in_collection, $parent_field, $parent_type );
            $cf_values = $field_data['value'];
            $layouts = $field_data['layouts'];

            ?>
            <div class="collection-items-wrapper">
            <?php

            if ( is_array( $cf_values ) && ! empty( $cf_values ) ) {
                ?>
                <a class="show-more-less show-more" href="#">Expand ▼</a>
                <?php
            }
            
            ?>
            <div class="collection-items">
            <?php

            if ( is_array( $cf_values ) && ! empty( $cf_values ) ) {
                foreach( $cf_values as $cf_value ) {
                    ?>
                    <div class="collection-item">
                    <?php
                    $current_layout = $cf_value['acf_fc_layout'];
                    foreach( $layouts as $layout ) {
                        if ( $current_layout == $layout['name'] ) {
                            ?>
                            <div class="collection-item-title"><?php echo esc_html( $layout['label'] ); ?></div>
                            <?php
                            $sub_fields = $layout['sub_fields'];
                            foreach( $cf_value as $item_key => $item_value ) {
                                foreach( $sub_fields as $sub_field ) {
                                    if ( $item_key == $sub_field['name'] ) {
                                        $item_type = $sub_field['type'];
                                        ?>
                                        <div class="collection-sub-item">
                                            <div class="sub-field-label"><?php echo esc_html( $sub_field['label'] ); ?>:</div>
                                            <div><?php echo $this->render_acf_cf_columns__premium_only( $item_type, $item_value, $item_key, $post_id, $typenow, true, $column_name, 'flexible_content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    </div>
                    <?php
                }
            }

            ?>
            </div>
            </div>
            <?php       

        }

    }

    /**
     * Render Meta Box custom fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_mb_cf_columns__premium_only( $cf_type = 'text', $cf_value = '', $column_name = '', $post_id = false, $typenow = '', $in_collection = false, $parent_field = '', $parent_type = '' ) {

        if ( 'text' == $cf_type || 'time' == $cf_type || 'oembed' == $cf_type || 'slider' == $cf_type || 'range' == $cf_type || 'email' == $cf_type || 'url' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_text_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_text_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }
        }

        if ( 'number' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_number_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_number_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }
        }

        if ( 'textarea' == $cf_type || 'wysiwyg' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_longtext_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_longtext_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }
        }

        if ( 'text_list' == $cf_type ) {
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_text_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return;
            }
        }
        
        if ( 'checkbox' == $cf_type || 'switch' == $cf_type ) {
            return $this->render_truefalse_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );          
        }

        if ( 'checkbox_list' == $cf_type || 'radio' == $cf_type || 'select' == $cf_type || 'select_advanced' == $cf_type || 'image_select' == $cf_type || 'autocomplete' == $cf_type || 'button_group' == $cf_type ) {
            if ( $in_collection ) {
                $parent_field_data = rwmb_get_field_settings( $parent_field, '', $post_id );
                $sub_fields = $parent_field_data['fields'];
                foreach( $sub_fields as $index => $sub_field ) {
                    if ( $column_name == $sub_field['id'] ) {
                        $field_data = $sub_fields[$index];
                    }
                }
            } else {
                $field_data = rwmb_get_field_settings( $column_name, '', $post_id );            
            }

            if ( is_array( $field_data ) && isset( $field_data['options'] ) ) {
                $choices = $field_data['options'];
            } else {
                $choices = array();
            }
            
            $choice_values = '';

            // Single choice
            if ( is_string( $cf_value ) ) {
                foreach( $choices as $choice_value => $choice_label ) {
                    if ( $cf_value == $choice_value ) {
                        $choice_values = $choice_label;                     
                    }
                }
            }

            // Multiple choice
            if ( is_array( $cf_value ) ) {
                $choice_values = array();
                foreach( $choices as $choice_value => $choice_label ) {
                    foreach( $cf_value as $item_value ) {
                        if ( $item_value == $choice_value ) {
                            $choice_values[] = $choice_label;                           
                        }   
                    }
                }
            }
            
            if ( 'image_select' == $cf_type ) {
                return '<img src="' . $this->render_choice_cf_column__premium_only( $choice_values, $column_name, $post_id, $typenow ) . '" />';
            } else {
                return $this->render_choice_cf_column__premium_only( $choice_values, $column_name, $post_id, $typenow );                                    
            }
        }

        if ( 'background' == $cf_type ) {
            if ( is_array( $cf_value ) && ( ! empty( $cf_value['color'] ) || ! empty( $cf_value['image'] ) ) ) {
                $wp_upload_dir = wp_get_upload_dir();
                $wp_upload_dir_url = $wp_upload_dir['url'];
                
                $output = '<div class="collection-items-wrapper mb-cloneable-field">';
                $output .= '<a class="show-more-less show-more" href="#">Expand ▼</a>';
                $output .= '<div class="collection-items">';
                $output .= '<div class="collection-item">';

                $output .= ( ! empty( $cf_value['color'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Color:</div><div>' . $this->render_color_cf_column__premium_only( $cf_value['color'] ) . '</div></div>' : '';
                $output .= ( ! empty( $cf_value['image'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Image:</div><div><a href="' . $cf_value['image'] . '" target="_blank">' . str_replace( $wp_upload_dir_url . '/', '', $cf_value['image'] ) . '</a></div></div>' : '';
                $output .= ( ! empty( $cf_value['repeat'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Repeat:</div><div>' . $cf_value['repeat'] . '</div></div>' : '';
                $output .= ( ! empty( $cf_value['attachment'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Attachment:</div><div>' . $cf_value['attachment'] . '</div></div>' : '';
                $output .= ( ! empty( $cf_value['position'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Position:</div><div>' . $cf_value['position'] . '</div></div>' : '';
                $output .= ( ! empty( $cf_value['size'] ) ) ? '<div class="collection-sub-item"><div class="sub-field-label">Size:</div><div>' . $cf_value['size'] . '</div></div>' : '';
                
                $output .= '</div></div></div>';
                return $output;
            } else {
                return;
            }       
        }

        if ( 'color' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_color_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_color_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }
        }

        if ( 'date' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_date_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_date_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }

            return $this->render_date_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }
        
        if ( 'datetime' == $cf_type ) {
            if ( is_array( $cf_value ) ) {
                return $this->render_mb_cloneable_field__premium_only( 'render_datetime_cf_column__premium_only', $cf_value, $column_name, $post_id, $typenow );
            } else {
                return $this->render_datetime_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
            }

            return $this->render_datetime_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        }

        if ( 'map' == $cf_type || 'osm' == $cf_type ) {
            if ( $in_collection ) {
                $parent_field_data = rwmb_get_field_settings( $parent_field, '', $post_id );
                $sub_fields = $parent_field_data['fields'];
                foreach( $sub_fields as $index => $sub_field ) {
                    if ( $column_name == $sub_field['id'] ) {
                        $field_data = $sub_fields[$index];
                    }
                }
            } else {
                $field_data = rwmb_get_field_settings( $column_name, '', $post_id );            
            }

            $address_field = $field_data['address_field'];
            $address =  rwmb_get_value( $address_field, '', $post_id );

            // When not in collection, map info is in an array
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                $latitude = $cf_value['latitude'];
                $longitude = $cf_value['longitude'];
                $zoom = $cf_value['zoom'];
            }
            
            // When in collection (cloneable group), map info is a comma separated string of latitude and longitude
            if ( is_string( $cf_value ) && ! empty( $cf_value ) ) {
                $coordinates = explode( ',', $cf_value );
                $latitude = $coordinates[0];
                $longitude = $coordinates[1];
                $zoom = $coordinates[2];
            }
            
            if ( ! empty( $cf_value ) ) {
                if ( 'map' == $cf_type ) {
                    return '<a href="http://maps.google.com/maps?z=' . $zoom . '&t=m&q=loc:' . $latitude . '+' . $longitude . '" target="_blank">' . $address . '</a>';                 
                }
                if ( 'osm' == $cf_type ) {
                    return '<a href="https://www.openstreetmap.org/#map=' . $zoom . '/' . $latitude . '/' . $longitude . '" target="_blank">' . $address . '</a>';
                }
            } else {
                return;
            }
        }

        if ( 'key_value' == $cf_type ) {
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                $output = '<div class="collection-items-wrapper mb-cloneable-field">';
                $output .= '<a class="show-more-less show-more" href="#">Expand ▼</a>';
                $output .= '<div class="collection-items">';
                $output .= '<div class="collection-item">';

                foreach( $cf_value as $item ) {
                    $key = $item[0];
                    $value = $item[1];
                    $output .= '<div class="collection-sub-item"><div class="sub-field-label">' . $key . ':</div><div>' . $value . '</div></div>';
                }

                $output .= '</div></div></div>';
                
                return $output;
            } else {
                return;
            }
        }

        if ( 'post' == $cf_type ) {
            return $this->render_relationship_cf_column__premium_only( $cf_value, $column_name, $post_id, $typenow );
        } 

        if ( 'taxonomy' == $cf_type || 'taxonomy_advanced' == $cf_type ) {
            $term_objects = array();
            $term_links = '';
            
            // Make sure we have an array of term objects
            if ( is_array( $cf_value ) && ! empty( $cf_value ) && is_object( $cf_value[0] ) ) {
                $term_objects = $cf_value;
            } elseif ( is_object( $cf_value ) ) {
                $term_objects[] = $cf_value;
            } elseif ( is_string( $cf_value ) && ! empty( $cf_value ) && false === strpos( $cf_value, ',' ) ) {
                // In cloneable group, singular value
                $cf_value = get_term( $cf_value, '', OBJECT );
                $term_objects[] = $cf_value;                
            } elseif ( is_array( $cf_value ) && ! empty( $cf_value ) && is_numeric( $cf_value[0] ) ) {
                // In cloneable group, multiple values
                foreach( $cf_value as $term_id ) {
                    $term_object = get_term( $term_id, '', OBJECT );
                    $term_objects[] = $term_object;
                }
            } else {}
                
            if ( is_array( $term_objects ) && ! empty( $term_objects ) ) {
                foreach( $term_objects as $term_object ) {
                    $term_links .= '<a href="' . admin_url( 'term.php?taxonomy=' . $term_object->taxonomy . '&tag_ID=' . $term_object->term_id . '&post_type=' . $typenow ) . '" class="acm-rendered-value">' . $term_object->name . '</a>, ';
                }                   

                $term_links = rtrim( $term_links, ', ' );
                return $term_links; 
            } else {
                return;
            }
        }

        if ( 'user' == $cf_type ) {
            $user_ids = array();
            
            // Make sure we have an array of user IDs
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                $user_ids = $cf_value;
            } elseif ( is_numeric( $cf_value ) ) {
                $user_ids[] = $cf_value;
            }

            if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
                $user_links = '';
                
                foreach( $user_ids as $user_id ) {
                    $user = get_user_by( 'id', $user_id );
                    $user_links .= '<a href="' . admin_url( 'user-edit.php?user_id=' . $user_id ) . '" class="acm-rendered-value">' . $user->display_name . '</a>, ';                               
                }

                $user_links = rtrim( $user_links, ', ' );
                return $user_links;
            } else {
                return;
            }
        }

        if ( 'sidebar' == $cf_type ) {
            global $wp_registered_sidebars;
            $sidebar_slugs = array();
            $sidebar_names = '';
            
            // Make sure we have an array of user IDs
            if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
                $sidebar_slugs = $cf_value;
            } elseif ( is_string( $cf_value ) ) {
                $sidebar_slugs[] = $cf_value;
            }
            
            if ( is_array( $sidebar_slugs ) && ! empty( $sidebar_slugs ) ) {
                foreach( $sidebar_slugs as $sidebar_slug ) {
                    foreach( $wp_registered_sidebars as $registered_sidebar_slug => $registered_sidebar ) {
                        if ( $sidebar_slug == $registered_sidebar_slug ) {
                            $sidebar_names .= $registered_sidebar['name'] . ', ';
                        }
                    }
                }

                $sidebar_names = rtrim( $sidebar_names, ', ' );
                return $sidebar_names;
            } else {
                return;
            }
        }
        
        if ( 'file_input' == $cf_type ) {
            if ( ! empty( $cf_value ) ) {
                if ( is_string( $cf_value ) ) {
                    $url_parts = explode( '/', $cf_value );
                    $file_name = $url_parts[count($url_parts)-1];
                    return '<a href="' . $cf_value . '" class="custom-field-file-link">' . $file_name . '</a>';
                }
                if ( is_array( $cf_value ) ) {
                $output = '<div class="mb-media-wrapper">';
                    $pointer = '<span class="pointer">&#9654;</span> ';
                    foreach( $cf_value as $file_url ) {
                        $url_parts = explode( '/', $file_url );
                        $file_name = $url_parts[count($url_parts)-1];
                        $output .= '<div class="mb-media-item mb-media-link">' . $pointer . '<a href="' . $file_url . '" class="custom-field-file-link">' . $file_name . '</a></div>';
                    }
                    $output .= '</div>';
                    return $output;
                }
            } else {
                return;
            }
            
        }

        if ( 'single_image' == $cf_type ) {
            if ( ! empty( $cf_value ) ) {
                if ( is_array( $cf_value ) && isset( $cf_value['ID'] ) ) {
                    $image_url = wp_get_attachment_image_url( $cf_value['ID'], 'thumbnail' ); // $cf_value is attachment ID
                } 
                if ( is_numeric( $cf_value ) ) {
                    $image_url = wp_get_attachment_image_url( $cf_value, 'thumbnail' ); // $cf_value is attachment ID
                }
                return '<img src="' . $image_url . '" class="custom-field-file-image-as-id" style="width:75px;height:auto;">';
            } else {
                return;
            }
        }

        if ( 'file' == $cf_type || 'file_upload' == $cf_type || 'file_advanced' == $cf_type || 'image' == $cf_type || 'image_upload' == $cf_type || 'image_advanced' == $cf_type || 'video' == $cf_type ) {
                        
            if ( ! $in_collection ) {
                // Already an array of key => meta data array
                $attachments = $cf_value;
            } else {
                // Still an array of attachment IDs
                $attachments = array();
                foreach( $cf_value as $attachment_id ) {
                    $attachment = wp_get_attachment_metadata( $attachment_id );
                    $attachments[$attachment_id] = $attachment;
                }
            }

            if ( is_array( $attachments ) && ! empty( $attachments ) ) {
                $output = '<div class="mb-media-wrapper">';
                foreach( $attachments as $attachment_id => $attachment ) {
                    $is_image = wp_attachment_is_image( $attachment_id );
                    if ( $is_image ) {
                        $image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); // $cf_value is attachment ID
                        $output .= '<div class="mb-media-item mb-media-image"><img src="' . $image_url . '" class="custom-field-file-image-as-id" style="width:75px;height:auto;"></div>';
                    } else {
                        // Video
                        if ( wp_attachment_is( 'video', $attachment_id ) ) {
                            // $file_url = $attachment['src'];
                            $file_url = wp_get_attachment_url( $attachment_id );
                            $url_parts = explode( '/', $file_url );
                            $file_name = $url_parts[count($url_parts)-1];
                            $output .= '<div class="mb-media-item mb-media-link"><a href="' . $file_url . '" class="custom-field-file-link">' . $file_name . '</a></div>';
                        }
                        
                        // Other file types
                        if ( isset( $attachment['name'] ) ) {
                            $pointer = '<span class="pointer">&#9654;</span> ';
                            $output .= '<div class="mb-media-item mb-media-link">' . $pointer . '<a href="' . $attachment['url'] . '" class="custom-field-file-link">' . $attachment['name'] . '</a></div>';
                        }                   
                    }
                }
                $output .= '</div>';
                return $output;
            } else {
                return;
            }
        }

        if ( 'group' == $cf_type ) {
            $field_data = rwmb_get_field_settings( $column_name, '', $post_id );
            $is_cloneable = $field_data['clone'];
            $cf_subfields = $field_data['fields'];
            
            // Make sure we have an array of sub-arrays to work with, where each sub-array is a set of values from sub-fields
            $cf_values = array();
            if ( $is_cloneable ) {
                $cf_values = $cf_value; // Already an array of arrays
            } else {
                $cf_values[] = $cf_value;
            }
            
            if ( is_array( $cf_values ) && ! empty( $cf_values[0] ) ) {
                ?>
                <div class="collection-items-wrapper">
                <a class="show-more-less show-more" href="#">Expand ▼</a>
                <div class="collection-items">
                <?php
            }
                        
            if ( is_array( $cf_values ) && ! empty( $cf_values[0] ) ) {
                foreach( $cf_values as $cf_value ) {
                    ?>
                    <div class="collection-item">
                    <?php
                    foreach( $cf_value as $item_key => $item_value ) {
                        foreach( $cf_subfields as $cf_subfield ) {
                            if ( 'group' != $cf_subfield['type'] ) {
                                if ( $cf_subfield['id'] == $item_key ) {
                                    $item_type = $cf_subfield['type'];
                                    ?>
                                    <div class="collection-sub-item">
                                        <div class="sub-field-label"><?php echo esc_html( $cf_subfield['name'] ); ?>:</div>
                                        <div><?php echo $this->render_mb_cf_columns__premium_only( $item_type, $item_value, $item_key, $post_id, $typenow, true, $column_name, 'group' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                    </div>
                    <?php
                }
            }
            
            if ( is_array( $cf_values ) && ! empty( $cf_values[0] ) ) {
            ?>
                </div>
                </div>
            <?php
            }
        }

    }
    
    /**
     * Get ACF field object data
     * 
     * @since 6.3.0
     */
    public function get_acf_field_object( $column_name = '', $post_id = false, $in_collection = false, $parent_field = false, $parent_type = '' ) {
        $field_data = array();

        if ( ! $in_collection ) {
            $field_data = get_field_object( $column_name, $post_id );
        } else {
            $parent_field_data = get_field_object( $parent_field, $post_id );

            if ( 'group' == $parent_type || 'repeater' == $parent_type ) {
                $sub_fields = $parent_field_data['sub_fields'];
                foreach( $sub_fields as $sub_field ) {
                    if ( $column_name == $sub_field['name'] ) {
                        $field_data = $sub_field;
                    }
                }
            }

            if ( 'flexible_content' == $parent_type ) {
                $layouts = $parent_field_data['layouts'];
                foreach( $layouts as $layout ) {
                    $sub_fields = $layout['sub_fields'];
                    foreach( $sub_fields as $sub_field ) {
                        if ( $column_name == $sub_field['name'] ) {
                            $field_data = $sub_field;
                        }
                    }
                }
            }               
        }
        
        return $field_data;
    }
    
    /**
     * Render Meta Box cloneable fields with the corresponding renderer
     * 
     * @link https://web.archive.org/web/20231218042432/https://codedtag.com/php/understanding-the-php-callable-function/
     * @since 6.3.0
     */
    public function render_mb_cloneable_field__premium_only( $renderer = '', $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $renderer = array( $this, $renderer );
        $cf_output_values = '';

        if ( ! empty( $cf_value ) ) {
            if ( count( $cf_value ) > 1 ) {
                $pointer = '<span class="pointer">&#9654;</span> ';
            } else {
                $pointer = '';
            }
            
            $cf_output_values .= '<div class="collection-items-wrapper mb-cloneable-field">';
            $cf_output_values .= '<a class="show-more-less show-more" href="#">Expand ▼</a>';
            $cf_output_values .= '<div class="collection-items">';
            $cf_output_values .= '<div class="collection-item">';

            foreach( $cf_value as $option_key => $option_value ) {
                $cf_output_values .= '<div class="collection-sub-item">' . $pointer . call_user_func( $renderer, $option_value, $column_name, $post_id, $typenow ) . '</div>';
            }
            
            $cf_output_values .= '</div></div></div>';

            return $cf_output_values;
        } else {
            return;
        }
        
    }

    /**
     * Render text fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_text_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        if ( filter_var( $cf_value, FILTER_VALIDATE_URL ) && false !== strpos( $cf_value, 'http' ) ) {
            $url = $cf_value;
            // $url_length = strlen( $url );
            // $max_chars = 21;
            // $url_truncated = substr_replace( $url, '...', $max_chars - 2, $url_length - $max_chars - 2 );            
            
            // return '<a href="' . $url . '">' . $url_truncated . '</a>';                              
            return '<a href="' . $url . '">' . $url . '</a>';                               
        } else {
            return $cf_value;
        }
        
    }
    
    /**
     * Render number fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_number_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {
        if ( ! empty( $cf_value ) && is_numeric( $cf_value ) ) {
            if ( class_exists( 'NumberFormatter' ) ) {
                $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
                return $formatter->format( $cf_value );             
            } else {
                return $cf_value;
            }           
        } else {
            return;
        }
    }

    /**
     * Render textarea and WYSIWYG fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_longtext_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        if ( ! is_null( $cf_value ) && ! empty( $cf_value ) ) {
            $word_limit = 25;
            return implode(" ", array_slice( explode(" ", $cf_value), 0, $word_limit ) ) . '...';           
        } else {
            return;
        }

    }

    /**
     * Render true false fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_truefalse_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        return ( 1 == $cf_value ) ? 'Yes' : 'No';

    }

    /**
     * Render date fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_date_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        if ( ! empty( $cf_value ) ) {
            return wp_date( get_option( 'date_format' ), strtotime( $cf_value ) );
        } else {
            return;
        }

    }

    /**
     * Render date fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_datetime_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        if ( ! empty( $cf_value ) ) {
            return wp_date( get_option( 'date_format' ), strtotime( $cf_value ) ) . ' - ' . wp_date( get_option( 'time_format' ), strtotime( $cf_value ) );
        } else {
            return;
        }

    }
    
    /**
     * Render color fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_color_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        if ( ! empty( $cf_value ) ) {
            return '<div class="custom-field-color-preview" style="background:' . $cf_value . ';"></div>' . $cf_value;      
        } else {
            return;
        }

    }

    /**
     * Render select fields in list tables, which may contain single (string) or multiple values (array)
     * 
     * @since 6.3.0
     */
    public function render_choice_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $cf_output_values = array();

        if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            if ( count( $cf_value ) > 1 ) {
                $pointer = '<span class="pointer">&#9654;</span> ';
            } else {
                $pointer = '';
            }

            foreach( $cf_value as $option_key => $option_value ) {
                $cf_output_values[] = $pointer . $option_value;
            }
            sort( $cf_output_values );
            return implode( '<br />', $cf_output_values );
        } else {
            return $cf_value;
        }
        
    }

    /**
     * Render gallery fields in list tables
     * 
     * @param  array        $cf_value    array of attachment IDs
     * @param  string       $column_name 
     * @param  boolean|int  $post_id     
     * @param  string       $typenow     
     * @return [type]               [description]
     */
    public function render_gallery_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {
        $thumbnails = '<div class="collection-items-wrapper ase-acf-gallery">';

        if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            $thumbnails .= '<a class="show-more-less show-more" href="#">Expand ▼</a>';
        }
        
        $thumbnails .= '<div class="collection-items">
                    <div class="collection-item">';

        if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            foreach( $cf_value as $attachment_id ) {
                $image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' ); // $cf_value is attachment ID
                $thumbnails .= '<img src="' . $image_url . '" class="custom-field-file-image-as-id" style="width:75px;height:auto;">';
            }               
        }

        $thumbnails .= '</div>
                    </div>
                    </div>';

        return $thumbnails;
    }

    /**
     * Render term fields in list tables
     * 
     * @since 6.5.1
     */
    public function render_term_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $cf_output = '<div class="related-items">';

        if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            if ( count( $cf_value ) > 1 ) {
                $pointer = '<span class="pointer">&#9654;</span> ';
            } else {
                $pointer = '';
            }

            foreach ( $cf_value as $term_id ) {
                $term = get_term( intval( $term_id ) );
                if ( is_object( $term ) ) {
                    $cf_output .= '<a href="' . get_edit_term_link( $term->term_id ) . '">' . $pointer . $term->name . '</a>';
                }
            }
        }

        $cf_output .= '</div>';
        
        return $cf_output;
        
    }

    /**
     * Render term fields in list tables
     * 
     * @since 6.5.1
     */
    public function render_user_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $cf_output = '<div class="related-items">';

        if ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            if ( count( $cf_value ) > 1 ) {
                $pointer = '<span class="pointer">&#9654;</span> ';
            } else {
                $pointer = '';
            }

            foreach ( $cf_value as $user_id ) {
                $user = get_user_by( 'id', intval( $user_id ) );
                if ( is_object( $user ) ) {
                    $cf_output .= '<a href="' . get_edit_user_link( $user->ID ) . '">' . $pointer . $user->display_name . ' (' . $user->user_login . ')</a>';
                }
            }
        }

        $cf_output .= '</div>';
        
        return $cf_output;
        
    }
    /**
     * Render relationship fields in list tables
     * 
     * @since 6.3.0
     */
    public function render_relationship_cf_column__premium_only( $cf_value = '', $column_name = '', $post_id = false, $typenow = '' ) {

        $cf_output = '<div class="related-items">';

        if ( is_object( $cf_value ) ) {
            $pointer = '';
            $cf_output .= '<a href="' . get_edit_post_link( $cf_value->ID ) . '">' . $pointer . $cf_value->post_title . '</a>';
        } elseif ( is_array( $cf_value ) && ! empty( $cf_value ) ) {
            if ( count( $cf_value ) > 1 ) {
                $pointer = '<span class="pointer">&#9654;</span> ';
            } else {
                $pointer = '';
            }

            foreach ( $cf_value as $object ) {
                if ( is_object( $object ) || is_numeric( $object ) ) {
                    $post = get_post( $object );
                    if ( is_object( $post ) ) { // post ID or object
                        $cf_output .= '<a href="' . get_edit_post_link( $object ) . '">' . $pointer . $post->post_title . '</a>';
                    }                   
                } elseif ( is_string( $object ) ) { // URL
                        $cf_output .= '<a href="' . $object . '">' . $pointer . $object . '</a>';                   
                } else {}
            }
        } else {
            $pointer = '';

            if ( ! empty( $cf_value ) ) {
                $post = get_post( $cf_value );
                if ( is_object( $post ) ) {
                    $cf_output .= '<a href="' . get_edit_post_link( $cf_value ) . '">' . $pointer . $post->post_title . '</a>';
                }               
            }
        }

        $cf_output .= '</div>';
        
        return $cf_output;
        
    }
    
    /**
     * Get custom field keys for selected post type
     * 
     * @since 5.3.0
     */
    public function get_custom_field_data__premium_only( $post_type_slug, $sample_size = 5, $type = 'unique_keys' ) {

        $custom_field_keys = array();
        $posts_meta_data = array();
        $custom_field_keys_values_sample = array();
        
        $post_ids = array();
        
        $args = array(
            'post_type'     => $post_type_slug,
            'limit'         => $sample_size,
            'order'         => 'DESC',
        );

        // For WooCommerce orders, we need an additional argument, otherwise, empty array is returned
        if ( 'shop_order' == $post_type_slug ) {
            $args['post_status'] = 'wc_completed';
        }
        
        $posts = get_posts( $args );
        
        // =========== Get raw custom field keys and meta data ===========
        
        foreach ( $posts as $post ) {
            $post_ids[] = $post->ID;
            $post_meta_keys = ( get_post_custom_keys( $post->ID ) ) ? get_post_custom_keys( $post->ID ) : array();
            $custom_field_keys = array_merge( $custom_field_keys, $post_meta_keys );
            
            $post_meta = get_post_meta( $post->ID );
            $posts_meta_data[$post_type_slug . '_id_' . $post->ID] = $post_meta;
        }
        
        // Custom field unique keys
        
        $custom_field_unique_keys = array_unique( $custom_field_keys );
        sort( $custom_field_unique_keys );
        
        // Remove certain custom field from the list so they're treated as an extra column
        
        $excluded_from_custom_fields_list = array(
            'rank_math_title',
            'rank_math_description',
        );
        
        $keys_to_unset = array();
        $indexes_to_unset = array();
        
        foreach( $custom_field_unique_keys as $index => $custom_field_key ) {
            if ( in_array( $custom_field_key, $excluded_from_custom_fields_list ) ) {
                $indexes_to_unset[] = $index; 
            }
        }
        
        if ( ! empty( $indexes_to_unset ) ) {
            foreach ( $indexes_to_unset as $index ) {
                unset( $custom_field_unique_keys[$index] );
            }       
        }
            
        // Re-index the keys so it's sequential again
        $custom_field_unique_keys = array_values( $custom_field_unique_keys );

        // =========== Get custom field keys managed by plugins ===========
        
        // ===== Get custom field keys from ASE =====

        if ( class_exists( 'Custom_Field_Group' ) ) {
            if ( ! empty( $post_ids ) ) {
                $args = array(
                    'post_id'   => $post_ids[0],
                );
                $cfg_fields_by_post_id = CFG()->find_fields( $args );

                $cfg_field_keys = array();
                
                if ( ! empty( $cfg_fields_by_post_id ) ) {
                    foreach ( $cfg_fields_by_post_id as $cfg_field ) {
                        $cfg_field_keys[] = $cfg_field['name'];
                    }
                }
                
                $custom_field_unique_keys = array_unique( array_merge( $custom_field_unique_keys, $cfg_field_keys ) );
                sort( $custom_field_unique_keys );
                // vi( $custom_field_unique_keys, '', 'for ' . $post_type_slug . ' after' );

                // $cfg_fields_by_post_id_api = CFG()->get( false, $post_ids[0], array( 'format', 'api' ) );
                // vi( $cfg_fields_by_post_id_api, '', 'for ' . $post_type_slug );

                // $cfg_fields_by_post_id_input = CFG()->get( false, $post_ids[0], array( 'format', 'input' ) );
                // vi( $cfg_fields_by_post_id_input, '', 'for ' . $post_type_slug );

                // $cfg_fields_by_post_id_raw = CFG()->get( false, $post_ids[0], array( 'format', 'raw' ) );
                // vi( $cfg_fields_by_post_id_raw, '', 'for ' . $post_type_slug );
            }
        }

        // ===== Get custom field keys from ACF =====

        if ( class_exists( 'ACF' ) ) {
            if ( ! empty( $post_ids ) ) {
                $acf_fields_by_post_id = get_field_objects( $post_ids[0] );
                // vi( $acf_fields_by_post_id, '', 'for ' . $post_type_slug );
                
                $acf_field_keys = array();

                if ( ! empty( $acf_fields_by_post_id ) ) {
                    $acf_field_keys = array_keys( $acf_fields_by_post_id );

                    // ===== We exclude ACF private fields =====

                    $acf_private_field_keys = array();
                    foreach( $acf_field_keys as $acf_field_key ) {
                        $acf_private_field_keys[] = '_' . $acf_field_key;
                    }

                    foreach ( $acf_private_field_keys as $acf_private_field_key ) {
                        foreach( $custom_field_unique_keys as $index => $custom_field_unique_key ) {
                            if ( $acf_private_field_key == $custom_field_unique_key ) {
                                unset( $custom_field_unique_keys[$index] );
                            }
                        }
                    }
                    // Re-index the keys so it's sequential again
                    $custom_field_unique_keys = array_values( $custom_field_unique_keys );

                    // ===== We exclude sub-fields of repeater and group fields =====

                    $acf_field_keys_to_exclude = array();
                    foreach ( $acf_fields_by_post_id as $acf_field_key => $acf_field ) {
                        if ( 'repeater' == $acf_field['type'] || 'group' == $acf_field['type'] || 'flexible_content' == $acf_field['type'] ) {
                            $acf_field_keys_to_exclude[] = $acf_field_key . '_';
                        }
                    }
                    
                    foreach ( $acf_field_keys_to_exclude as $acf_field_key_to_exclude ) {
                        foreach( $custom_field_unique_keys as $index => $custom_field_unique_key ) {
                            if ( false !== strpos( $custom_field_unique_key, $acf_field_key_to_exclude ) ) {
                                unset( $custom_field_unique_keys[$index] );
                            }
                        }                       
                    }
                    // Re-index the keys so it's sequential again
                    $custom_field_unique_keys = array_values( $custom_field_unique_keys );
                }

                $custom_field_unique_keys = array_unique( array_merge( $custom_field_unique_keys, $acf_field_keys ) );
                sort( $custom_field_unique_keys );
            }
        }

        // ===== Get custom field keys from Meta Box =====

        if ( class_exists( 'RWMB_Core' ) ) {
            if ( ! empty( $post_ids ) ) {
                $mb_fields_by_post_id = rwmb_get_object_fields( $post_ids[0], 'post' );
                // vi( $mb_fields_by_post_id, '', 'for ' . $post_type_slug );
                
                $mb_field_keys = array_keys( $mb_fields_by_post_id );
                $custom_field_unique_keys = array_unique( array_merge( $custom_field_unique_keys, $mb_field_keys ) );
                sort( $custom_field_unique_keys );
                
            }
        }

        // =========== Custom field sample values ===========

        // foreach ( $custom_field_unique_keys as $key ) {
        //  $i = 1;
        //  // Get sample values from default WP post meta
        //  foreach ( $posts_meta_data as $id => $meta_data  ) {
        //      foreach ( $meta_data as $meta_key => $meta_value ) {
        //          if ( $key == $meta_key ) {
        //              if ( isset( $meta_value[0] ) && $i < 2 ) {
        //                  // $metval = $meta_value[0];
        //                  // vi( $metval, '', $i );
        //                  $custom_field_keys_values_sample[$key]['in_post_meta'] = maybe_unserialize( $meta_value[0] );
        //                  $i++;
        //              }
        //          }
        //      }
        //  }

        //  // Get sample values from each custom field handler

        //  // ASE
        //  if ( isset( $cfg_fields_by_post_id_api ) ) {
        //      foreach ( $cfg_fields_by_post_id_api as $cfg_field_key => $cfg_field_value ) {
        //          if ( $key == $cfg_field_key && $i < 3 ) {
        //              $custom_field_keys_values_sample[$cfg_field_key]['by_handler'] = $cfg_field_value;
        //              $i++;
        //          }
        //      }
        //  }

        // }

        // vi( $custom_field_keys_values_sample, '', 'for ' . $post_type_slug );
        
        // =========== Custom field comprehensive info ===========

        $custom_fields = array();

        foreach ( $custom_field_unique_keys as $custom_field_key ) {
            $custom_field_label = '';
            $custom_field_type = '';
            $is_sub_field = false;
            $is_private = ( ! empty( $custom_field_key ) && '_' == $custom_field_key[0] ) ? true : false;
            
            // ===== ASE fields =====

            if ( isset( $cfg_fields_by_post_id ) 
                && ! empty ( $cfg_fields_by_post_id )
                && in_array( $custom_field_key, $cfg_field_keys ) 
            ) {

                $custom_field_handler = 'ASE';

                foreach ( $cfg_fields_by_post_id as $cfg_field ) {
                    if ( $custom_field_key == $cfg_field['name'] ) {

                        $custom_field_label = $cfg_field['label'];
                        $custom_field_type = $cfg_field['type'];

                        if ( $cfg_field['parent_id'] > 0 ) {
                            $is_sub_field = true;
                        }

                    }
                }

            // ===== ACF fields =====

            } elseif ( isset( $acf_fields_by_post_id ) 
                && ! empty ( $acf_fields_by_post_id )
                && in_array( $custom_field_key, $acf_field_keys ) 
            ) {

                $custom_field_handler = 'ACF';

                foreach ( $acf_fields_by_post_id as $acf_field_key => $acf_field ) {
                    if ( $custom_field_key == $acf_field_key ) {
                        $custom_field_label = $acf_field['label'];
                        $custom_field_type = $acf_field['type'];
                        $is_sub_field = false; // No sub-fields in $acf_fields_by_post_id
                    }
                }

            // ===== Meta Box fields =====

            } elseif ( isset( $mb_fields_by_post_id ) 
                && ! empty ( $mb_fields_by_post_id )
                && in_array( $custom_field_key, $mb_field_keys ) 
            ) {

                $custom_field_handler = 'MB';

                foreach ( $mb_fields_by_post_id as $mb_field_key => $mb_field ) {
                    if ( $custom_field_key == $mb_field_key ) {
                        $custom_field_label = $mb_field['name'];
                        $custom_field_type = $mb_field['type'];
                        $is_sub_field = false; // No sub-fields in $mb_fields_by_post_id
                    }
                }

            } else {

                $custom_field_handler = '';

            }

            $custom_fields[$custom_field_key] = array(
                'handler'       => $custom_field_handler, // e.g. CFG, ACF, MB, Toolset, Pods, etc.
                'label'         => $custom_field_label,
                'type'          => $custom_field_type,
                'is_sub_field'  => $is_sub_field,
                'is_private'    => $is_private,
                // 'sample_values'  => ( isset( $custom_field_keys_values_sample[$custom_field_key] ) ) ? $custom_field_keys_values_sample[$custom_field_key] : '',
            );
        }
        
        // Save custom fields info for the post type in ASENHA extra options
        $options = get_option( ASENHA_SLUG_U . '_extra', array() );
        $options['custom_fields'][$post_type_slug] = $custom_fields;
        update_option( ASENHA_SLUG_U . '_extra', $options );
                
        if ( $type == 'unique_keys' ) {
            return $custom_field_unique_keys;
        // } elseif ( $type == 'keys_values_sample' ) {
        //  return $custom_field_keys_values_sample;
        } elseif ( $type == 'comprehensive' ) {
            return $custom_fields;          
        }
                
    }
    
    /**
     * Save custom columns order via AJAX call
     * 
     * @since 5.3.0
     */
    public function save_columns_order__premium_only() {

        if ( isset( $_REQUEST ) ) {
            if ( check_ajax_referer( 'admin-columns-nonce', 'nonce', false ) ) {

                $post_type = $_REQUEST['post_type'];

                $options = get_option( ASENHA_SLUG_U . '_extra', array() );

                // Handle selected active columns

                $columns_raw = $_REQUEST['columns'];
                $columns_order = json_decode( stripslashes( $columns_raw ), true );

                $options['admin_columns'][$post_type] = $columns_order;

                // Handle discarded columns to update list of extra columns stored in options

                $discarded_columns_raw = $_REQUEST['discarded_columns'];
                $discarded_columns = json_decode( stripslashes( $discarded_columns_raw ), true );
                
                $extra_columns = ( isset( $options['admin_columns_extra'][$post_type] ) ) ? $options['admin_columns_extra'][$post_type] : array();
                $extra_columns_keys = array_keys( $extra_columns );
                
                if ( empty( $extra_columns ) ) {
                    foreach( $discarded_columns as $column_key => $column_data ) {
                        if ( $column_data['is_extra_field'] ) {
                            $extra_columns[$column_key] = $column_data;
                        }
                    }
                } else {
                    // Remove extra columns added as active columns
                    foreach( $columns_order as $column_key => $column_data ) {
                        if ( in_array( $column_key, $extra_columns_keys ) ) {
                            unset( $extra_columns[$column_key] );
                        }
                    }

                    // Add discarded extra column
                    foreach( $discarded_columns as $column_key => $column_data ) {
                        if ( $column_data['is_extra_field'] ) {
                            $extra_columns[$column_key] = $column_data;                     
                        }
                    }
                }

                $options['admin_columns_extra'][$post_type] = $extra_columns;
                update_option( ASENHA_SLUG_U . '_extra', $options );

                $response = array(
                    'status'    => 'success',
                );
                
                echo json_encode( $response );
                
            }
        }

    }
    
    /**
     * Reset active columns for the selected post type
     * 
     * @since 6.0.8
     */
    public function reset_columns__premium_only() {

        if ( isset( $_REQUEST ) ) {
            if ( check_ajax_referer( 'admin-columns-nonce', 'nonce', false ) ) {
                
                $post_type = $_REQUEST['post_type'];

                $options = get_option( ASENHA_SLUG_U . '_extra', array() );
                
                unset( $options['admin_columns_available'][$post_type] );
                unset( $options['admin_columns'][$post_type] );
                unset( $options['admin_columns_extra'][$post_type] );
                unset( $options['custom_fields'][$post_type] );

                update_option( ASENHA_SLUG_U . '_extra', $options );
                            
                $response = array(
                    'status'    => 'success',
                );
                
                echo json_encode( $response );

            }
        }
        
    }

    /**
     * Reindex custom fields for the selected post type
     * 
     * @since 6.0.8
     */
    public function reindex_custom_fields__premium_only() {

        if ( isset( $_REQUEST ) ) {
            if ( check_ajax_referer( 'admin-columns-nonce', 'nonce', false ) ) {
                
                $post_type = $_REQUEST['post_type'];

                $options = get_option( ASENHA_SLUG_U . '_extra', array() );             
                unset( $options['custom_fields'][$post_type] );
                update_option( ASENHA_SLUG_U . '_extra', $options );
                            
                $response = array(
                    'status'    => 'success',
                );
                
                echo json_encode( $response );

            }
        }
        
    }

    /**
     * Append custom body classes to the <body> tag of admin listing pages
     *
     * @since 4.4.0
     */
    public function admin_columns_body_class__premium_only( $classes ) {
        global $pagenow, $typenow;
        
        $classes_list = $classes;

        if ( in_array( $pagenow, array( 'edit.php' ) ) ) {
            $classes .= ' asenha-' . sanitize_html_class( $typenow );
        }
        
        $classes_list = $classes;

        return $classes;
    }
    
    /**
     * Enqueue custom admin CSS for list tables of each post type
     *
     * @since 2.3.0
     */
    public function admin_columns_css__premium_only() {
        global $pagenow, $typenow;
        
        if ( 'edit.php' != $pagenow ) {
            return;
        }

        $options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
        $admin_columns = ( isset( $options_extra['admin_columns'] ) ) ? $options_extra['admin_columns'] : array();

        $options = get_option( ASENHA_SLUG_U, array() );

        if ( array_key_exists( 'wider_admin_menu', $options ) && $options['wider_admin_menu'] ) {
            $custom_admin_menu_width = $options['admin_menu_width'];
            $custom_admin_menu_width = str_replace( 'px', '', $custom_admin_menu_width );
            $quick_edit_wrap_adjustment = $custom_admin_menu_width + 40 + 28;
        } else {
            $quick_edit_wrap_adjustment = 160 + 40 + 28;
        }

        // // Hide columns that are not meant to be shown after performing quick edit
        // $shown_columns_classes = array();
        
        // if ( isset( $admin_columns[$typenow] ) ) {

        //  // Remove 'cb', which is the first column / item for most post type listings
        //  if ( isset( $shown_columns['cb'] ) ) {
        //      unset( $shown_columns['cb'] );
        //  }

        //  $shown_columns = array_keys( $admin_columns[$typenow] );
            
        //  // Get list of classes for shown columns. 
        //  // This is used to hide columns that are not meant to be shown after performing Quick Edit
        //  foreach ( $shown_columns as $column_name ) {
        //      $shown_columns_classes[] = '.' . $column_name;
        //  }
        //  $shown_columns_classes = implode( ', ', $shown_columns_classes );
        // }
        
        // $excluded_post_types = array(
        //  'download', // EDD downloads
        // );
        
        foreach ( $admin_columns as $post_type => $columns_info ) {
            // if ( $post_type == $typenow && ! in_array( $typenow, $excluded_post_types ) ) {
            if ( $post_type == $typenow ) {
                ?>
                <style type="text/css" id="admin-columns">
                    @media screen and (min-width: 783px) {

                        #list-table-wrapper .inline-edit-wrapper {
                            max-width: calc(100vw - <?php echo esc_attr( $quick_edit_wrap_adjustment ); ?>px);
                        }
                        
                        /* #the-list > tr:not(.inline-edit-row) > td:not(<?php // echo $shown_columns_classes; ?>) {
                             display: none; 
                        } */
                        <?php
                        foreach ( $columns_info as $column_key => $column_info ) {
                            
                            if ( ! empty( $column_info['width'] ) ) {
                                $width = 'width: ' . $column_info['width'] . $column_info['width_type'] . ' !important;';
                            } else {
                                $width = '';                                
                            }
                            ?>
                            .asenha-<?php echo wp_kses_post( $typenow ); ?> .wp-list-table th.column-<?php echo wp_kses_post( $column_key ); ?> {
                                display: table-cell !important;
                                <?php echo wp_kses_post( $width ); ?>
                            }

                            .asenha-<?php echo wp_kses_post( $typenow ); ?> .wp-list-table th.column-<?php echo wp_kses_post( $column_key ); ?>.hidden {
                                display: none !important;
                            }
                            <?php

                            // Handle 'Auto' width when list table is horizontally scrollable. 
                            // Make them 100px wide to prevent layout issue because no width is assigned.
                            if ( empty( $column_info['width'] ) ) {
                                ?>
                                .asenha-<?php echo wp_kses_post( $typenow ); ?> .h-scrollable .wp-list-table th.column-<?php echo wp_kses_post( $column_key ); ?> {
                                    width: 100px !important;
                                }
                                <?php
                            } 

                        }
                        ?>
                    }
                </style>
                <?php
            }
        }

    }

    /**
     * Reload list table after performing Quick Edit on post types with custom admin columns
     * This is modified from wp_ajax_inline_save() by adding a script before wp_die() at the end
     * 
     * @link https://github.com/WordPress/wordpress-develop/blob/6.3/src/wp-admin/includes/ajax-actions.php#L2049-L2159
     * @since 6.0.6
     */
    public function wp_ajax_inline_save_with_page_reload() {
        global $mode;

        check_ajax_referer( 'inlineeditnonce', '_inline_edit' );

        if ( ! isset( $_POST['post_ID'] ) || ! (int) $_POST['post_ID'] ) {
            wp_die();
        }

        $post_id = (int) $_POST['post_ID'];

        if ( 'page' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                wp_die( esc_html( __( 'Sorry, you are not allowed to edit this page.' ) ) );
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                wp_die( esc_html( __( 'Sorry, you are not allowed to edit this post.' ) ) );
            }
        }

        $last = wp_check_post_lock( $post_id );
        if ( $last ) {
            $last_user      = get_userdata( $last );
            $last_user_name = $last_user ? $last_user->display_name : __( 'Someone' );

            /* translators: %s: User's display name. */
            $msg_template = __( 'Saving is disabled: %s is currently editing this post.' );

            if ( 'page' === $_POST['post_type'] ) {
                /* translators: %s: User's display name. */
                $msg_template = __( 'Saving is disabled: %s is currently editing this page.' );
            }

            printf( esc_html( $msg_template ), esc_html( $last_user_name ) );
            wp_die();
        }

        $data = &$_POST;

        $post = get_post( $post_id, ARRAY_A );

        // Since it's coming from the database.
        $post = wp_slash( $post );

        $data['content'] = $post['post_content'];
        $data['excerpt'] = $post['post_excerpt'];

        // Rename.
        $data['user_ID'] = get_current_user_id();

        if ( isset( $data['post_parent'] ) ) {
            $data['parent_id'] = $data['post_parent'];
        }

        // Status.
        if ( isset( $data['keep_private'] ) && 'private' === $data['keep_private'] ) {
            $data['visibility']  = 'private';
            $data['post_status'] = 'private';
        } else {
            $data['post_status'] = $data['_status'];
        }

        if ( empty( $data['comment_status'] ) ) {
            $data['comment_status'] = 'closed';
        }

        if ( empty( $data['ping_status'] ) ) {
            $data['ping_status'] = 'closed';
        }

        // Exclude terms from taxonomies that are not supposed to appear in Quick Edit.
        if ( ! empty( $data['tax_input'] ) ) {
            foreach ( $data['tax_input'] as $taxonomy => $terms ) {
                $tax_object = get_taxonomy( $taxonomy );
                /** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
                if ( ! apply_filters( 'quick_edit_show_taxonomy', $tax_object->show_in_quick_edit, $taxonomy, $post['post_type'] ) ) {
                    unset( $data['tax_input'][ $taxonomy ] );
                }
            }
        }

        // Hack: wp_unique_post_slug() doesn't work for drafts, so we will fake that our post is published.
        if ( ! empty( $data['post_name'] ) && in_array( $post['post_status'], array( 'draft', 'pending' ), true ) ) {
            $post['post_status'] = 'publish';
            $data['post_name']   = wp_unique_post_slug( $data['post_name'], $post['ID'], $post['post_status'], $post['post_type'], $post['post_parent'] );
        }

        // Update the post.
        edit_post();

        $wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => $_POST['screen'] ) );

        $mode = 'excerpt' === $_POST['post_view'] ? 'excerpt' : 'list';

        $level = 0;
        if ( is_post_type_hierarchical( $wp_list_table->screen->post_type ) ) {
            $request_post = array( get_post( $_POST['post_ID'] ) );
            $parent       = $request_post[0]->post_parent;

            while ( $parent > 0 ) {
                $parent_post = get_post( $parent );
                $parent      = $parent_post->post_parent;
                $level++;
            }
        }

        $wp_list_table->display_rows( array( get_post( $_POST['post_ID'] ) ), $level );

        // INTERCEPT: Add a script to reload the list table page
        ?>
            <script type="text/javascript">
                jQuery('#post-<?php echo esc_attr( $_POST['post_ID'] ); ?>').css('opacity','0.3');
                document.location.reload(true);
            </script>
        <?php       
        // end INTERCEPT

        wp_die();
    }
    
}