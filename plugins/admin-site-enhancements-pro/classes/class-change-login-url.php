<?php

namespace ASENHA\Classes;

/**
 * Class for Change Login URL module
 *
 * @since 6.9.5
 */
class Change_Login_URL {

    /**
     * Redirect to valid login URL when custom login slug is part of the request URL
     *
     * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L134
     * @since 1.4.0
     */
    public function redirect_on_custom_login_url() {

        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        $url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );

        // Make sure $url_input ends with /
        if ( false !== strpos( $url_input, $custom_login_slug ) ) {
            if ( substr( $url_input, -1 ) != '/' ) {
                $url_input = $url_input . '/';
            }
        }
        
        // If URL contains the custom login slug, redirect to the dashboard
        if ( false !== strpos( $url_input, '/' . $custom_login_slug . '/' ) ) {
            if ( is_user_logged_in() ) {
                // Redirect to dashboard
                wp_safe_redirect( get_admin_url() );            
            } else {
                // Redirect to the login URL with custom login slug in the query parameters
                wp_safe_redirect( site_url( '/wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
            }
            exit();
        }

    }

    /**
     * Customize login URL returned when calling wp_login_url(). Add the custom login slug.
     * 
     * @since 5.8.0
     */
    public function customize_login_url( $lostpassword_url ) {
        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        return home_url( '/' . $custom_login_slug . '/' );
    }

    
    /**
     * Customize lost password URL. Add the custom login slug.
     * 
     * @since 5.8.0
     */
    public function customize_lost_password_url( $lostpassword_url ) {
        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        // return home_url( '/wp-login.php?manage&action=lostpassword' );
        return $lostpassword_url . '&' . $custom_login_slug;
    }

    /**
     * Customize registration URL. Add the custom login slug.
     * 
     * @since 6.2.5
     */
    public function customize_register_url( $registration_url ) {
        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        // return home_url( '/wp-login.php?manage&action=lostpassword' );
        return $registration_url . '&' . $custom_login_slug;
    }
    
    /**
     * Redirect to /not_found when login URL does not contain the custom login slug
     * This will redirect /wp-login.php and /wp-admin/ to /not_found/
     *
     * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L121
     * @since 1.4.0
     */
    public function redirect_on_default_login_urls() {

        global $interim_login;

        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug']; // e.g. manage
        $url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
            $redirect_slug = isset( $options['default_login_redirect_slug'] ) ? $options['default_login_redirect_slug'] : 'not_found';
        } else {
            $redirect_slug = 'not_found';
        }
        
        // When logging in
        if ( ( isset( $_POST['log'] ) && isset( $_POST['pwd'] ) ) 
            || ( isset( $_POST['post_password'] ) )
            || is_user_logged_in()
        ) {
        
            // Do nothing. i.e. do not redirect to /not_found/ as this contains a login POST request
            // or the user is already logged in
            // upon successful login, redirection to logged-in view of /wp-admin/ happens.
            // Without this condition, login attempt will redirect to /not_found/
        
        } else {
        
            // When landing on the login page
            // At this point /admin, /wp-admin/, /login redirects to /wp-login.php
            if ( false !== strpos( $url_input, 'wp-login' ) ) {

                if ( ( isset( $_GET['action'] ) 
                        && ( 'logout' == $_GET['action'] 
                            || 'rp' == $_GET['action'] 
                            || 'resetpass' == $_GET['action'] ) ) 
                    || ( isset( $_GET['checkemail'] ) && ( 'confirm' == $_GET['checkemail'] || 'registered' == $_GET['checkemail'] ) ) 
                    || ( isset( $_GET['interim-login'] ) && '1' == $_GET['interim-login'] ) 
                    || 'success' == $interim_login
                ) {

                    // When we're logging out, inside the reset password flow, inside the registration flow or within the interim login flow
                    // e.g. https://www.example.com/wp-login.php?action=logout&_wpnonce=49bb818269
                    // e.g. https://www.example.com/wp-login.php?action=rp --> reset password
                    // e.g. https://www.example.com/wp-login.php?action=resetpass --> reset password
                    // e.g. https://www.example.com/wp-login.php?checkmail=confirm --> reset password
                    // e.g. https://www.example.com/wp-login.php?checkmail=registered --> register account
                    // e.g. https://www.example.com/wp-login.php?interim-login=1&wp_lang=en_US  
                    // Do nothing.. proceed...

                } elseif ( isset( $_GET['action'] ) && ( 'lostpassword' == $_GET['action'] || 'register' == $_GET['action'] ) ) {

                    // When resetting password or registering an account

                    if ( isset( $_POST['user_login'] ) ) {

                        // Sending the form to reset password or register an account...
                        // Do nothing.. proceed with password reset or account registration

                    } else {
                        
                        // When landing on the password reset or registration form
                        // ...and custom login slug is not in the URL
                        if ( false === strpos( $url_input, $custom_login_slug ) ) {
                            // Redirect to /not_found/
                            wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
                            exit();
                        } 

                        // or, custom login slug is in the url
                        // e.g. https://www.example.com/wp-login.php?action=lostpassword&customloginslug
                        // e.g. https://www.example.com/wp-login.php?action=register&customloginslug
                        // Do nothing... allow reset password or registration
                        
                    }
                    
                } else {
                    
                    // When landing on the login form
                    // ...and custom login slug is not in the URL
                    if ( false === strpos( $url_input, $custom_login_slug ) ) {
                        // Redirect to /not_found/
                        wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
                        exit();
                    } 

                    // ...or, custom login slug is in the URL
                    // e.g. https://www.example.com/wp-login.php?customloginslug&redirect=false
                    // Do nothing... allow login
                                        
                }

            }
            
        }
    }

    /**
     * Redirect to custom login URL on failed login
     *
     * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L148
     * @since 1.4.0
     */
    public function redirect_to_custom_login_url_on_login_fail() {
        
        global $asenha_limit_login;

        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        if ( isset( $asenha_limit_login ) && is_array( $asenha_limit_login ) && $asenha_limit_login['within_lockout_period'] ) {
            // Do nothing. This prevents redirection loop.
        } else {

            $should_redirect = true;                

            if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
                
                if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

                    // Prevent redirection to wp-login.php when user failed logging in in a WooCommerce checkout page

                    $request_uri = sanitize_url( $_SERVER['REQUEST_URI'] );
                    $request_url = get_site_url() . $request_uri;
                    
                    $checkout_page_url = wc_get_page_permalink( 'checkout' );

                    if ( $request_url == $checkout_page_url ) {
                        $should_redirect = false;               
                    }
                }
                
            }
            
            if ( $should_redirect ) {

                // Append 'failed_login=true' so we can output custom error message above the login form
                wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false&failed_login=true' ) );
                exit();
                
            }
        }

    }
    
    /**
     * Add login error message on top of the login form. 
     * Only shown if there's a failed_login URL parameter, and Limit Login Attempts module is not enabled. 
     * If LLA module is enabled, the same custom login error message is handled there.
     *
     * @since 6.9.1
     */
    public function add_failed_login_message( $message ) {
        global $asenha_limit_login;

        if ( isset( $_REQUEST['failed_login'] ) && $_REQUEST['failed_login'] == 'true' ) {
            if ( is_null( $asenha_limit_login ) ) {
                $message = '<div id="login_error" class="notice notice-error"><b>Error:</b> Invalid username/email or incorrect password.</div>';               
            }
        }

        return $message;
    }
    
    /**
     * Redirect to custom login URL on successful logout
     *
     * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L148
     * @since 1.4.0
     */
    public function redirect_to_custom_login_url_on_logout_success() {

        $options = get_option( ASENHA_SLUG_U );
        $custom_login_slug = $options['custom_login_slug'];

        // Redirect to the login URL with custom login slug in it
        wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
        exit();

    }
    
}