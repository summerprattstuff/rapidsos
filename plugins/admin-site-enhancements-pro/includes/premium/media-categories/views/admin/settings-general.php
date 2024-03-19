<?php
/**
 * Output the Settings tabs and panels.
 *
 * @since   1.0.0
 *
 * @package Media_Categories_Module
 * @author  WP Media Library
 */

?>

<div class="postbox wpzinc-vertical-tabbed-ui">
	<!-- Second level tabs -->
	<ul class="wpzinc-nav-tabs wpzinc-js-tabs" data-panels-container="#settings-container" data-panel=".panel" data-active="wpzinc-nav-tab-vertical-active">
		<?php
		// Iterate through this screen's tabs.
		foreach ( (array) $tabs as $index => $tab_item ) {
			$css_class = ( ( $tab_item['name'] === $tab['name'] ) ? 'wpzinc-nav-tab-vertical-active' : '' );
			?>
			<li class="wpzinc-nav-tab <?php echo esc_attr( isset( $tab_item['menu_icon'] ) ? $tab_item['menu_icon'] : 'default' ); ?>">
				<a href="#<?php echo esc_attr( $tab_item['name'] ); ?>" class="<?php echo esc_attr( $css_class ); ?>" <?php echo esc_attr( isset( $tab_item['documentation'] ) ? ' data-documentation="' . $tab_item['documentation'] . '"' : '' ); ?>>
					<?php
					echo esc_html( $tab_item['label'] );
					?>
				</a>
			</li>
			<?php
		}

		// Iterate through this screen's addon tabs.
		foreach ( (array) $addon_tabs as $addon_name => $tab_item ) {
			$css_class = ( ( $tab_item['name'] === $tab['name'] ) ? 'wpzinc-nav-tab-vertical-active' : '' );
			?>
			<li class="wpzinc-nav-tab <?php echo esc_attr( isset( $tab_item['menu_icon'] ) ? $tab_item['menu_icon'] : 'default' ); ?>">
				<a href="#<?php echo esc_attr( $tab_item['name'] ); ?>" class="<?php echo esc_attr( $css_class ); ?>" <?php echo esc_attr( isset( $tab_item['documentation'] ) ? ' data-documentation="' . $tab_item['documentation'] . '"' : '' ); ?>>
					<?php
					echo esc_html( $tab_item['label'] );

					if ( isset( $tab_item['is_pro'] ) && $tab_item['is_pro'] ) {
						?>
						<span class="tag"><?php esc_html_e( 'Pro', 'admin-site-enhancements' ); ?></span>
						<?php
					}
					?>
				</a>
			</li>
			<?php
		}
		?>
	</ul>

	<!-- Content -->
	<div id="settings-container" class="wpzinc-nav-tabs-content no-padding">
		<!-- General -->
		<div id="general" class="panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'Filter Settings', 'admin-site-enhancements' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Determines which filters should be displayed on list and grid Media Library views.', 'admin-site-enhancements' ); ?>
					</p>
				</header>

				<?php
				foreach ( $taxonomies as $taxonomy_name => $asenha_media_taxonomy ) {
					?>
					<div class="wpzinc-option">
						<div class="left">
							<label for="general_<?php echo esc_attr( $taxonomy_name ); ?>_enabled"><?php echo esc_html( $asenha_media_taxonomy['plural_name'] ); ?></label>
						</div>
						<div class="right">
							<select name="general[<?php echo esc_attr( $taxonomy_name ); ?>_enabled]" id="general_<?php echo esc_attr( $taxonomy_name ); ?>_enabled" size="1">
								<option value="1"<?php selected( $this->get_setting( 'general', $taxonomy_name . '_enabled' ), 1 ); ?>><?php esc_html_e( 'Enabled', 'admin-site-enhancements' ); ?></option>
								<option value="0"<?php selected( $this->get_setting( 'general', $taxonomy_name . '_enabled' ), 0 ); ?><?php selected( $this->get_setting( 'general', $taxonomy_name . '_enabled' ), '' ); ?>><?php esc_html_e( 'Disabled', 'admin-site-enhancements' ); ?></option>
							</select>

							<p class="description">
								<?php
								echo esc_html(
									sprintf(
									/* translators: Taxonomy Label, Singular */
										__( 'If enabled, displays a dropdown option to filter Media Library items by %s', 'admin-site-enhancements' ),
										$asenha_media_taxonomy['singular_name']
									)
								);
								?>
							</p>
						</div>
					</div>
					<?php
				}
				?>

				<div class="wpzinc-option">
					<div class="left">
						<label for="general_orderby_enabled"><?php esc_html_e( 'Sorting', 'admin-site-enhancements' ); ?></label>
					</div>
					<div class="right">
						<select name="general[orderby_enabled]" id="general_orderby_enabled" size="1">
							<option value="1"<?php selected( $this->get_setting( 'general', 'orderby_enabled' ), 1 ); ?>><?php esc_html_e( 'Enabled', 'admin-site-enhancements' ); ?></option>
							<option value="0"<?php selected( $this->get_setting( 'general', 'orderby_enabled' ), 0 ); ?>><?php esc_html_e( 'Disabled', 'admin-site-enhancements' ); ?></option>
						</select>

						<p class="description">
							<?php esc_html_e( 'If enabled, displays a dropdown option to select how to order Media Library items', 'admin-site-enhancements' ); ?>
						</p>
					</div>
				</div>

				<div class="wpzinc-option">
					<div class="left">
						<label for="general_order_enabled"><?php esc_html_e( 'Sort Order', 'admin-site-enhancements' ); ?></label>
					</div>
					<div class="right">
						<select name="general[order_enabled]" id="general_order_enabled" size="1">
							<option value="1"<?php selected( $this->get_setting( 'general', 'order_enabled' ), 1 ); ?>><?php esc_html_e( 'Enabled', 'admin-site-enhancements' ); ?></option>
							<option value="0"<?php selected( $this->get_setting( 'general', 'order_enabled' ), 0 ); ?>><?php esc_html_e( 'Disabled', 'admin-site-enhancements' ); ?></option>
						</select>

						<p class="description">
							<?php esc_html_e( 'If enabled, displays a dropdown option to select whether to order Media Library items ascending or descending', 'admin-site-enhancements' ); ?>
						</p>
					</div>
				</div>

				<?php do_action( 'media_categories_module_admin_output_settings_panel_general' ); ?>
			</div>
		</div>

		<!-- User Options -->
		<div id="user-options" class="panel">
			<div class="postbox">
				<header>
					<h3><?php esc_html_e( 'User Settings', 'admin-site-enhancements' ); ?></h3>
					<p class="description">
						<?php esc_html_e( 'Determines which filter settings should persist across different screens for the logged in WordPress User.', 'admin-site-enhancements' ); ?>
					</p>
				</header>

				<div class="wpzinc-option">
					<div class="left">
						<label for="user_options_orderby_enabled"><?php esc_html_e( 'Sorting', 'admin-site-enhancements' ); ?></label>
					</div>
					<div class="right">
						<select name="user-options[orderby_enabled]" id="user_options_orderby_enabled" size="1">
							<option value="1"<?php selected( $this->get_setting( 'user-options', 'orderby_enabled' ), 1 ); ?>><?php esc_html_e( 'Remember', 'admin-site-enhancements' ); ?></option>
							<option value="0"<?php selected( $this->get_setting( 'user-options', 'orderby_enabled' ), 0 ); ?>><?php esc_html_e( 'Don\'t Remember', 'admin-site-enhancements' ); ?></option>
						</select>

						<p class="description">
							<?php esc_html_e( 'When set to Remembered, the User\'s last chosen Order By filter option will be remembered across all Media Views.  When set to Don\'t Remember, the filters will reset when switching between WordPress Administration screens.', 'admin-site-enhancements' ); ?>
						</p>
					</div>
				</div>

				<div class="wpzinc-option">
					<div class="left">
						<label for="user_options_order_enabled"><?php esc_html_e( 'Sort Order', 'admin-site-enhancements' ); ?></label>
					</div>
					<div class="right">
						<select name="user-options[order_enabled]" id="user_options_order_enabled" size="1">
							<option value="1"<?php selected( $this->get_setting( 'user-options', 'order_enabled' ), 1 ); ?>><?php esc_html_e( 'Remember', 'admin-site-enhancements' ); ?></option>
							<option value="0"<?php selected( $this->get_setting( 'user-options', 'order_enabled' ), 0 ); ?>><?php esc_html_e( 'Don\'t Remember', 'admin-site-enhancements' ); ?></option>
						</select>

						<p class="description">
							<?php esc_html_e( 'When set to Remembered, the User\'s last chosen Order filter option will be remembered across all Media Views.  When set to Don\'t Remember, the filters will reset when switching between WordPress Administration screens.', 'admin-site-enhancements' ); ?>
						</p>
					</div>
				</div>

				<?php do_action( 'media_categories_module_admin_output_settings_panel_user_options' ); ?>
			</div>
		</div>

		<?php
		do_action( 'media_categories_module_admin_output_settings_panels' );
		?>
	</div>
</div>

<!-- Save -->
<div>
	<input type="submit" name="submit" value="<?php esc_html_e( 'Save', 'admin-site-enhancements' ); ?>" class="button button-primary" />
</div>
