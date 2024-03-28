<?php

namespace ASENHA\Classes;

/**
 * Class for Email Address Obfuscator module
 *
 * @since 6.9.5
 */
class Email_Address_Obfuscator {

    /**
     * Obfuscate email address on the frontend using antispambot() native WP function
     * 
     * @link: https://gist.github.com/eclarrrk/349360b52e8822b69cb6fc499722520f
     * @since 5.5.0
     */
    public function obfuscate_string( $atts ) {
        
        $atts = shortcode_atts( array(
            'email'     => '',
            'subject'   => '',
            'display'   => 'newline',
            'link'      => 'no',
            'class'     => '',
        ), $atts );

        $email = $atts['email'];

        if ( ! is_email( $email ) ) {
            return;
        }
        
        // Reverse email address characters if not in Firefox, which has bug related to unicode-bidi CSS property
        $http_user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'generic';
        if ( false !== stripos( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 'firefox' ) ) {
            // Do nothing. Do not reverse characters.
            $email_reversed = $email;
            $email_rev_parts = explode( '@', $email_reversed );
            $email_rev_parts = array( $email_rev_parts[0], $email_rev_parts[1] );
            $css_bidi_styles = '';
        } else {
            $email_reversed = strrev( $email );     
            $email_rev_parts = explode( '@', $email_reversed );
            $css_bidi_styles = 'unicode-bidi:bidi-override;';
        }

        $display = $atts['display'];
        if ( 'newline' == $display ) {
            $display_css = 'display:flex;justify-content:flex-end;';
        } elseif ( 'inline' == $display ) {
            $display_css = 'display:inline;';
        }

        $subject = $atts['subject'];
        if ( ! empty ( $subject ) ) {
            $subject = '?subject=' . $subject;
        }
        $link = $atts['link'];
        $class = $atts['class'];

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {
            
            if ( 'yes' == $link ) {
                return '<a href="mailto:' . esc_html( antispambot( $email, 1 ) ) . $subject . '" class="' . esc_attr( $class ) . '">' . '<span style="' . esc_attr( $display_css ) . esc_attr( $css_bidi_styles ) . 'direction:rtl;">' . esc_html( $email_rev_parts[0] ) . '<span style="display:none;">obfsctd</span>&#64;' . esc_html( $email_rev_parts[1] ) . '</span>' . '</a>';
            } else {    
                return '<span style="' . esc_attr( $display_css ) . esc_attr( $css_bidi_styles ) . 'direction:rtl;" class="' . esc_attr( $class ) . '">' . esc_html( $email_rev_parts[0] ) . '<span style="display:none;">obfsctd</span>&#64;' . esc_html( $email_rev_parts[1] ) . '</span>';
            }

        } else {

            return '<div style="display:inline;' . esc_attr( $css_bidi_styles ) . ';direction:rtl;" class="' . esc_attr( $class ) . '">' . esc_html( $email_rev_parts[0] ) . '<span style="display:none;">obfsctd</span>&#64;' . esc_html( $email_rev_parts[1] ) . '</div>';
            
        }   
        
    }
    
    /**
     * Replace email addresses in post content with the obfuscation shortcode
     * 
     * @since 6.2.1
     */
    public function obfuscate_emails_in_content__premium_only( $content ) {
        // Regex pattern for an email address
        // $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/';
        // Regex pattern for an email address preceeded by double qoute, space or >
        $pattern = '/(?:^|[>"\s])([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/';
        $content = preg_replace_callback( $pattern, [ $this, 'replace_email_with_shortcode__premium_only' ], $content );
        
        return $content;        
    }
    
    /**
     * Replace each email with the obfuscation shortcode
     * 
     * @since 6.2.1
     */
    public function replace_email_with_shortcode__premium_only( $matches ) {
        $email = $matches[0];
        if ( ' ' == $email[0] ) {
            // Matched email is preceeded by a blank space, let's replace it with the obfuscate shortcode
            // so the email addres is obfuscated
            $email = substr($email, 1);
            $shortcode = '[obfuscate email="' . $email . '" display="inline"]';
            return " " . $shortcode;            
        } elseif ('>' == $email[0]) {
            // Matched email is preceeded by a ">", e.g. inside a <div> or <span> 
            // let's replace it with the obfuscate shortcode, so the email addres is obfuscated
            // $email = substr($email, 1);
            $email = substr($email, 1);
            $shortcode = '[obfuscate email="' . $email . '" display="inline"]';
            return ">" . $shortcode;
        } elseif ( '"' == $email[0] ) {
            // Matched email is preceeded by double quote, it's already part of the obfuscate shortcode.
            // Let's return as is, so the shortcode can be executed as is.
            return $email;
        } else {}
    }

}