<?php

namespace ASENHA\Classes;

/**
 * Class related to Custom Code features
 *
 * @since 3.6.0
 */
class Custom_Code {
	
	/**
	 * Record the post ID of the last edited PHP snippet. This will be used on the custom wp_die_handler callback
	 * 
	 * @since 5.8.0
	 */
	public function record_last_edited_php_snippets__premium_only( $post_id, $data ) {
		
		if ( 'asenha_code_snippet' == get_post_type( $post_id ) ) {
			$options_extra = get_option( ASENHA_SLUG_U . '_extra', array() );
			$options_extra['last_edited_csm_php_snippet'] = $post_id;
			update_option( ASENHA_SLUG_U. '_extra', $options_extra );
		}
		
	}

	/**
	 * Maybe show Safe Mode admin bar status icon
	 *
	 * @since 5.8.0
	 */
	public function maybe_show_safe_mode_admin_bar_icon__premium_only() {

		$is_safe_mode_enabled = defined( 'CSM_SAFE_MODE' ) ? CSM_SAFE_MODE : false;
		
		if ( $is_safe_mode_enabled ) {
			add_action( 'wp_before_admin_bar_render', [ $this, 'add_safe_mode_admin_bar_item__premium_only' ] );
			add_action( 'admin_head', [ $this, 'add_safe_mode_admin_bar_item_styles__premium_only' ] );
			add_action( 'wp_head', [ $this, 'add_safe_mode_admin_bar_item_styles__premium_only' ] );			
		}

	}

	/**
	 * Show Safe Mode WP Admin Bar item
	 *
	 * @since 5.8.0
	 */
	public function add_safe_mode_admin_bar_item__premium_only() {
		global $wp_admin_bar;

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id'	=> 'safe_mode',
					'parent'	=> 'top-secondary',
					'title'	=> '',
					'href'	=> '',
					'meta'	=> array(
						'html'	=> '<div id="disabling-csm-safe-mode" style="display:none;"><div class="disabling-csm-message"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/><path fill="currentColor" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path></svg><span>Disabling safe mode and resuming PHP snippets execution...</span></div></div>',
						'title'	=> 'Safe mode for ASE Code Snippets Manager is currently enabled for this site. Execution of all PHP snippets is currently stopped. Once you\'ve fixed the code in the snippet you last edited, click this icon to disable safe mode and re-enable PHP snippets execution.',
					),
				) );
			}
		}

	}

	/**
	 * Add JS and CSS for Safe Mode admin bar item
	 *
	 * @since 5.8.0
	 */
	public function add_safe_mode_admin_bar_item_styles__premium_only() {
		
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				?>
				<script>
				jQuery(document).ready( function() {
					// Reposition notice
				    jQuery('#disabling-csm-safe-mode').detach().insertAfter('#wpadminbar');
				    // Disable safe mode on clicking admin bar icon for safe mode status
				    jQuery("#wp-admin-bar-safe_mode > .ab-item, #disable-csm-safe-mode-link").click( function(e) {
				        e.preventDefault();
						jQuery('#disabling-csm-safe-mode').show();
						let searchParams = new URLSearchParams(window.location.search)
						if ( searchParams.has('post') ) {
							var code_id = searchParams.get('post');
						} else {
							var code_id = '';
						}
						// alert('This is post ' + code_id);
				        jQuery.ajax({
				            url: ajaxurl,
				            data: {
				            	'action':'csm_disable_safe_mode',
				            	'code_id':code_id
				            },
				            success: function(data){
								var data = data.slice(0,-1); // remove strange trailing zero in string returned by AJAX call
								var response = JSON.parse(data);

								if ( response.success == true ) {
									// alert('Safe mode has been disabled');
									location.reload();
								}
				            }
				        });
				    });
				});
				</script>
				<style>
					#wpadminbar .quicklinks #wp-admin-bar-safe_mode .ab-empty-item {
						padding: 0 6px;
					}
					#wp-admin-bar-safe_mode { 
						background-color: #ff9a00 !important;
						transition: .25s;
					}
					#wp-admin-bar-safe_mode > .ab-item { 
						color: #fff !important;  
					}
					#wp-admin-bar-safe_mode > .ab-item:before { 
						content: ""; 
						display: block;
						position: relative;
						top: 2px;
						z-index: 1;
						background-image: url("<?php echo ASENHA_URL . 'includes/premium/code-snippets-manager/assets/images/code.svg'; ?>") !important;
						background-repeat: no-repeat;
						background-position: center center;
						background-size: 20px 20px;
						width: 28px;
						height: 28px;
						padding: 0;
						margin-right: 0px; 
					}
					#wp-admin-bar-safe_mode > .ab-item:after { 
						content: ""; 
						display: block;
						position: relative;
						top: 2px;
						z-index: 2;
						background-image: url("<?php echo ASENHA_URL . 'includes/premium/code-snippets-manager/assets/images/stop-o.svg'; ?>") !important;
						background-repeat: no-repeat;
						background-position: center center;
						background-size: 28px 28px;
						width: 28px;
						height: 28px;
						padding: 0;
						margin-right: 0px;
						transition: .25s;
					}
					#wp-admin-bar-safe_mode:hover > .ab-item {
						background-color: #ff9a00 !important;
						cursor: pointer;
					}
					#wp-admin-bar-safe_mode:hover > .ab-item:after {
						opacity: 0.25;
					}
					#disabling-csm-safe-mode {
					    position:absolute;
					    z-index: 999;
					    top: 0;
					    right: 0;
					    bottom: 0;
					    left: 0;
					    background: rgba(255,255,255,0.9);
					    display: flex;
					    flex-direction: column;
					    align-items: center;
					}
					.disabling-csm-message {
					    display: flex;
					    align-items: center;
					    margin-top: 80px;
					    margin-left: 80px;
					}
					.disabling-csm-message svg {
					    margin-right: 8px;
					}
					.disabling-csm-message span {
					    font-size: 1.25em;
					    font-weight: 600;
					}
				</style>
				<?php

			}
		}

	}
	
	/** 
	 * Return the custom wp_die_handler callback
	 * 
	 * @since 5.8.0
	 */
	public function custom_wp_die_handler__premium_only() {
		return '_custom_wp_die_handler__premium_only';
	}

	/**
	 * Enqueue custom admin CSS
	 * Consider using https://github.com/Cerdic/CSSTidy in the future
	 *
	 * @since 2.3.0
	 */
	public function custom_admin_css() {

		$options = get_option( ASENHA_SLUG_U, array() );
		$custom_admin_css = $options['custom_admin_css'];

		?>
		<style type="text/css">
			<?php echo $custom_admin_css; ?>
		</style>
		<?php

	}

	/**
	 * Enqueue custom frontend CSS
	 * Consider using https://github.com/Cerdic/CSSTidy in the future
	 *
	 * @since 2.3.0
	 */
	public function custom_frontend_css() {

		$options = get_option( ASENHA_SLUG_U, array() );
		$custom_frontend_css = $options['custom_frontend_css'];

		?>
		<style type="text/css">
			<?php echo $custom_frontend_css; ?>
		</style>
		<?php

	}

	/**
	 * Add Custom Body Class meta box for enabled post types
	 * 
	 * @since 3.9.0
	 */
	public function add_custom_body_class_meta_box( $post_type, $post ) {

		$options = get_option( ASENHA_SLUG_U, array() );
		$enable_custom_body_class_for = $options['enable_custom_body_class_for'];

		foreach ( $enable_custom_body_class_for as $post_type_slug => $is_custom_body_class_enabled ) {
			if ( ( get_post_type() == $post_type_slug ) && $is_custom_body_class_enabled ) {

				// Skip adding meta box for post types where Gutenberg is enabled
				// if ( 
				// 	function_exists( 'use_block_editor_for_post_type' ) 
				// 	&& use_block_editor_for_post_type( $post_type_slug ) 
				// ) {
				// 	continue; // go to the beginning of next iteration
				// }

				add_meta_box(
					'asenha-custom-body-class', // ID of meta box
					'Custom &lt;body&gt; Class', // Title of meta box
					[ $this, 'output_custom_body_class_meta_box' ], // Callback function
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
	public function output_custom_body_class_meta_box( $post ) {
		?>
		<div class="custom-body-class-input">
			<input name="<?php echo esc_attr( 'custom_body_class' ); ?>" class="large-text" id="<?php echo esc_attr( 'custom_body_class' ); ?>" type="text" value="<?php echo esc_attr( get_post_meta( $post->ID, '_custom_body_class', true ) ); ?>" placeholder="e.g. light-theme new-year-promo" />
			<div class="custom-body-class-input-description">Use blank space to separate multiple classes, e.g. first-class second-class</div>
			<?php wp_nonce_field( 'custom_body_class_' . $post->ID, 'custom_body_class_nonce', false, true ); ?>
		</div>
		<?php
	}

	/**
	 * Save custom body class input
	 *
	 * @since 3.9.0
	 */
	public function save_custom_body_class( $post_id ) {

		// Only proceed if nonce is verified
		if ( isset( $_POST['custom_body_class_nonce'] ) && wp_verify_nonce( $_POST['custom_body_class_nonce'], 'custom_body_class_' . $post_id ) ) {

			// Get the value of external permalink from input field
			$custom_body_class = isset( $_POST['custom_body_class'] ) ? sanitize_text_field( trim( $_POST['custom_body_class'] ) ) : '';

			// Update or delete external permalink post meta
			if ( ! empty( $custom_body_class ) ) {
				update_post_meta( $post_id, '_custom_body_class', $custom_body_class );
			} else {
				delete_post_meta( $post_id, '_custom_body_class' );
			}

		}

	}

	/**
	 * Append custom body classes to the frontend <body> tag
	 *
	 * @since 4.4.0
	 */
	public function append_custom_body_class( $classes ) {

		// Only add custom body classes to the singular view of enabled post types
		if ( is_singular() ) {

			global $post;
			$custom_body_classes = get_post_meta( $post->ID, '_custom_body_class', true );

			if ( ! empty( $custom_body_classes ) ) {

				$custom_body_classes = explode( ' ', $custom_body_classes );

				foreach( $custom_body_classes as $custom_body_class ) {
					$classes[] = sanitize_html_class( $custom_body_class );
				}

			}

		}

		return $classes;

	}

	/** 
	 * Show content of ads.txt saved to options
	 *
	 * @since 3.2.0
	 */
	public function show_ads_appads_txt_content() {

		$options = get_option( ASENHA_SLUG_U, array() );

		$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;

		if ( '/ads.txt' === $request ) {

			$ads_txt_content = array_key_exists( 'ads_txt_content', $options ) ? $options['ads_txt_content'] : '';

			header( 'Content-Type: text/plain' );
			echo esc_textarea( $ads_txt_content );
			die();

		}

		if ( '/app-ads.txt' === $request ) {

			$app_ads_txt_content = array_key_exists( 'app_ads_txt_content', $options ) ? $options['app_ads_txt_content'] : '';

			header( 'Content-Type: text/plain' );
			echo esc_textarea( $app_ads_txt_content );
			die();

		}

	}

	/**
	 * Maybe show custom robots.txt content
	 *
	 * @since 3.5.0
	 */
	public function maybe_show_custom_robots_txt_content( $output, $public ) {

		$options = get_option( ASENHA_SLUG_U, array() );

		if ( array_key_exists( 'robots_txt_content', $options ) && ! empty( $options['robots_txt_content'] ) ) {

			$output = wp_strip_all_tags( $options['robots_txt_content'] );

		}

		return $output;

	}

	/**
	 * Insert code before </head> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_head_code() {

		$this->insert_code( 'head' );

	}

	/**
	 * Insert code after <body> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_body_code() {

		$this->insert_code( 'body' );
		
	}

	/**
	 * Insert code in footer section before </body> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_footer_code() {

		$this->insert_code( 'footer' );
		
	}

	/**
	 * Insert code
	 *
	 * @since 3.3.0
	 */
	public function insert_code( $location ) {

		// Do not insert code in admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		// Get option that stores the code
		$options = get_option( ASENHA_SLUG_U, array() );

		if ( 'head' == $location ) {

			$code = array_key_exists( 'head_code', $options ) ? $options['head_code'] : '';

		}

		if ( 'body' == $location ) {

			$code = array_key_exists( 'body_code', $options ) ? $options['body_code'] : '';

		}

		if ( 'footer' == $location ) {

			$code = array_key_exists( 'footer_code', $options ) ? $options['footer_code'] : '';

		}

		echo wp_unslash( $code ) . PHP_EOL;

	}

}