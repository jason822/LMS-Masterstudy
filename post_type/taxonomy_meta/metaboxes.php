<?php

require_once STM_LMS_PATH . '/post_type/taxonomy_meta/enqueue.php';
require_once STM_LMS_PATH . '/post_type/taxonomy_meta/fields/default.php';
require_once STM_LMS_PATH . '/post_type/taxonomy_meta/fields/image.php';
require_once STM_LMS_PATH . '/post_type/taxonomy_meta/fields/icon.php';
require_once STM_LMS_PATH . '/post_type/taxonomy_meta/fields/color.php';

function stm_lms_term_meta_fields()
{
	return apply_filters('stm_lms_term_meta_fields', array(
		'stm_lms_course_taxonomy' => array(
			'course_image' => array(
				'label' => esc_html__('Category Image', 'masterstudy-lms-learning-management-system'),
				'type'  => 'image',
			),
			'course_icon' => array(
				'label' => esc_html__('Category Icon', 'masterstudy-lms-learning-management-system'),
				'type'  => 'icon',
			),
			'course_color' => array(
				'label' => esc_html__('Category Color', 'masterstudy-lms-learning-management-system'),
				'type'  => 'color',
			),
		)
	));
}

add_action('init', 'stm_lms_register_term_meta');
function stm_lms_register_term_meta()
{
	$term_meta = stm_lms_term_meta_fields();
	foreach ($term_meta as $taxonomy => $meta_fields) {
		foreach ($meta_fields as $meta_field_key => $meta_field) {
			//register_meta('term', $meta_field_key, 'stm_lms_sanitize_term_meta');
		}
	}
}

function stm_lms_sanitize_term_meta($value)
{
	return sanitize_text_field($value);
}

function stm_lms_get_term_meta_text($term_id, $term_key)
{
	$value = get_term_meta($term_id, $term_key, true);
	$value = stm_lms_sanitize_term_meta($value);
	return $value;
}

$taxonomies = stm_lms_term_meta_fields();
foreach ($taxonomies as $taxonomy => $fields) {
	add_action("{$taxonomy}_add_form_fields", 'stm_lms_add_term_meta_fields');
	add_action("{$taxonomy}_edit_form_fields", 'stm_lms_edit_term_meta_fields');

	add_action("edit_{$taxonomy}", 'stm_lms_save_term_meta_field');
	add_action("create_{$taxonomy}", 'stm_lms_save_term_meta_field');
}

function stm_lms_add_term_meta_fields($tax)
{
	$meta = stm_lms_term_meta_fields();
	$fields = $meta[$tax]; ?>
    <table class="form-table">
        <tbody>
		<?php foreach ($fields as $field_key => $field): ?>

            <tr class="form-field">
                <th scope="row">
                    <label for="<?php echo esc_attr($field_key) ?>"><?php echo sanitize_text_field($field['label']); ?></label>
                </th>
                <td>
					<?php switch ($field['type']) {
						case 'image':
							stm_lms_term_meta_field_image($field_key, '');
							break;
						case 'icon':
							stm_lms_term_meta_field_icon($field_key, '');
							break;
						case 'color':
							stm_lms_term_meta_field_color($field_key, '');
							break;
						default:
							stm_lms_term_meta_field_default($field_key, '');
					} ?>
                </td>
            </tr>


		<?php endforeach; ?>
        </tbody>
    </table>
<?php }

function stm_lms_edit_term_meta_fields($term)
{
	$taxonomy = $term->taxonomy;
	$meta = stm_lms_term_meta_fields();
	$fields = $meta[$taxonomy]; ?>
    <table class="form-table">
        <tbody>
		<?php foreach ($fields as $field_key => $field):
			$value = stm_lms_get_term_meta_text($term->term_id, $field_key);
			?>

            <tr class="form-field">
                <th scope="row">
                    <label for="<?php echo esc_attr($field_key) ?>"><?php echo sanitize_text_field($field['label']); ?></label>
                </th>
                <td>
					<?php switch ($field['type']) {
						case 'image':
							stm_lms_term_meta_field_image($field_key, $value);
							break;
						case 'icon':
							stm_lms_term_meta_field_icon($field_key, $value);
							break;
						case 'color':
							stm_lms_term_meta_field_color($field_key, $value);
							break;
						default:
							stm_lms_term_meta_field_default($field_key, $value);
					} ?>
                </td>
            </tr>


		<?php endforeach; ?>
        </tbody>
    </table>
<?php }

function stm_lms_save_term_meta_field($term_id)
{
	if (!empty($_POST['taxonomy'])) {
		$taxonomy = sanitize_text_field($_POST['taxonomy']);
		$meta = stm_lms_term_meta_fields();
		if (!empty($meta[$taxonomy])) {
			$fields = $meta[$taxonomy];
			foreach ($fields as $field_key => $field) {
				$field_value = (!empty($_POST[$field_key])) ? sanitize_text_field($_POST[$field_key]) : '';
				update_term_meta($term_id, $field_key, $field_value);
			}
		}
	}
}