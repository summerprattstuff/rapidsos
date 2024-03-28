<?php

namespace ASENHA\Classes;

/**
 * Class for Redirect After Login module
 *
 * @since 6.9.5
 */
class Redirect_After_Login {

    /**
     * Redirect to custom internal URL after login for user roles
     *
     * @param string $redirect_to_url URL to redirect to. Default is admin dashboard URL.
     * @param string $origin_url URL the user is coming from.
     * @param object $user logged-in user's data.
     * @since 1.5.0
     */
    public function redirect_for_roles_after_login( $username, $user ) {

        $options = get_option( ASENHA_SLUG_U, array() );
        $redirect_after_login_to_slug = trim( trim( $options['redirect_after_login_to_slug'] ), '/');
        if ( false !== strpos( $redirect_after_login_to_slug, '.php' ) ) {
            $slug_suffix = '';
        } else {
            $slug_suffix = '/';
        }
        $redirect_after_login_for = $options['redirect_after_login_for'];

        if ( isset( $redirect_after_login_for ) && ( count( $redirect_after_login_for ) > 0 ) ) {

            // Assemble single-dimensional array of roles for which custom URL redirection should happen
            $roles_for_custom_redirect = array();

            foreach( $redirect_after_login_for as $role_slug => $custom_redirect ) {
                if ( $custom_redirect ) {
                    $roles_for_custom_redirect[] = $role_slug;
                }
            }

            // Does the user have roles data in array form?
            if ( isset( $user->roles ) && is_array( $user->roles ) ) {

                $current_user_roles = $user->roles;

            }

            // Set custom redirect URL for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
            foreach ( $current_user_roles as $role ) {
                if ( in_array( $role, $roles_for_custom_redirect ) ) {
                    
                    wp_safe_redirect( home_url( $redirect_after_login_to_slug . $slug_suffix ) );
                    exit();

                }
            }

        }

    }
    
}