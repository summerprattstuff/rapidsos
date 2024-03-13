<?php

namespace ASENHA\Classes;
use WP_Query;

/**
 * Class that provides common methods used throughout the plugin
 *
 * @since 2.5.0
 */
class Common_Methods {

	/**
	 * Get IP of the current visitor/user. In use by at least the Limit Login Attempts feature.
	 * This takes a best guess of the visitor's actual IP address.
	 * Takes into account numerous HTTP proxy headers due to variations
	 * in how different ISPs handle IP addresses in headers between hops.
	 *
	 * @link https://stackoverflow.com/q/1634782
	 * @since 2.5.0
	 */
	public function get_user_ip_address() {

		// Check for shared internet/ISP IP
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) && $this->is_ip_valid( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
		}

		// Check for IPs passing through proxies
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// Check if multiple IP addresses exist in var
			$ip_list = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			if ( is_array( $ip_list ) ) {
				foreach ( $ip_list as $ip ) {
					if ( $this->is_ip_valid( trim( $ip ) ) ) {
						return sanitize_text_field( trim( $ip ) );
					}
				}
			} else {
				return sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
			}
		}

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED'] ) && $this->is_ip_valid( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_X_FORWARDED'] );
		}

		if ( ! empty( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) && $this->is_ip_valid( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] );		
		}

		if ( ! empty( $_SERVER['HTTP_FORWARDED_FOR'])  && $this->is_ip_valid( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_FORWARDED_FOR'] );
		}

		if ( ! empty( $_SERVER['HTTP_FORWARDED'] ) && $this->is_ip_valid( $_SERVER['HTTP_FORWARDED'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_FORWARDED'] );
		}

		// Return unreliable IP address since all else failed
		return sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
	}
	
	/**
	 * Check if the supplied IP address is valid or not
	 * 
	 * @param  string  $ip an IP address
	 * @link https://stackoverflow.com/q/1634782
	 * @return boolean		true if supplied address is valid IP, and false otherwise
	 */
	public function is_ip_valid( $ip ) {
		if ( empty( $ipt ) ) {
			return false;
		}
		
		if ( false == filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return false;
		} else {
			return true;
		}		
	}

	/**
	 * Convert number of seconds into hours, minutes, seconds. In use by at least the Limit Login Attempts feature.
	 *
	 * @since 2.5.0
	 */
	public function seconds_to_period( $seconds, $conversion_type ) {

	    $period_start = new \DateTime('@0');
	    $period_end = new \DateTime("@$seconds");

	    if ( $conversion_type == 'to-days-hours-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%a days, %h hours, %i minutes and %s seconds');

	    } elseif ( $conversion_type == 'to-hours-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%h hours, %i minutes and %s seconds');

	    } elseif ( $conversion_type == 'to-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%i minutes and %s seconds');

	    } else {

		    return $period_start->diff($period_end)->format('%a days, %h hours, %i minutes and %s seconds');

	    }

	}

	/**
	 * Remove html tags and content inside the tags from a string
	 *
	 * @since 3.0.3
	 */
	public function strip_html_tags_and_content( $string ) {

		// Strip HTML tags and content inside them. Ref: https://stackoverflow.com/a/39320168
		if ( ! is_null( $string ) ) {
			$string = preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $string);		

	        // Strip any remaining HTML or PHP tags
	        $string = strip_tags( $string );
		}

        return $string;

	}
	
	/**
	 * Get menu hidden by toggle
	 * 
	 * @since 5.1.0
	 */
	public function get_menu_hidden_by_toggle() {

		$menu_hidden_by_toggle = array();

		$options = get_option( ASENHA_SLUG_U, array() );

        if ( bwasenha_fs()->can_use_premium_code__premium_only() ) {

			if ( array_key_exists( 'custom_menu_always_hidden', $options ) ) {
				$menu_always_hidden = $options['custom_menu_always_hidden'];
				$menu_always_hidden = json_decode( $menu_always_hidden, true );
				
				if ( ! empty( $menu_always_hidden ) ) {
					foreach( $menu_always_hidden as $menu_id => $hidden_info ) {
						if ( isset( $hidden_info['hide_by_toggle'] ) 
							&& $hidden_info['hide_by_toggle'] 
							) {
							// Exclude menu items that are set to always be hidden
							if ( isset( $hidden_info['always_hide_for'] ) 
								&& ( $hidden_info['always_hide_for'] == 'all-roles' )
								) {
								// Do nothing
							} else {
									$menu_hidden_by_toggle[] = $this->restore_menu_item_id( $menu_id );							
							}
						}
					}
				}
			}

		} else {

			if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
				$menu_hidden = $options['custom_menu_hidden'];
				$menu_hidden = explode( ',', $menu_hidden );
				$menu_hidden_by_toggle = array();
				foreach ( $menu_hidden as $menu_id ) {
					$menu_hidden_by_toggle[] = $this->restore_menu_item_id( $menu_id );
				}
			}       	

		}

		return $menu_hidden_by_toggle;

	}

	/**
	 * Get user capabilities for which the "Show All/Less" menu toggle should be shown for
	 * 
	 * @since 5.1.0
	 */
	public function get_user_capabilities_to_show_menu_toggle_for() {
		
		global $menu;

		$menu_always_hidden = array();
		$user_capabilities_menus_are_hidden_for = array();
		
		$menu_hidden_by_toggle = $this->get_menu_hidden_by_toggle(); // indexed array
		
		foreach( $menu as $menu_key => $menu_info ) {
			foreach( $menu_hidden_by_toggle as $hidden_menu_id ) {
				if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
					$menu_item_id = $menu_info[2];
				} else {
					$menu_item_id = $menu_info[5];
				}

				if ( $menu_item_id == $hidden_menu_id ) {
					$user_capabilities_menus_are_hidden_for[] = $menu_info[1];
				}
			}
		}
		
		$user_capabilities_menus_are_hidden_for = array_unique( $user_capabilities_menus_are_hidden_for );

		return $user_capabilities_menus_are_hidden_for; // indexed array
	}

	/**
	 * Get user roles menu is hidden for
	 * 
	 * @since 5.1.0
	 */
	public function get_roles_menu_is_hidden_for__premium_only( $menu_item_id ) {
		$roles_menu_is_hidden_for = array();

		$options = get_option( ASENHA_SLUG_U, array() );
		$menu_always_hidden = $options['custom_menu_always_hidden'];
		$menu_always_hidden = json_decode( $menu_always_hidden, true );

		foreach( $menu_always_hidden as $menu_id => $hidden_info ) {
			if ( $menu_id == $menu_item_id ) {
				if ( isset( $hidden_info['which_roles'] ) && ! empty( $hidden_info['which_roles'] ) ) {
					$which_roles = $hidden_info['which_roles'];
					$roles_menu_is_hidden_for = array_values( $hidden_info['which_roles'] );
				}				
			}
		}
		
		return $roles_menu_is_hidden_for;		
	}
	
	/**
	 * Get url fragments of admin menu pages that may always be hidden / restricted by user roles etc
	 * 
	 * @since 5.1.0
	 */
	public function get_url_fragments_of_always_hidden_menu_pages__premium_only() {

		$options = get_option( ASENHA_SLUG_U, array() );
		if ( array_key_exists( 'custom_menu_always_hidden', $options ) ) {
			$menu_always_hidden = $options['custom_menu_always_hidden'];
			$menu_always_hidden = json_decode( $menu_always_hidden, true );			
		} else {
			$menu_always_hidden = array();
		}
		
		$menu_url_fragments = array();
		
		if ( is_array( $menu_always_hidden ) && ! empty( $menu_always_hidden ) ) {
			foreach ( $menu_always_hidden as $hidden_menu_id => $hidden_menu_info ) {
				if ( isset( $hidden_menu_info['always_hide'] ) && $hidden_menu_info['always_hide'] ) {
					if ( isset( $hidden_menu_info['menu_url_fragment'] ) ) {
						$menu_url_fragment = $hidden_menu_info['menu_url_fragment'];					
					} else {
						$menu_url_fragment = '';
					}
					$menu_url_fragments[] = $menu_url_fragment;
				}
			}
		}

		// Clean up empty fragments
		if ( is_array( $menu_url_fragments ) && ! empty( $menu_url_fragments ) ) {
			foreach ( $menu_url_fragments as $index => $url_fragment ) {
				if ( empty( $url_fragment ) ) {
					unset( $menu_url_fragments[$index] );
				}
			}			
			// Restart indexing
			$menu_url_fragments = array_values( $menu_url_fragments );
		}
				
		return $menu_url_fragments;		
	}
	
	/**
	 * Get roles etc for which menu item should be hidden
	 * 
	 * @since 5.1.0
	 */
	public function get_always_hide_for__premium_only( $menu_url_fragment ) {

		$options = get_option( ASENHA_SLUG_U, array() );
		if ( array_key_exists( 'custom_menu_always_hidden', $options ) ) {
			$menu_always_hidden = $options['custom_menu_always_hidden'];
			$menu_always_hidden = json_decode( $menu_always_hidden, true );			
		} else {
			$menu_always_hidden = array(
				$menu_url_fragment => array(
					'menu_url_fragment'	=> ''
				)
			);
		}
		
		$always_hide_for = array();
		
		foreach( $menu_always_hidden as $menu_id => $menu_info ) {
			if ( isset( $menu_info['menu_url_fragment'] ) ) {
				$hidden_menu_url_fragment = $menu_info['menu_url_fragment'];			
			} else {
				$hidden_menu_url_fragment = '';
			}
			if ( $hidden_menu_url_fragment == $menu_url_fragment ) {
				if ( isset( $menu_info['always_hide'] ) ) {
					$always_hide_for[$menu_url_fragment]['always_hide'] = $menu_info['always_hide'];
				} else {
					$always_hide_for[$menu_url_fragment]['always_hide'] = false;					
				}
				if ( isset( $menu_info['always_hide_for'] ) ) {
					$always_hide_for[$menu_url_fragment]['always_hide_for'] = $menu_info['always_hide_for'];
				} else {
					$always_hide_for[$menu_url_fragment]['always_hide_for'] = '';
				}
				if ( isset( $menu_info['which_roles'] ) ) {
					$always_hide_for[$menu_url_fragment]['which_roles'] = $menu_info['which_roles'];
				} else {
					$always_hide_for[$menu_url_fragment]['which_roles'] = array();					
				}
			}
		}
		
		return $always_hide_for;
	}
	
	/**
	 * Transform menu item's ID
	 * 
	 * @since 5.1.0
	 */
	public function transform_menu_item_id( $menu_item_id ) {

		// Transform e.g. edit.php?post_type=page ==> edit__php___post_type____page
		$menu_item_id_transformed = str_replace( array( ".", "?", "=/", "=", "&", "/" ), array( "__", "___", "_______", "____", "_____", "______" ), $menu_item_id );
		
		return $menu_item_id_transformed;
		
	}

	/**
	 * Transform menu item's ID
	 * 
	 * @since 5.1.0
	 */
	public function restore_menu_item_id( $menu_item_id_transformed ) {

		// Transform e.g. edit__php___post_type____page ==> edit.php?post_type=page
		$menu_item_id = str_replace( array( "_______", "______", "_____", "____", "___", "__"  ), array( "=/", "/", "&", "=", "?", "."  ), $menu_item_id_transformed );
		
		return $menu_item_id;
		
	}
	
	/**
	 * Sanitize array with string values
	 * 
	 * @since 5.1.0
	 */
	public function sanitize_array__premium_only( $input ) {
		$output = array();
		foreach ( $input as $key => $value ) {
			$output[$key] = sanitize_text_field( $value );
		}
		return $output;
	}
	
	/**
	 * Sanitize text input and update post meta with it
	 * 
	 * @since 5.1.0
	 */
	public function update_post_meta_after_sanitization__premium_only( $post_id, $key, $sanitization_method, $default_value = '' ) {

		if ( $sanitization_method == 'sanitize_text_field' ) {
			$$key = isset( $_POST[$key] ) ? sanitize_text_field( trim( $_POST[$key] ) ) : $default_value;
		} elseif ( $sanitization_method == 'sanitize_textarea_field' ) {
			$$key = isset( $_POST[$key] ) ? sanitize_textarea_field( trim( $_POST[$key] ) ) : $default_value;
		} elseif ( $sanitization_method == 'sanitize_checkbox' ) {
			$$key = ( isset( $_POST[$key] ) && 'on' == $_POST[$key] ) ? true : false;
		} elseif ( $sanitization_method == 'sanitize_title' ) {
			$$key = isset( $_POST[$key] ) ? sanitize_title( str_replace( ' ', '_', trim( $_POST[$key] ) ) ) : $default_value;	
		} elseif ( $sanitization_method == 'sanitize_title_underscore' ) {
			$$key = isset( $_POST[$key] ) ? sanitize_title( strtolower( str_replace( ' ', '_', trim( $_POST[$key] ) ) ) ) : $default_value;	
		} elseif ( $sanitization_method == 'sanitize_array' ) {
			$$key = ( isset( $_POST[$key] ) ) ? $this->sanitize_array__premium_only( $_POST[$key] ) : $default_value;
		}

		update_post_meta( 
			$post_id, 
			$key, 
			$$key 
		);
	}

	/**
	 * Sanitize hexedecimal numbers used for colors
	 *
	 * @link https://plugins.trac.wordpress.org/browser/bm-custom-login/trunk/bm-custom-login.php
	 * @param string $color Hex number to sanitize.
	 * @return string
	 */
	public function sanitize_hex_color( $color ) {

		if ( '' === $color ) {
			return '';
		}

		// Make sure the color starts with a hash.
		$color = '#' . ltrim( $color, '#' );

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return null;

	}

	/**
	 * Get the post ID of the most recent post in a custom post type
	 * 
	 * @since 6.4.1
	 */
	public function get_most_recent_post_id( $post_type ) {

	    $args = array(
	        'post_type'      => $post_type,
	        'posts_per_page' => 1,
	        'orderby'        => 'date',
	        'order'          => 'DESC',
	    );

	    $query = new WP_Query( $args );

	    if ( $query->have_posts() ) {
	        $query->the_post();
	        $post_id = get_the_ID();
	        wp_reset_postdata();
	        return $post_id;
	    }

	    return 0; // Return 0 if no posts found
		
	}

	/**
	 * Extended ruleset for wp_kses() that includes SVG tag and it's children
	 * 
	 * @since 6.8.3
	 */
	public function get_kses_extended_ruleset() {
	    $kses_defaults = wp_kses_allowed_html( 'post' );

	    // For SVG icons
	    $svg_args = array(
	        'svg'   => array(
	            'class'           => true,
	            'aria-hidden'     => true,
	            'aria-labelledby' => true,
	            'role'            => true,
	            'xmlns'           => true,
	            'width'           => true,
	            'height'          => true,
	            'viewbox'         => true, // <= Must be lower case!
	        ),
	        'g'     => array( 'fill' => true ),
	        'title' => array( 'title' => true ),
	        'path'  => array(
	            'd'    => true,
	            'fill' => true,
	        ),
	    );

	    $kses_with_extras = array_merge( $kses_defaults, $svg_args );
	    
	    // For embedded PDF viewer
	    $style_script_args = array(
	    	'style'		=> true,
	    	'script'	=> array(
	    		'src'	=> true,
	    	),
	    );
	    
	    return array_merge( $kses_with_extras, $style_script_args );
	}
}