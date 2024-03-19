<?php
/**
 * Output Import from Filebird options.
 *
 * @since   1.0.0
 *
 * @package Media_Categories_Module
 * @author  WP Media Library
 */

?>

<!-- Import from FileBird -->
<div id="import_filebird" class="panel">
	<div class="postbox">
		<header>
			<h3><?php esc_html_e( 'Import from FileBird', 'admin-site-enhancements' ); ?></h3>
		</header>

		<div class="wpzinc-option">	
			<p class="description">
				<?php
				esc_html_e( 'FileBird\'s folders (categories) will be imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
				<br />
				<?php
				esc_html_e( 'Attachments assigned to FileBird folders will be reassigned to the equivalent Categories imported into Media Categories Module.', 'admin-site-enhancements' );
				?>
			</p>
		</div>

		<div class="wpzinc-option">
			<input name="import_filebird" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import', 'admin-site-enhancements' ); ?>" />              
		</div>
	</div>
</div>
