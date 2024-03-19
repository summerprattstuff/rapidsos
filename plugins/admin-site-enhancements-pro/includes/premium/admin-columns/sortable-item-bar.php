<div class="item-bar">
	<div class="item-left-side">
		<span class="dashicons dashicons-menu"></span> <span class="column-title"><?php echo esc_html( wp_strip_all_tags( $custom_title ) ); ?></span><?php if ( ! empty( $handler ) ): ?><span class="custom-field-handler" title="<?php echo esc_attr( $handler_title );?>"><?php echo esc_html( $handler ) . '<span>: ' . esc_html( $type ) . '</span>'; ?></span><?php endif; ?><?php if ( $is_taxonomy ) : ?><span class="taxonomy-tag">Taxonomy</span><?php endif; ?><span class="column-key<?php echo esc_attr( $column_key_class ); ?>"><?php if ( $is_custom_field == 'yes' ): ?><span class="custom-field-indicator"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="#bbb" d="M22 4H2v16h10v-2H4V6h16v4h2V4zm-3 13h3v-2h-3v-3h-2v3h-3v2h3v3h2v-3z"/></svg></span><?php endif; ?><?php if ( $is_extra_column == 'yes' ): ?><span class="extra-column-indicator"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"><path fill="#bbb" d="M13.11 4.36L9.87 7.6L8 5.73l3.24-3.24c.35-.34 1.05-.2 1.56.32c.52.51.66 1.21.31 1.55zm-8 1.77l.91-1.12l9.01 9.01l-1.19.84c-.71.71-2.63 1.16-3.82 1.16H6.14L4.9 17.26c-.59.59-1.54.59-2.12 0a1.49 1.49 0 0 1 0-2.12l1.24-1.24v-3.88c0-1.13.4-3.19 1.09-3.89zm7.26 3.97l3.24-3.24c.34-.35 1.04-.21 1.55.31c.52.51.66 1.21.31 1.55l-3.24 3.25z"/></svg></span><?php endif; ?><?php echo esc_html( $column_key ); ?></span>
	</div>
	<div class="item-right-side">
		<div class="column-action-links">
			<a href="#" class="button button-small button-secondary settings-button">Edit</a><a href="#" class="button button-small button-secondary delete-button">Discard</a>
		</div>
		<div class="data-type">
		</div>
		<div class="column-width">
			<span class="width-number"><?php echo esc_html( $width_number ); ?></span><span class="width-type"><?php echo esc_html( $width_type_label ); ?></span>
		</div>
		<div class="sortable-icon"><?php if ( 'yes' == $is_sortable ): // https://icon-sets.iconify.design/fluent/text-sort-ascending-16-regular/ ?><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="#3c434a" d="M5.462 1.308a.5.5 0 0 0-.923 0l-2.5 6a.5.5 0 0 0 .923.384L3.667 6h2.666l.705 1.692a.5.5 0 1 0 .924-.384zM4.083 5L5 2.8L5.917 5zM2.5 9.5A.5.5 0 0 1 3 9h3.5a.5.5 0 0 1 .41.787L3.96 14H6.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.41-.787L5.54 10H3a.5.5 0 0 1-.5-.5m10-8.5a.5.5 0 0 1 .5.5v11.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L12 13.293V1.5a.5.5 0 0 1 .5-.5"/></svg><?php else: ?><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="#ccc" d="M5.462 1.308a.5.5 0 0 0-.923 0l-2.5 6a.5.5 0 0 0 .923.384L3.667 6h2.666l.705 1.692a.5.5 0 1 0 .924-.384zM4.083 5L5 2.8L5.917 5zM2.5 9.5A.5.5 0 0 1 3 9h3.5a.5.5 0 0 1 .41.787L3.96 14H6.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.41-.787L5.54 10H3a.5.5 0 0 1-.5-.5m10-8.5a.5.5 0 0 1 .5.5v11.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L12 13.293V1.5a.5.5 0 0 1 .5-.5"/></svg><?php endif; ?></div>
	</div>
</div>
<div class="item-settings" style="display:none;">
	<div class="item-setting">
		<label for="title_custom_<?php echo esc_attr( $column_key ); ?>">Label</label>
		<input id="title_custom_<?php echo esc_attr( $column_key ); ?>" type="text" value="<?php echo esc_attr( wp_strip_all_tags( $custom_title ) ); ?>" name="title_custom_<?php echo esc_attr( $column_key ); ?>" class="column-label title-custom" />
		<?php if ( $may_use_original_title ): ?>
		<div class="title-original">
			<input type="checkbox" id="title_original_<?php echo esc_attr( $column_key ); ?>" name="title_original_<?php echo esc_attr( $column_key ); ?>" <?php checked( $use_original_title, 'yes' ); ?> class="settings-checkbox title-original-checkbox" />
			<label for="title_original_<?php echo esc_attr( $column_key ); ?>">Use original label</label>
		</div>
		<?php endif; ?>
	</div>
	<div class="item-setting">
		<label for="column_width_<?php echo esc_attr( $column_key ); ?>">Width</label>
		<input id="column_width_<?php echo esc_attr( $column_key ); ?>" type="number" value="<?php echo esc_attr( $width_number ); ?>" name="column_width_<?php echo esc_attr( $column_key ); ?>" class="column-width-input">
		<div class="radio-group width-type">
			<input type="radio" name="width_type_<?php echo esc_attr( $column_key ); ?>" id="px_<?php echo esc_attr( $column_key ); ?>" value="px" class="width-type-radios" <?php checked( $width_type, 'px' ); ?> />
			<label for="px_<?php echo esc_attr( $column_key ); ?>">px</label>
			<input type="radio" name="width_type_<?php echo esc_attr( $column_key ); ?>" id="percent_<?php echo esc_attr( $column_key ); ?>" value="%" class="width-type-radios" <?php checked( $width_type, '%' ); ?> />
			<label for="percent_<?php echo esc_attr( $column_key ); ?>">%</label>
			<input type="radio" name="width_type_<?php echo esc_attr( $column_key ); ?>" id="auto_<?php echo esc_attr( $column_key ); ?>" value="auto" class="width-type-radios" <?php checked( $width_type, 'auto' ); ?> />
			<label for="auto_<?php echo esc_attr( $column_key ); ?>">Auto</label>
		</div>
	</div>
	<div class="settings-footer">
		<a class="button button-small button-secondary close-settings-button">Close</a>
	</div>
</div>