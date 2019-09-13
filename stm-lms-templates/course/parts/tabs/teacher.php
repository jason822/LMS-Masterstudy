<?php
stm_lms_register_style('teacher');
$author = STM_LMS_User::get_current_user(get_the_author_meta('ID'));

$expert = get_post_meta(get_the_ID(), 'course_expert', true);

$expert_post = get_post($expert);

$origin_socials = array(
	'facebook',
	'linkedin',
	'twitter',
	'google-plus',
	'youtube-play',
);


$content = $expert_post->post_content;

?>

<div class="pull-left">
	<?php 
	echo do_shortcode( $content );
	?>
</div>
<br>
<div class="pull-right" style="display: block;">
	<?php 
	foreach ($origin_socials as $social): ?>
		<?php $current_social = get_post_custom_values($social, $expert_post->ID); ?>
		<?php if (!empty($current_social[0])): ?>
			<a class="expert-social-<?php echo esc_attr($social); ?>"
				href="<?php echo esc_url($current_social[0]); ?>"
				title="<?php echo __('View expert on', 'masterstudy') . ' ' . $social ?>">
				<i class="fab fa-<?php echo esc_attr(str_replace('youtube-play', 'youtube', $social)); ?>"></i>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>