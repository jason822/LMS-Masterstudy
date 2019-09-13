<?php
stm_lms_register_style('teacher');
$author = STM_LMS_User::get_current_user(get_the_author_meta('ID'));

$gallery = get_post_meta(get_the_ID(), 'course_gallery', true);

echo do_shortcode($gallery);

?>

