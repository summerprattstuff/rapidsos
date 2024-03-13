<?php
/**
 * Output Import from Wicked Folder options.
 *
 * @since   1.0.0
 *
 * @package Media_Categories_Module
 * @author  WP Media Library
 */

?>

<!-- Import from Wicked Folders -->
<div id="import_wicked_folders" class="panel">
	<div class="postbox">
		<header>
			<h3><?php esc_html_e( 'Import from Wicked Folders', 'admin-site-enhancements' ); ?></h3>
		</header>

		<div class="wpzinc-option">	
			<p class="description">
				<?php
				esc_html_e( 'Wicked Folder\'s folders (categories) will be imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
				<br />
				<?php
				esc_html_e( 'Attachments assigned to Wicked Folder\'s folders will be reassigned to the equivalent Categories imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
			</p>
		</div>

		<div class="wpzinc-option">
			<input name="import_wicked_folders" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import', 'admin-site-enhancements' ); ?>" />              
		</div>
	</div>
</div>
