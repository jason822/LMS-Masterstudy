<?php
/**
 * @var $current_user
 */

wp_enqueue_script('vue-resource.js');
stm_lms_register_script('user-tabs');

$tabs = array(
	'my-courses' => esc_html__('My Courses', 'masterstudy-lms-learning-management-system'),
	'my-orders'  => esc_html__('My Orders', 'masterstudy-lms-learning-management-system'),
);


$active = 'my-courses';
?>

<ul class="nav nav-tabs" role="tablist">
	<?php foreach ($tabs as $slug => $name): ?>
        <li role="presentation" class="<?php echo ($slug == $active) ? 'active' : '' ?>">
            <a href="#<?php echo esc_attr($slug); ?>" data-toggle="tab">
				<?php echo wp_kses_post($name); ?>
            </a>
        </li>
	<?php endforeach; ?>
</ul>


<div class="tab-content">
	<?php foreach ($tabs as $slug => $name): ?>
        <div role="tabpanel"
             class="tab-pane <?php echo ($slug == $active) ? 'active' : '' ?>"
             id="<?php echo esc_attr($slug); ?>">
			<?php STM_LMS_Templates::show_lms_template('account/private/parts/tabs/' . $slug); ?>
        </div>
	<?php endforeach; ?>
</div>