<?php

namespace ASENHA\Classes;

use WP_Admin_Bar;

/**
 * Class for Hide Admin Notices module
 *
 * @since 6.9.5
 */
class Hide_Admin_Notices {
    
    /**
     * Wrapper for admin notices being output on admin screens
     *
     * @since 1.2.0
     */
    public function admin_notices_wrapper() {
        
        $options = get_option( ASENHA_SLUG_U, array() );
        $hide_for_nonadmins = isset( $options['hide_admin_notices_for_nonadmins'] ) ? $options['hide_admin_notices_for_nonadmins'] : false;
        
        $minimum_capability = 'manage_options';

        if ( function_exists( 'bwasenha_fs' ) ) {
            if ( $hide_for_nonadmins && bwasenha_fs()->can_use_premium_code__premium_only() ) {
                $minimum_capability = 'read';           
            }
        }

        if ( current_user_can( $minimum_capability ) ) {

            echo '<div class="asenha-admin-notices-drawer" style="display:none;"><h2>Admin Notices</h2></div>';
            
        }

    }

    /**
     * Admin bar menu item for the hidden admin notices
     *
     * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_menu/
     * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_node/
     * @since 1.2.0
     */
    public function admin_notices_menu( WP_Admin_Bar $wp_admin_bar ) {

        // Only show Notices menu in wp-admin but when not in Customizer preview
        if ( is_admin() && ! is_customize_preview() ) {
            $options = get_option( ASENHA_SLUG_U, array() );
            $hide_for_nonadmins = isset( $options['hide_admin_notices_for_nonadmins'] ) ? $options['hide_admin_notices_for_nonadmins'] : false;
            
            $minimum_capability = 'manage_options';

            if ( function_exists( 'bwasenha_fs' ) ) {
                if ( $hide_for_nonadmins && bwasenha_fs()->can_use_premium_code__premium_only() ) {
                    $minimum_capability = 'read';           
                }
            }

            if ( current_user_can( $minimum_capability ) ) {
                
                $wp_admin_bar->add_menu( array(
                    'id'        => 'asenha-hide-admin-notices',
                    'parent'    => 'top-secondary',
                    'grou'      => null,
                    'title'     => 'Notices<span class="asenha-admin-notices-counter" style="opacity:0;">0</span>',
                    // 'href'       => '',
                    'meta'      => array(
                        'class'     => 'asenha-admin-notices-menu',
                        'title'     => 'Click to view hidden admin notices',
                    ),
                ) );

            }           
        }

    }

    /**
     * Inline CSS for the hiding notices on page load in wp admin pages
     *
     * @since 1.2.0
     */
    public function admin_notices_menu_inline_css() {

        $options = get_option( ASENHA_SLUG_U, array() );
        $hide_for_nonadmins = isset( $options['hide_admin_notices_for_nonadmins'] ) ? $options['hide_admin_notices_for_nonadmins'] : false;
        
        $minimum_capability = 'manage_options';

        if ( function_exists( 'bwasenha_fs' ) ) {
            if ( $hide_for_nonadmins && bwasenha_fs()->can_use_premium_code__premium_only() ) {
                $minimum_capability = 'read';           
            }
        }

        if ( is_admin() && ! is_customize_preview() && current_user_can( $minimum_capability ) ) {

            // Below we pre-emptively hide notices to avoid having them shown briefly before being moved into the notices panel via JS
            ?>
            <style type="text/css">
                #wpadminbar .asenha-admin-notices-menu .ab-empty-item {
                    cursor: pointer;
                }
                
                #wpadminbar .asenha-admin-notices-counter {
                    box-sizing: border-box;
                    margin: 1px 0 -1px 6px ;
                    padding: 2px 6px 3px 5px;
                    min-width: 18px;
                    height: 18px;
                    border-radius: 50%;
                    background-color: #ca4a1f;
                    color: #fff;
                    font-size: 11px;
                    line-height: 1.6;
                    text-align: center;
                }

                /* #wpbody-content .notice:not(.system-notice,.update-message),
                #wpbody-content .notice-error,
                #wpbody-content .error,
                #wpbody-content .notice-info,
                #wpbody-content .notice-information,
                #wpbody-content #message,
                #wpbody-content .notice-warning:not(.update-message),
                #wpbody-content .notice-success:not(.update-message),
                #wpbody-content .notice-updated,
                #wpbody-content .updated:not(.active, .inactive, .plugin-update-tr),
                #wpbody-content .update-nag, */
                #wpbody-content > .wrap > .notice:not(.system-notice,.hidden),
                #wpbody-content > .wrap > .notice-error,
                #wpbody-content > .wrap > .error:not(.hidden),
                #wpbody-content > .wrap > .notice-info,
                #wpbody-content > .wrap > .notice-information,
                #wpbody-content > .wrap > #message,
                #wpbody-content > .wrap > .notice-warning:not(.hidden),
                #wpbody-content > .wrap > .notice-success,
                #wpbody-content > .wrap > .notice-updated,
                #wpbody-content > .wrap > .updated,
                #wpbody-content > .wrap > .update-nag,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice:not(.system-notice,.hidden),
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-error,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .error:not(.hidden),
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-info,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-information,
                #wpbody-content > .wrap > div > #message,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-warning:not(.hidden),
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-success,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .notice-updated,
                #wpbody-content > .wrap > div:not(#loco-notices,#loco-content) > .updated,
                #wpbody-content > .wrap > div > .update-nag,
                #wpbody-content > div > .wrap > .notice:not(.system-notice,.hidden),
                #wpbody-content > div > .wrap > .notice-error,
                #wpbody-content > div > .wrap > .error:not(.hidden),
                #wpbody-content > div > .wrap > .notice-info,
                #wpbody-content > div > .wrap > .notice-information,
                #wpbody-content > div > .wrap > #message,
                #wpbody-content > div > .wrap > .notice-warning:not(.hidden),
                #wpbody-content > div > .wrap > .notice-success,
                #wpbody-content > div > .wrap > .notice-updated,
                #wpbody-content > div > .wrap > .updated,
                #wpbody-content > div > .wrap > .update-nag,
                #wpbody-content > .notice,
                #wpbody-content > .error,
                #wpbody-content > .updated,
                #wpbody-content > .update-nag,
                #wpbody-content > .jp-connection-banner,
                #wpbody-content > .jitm-banner,
                #wpbody-content > .jetpack-jitm-message,
                #wpbody-content > .ngg_admin_notice,
                #wpbody-content > .imagify-welcome,
                #wpbody-content #wordfenceAutoUpdateChoice,
                #wpbody-content #easy-updates-manager-dashnotice,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice:not(.system-notice,.hidden),
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-error,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .error:not(.hidden),
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-info,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-information,
                #wpbody-content > .wrap.gblocks-dashboard-wrap #message,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-warning:not(.hidden),
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-success,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .notice-updated,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .updated,
                #wpbody-content > .wrap.gblocks-dashboard-wrap .update-nag {
                    position: absolute !important;
                    visibility: hidden !important;
                }
            </style>
            <?php
        
        }
    }
    
}