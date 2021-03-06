<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>


<?php
	$tabs = array(
		'description' => esc_html__('Description', 'masterstudy-lms-learning-management-system'),
		'curriculum' => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system'),
		'gallery' => esc_html__('Gallery', 'masterstudy-lms-learning-management-system'),
		'faq' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
		'announcement' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
		'teacher' => esc_html__('Teacher', 'masterstudy-lms-learning-management-system'),
		'reviews' => esc_html__('Reviews', 'masterstudy-lms-learning-management-system'),
	);

	$active = 'description';
	//$active = 'reviews';
?>


<ul class="nav nav-tabs" role="tablist">

	<?php foreach($tabs as $slug => $name): ?>
		<li role="presentation" class="<?php echo ($slug == $active) ? 'active' : '' ?>">
			<a href="#<?php echo esc_attr($slug); ?>"
			   data-toggle="tab">
				<?php echo wp_kses_post($name); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>


<div class="tab-content">
	<?php foreach($tabs as $slug => $name): ?>
		<div role="tabpanel"
			 class="tab-pane <?php echo ($slug == $active) ? 'active' : '' ?>"
			 id="<?php echo esc_attr($slug); ?>">
			<?php STM_LMS_Templates::show_lms_template('course/parts/tabs/' . $slug); ?>
		</div>
	<?php endforeach; ?>
</div>