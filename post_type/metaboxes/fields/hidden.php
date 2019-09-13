<?php
/**
 * @var $field_name
 * @var $section_name
 *
 */

$field = "data['{$section_name}']['fields']['{$field_name}']";

?>

<input type="hidden"
       name="<?php echo esc_attr($field_name);?>"
       v-bind:id="'<?php echo esc_attr($section_name . '-' . $field_name);?>'"
       v-model="<?php echo esc_attr($field); ?>['value']" />