<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

require_once STM_LMS_PATH . '/db/_names.php';

require_once STM_LMS_PATH . '/db/helpers/user_courses.php';
require_once STM_LMS_PATH . '/db/helpers/user_quizzes.php';
require_once STM_LMS_PATH . '/db/helpers/user_quizzes.times.php';
require_once STM_LMS_PATH . '/db/helpers/user_lessons.php';
require_once STM_LMS_PATH . '/db/helpers/user_answers.php';
require_once STM_LMS_PATH . '/db/helpers/user_cart.php';
require_once STM_LMS_PATH . '/db/helpers/user_chat.php';

if(is_admin()) {
	require_once STM_LMS_PATH . '/db/tables/user_courses.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_quizzes.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_quizzes.times.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_lessons.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_answers.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_cart.table.php';
	require_once STM_LMS_PATH . '/db/tables/user_chat.table.php';
}