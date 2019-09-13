<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly


add_action('init', 'stm_lms_user_cart');

register_activation_hook( STM_LMS_FILE, 'stm_lms_user_cart' );

function stm_lms_user_cart() {
	global $wpdb;

	$table_name = stm_lms_user_cart_name($wpdb);

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_cart_id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NOT NULL,
		item_id mediumint(9) NOT NULL,
		quantity mediumint(9) NOT NULL,
		price float(9) NOT NULL,
		PRIMARY KEY  (user_cart_id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'stm_lms_db_version', STM_LMS_DB_VERSION );

}