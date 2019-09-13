<?php
stm_lms_register_style('teacher');
$author = STM_LMS_User::get_current_user(get_the_author_meta('ID'));


$school_name = '';
$school_name = get_post_meta(get_the_ID(), 'school_name', true);

// echo do_shortcode($school_name);
?>

<div class="pull-left">
		<div class="meta-unit teacher clearfix">
			<div class="pull-left">
				<!-- <?php echo wp_kses_post($author['avatar']); ?> -->
			</div>
			<div class="meta_values">
				<div class="label h6"><?php esc_html_e('School Name', 'masterstudy-lms-learning-management-system'); ?></div>
				<div class="value heading_font h6">
					<?php echo do_shortcode($school_name); ?>
				</div>
			</div>
		</div>
</div>