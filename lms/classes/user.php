<?php

STM_LMS_User::init();

class STM_LMS_User
{

	public static function init()
	{
		add_action('wp_ajax_stm_lms_login', 'STM_LMS_User::stm_lms_login');
		add_action('wp_ajax_nopriv_stm_lms_login', 'STM_LMS_User::stm_lms_login');

		add_action('wp_ajax_stm_lms_logout', 'STM_LMS_User::stm_lms_logout');

		add_action('wp_ajax_stm_lms_register', 'STM_LMS_User::stm_lms_register');
		add_action('wp_ajax_nopriv_stm_lms_register', 'STM_LMS_User::stm_lms_register');

		add_action('wp_ajax_stm_lms_become_instructor', 'STM_LMS_User::apply_for_instructor');

		add_action('wp_ajax_stm_lms_enterprise', 'STM_LMS_User::enterprise');
		add_action('wp_ajax_nopriv_stm_lms_enterprise', 'STM_LMS_User::enterprise');

		add_action('wp_ajax_stm_lms_get_user_courses', 'STM_LMS_User::get_user_courses');

		add_action('wp_ajax_stm_lms_get_user_quizzes', 'STM_LMS_User::get_user_quizzes');

		add_action('wp_ajax_stm_lms_wishlist', 'STM_LMS_User::wishlist');

		add_action("wsl_hook_process_login_before_wp_safe_redirect", "STM_LMS_User::wsl_new_register_redirect_url", 100, 4);

		add_action('wp_login', 'STM_LMS_User::user_logged_in', 100, 2);

		add_action('show_user_profile', "STM_LMS_User::extra_fields_display");
		add_action('edit_user_profile', 'STM_LMS_User::extra_fields_display');

		add_action('personal_options_update', 'STM_LMS_User::save_extra_fields');
		add_action('edit_user_profile_update', 'STM_LMS_User::save_extra_fields');

		add_action('wp_ajax_stm_lms_save_user_info', 'STM_LMS_User::save_user_info');
	}

	public static function wsl_new_register_redirect_url($user_id)
	{
		if ($user_id != null) {
			do_action('wsl_clear_user_php_session');
			wp_safe_redirect(STM_LMS_USER::user_page_url($user_id));
			die();
		}
	}

	public static function login_page_url()
	{
		return home_url('/') . STM_LMS_WP_Router::route_urls('login');
	}

	public static function user_page_url($user_id = '', $force = false)
	{
		if (!is_user_logged_in() and !$force) return STM_LMS_User::login_page_url();
		if (empty($user_id)) {
			$user = STM_LMS_User::get_current_user();
			$user_id = $user['id'];
		}
		return home_url('/') . STM_LMS_WP_Router::route_urls('user') . "/{$user_id}";
	}

	public static function user_public_page_url($user_id)
	{
		return home_url('/') . STM_LMS_WP_Router::route_urls('user_profile') . "/{$user_id}";
	}

	public static function stm_lms_login()
	{
		$r = array(
			'status' => 'error'
		);

		$recaptcha_passed = STM_LMS_Helpers::check_recaptcha();
		if (!$recaptcha_passed) {
			$r['message'] = esc_html__('CAPTCHA verification failed.', 'masterstudy-lms-learning-management-system');

			wp_send_json($r);
			die;
		}

		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body, true);


		$user = wp_signon($data, is_ssl());

		if (is_wp_error($user)) {
			$r['message'] = esc_html__('Wrong Username or Password', 'masterstudy-lms-learning-management-system');
		} else {
			$r['user_page'] = STM_LMS_User::user_page_url($user->ID, true);
			$r['message'] = esc_html__('Successfully logged in. Redirecting...', 'masterstudy-lms-learning-management-system');
			$r['status'] = 'success';
		}

		wp_send_json($r);
		die;
	}

	public static function stm_lms_register()
	{
		$r = array(
			'message' => '',
			'status'  => 'error'
		);

		$recaptcha_passed = STM_LMS_Helpers::check_recaptcha();
		if (!$recaptcha_passed) {
			$r['message'] = esc_html__('CAPTCHA verification failed.', 'masterstudy-lms-learning-management-system');

			wp_send_json($r);
			die;
		}

		$fields = array(
			'user_login'       => array(
				'label' => esc_html__('Login', 'masterstudy-lms-learning-management-system'),
				'type'  => 'text'
			),
			'user_email'       => array(
				'label' => esc_html__('E-mail', 'masterstudy-lms-learning-management-system'),
				'type'  => 'email'
			),
			'user_password'    => array(
				'label' => esc_html__('Password', 'masterstudy-lms-learning-management-system'),
				'type'  => 'text'
			),
			'user_password_re' => array(
				'label' => esc_html__('Password confirm', 'masterstudy-lms-learning-management-system'),
				'type'  => 'text'
			),
		);

		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body, true);

		foreach ($fields as $field_key => $field) {
			if (empty($data[$field_key])) {
				$r['message'] = sprintf(esc_html__('%s field is required', 'masterstudy-lms-learning-management-system'), $field['label']);
				wp_send_json($r);
				die;
			} else {
				$data[$field_key] = STM_LMS_Helpers::sanitize_fields($data[$field_key], $field['type']);
				if (empty($data[$field_key])) {
					$r['message'] = sprintf(esc_html__('Please enter valid %s field', 'masterstudy-lms-learning-management-system'), $field['label']);
					wp_send_json($r);
					die;
				}
			}
		}

		extract($data);
		/**
		 * @var $user_login ;
		 * @var $user_email ;
		 * @var $user_password ;
		 * @var $user_password_re ;
		 */

		/*If password is equal*/
		if ($user_password !== $user_password_re) {
			$r['message'] = esc_html__('Passwords do not match', 'masterstudy-lms-learning-management-system');
			wp_send_json($r);
			die;
		}

		/*Now we have valid data*/
		$user = wp_create_user($user_login, $user_password, $user_email);

		if (is_wp_error($user)) {
			$r['message'] = $user->get_error_message();
		} else {
			wp_signon($data, is_ssl());
			$r['status'] = 'success';
			$r['user_page'] = STM_LMS_User::user_page_url($user, true);
			$r['message'] = esc_html__('Registration completed successfully.', 'masterstudy-lms-learning-management-system');

			/*If everything is right, check for Instructor application*/
			STM_LMS_Instructor::become_instructor($data, $user);
		}

		wp_send_json($r);
	}

	public static function get_current_user($id = '', $get_role = false, $get_meta = false)
	{
		$user = array(
			'id' => 0
		);
		$current_user = (!empty($id)) ? get_userdata($id) : wp_get_current_user();
		if (!empty($current_user->ID) and 0 != $current_user->ID) {
			$avatar = get_avatar($current_user->ID, '215');
			$user = array(
				'id'     => $current_user->ID,
				'login'  => STM_LMS_User::display_name($current_user),
				'avatar' => $avatar,
				'email'  => $current_user->data->user_email,
				'url'    => STM_LMS_User::user_public_page_url($current_user->ID)
			);

			if ($get_role) {
				$user_meta = get_userdata($current_user->ID);
				$user['roles'] = $user_meta->roles;
			}

			if ($get_meta) {
				$fields = STM_LMS_User::extra_fields();
				$fields = array_merge($fields, STM_LMS_User::additional_fields());
				$user['meta'] = array();
				foreach ($fields as $field_key => $field) {
					$meta = get_user_meta($current_user->ID, $field_key, true);
					$user['meta'][$field_key] = (!empty($meta)) ? $meta : '';
				}
			}

		}

		return apply_filters('stm_lms_current_user_data', $user);
	}

	public static function display_name($user)
	{
		$first_name = get_user_meta($user->ID, 'first_name', true);
		$last_name = get_user_meta($user->ID, 'last_name', true);
		if (!empty($first_name) and !empty($last_name)) $first_name .= ' ' . $last_name;
		if (empty($first_name) and !empty($user->data->display_name)) $first_name = $user->data->display_name;
		$login = (!empty($first_name)) ? $first_name : $user->data->user_login;
		return $login;
	}

	public static function js_redirect($page)
	{
		?>
        <script type="text/javascript">
            window.location = '<?php echo esc_url($page); ?>';
        </script>
	<?php }

	public static function get_user_courses()
	{
		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];

		$r = array(
			'posts' => array(),
			'total' => false
		);

		$pp = get_option('posts_per_page');
		$offset = (!empty($_GET['offset'])) ? intval($_GET['offset']) : 0;

		$offset = $offset * $pp;

		$courses = stm_lms_get_user_courses($user_id, $pp, $offset, array('course_id', 'current_lesson_id', 'progress_percent', 'start_time'));
		$total = stm_lms_get_user_courses($user_id, '', '', array(), true);
		$total = STM_LMS_Helpers::simplify_db_array($total);
		$total = intval($total['COUNT(*)']);

		$r['total'] = $total <= $offset + $pp;

		if (!empty($courses)) {
			foreach ($courses as $course) {
				$id = $course['course_id'];

				if (!get_post_status($id)) continue;

				$price = get_post_meta($id, 'price', true);
				$sale_price = STM_LMS_Course::get_sale_price($id);

				if (empty($price) and !empty($sale_price)) {
					$price = $sale_price;
					$sale_price = '';
				}

				$post_status = STM_LMS_Course::get_post_status($id);

				$image = (function_exists('stm_get_VC_img')) ? stm_get_VC_img(get_post_thumbnail_id($id), '272x161') : get_the_post_thumbnail($id, 'img-300-225');

				$course['progress_percent'] = ($course['progress_percent'] > 100) ? 100 : $course['progress_percent'];

				$post = array(
					'title'             => get_the_title($id),
					'link'              => get_the_permalink($id),
					'image'             => $image,
					'terms'             => stm_lms_get_terms_array($id, 'stm_lms_course_taxonomy', false, true),
					'views'             => STM_LMS_Course::get_course_views($id),
					'price'             => STM_LMS_Helpers::display_price($price),
					'sale_price'        => STM_LMS_Helpers::display_price($sale_price),
					'post_status'       => $post_status,
					'progress'          => $course['progress_percent'],
					'progress_label'    => sprintf(esc_html__('%s%% Complete', 'masterstudy-lms-learning-management-system'), $course['progress_percent']),
					'current_lesson_id' => STM_LMS_Lesson::get_lesson_url($id, $course['current_lesson_id']),
					'start_time'        => sprintf(esc_html__('Started %s', 'masterstudy-lms-learning-management-system'), date_i18n(get_option('date_format'), $course['start_time'])),
					'duration'          => get_post_meta($id, 'duration_info', true)
				);

				$r['posts'][] = $post;
			}
		}

		wp_send_json($r);
	}

	public static function get_user_quizzes()
	{
		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];

		$r = array(
			'posts' => array(),
			'total' => false
		);

		$pp = get_option('posts_per_page');
		$offset = (!empty($_GET['offset'])) ? intval($_GET['offset']) : 0;

		$offset = $offset * $pp;

		$quizzes = stm_lms_get_user_all_quizzes($user_id, $pp, $offset, array('course_id', 'quiz_id', 'progress', 'status'));

		$total = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_all_quizzes($user_id, '', '', array('course_id'), true));
		$total = $total['COUNT(*)'];

		$r['total'] = $total <= $offset + $pp;


		if (!empty($quizzes)) {
			foreach ($quizzes as $quiz) {
				$post_id = $quiz['course_id'];
				$item_id = $quiz['quiz_id'];
				$status_label = ($quiz['status'] == 'passed') ? esc_html__('Passed', 'masterstudy-lms-learning-management-system') : esc_html__('Failed', 'masterstudy-lms-learning-management-system');
				$course_title = (!empty(get_post_status($post_id))) ? get_the_title($post_id) : esc_html__('Course Deleted', 'masterstudy-lms-learning-management-system');
				$r['posts'][] = array_merge($quiz, array(
					'course_title' => $course_title,
					'course_url'   => get_the_permalink($post_id),
					'title'        => get_the_title($item_id),
					'url'          => STM_LMS_Lesson::get_lesson_url($post_id, $item_id),
					'status_label' => $status_label
				));
			}
		}

		wp_send_json($r);
	}

	public static function get_user_meta($user_id, $key)
	{
		return get_user_meta($user_id, $key, true);
	}

	public static function has_course_access($course_id)
	{

		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) return false;
		$user_id = $user['id'];

		/*If course Author*/
		$author_id = get_post_field('post_author', $course_id);
		if($author_id == $user_id) {
			STM_LMS_Course::add_user_course($course_id, $user_id, STM_LMS_Course::item_url($course_id, ''), 0);
		    return true;
		}

		if(STM_LMS_Cart::woocommerce_checkout_enabled()) {
			$bought = wc_customer_bought_product($user['email'], $user_id, $course_id);
			//var_dump($bought);
        }

		$course = stm_lms_get_user_course($user_id, $course_id, array('user_course_id'));

		if (!count($course)) {
			/*If course is free*/
			$course_price = STM_LMS_Course::get_course_price($course_id);
			if (empty($course_price)) {
				STM_LMS_Course::add_user_course($course_id, $user_id, STM_LMS_Course::item_url($course_id, ''), 0);
				return true;
			}
		}

		return count($course);
	}

	public static function get_wishlist($user_id = 0)
	{
		if (!is_user_logged_in()) {
			$wishlist = (!empty($_COOKIE['stm_lms_wishlist'])) ? $_COOKIE['stm_lms_wishlist'] : array();
			if (!empty($wishlist)) {
				$wishlist = array_filter(array_unique(explode(',', $wishlist)));
			}
			return $wishlist;
		}
		$wishlist = get_user_meta($user_id, 'stm_lms_wishlist', true);
		if (empty($wishlist)) $wishlist = array();
		return $wishlist;
	}

	public static function update_wishlist($user_id, $wishlist)
	{
		return update_user_meta($user_id, 'stm_lms_wishlist', array_unique(array_filter($wishlist)));
	}

	public static function wishlist()
	{
		if (empty($_GET['post_id'])) die;

		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];

		$r = array(
			'icon' => 'far fa-heart',
			'text' => esc_html__('Add to Wishlist', 'masterstudy-lms-learning-management-system')
		);

		$post_id = intval($_GET['post_id']);

		$wishlist = STM_LMS_User::get_wishlist($user_id);

		/*Add to wishlist*/
		if (!in_array($post_id, $wishlist)) {
			$wishlist[] = $post_id;
			$r = array(
				'icon' => 'fa fa-heart',
				'text' => esc_html__('Wishlisted', 'masterstudy-lms-learning-management-system')
			);
		} else {
			/*Remove*/
			$index = array_search($post_id, $wishlist);
			unset($wishlist[$index]);
		}

		STM_LMS_User::update_wishlist($user_id, $wishlist);

		wp_send_json($r);

	}

	public static function is_wishlisted($course_id)
	{
		if (is_user_logged_in()) {
			$user = STM_LMS_User::get_current_user();
			$user_id = $user['id'];
			$wishlist = STM_LMS_User::get_wishlist($user_id);
		} else {
			if (empty($_COOKIE['stm_lms_wishlist'])) return false;
			$wishlist = explode(',', sanitize_text_field($_COOKIE['stm_lms_wishlist']));
		}

		return in_array($course_id, $wishlist);
	}

	public static function user_logged_in($user_name, $user)
	{
		$user_id = $user->ID;
		STM_LMS_User::move_wishlist_to_user($user_id);
	}

	public static function move_wishlist_to_user($user_id)
	{
		if (empty($_COOKIE['stm_lms_wishlist'])) return false;
		$wishlist = explode(',', sanitize_text_field($_COOKIE['stm_lms_wishlist']));
		STM_LMS_User::update_wishlist($user_id, array_merge(STM_LMS_User::get_wishlist($user_id), $wishlist));
	}

	public static function wishlist_url($user_id = '')
	{
		return home_url('/') . STM_LMS_WP_Router::route_urls('wishlist');
	}

	public static function extra_fields()
	{
		return array(
			'facebook'    => array(
				'label' => esc_html__('Facebook', 'masterstudy-lms-learning-management-system'),
				'icon'  => 'facebook-f',
			),
			'twitter'     => array(
				'label' => esc_html__('Twitter', 'masterstudy-lms-learning-management-system'),
				'icon'  => 'twitter',
			),
			'instagram'   => array(
				'label' => esc_html__('Instagram', 'masterstudy-lms-learning-management-system'),
				'icon'  => 'instagram',
			),
			'google-plus' => array(
				'label' => esc_html__('Google Plus', 'masterstudy-lms-learning-management-system'),
				'icon'  => 'google-plus-g',
			),
			'position'    => array(
				'label' => esc_html__('Position', 'masterstudy-lms-learning-management-system'),
			),
		);
	}

	public static function additional_fields()
	{
		return array(
			'description' => array(
				'label' => esc_html__('Bio', 'masterstudy-lms-learning-management-system'),
			),
			'first_name'  => array(
				'label' => esc_html__('Name', 'masterstudy-lms-learning-management-system'),
			),
			'last_name'   => array(
				'label' => esc_html__('Name', 'masterstudy-lms-learning-management-system'),
			),
		);
	}

	public static function extra_fields_display($user)
	{ ?>
        <h3><?php esc_html_e("Extra profile information", "stm-lms"); ?></h3>


        <table class="form-table">
			<?php $fields = STM_LMS_User::extra_fields();
			foreach ($fields as $field_key => $field):?>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr($field_key); ?>"><?php echo esc_attr($field['label']); ?></label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo esc_attr($field_key); ?>"
                               id="<?php echo esc_attr($field_key); ?>"
                               value="<?php echo esc_attr(get_the_author_meta($field_key, $user->ID)); ?>"
                               class="regular-text"/><br/>
                    </td>
                </tr>
			<?php endforeach; ?>
        </table>
		<?php
	}

	public static function save_extra_fields($user_id)
	{
		if (!current_user_can('edit_user', $user_id)) {
			return false;
		}

		$fields = STM_LMS_User::extra_fields();
		foreach ($fields as $field_key => $field) {
			update_user_meta($user_id, $field_key, sanitize_text_field($_POST[$field_key]));
		}
	}

	public static function save_user_info()
	{
		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];

		$new_pass = (isset($_GET['new_pass'])) ? sanitize_text_field($_GET['new_pass']) : '';
		$new_pass_re = (isset($_GET['new_pass_re'])) ? sanitize_text_field($_GET['new_pass_re']) : '';

		if(!empty($new_pass) and !empty($new_pass_re)) {
			if($new_pass !== $new_pass_re) {
				wp_send_json(array(
					'status'  => 'error',
					'message' => esc_html__('New password do not match', 'masterstudy-lms-learning-management-system')
				));
			} else {
				wp_set_password($new_pass, $user_id);
				wp_send_json(array(
                    'relogin' => STM_LMS_User::login_page_url(),
					'status'  => 'success',
					'message' => esc_html__('Password Changed. Re-login now', 'masterstudy-lms-learning-management-system')
				));
            }
		}

		$fields = STM_LMS_User::extra_fields();
		$fields = array_merge($fields, STM_LMS_User::additional_fields());
		$data = array();

		foreach ($fields as $field_name => $field) {
			if (isset($_GET[$field_name])) {
				$new_value = sanitize_text_field($_GET[$field_name]);
				update_user_meta($user_id, $field_name, $new_value);
				$data[$field_name] = $new_value;
			}
		}

		$r = array(
			'data'    => $data,
			'status'  => 'success',
			'message' => esc_html__('Successfully saved', 'masterstudy-lms-learning-management-system')
		);

		wp_send_json($r);
	}

	public static function stm_lms_logout()
	{
		wp_logout();
		die;
	}

	public static function apply_for_instructor()
	{
		$user = STM_LMS_User::get_current_user();
		if (empty($user['id'])) die;
		$user_id = $user['id'];

		$r = array(
			'status'  => 'success',
			'message' => esc_html__('Your Application is under submission.', 'masterstudy-lms-learning-management-system')
		);

		$data = array(
			'become_instructor' => true
		);

		$request_body = file_get_contents('php://input');
		$get = json_decode($request_body, true);

		$data['degree'] = (!empty($get['degree'])) ? sanitize_text_field($get['degree']) : '';
		$data['expertize'] = (!empty($get['expertize'])) ? sanitize_text_field($get['expertize']) : '';

		if (empty($data['degree']) or empty($data['expertize'])) {
			$r['status'] = 'error';
			$r['message'] = esc_html__('Please fill all fields', 'masterstudy-lms-learning-management-system');
		}

		STM_LMS_Instructor::become_instructor($data, $user_id);

		wp_send_json($r);
	}

	public static function enterprise()
	{
		$r = array(
			'status'  => 'success',
			'message' => esc_html__('Message sent.', 'masterstudy-lms-learning-management-system')
		);

		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body, true);

		$fields = array('name', 'email', 'text');
		foreach ($fields as $field) {
			if (empty($data[$field])) {
				$r = array(
					'status'  => 'error',
					'message' => esc_html__('Please fill al fields', 'masterstudy-lms-learning-management-system')
				);
			}
		}

		if ($r['status'] !== 'error') {
			$subject = esc_html__('Enterprise Request', 'masterstudy-lms-learning-management-system');
			$message = sprintf(esc_html__('Name - %s; Email - %s; Message - %s', 'masterstudy-lms-learning-management-system'),
				$data['name'],
				$data['email'],
				$data['text']
			);
			STM_LMS_Helpers::send_email('', $subject, $message);
		}


		wp_send_json($r);
	}
}