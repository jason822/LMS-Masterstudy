<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly

if (!defined('STM_LMS_PRO_PATH')) {
	$routes = new STM_LMS_WP_Router();
	$routes->create_routes();
}

class STM_LMS_WP_Router
{

	public function create_routes()
	{
		$wp_router = new WP_Router;

		$routes = $this->routes();

		foreach ($routes as $uri => $method) {

			$wp_router->get(array(
				'as'   => 'simpleRoute',
				'uri'  => $uri,
				'uses' => "STM_LMS_WP_Router@{$method}"
			));

		}

	}

	public function routes($routes = array())
	{

		$lms_settings = get_option('stm_lms_settings', array());

		$courses_url = STM_LMS_Options::courses_page_slug();

		$lms_login = (!empty($lms_settings['login_url'])) ? $lms_settings['login_url'] : '/lms-login';
		$chat_url = (!empty($lms_settings['chat_url'])) ? $lms_settings['chat_url'] : '/lms-chats';
		$wishlist_url = (!empty($lms_settings['wishlist_url'])) ? $lms_settings['wishlist_url'] : '/lms-wishlist';
		$checkout_url = (!empty($lms_settings['checkout_url'])) ? $lms_settings['checkout_url'] : '/lms-checkout';
		$user_url = (!empty($lms_settings['user_url'])) ? $lms_settings['user_url'] : '/lms-user';
		$user_profile_url = (!empty($lms_settings['user_url_profile'])) ? $lms_settings['user_url_profile'] : '/lms-user_profile';
		$certificates_url = (!empty($lms_settings['certificate_url'])) ? $lms_settings['certificate_url'] : '/lms-certificates';

		$base_routes = array(
			"/{$courses_url}/{course}/{lesson}" => 'stm_lms_lesson',
			$lms_login                         => 'stm_lms_login',
			"{$user_url}/{user_id}"            => 'stm_lms_user',
			"{$user_profile_url}/{user_id}"    => 'stm_lms_user_public',
			$chat_url                          => 'stm_lms_user_chats',
			$wishlist_url                      => 'stm_lms_user_wishlist',
			$certificates_url                  => 'stm_lms_user_certificates',
			"{$certificates_url}/{course}"     => 'stm_lms_user_certificates_generate',
			$checkout_url                      => 'stm_lms_cart',
		);

		if (!empty($routes)) return array_merge($routes, $base_routes);

		return $base_routes;

	}

	public static function route_urls($route = 'courses') {
		$lms_settings = get_option('stm_lms_settings', array());

		$course_base = STM_LMS_Options::courses_page_slug();
		$courses = "/{$course_base}";
		$login = (!empty($lms_settings['login_url'])) ? $lms_settings['login_url'] : '/lms-login';
		$chat = (!empty($lms_settings['chat_url'])) ? $lms_settings['chat_url'] : '/lms-chats';
		$wishlist = (!empty($lms_settings['wishlist_url'])) ? $lms_settings['wishlist_url'] : '/lms-wishlist';
		$checkout = (!empty($lms_settings['checkout_url'])) ? $lms_settings['checkout_url'] : '/lms-checkout';
		$user = (!empty($lms_settings['user_url'])) ? $lms_settings['user_url'] : '/lms-user';
		$user_profile = (!empty($lms_settings['user_url_profile'])) ? $lms_settings['user_url_profile'] : '/lms-user_profile';
		$certificates = (!empty($lms_settings['certificate_url'])) ? $lms_settings['certificate_url'] : '/lms-certificates';

		return !empty(${$route}) ? sanitize_title(${$route}) : '';
	}

	function stm_lms_login()
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-login.php';
	}

	function stm_lms_lesson($course, $lesson)
	{
		if (class_exists('WPBMap')) {
			WPBMap::addAllMappedShortcodes();
		}
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-lesson.php';
	}

	function stm_lms_user($user_id)
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-user.php';
	}

	function stm_lms_user_public($user_id)
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-user-public.php';
	}

	function stm_lms_user_chats()
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-user-chats.php';
	}

	function stm_lms_user_wishlist()
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-wishlist.php';
	}

	function stm_lms_user_certificates()
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-certificates.php';
	}

	function stm_lms_user_certificates_generate($course_id)
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-certificates-generator.php';
	}

	function stm_lms_cart()
	{
		require_once STM_LMS_PATH . '/stm-lms-templates/stm-lms-checkout.php';
	}

	public function stm_lms_manage_course($course_id = '')
	{
		if(STM_LMS_Instructor::is_instructor()) {
			require_once STM_LMS_PRO_PATH . '/stm-lms-templates/stm-lms-manage-course.php';
		} else {
			wp_safe_redirect(STM_LMS_User::login_page_url());
		}
	}
}