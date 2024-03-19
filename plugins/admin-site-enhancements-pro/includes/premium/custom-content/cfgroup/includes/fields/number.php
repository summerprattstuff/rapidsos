<?php

class cfgroup_number extends cfgroup_field
{

    function __construct() {
        $this->name = 'number';
        $this->label = __( 'Number', 'admin-site-enhancements' );
    }

    /**
     * Generate the field HTML
     * @param object $field 
     * @since 1.0.5
     */
    function html( $field ) {
    ?>
        <input type="number" name="<?php echo $field->input_name; ?>" class="<?php echo $field->input_class; ?>" value="<?php echo $field->value; ?>" />
    <?php
    }

    function options_html( $key, $field ) {
    ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e( 'Default Value', 'admin-site-enhancements' ); ?></label>
            </td>
            <td>
                <?php
                    CFG()->create_field( [
                        'type'          => 'number',
                        'input_name'    => "cfgroup[fields][$key][options][default_value]",
                        'value'         => $this->get_option( $field, 'default_value' ),
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

    /**
     * Format the value for use with $cfgroup->get
     * @param mixed $value 
     * @param mixed $field The field object (optional)
     * @return mixed
     * @since 1.0.5
     */
    function format_value_for_api( $value, $field = null ) {
        return (int) $value;
    }

}
