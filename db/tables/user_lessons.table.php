<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly


add_action('init', 'stm_lms_user_lessons');

register_activation_hook( STM_LMS_FILE, 'stm_lms_user_lessons' );

function stm_lms_user_lessons() {
	global $wpdb;

	$table_name = stm_lms_user_lessons_name($wpdb);

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_lesson_id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NOT NULL,
		course_id mediumint(9) NOT NULL,
		lesson_id mediumint(9) NOT NULL,
		PRIMARY KEY  (user_lesson_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'stm_lms_db_version', STM_LMS_DB_VERSION );
}