<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php get_header(); ?>

	<div class="stm-lms-wrapper">
		<div class="container">
			<?php STM_LMS_Templates::show_lms_template('courses_taxonomy/archive'); ?>
		</div>
	</div>

<?php get_footer(); ?>