<?php

class cfgroup_file extends cfgroup_field
{

    function __construct() {
        $this->name = 'file';
        $this->label = __( 'File / Media', 'admin-site-enhancements' );
    }


    function html( $field ) {
        $file_url = $field->value;

        if ( ctype_digit( $field->value ) ) {
            if ( wp_attachment_is_image( $field->value ) ) {
                if ( isset( $field->options['image_preview_size'] ) && ! empty( $field->options['image_preview_size'] ) ) {
                    $image_size = $field->options['image_preview_size'];
                } else {
                    $image_size = 'medium';
                }

                $file_url = wp_get_attachment_image_src( $field->value, $image_size );
                $file_url = '<img src="' . $file_url[0] . '" />';
            }
            else
            {
                $file_url = wp_get_attachment_url( $field->value );
                $filename = substr( $file_url, strrpos( $file_url, '/' ) + 1 );
                $file_url = '<a href="'. $file_url .'" target="_blank">'. $filename .'</a>';
            }
        }

        // CSS logic for "Add" / "Remove" buttons
        $css = empty( $field->value ) ? [ '', ' hidden' ] : [ ' hidden', '' ];
    ?>
        <span class="file_url <?php echo $field->options['file_type']; ?>"><?php echo $file_url; ?></span>
        <input type="button" class="media button add<?php echo $css[0]; ?>" value="<?php _e( 'Add File', 'admin-site-enhancements' ); ?>" />
        <input type="button" class="media button remove<?php echo $css[1]; ?>" value="<?php _e( 'Remove', 'admin-site-enhancements' ); ?>" />
        <input type="hidden" name="<?php echo $field->input_name; ?>" class="file_value" value="<?php echo $field->value; ?>" />
    <?php
    }


    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'File / Media Type', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'select',
                        'input_name' => "cfgroup[fields][$key][options][file_type]",
                        'options' => [
                            'choices' => [
                                'file'  => __( 'Any', 'admin-site-enhancements' ),
                                'image' => __( 'Image', 'admin-site-enhancements' ),
                                'audio' => __( 'Audio', 'admin-site-enhancements' ),
                                'video' => __( 'Video', 'admin-site-enhancements' ),
                                'pdf'   => __( 'PDF', 'admin-site-enhancements' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'file_type', 'file' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Image Preview Size', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'select',
                        'input_name' => "cfgroup[fields][$key][options][image_preview_size]",
                        'options' => [
                            'choices' => [
                                'thumbnail' => __( 'Thumbnail (cropped)', 'admin-site-enhancements' ),
                                'medium'    => __( 'Medium (uncropped)', 'admin-site-enhancements' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'image_preview_size', '' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Return Value', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'select',
                        'input_name' => "cfgroup[fields][$key][options][return_value]",
                        'options' => [
                            'choices' => [
                                'url' => __( 'File URL', 'admin-site-enhancements' ),
                                'id' => __( 'Attachment ID', 'admin-site-enhancements' )
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'return_value', 'url' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label validation-label">
                <label><?php _e( 'Validation', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'true_false',
                        'input_name' => "cfgroup[fields][$key][options][required]",
                        'input_class' => 'true_false',
                        'value' => $this->get_option( $field, 'required' ),
                        'options' => [ 'message' => __( 'This is a required field', 'admin-site-enhancements' ) ],
                    ] );
                ?>
            </td>
        </tr>
    <?php
    }


    function input_head( $field = null ) {
        if ( isset( $field->options['image_preview_size'] ) && ! empty( $field->options['image_preview_size'] ) ) {
            $image_size = $field->options['image_preview_size'];
        } else {
            $image_size = 'medium';
        }

        wp_enqueue_media();
    ?>
        <style>
        .cfgroup_frame .media-frame-menu {
            display: none;
        }
        
        .cfgroup_frame .media-frame-title,
        .cfgroup_frame .media-frame-router,
        .cfgroup_frame .media-frame-content,
        .cfgroup_frame .media-frame-toolbar {
            left: 0;
        }
        </style>

        <script>
        (function($) {
            $(function() {

                var cfgroup_frame;

                $(document).on('click', '.cfgroup_input .media.button.add', function(e) {
                    $this = $(this);

                    if (cfgroup_frame) {
                        cfgroup_frame.open();
                        return;
                    }

                    cfgroup_frame = wp.media.frames.cfgroup_frame = wp.media({
                        className: 'media-frame cfgroup_frame',
                        frame: 'post',
                        multiple: false,
                        library: {
                            type: 'image'
                        }
                    });

                    cfgroup_frame.on('insert', function() {
                        var attachment = cfgroup_frame.state().get('selection').first().toJSON();
                        var imageSize = '<?php echo esc_html( $image_size ); ?>';
                        if ('image' == attachment.type && 'undefined' != typeof attachment.sizes) {
                            file_url = attachment.sizes.full.url;
                            if ('undefined' != typeof attachment.sizes.thumbnail && 'thumbnail' == imageSize ) {
                                file_url = attachment.sizes.thumbnail.url;
                            }
                            if ('undefined' != typeof attachment.sizes.medium && 'medium' == imageSize ) {
                                file_url = attachment.sizes.medium.url;
                            }
                            file_url = '<img src="' + file_url + '" />';
                        }
                        else {
                            file_url = '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>';
                        }
                        $this.hide();
                        $this.siblings('.media.button.remove').show();
                        $this.siblings('.file_value').val(attachment.id);
                        $this.siblings('.file_url').html(file_url);
                    });

                    cfgroup_frame.open();
                    cfgroup_frame.content.mode('upload');
                });

                $(document).on('click', '.cfgroup_input .media.button.remove', function() {
                    $(this).siblings('.file_url').html('');
                    $(this).siblings('.file_value').val('');
                    $(this).siblings('.media.button.add').show();
                    $(this).hide();
                });
            });
        })(jQuery);
        </script>
    <?php
    }


    function format_value_for_api( $value, $field = null ) {
        if ( ctype_digit( $value ) ) {
            $return_value = $this->get_option( $field, 'return_value', 'url' );
            return ( 'id' == $return_value ) ? (int) $value : wp_get_attachment_url( $value );
        }
        return $value;
    }
}
