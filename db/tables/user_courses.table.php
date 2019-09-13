<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly


add_action('init', 'stm_lms_user_courses');

register_activation_hook( STM_LMS_FILE, 'stm_lms_user_courses' );

function stm_lms_user_courses() {
	global $wpdb;

	$table_name = stm_lms_user_courses_name($wpdb);

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_course_id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NOT NULL,
		course_id mediumint(9) NOT NULL,
		current_lesson_id mediumint(9),
		progress_percent mediumint(9) NOT NULL,
		status varchar(45) NOT NULL DEFAULT '',
		subscription_id mediumint(9),
		start_time INT NOT NULL,
		PRIMARY KEY (user_course_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'stm_lms_db_version', STM_LMS_DB_VERSION );
}