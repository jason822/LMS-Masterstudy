<?php
/**
 * @var $current_user
 */

$rating = STM_LMS_Instructor::my_rating();
?>

<div class="stm_lms_user_side">

	<?php if (!empty($current_user['avatar'])): ?>
        <div class="stm-lms-user_avatar">
			<?php echo wp_kses_post($current_user['avatar']); ?>
        </div>
	<?php endif; ?>

    <h3 class="stm_lms_update_field__first_name"><?php echo esc_attr($current_user['login']); ?></h3>

	<?php if (!empty($current_user['meta']['position'])): ?>
        <h5 class="stm_lms_update_field__position"><?php echo sanitize_text_field($current_user['meta']['position']); ?></h5>
	<?php endif; ?>

	<?php if (!empty($rating['total_marks'])): ?>
        <div class="stm-lms-user_rating">
            <div class="star-rating star-rating__big">
                <span style="width: <?php echo floatval($rating['percent']); ?>%;"></span>
            </div>
            <strong class="rating heading_font"><?php echo floatval($rating['average']); ?></strong>
            <div class="stm-lms-user_rating__total">
				<?php echo sanitize_text_field($rating['total_marks']); ?>
            </div>
        </div>
	<?php endif; ?>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/messages_btn', array('current_user' => $current_user)); ?>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/create_announcement_btn', array('current_user' => $current_user)); ?>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/my_certificates_btn', array('current_user' => $current_user)); ?>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/edit_profile_btn', array('current_user' => $current_user)); ?>


</div>