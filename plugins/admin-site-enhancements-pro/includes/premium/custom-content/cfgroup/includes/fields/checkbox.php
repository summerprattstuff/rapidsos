<?php

class cfgroup_checkbox extends cfgroup_field
{

    function __construct() {
        $this->name = 'checkbox';
        $this->label = __( 'Checkbox', 'admin-site-enhancements' );
    }

    function html( $field ) {
        $field->input_class = empty( $field->input_class ) ? '' : $field->input_class;

        // Layout: vertical or horizontal
        $field->input_class .= ' ' . $field->options['layout'];

        // Checkboxes should return arrays (unless "force_single" is true)
        if ( '[]' != substr( $field->input_name, -2 ) && empty( $field->options['force_single'] ) ) {
            $field->input_name .= '[]';
        }
    	?>
    	<div class="<?php echo trim( $field->input_class ); ?>">
    		<?php foreach ( $field->options['choices'] as $val => $label ) : ?>
            <?php $val = ( '{empty}' == $val ) ? '' : $val; ?>
            <?php $checked = in_array( $val, (array) $field->value ) ? ' checked' : ''; ?>
    		<label for="<?php echo esc_attr( $field->input_name . '-' . $val ); ?>">
    			<input type="checkbox" id="<?php echo esc_attr( $field->input_name . '-' . $val ); ?>" name="<?php echo esc_attr( $field->input_name ); ?>" value="<?php echo esc_attr( $val ); ?>" class="checkbox"<?php echo $checked; ?>/>
    			<?php echo esc_attr( $label ); ?>
    		</label>
    		<?php endforeach; ?>
    	</div>
		<?php
    }

    function options_html( $key, $field ) {

        // Convert choices to textarea-friendly format
        if ( isset( $field->options['choices'] ) && is_array( $field->options['choices'] ) ) {
            foreach ( $field->options['choices'] as $choice_key => $choice_val ) {
                $field->options['choices'][ $choice_key ] = "$choice_key : $choice_val";
            }

            $field->options['choices'] = implode( "\n", $field->options['choices'] );
        }
        else {
            $field->options['choices'] = '';
        }
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Choices', 'admin-site-enhancements' ); ?></label>
                <p class="description"><?php /* translators: Do not translate {empty}. Keep as is. */ _e( 'One <code>value : label</code> per line, or just <code>label</code>. Use <code>{empty}</code> for an empty value.', 'admin-site-enhancements' ); ?></p>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'textarea',
                        'input_name' => "cfgroup[fields][$key][options][choices]",
                        'value' => $this->get_option( $field, 'choices' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Default Value(s)', 'admin-site-enhancements' ); ?></label>
                <p class="description"><?php _e( 'For multiple values, separate with <code> |</code>, e.g. <code>value1 | value2</code>', 'admin-site-enhancements' ); ?></p>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type'          => 'text',
                        'input_name'    => "cfgroup[fields][$key][options][default_value]",
                        'value'         => $this->get_option( $field, 'default_value' ),
                    ] );
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Layout', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type' => 'select',
                        'input_name' => "cfgroup[fields][$key][options][layout]",
                        'options' => [
                            'choices' => [
                                'vertical'		=> __( 'Vertical', 'admin-site-enhancements' ),
                                'horizontal'	=> __( 'Horizontal', 'admin-site-enhancements' ),
                            ],
                            'force_single' => true,
                        ],
                        'value' => $this->get_option( $field, 'layout', 'vertical' ),
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

    function format_value_for_api( $value, $field = null ) {
        $value_array = [];
        $choices = $field->options['choices'];

        // Return an associative array (value, label)
        if ( is_array( $value ) ) {
            foreach ( $value as $val ) {
                $value_array[ $val ] = isset( $choices[ $val ] ) ? $choices[ $val ] : $val;
            }
        }

        return $value_array;
    }

    function prepare_value( $value, $field = null ) {
        return $value;
    }

    function pre_save_field( $field ) {
        $new_choices = [];
        $choices = trim( $field['options']['choices'] );

        if ( ! empty( $choices ) ) {
            $choices = str_replace( "\r\n", "\n", $choices );
            $choices = str_replace( "\r", "\n", $choices );
            $choices = ( false !== strpos( $choices, "\n" ) ) ? explode( "\n", $choices ) : (array) $choices;

            foreach ( $choices as $choice ) {
                $choice = trim( $choice );
                if ( false !== ( $pos = strpos( $choice, ' : ' ) ) ) {
                    $array_key = substr( $choice, 0, $pos );
                    $array_value = substr( $choice, $pos + 3 );
                    $new_choices[ $array_key ] = $array_value;
                }
                else {
                    $new_choices[ $choice ] = $choice;
                }
            }
        }

        $field['options']['choices'] = $new_choices;

        return $field;
    }
}