<?php
/**
 * Output Import from WP Media Folder options.
 *
 * @since   1.0.0
 *
 * @package Media_Categories_Module
 * @author  WP Media Library
 */

?>

<!-- Import from WP Media Folder -->
<div id="import_wp_media_folder" class="panel">
	<div class="postbox">
		<header>
			<h3><?php esc_html_e( 'Import from WP Media Folder', 'admin-site-enhancements' ); ?></h3>
		</header>

		<div class="wpzinc-option">	
			<p class="description">
				<?php
				esc_html_e( 'WP Media Folder\'s folders (categories) will be imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
				<br />
				<?php
				esc_html_e( 'Attachments assigned to WP Media Folder\'s folders will be reassigned to the equivalent Categories imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
			</p>
		</div>

		<div class="wpzinc-option">
			<input name="import_wp_media_folder" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import', 'admin-site-enhancements' ); ?>" />              
		</div>
	</div>
</div>
