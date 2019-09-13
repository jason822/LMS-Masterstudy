<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly


class STM_Metaboxes
{

	function __construct()
	{
		add_action('add_meta_boxes', array($this, 'stm_lms_register_meta_boxes'));

		add_action('admin_enqueue_scripts', array($this, 'stm_lms_scripts'));

		add_action('save_post', array($this, 'stm_lms_save'), 10, 3);

		add_action('wp_ajax_stm_curriculum', array($this, 'stm_search_posts'));

		add_action('wp_ajax_stm_manage_posts', array($this, 'manage_posts'));

		add_action('wp_ajax_stm_lms_change_post_status', array($this, 'change_status'));

		add_action('wp_ajax_stm_curriculum_create_item', array($this, 'stm_curriculum_create_item'));

		add_action('wp_ajax_stm_curriculum_get_item', array($this, 'stm_curriculum_get_item'));

		add_action('wp_ajax_stm_save_questions', array($this, 'stm_save_questions'));

		add_action('wp_ajax_stm_save_title', array($this, 'stm_save_title'));
	}

	function boxes()
	{
		return apply_filters('stm_lms_boxes', array(
						
			'stm_courses_curriculum' => array(
				'post_type' => array('stm-courses'),
				'label'     => esc_html__('Course curriculum', 'masterstudy-lms-learning-management-system'),
			),
			'stm_courses_settings'   => array(
				'post_type' => array('stm-courses'),
				'label'     => esc_html__('Course Settings', 'masterstudy-lms-learning-management-system'),
			),
			
			'stm_lesson_settings'    => array(
				'post_type' => array('stm-lessons'),
				'label'     => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
			),
			'stm_quiz_questions'     => array(
				'post_type' => array('stm-quizzes'),
				'label'     => esc_html__('Quiz Questions', 'masterstudy-lms-learning-management-system'),
			),
			'stm_quiz_settings'      => array(
				'post_type' => array('stm-quizzes'),
				'label'     => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
			),
			'stm_question_settings'  => array(
				'post_type' => array('stm-questions'),
				'label'     => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
			),
			'stm_reviews'            => array(
				'post_type' => array('stm-reviews'),
				'label'     => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
			),
			'stm_order_info'         => array(
				'post_type' => array('stm-orders'),
				'label'     => esc_html__('Order info', 'masterstudy-lms-learning-management-system'),
			),
			'stm_courses_teacher' => array(
				'post_type' => array('stm-courses'),
				'label'     => esc_html__('Course Teacher', 'masterstudy-lms-learning-management-system'),
			),
			'stm_courses_gallery' => array(
				'post_type' => array('stm-courses'),
				'label'     => esc_html__('Course Gallery', 'masterstudy-lms-learning-management-system'),
			),
		));
	}

	function get_users()
	{
		$users = array(
			'' => esc_html__('Choose User', 'masterstudy-lms-learning-management-system')
		);

		if (!is_admin()) return $users;

		$users_data = get_users();
		foreach ($users_data as $user) {
			$users[$user->ID] = $user->data->user_nicename;
		}

		return $users;
	}

	function fields()
	{
		$users = $this->get_users();

		$courses = (class_exists('STM_LMS_Settings')) ? STM_LMS_Settings::stm_get_post_type_array('stm-courses') : array();
		$experts = array(
		);

		$experts_args = array(
			'post_type'		=> 'teachers',
			'post_status' => 'publish',
			'posts_per_page'=> -1,
		);

		$experts_query = new WP_Query($experts_args);

		foreach($experts_query->posts as $expert){
			$experts[$expert->ID] = $expert->post_title;
		}


		return apply_filters('stm_lms_fields', array(
			'stm_courses_curriculum' => array(
				'section_curriculum' => array(
					'name'   => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system'),
					'fields' => array(
						'curriculum' => array(
							'type'      => 'post_type_repeat',
							'post_type' => array('stm-lessons', 'stm-quizzes'),
						),
					)
				)
			),
			
			'stm_courses_settings'   => array(
				'section_settings'      => array(
					'name'   => esc_html__('Settings', 'masterstudy-lms-learning-management-system'),
					'fields' => array(
						'views'            => array(
							'type'     => 'number',
							'label'    => esc_html__('Course Views', 'masterstudy-lms-learning-management-system'),
							'sanitize' => 'stm_lms_save_number'
						),
						'level'            => array(
							'type'    => 'select',
							'label'   => esc_html__('Course Level', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'beginner'     => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
								'intermediate' => esc_html__('Intermediate', 'masterstudy-lms-learning-management-system'),
								'advanced'     => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
							)
						),
						'current_students' => array(
							'type'     => 'number',
							'label'    => esc_html__('Current students', 'masterstudy-lms-learning-management-system'),
							'sanitize' => 'stm_lms_save_number'
						),
						'language' => array(
							'type'     => 'select',
							'label'    => esc_html__('Course language', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'Australian' => esc_html__('Australian', 'masterstudy-lms-learning-management-system' ),
								'English' => esc_html__('English', 'masterstudy-lms-learning-management-system' ),
							)
						),
//						'featured_course'   => array(
//							'type'  => 'checkbox',
//							'label' => esc_html__('Featured Course', 'masterstudy-lms-learning-management-system'),
//						),
//						'external_buy_link' => array(
//							'type'  => 'text',
//							'label' => esc_html__('External Buy link', 'masterstudy-lms-learning-management-system'),
//						),
						'duration_info'    => array(
							'type'  => 'text',
							'label' => esc_html__('Duration info', 'masterstudy-lms-learning-management-system'),
						),
						'school_name'    => array(
							'type'  => 'text',
							'label' => esc_html__('School Name', 'masterstudy-lms-learning-management-system'),
						),
						'class_time'    => array(
							'type'  => 'select',
							'label' => esc_html__('Class time', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'am' => esc_html__('Morning', 'masterstudy-lms-learning-management-system' ),
								'pm' => esc_html__('Afternoon', 'masterstudy-lms-learning-management-system' ),
								'we' => esc_html__('Weekend', 'masterstudy-lms-learning-management-system' ),
							)
						),
						'country'    => array(
							'type'  => 'select',
							'label' => esc_html__('Country info', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'AU' => esc_html__('Australia', 'masterstudy-lms-learning-management-system' ),
								'NZ' => esc_html__('New Zealand', 'masterstudy-lms-learning-management-system' ),
							)
						),
						'city'    => array(
							'type'  => 'select',
							'label' => esc_html__('City info', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'1' => esc_html__('Sydney', 'masterstudy-lms-learning-management-system' ),
								'2' => esc_html__('Melbourne', 'masterstudy-lms-learning-management-system' ),
								'3' => esc_html__('Perth', 'masterstudy-lms-learning-management-system' ),
								'4' => esc_html__('Cairns', 'masterstudy-lms-learning-management-system' ),
								'5' => esc_html__('Hobart', 'masterstudy-lms-learning-management-system' ),
								'6' => esc_html__('Auckland', 'masterstudy-lms-learning-management-system' ),
								'7' => esc_html__('Chrischurch', 'masterstudy-lms-learning-management-system' ),
								'8' => esc_html__('Queentown', 'masterstudy-lms-learning-management-system' ),
								'9' => esc_html__('Rotorua', 'masterstudy-lms-learning-management-system' ),
								'10' => esc_html__('Wellington', 'masterstudy-lms-learning-management-system' ),
							)
						),
						'time'   => array(
							'type'  => 'text',
							'label' => esc_html__('Time', 'masterstudy-lms-learning-management-system'),
						),
						'schedule'   => array(
							'type'  => 'text',
							'label' => esc_html__('Schedule', 'masterstudy-lms-learning-management-system'),
						),
						'weeks'      => array(
							'type'    => 'select',
							'label'   => esc_html__('Weeks', 'masterstudy-lms-learning-management-system'),
							'options' => array(
								'' => esc_html__('No weeks', 'masterstudy-lms-learning-management-system'),
								'1' => esc_html__('1', 'masterstudy-lms-learning-management-system'),
								'2' => esc_html__('2', 'masterstudy-lms-learning-management-system'),
								'3' => esc_html__('3', 'masterstudy-lms-learning-management-system'),
								'4' => esc_html__('4', 'masterstudy-lms-learning-management-system'),
								'5' => esc_html__('5', 'masterstudy-lms-learning-management-system'),
								'6' => esc_html__('6', 'masterstudy-lms-learning-management-system'),
								'7' => esc_html__('7', 'masterstudy-lms-learning-management-system'),
								'8' => esc_html__('8', 'masterstudy-lms-learning-management-system'),
								'9' => esc_html__('9', 'masterstudy-lms-learning-management-system'),
								'10' => esc_html__('10', 'masterstudy-lms-learning-management-system'),
								'11' => esc_html__('11', 'masterstudy-lms-learning-management-system'),
								'12' => esc_html__('12', 'masterstudy-lms-learning-management-system'),
								'13' => esc_html__('13', 'masterstudy-lms-learning-management-system'),
								'14' => esc_html__('14', 'masterstudy-lms-learning-management-system'),
								'15' => esc_html__('15', 'masterstudy-lms-learning-management-system'),
								'16' => esc_html__('16', 'masterstudy-lms-learning-management-system'),
								'17' => esc_html__('17', 'masterstudy-lms-learning-management-system'),
								'18' => esc_html__('18', 'masterstudy-lms-learning-management-system'),
								'19' => esc_html__('19', 'masterstudy-lms-learning-management-system'),
								'20' => esc_html__('20', 'masterstudy-lms-learning-management-system'),
								'21' => esc_html__('21', 'masterstudy-lms-learning-management-system'),
								'22' => esc_html__('22', 'masterstudy-lms-learning-management-system'),
								'23' => esc_html__('23', 'masterstudy-lms-learning-management-system'),
								'24' => esc_html__('24', 'masterstudy-lms-learning-management-system'),
								'25' => esc_html__('25', 'masterstudy-lms-learning-management-system'),
								'26' => esc_html__('26', 'masterstudy-lms-learning-management-system'),
								'27' => esc_html__('27', 'masterstudy-lms-learning-management-system'),
								'28' => esc_html__('28', 'masterstudy-lms-learning-management-system'),
								'29' => esc_html__('29', 'masterstudy-lms-learning-management-system'),
								'30' => esc_html__('30', 'masterstudy-lms-learning-management-system'),
								'31' => esc_html__('31', 'masterstudy-lms-learning-management-system'),
								'32' => esc_html__('32', 'masterstudy-lms-learning-management-system'),
								'33' => esc_html__('33', 'masterstudy-lms-learning-management-system'),
								'34' => esc_html__('34', 'masterstudy-lms-learning-management-system'),
								'35' => esc_html__('35', 'masterstudy-lms-learning-management-system'),
								'36' => esc_html__('36', 'masterstudy-lms-learning-management-system'),
								'37' => esc_html__('37', 'masterstudy-lms-learning-management-system'),
								'38' => esc_html__('38', 'masterstudy-lms-learning-management-system'),
								'39' => esc_html__('39', 'masterstudy-lms-learning-management-system'),
								'40' => esc_html__('40', 'masterstudy-lms-learning-management-system'),
								'41' => esc_html__('41', 'masterstudy-lms-learning-management-system'),
								'42' => esc_html__('42', 'masterstudy-lms-learning-management-system'),
								'43' => esc_html__('43', 'masterstudy-lms-learning-management-system'),
								'44' => esc_html__('44', 'masterstudy-lms-learning-management-system'),
								'45' => esc_html__('45', 'masterstudy-lms-learning-management-system'),
								'46' => esc_html__('46', 'masterstudy-lms-learning-management-system'),
								'47' => esc_html__('47', 'masterstudy-lms-learning-management-system'),
								'48' => esc_html__('48', 'masterstudy-lms-learning-management-system'),
								'49' => esc_html__('49', 'masterstudy-lms-learning-management-system'),
								'50' => esc_html__('50', 'masterstudy-lms-learning-management-system'),
								'51' => esc_html__('51', 'masterstudy-lms-learning-management-system'),
								'52' => esc_html__('52', 'masterstudy-lms-learning-management-system'),
								'53' => esc_html__('53', 'masterstudy-lms-learning-management-system'),
								'54' => esc_html__('54', 'masterstudy-lms-learning-management-system'),
							)
),
'study_level'      => array(
	'type'    => 'select',
	'label'   => esc_html__('Study level', 'masterstudy-lms-learning-management-system'),
	'options' => array(
		'' => esc_html__('No level', 'masterstudy-lms-learning-management-system'),
		'lvl1' => esc_html__('Certificate II', 'masterstudy-lms-learning-management-system'),
		'lvl2'   => esc_html__('Certificate III', 'masterstudy-lms-learning-management-system'),
		'lvl3' => esc_html__('Certificate IV', 'masterstudy-lms-learning-management-system'),
		'lvl4' => esc_html__('Diploma', 'masterstudy-lms-learning-management-system'),
		'lvl5' => esc_html__('Advanced Diploma', 'masterstudy-lms-learning-management-system'),
	)
),
'retake'           => array(
	'type'  => 'checkbox',
	'label' => esc_html__('Retake Course', 'masterstudy-lms-learning-management-system'),
),
'status'           => array(
	'type'    => 'select',
	'label'   => esc_html__('Status', 'masterstudy-lms-learning-management-system'),
	'options' => array(
		''        => esc_html__('No status', 'masterstudy-lms-learning-management-system'),
		'hot'     => esc_html__('Hot', 'masterstudy-lms-learning-management-system'),
		'new'     => esc_html__('New', 'masterstudy-lms-learning-management-system'),
		'special' => esc_html__('Special', 'masterstudy-lms-learning-management-system'),
	)
),
'status_dates'     => array(
	'type'       => 'dates',
	'label'      => esc_html__('Status Dates', 'masterstudy-lms-learning-management-system'),
	'sanitize'   => 'stm_lms_save_dates',
	'dependency' => array(
		'key'   => 'status',
		'value' => 'not_empty'
	)
),
)
),
//				'section_assestment'    => array(
//					'name'   => esc_html__('Assestment', 'masterstudy-lms-learning-management-system'),
//					'fields' => array(
//						'course_result' => array(
//							'type'    => 'radio',
//							'label'   => esc_html__('Course Result', 'masterstudy-lms-learning-management-system'),
//							'options' => array(
//								'lessons'       => esc_html__('Via lessons', 'masterstudy-lms-learning-management-system'),
//								'quizzes'       => esc_html__('Quizzes mark', 'masterstudy-lms-learning-management-system'),
//								'total_quizzes' => esc_html__('Total quizzes', 'masterstudy-lms-learning-management-system'),
//							)
//						),
//						'passing_value' => array(
//							'type'     => 'number',
//							'label'    => esc_html__('Passing value (%)', 'masterstudy-lms-learning-management-system'),
//							'sanitize' => 'stm_lms_save_number'
//						)
//					)
//				),
'section_accessibility' => array(
	'name'   => esc_html__('Accessibility', 'masterstudy-lms-learning-management-system'),
	'fields' => array(
		'price'                => array(
			'type'     => 'number',
			'label'    => esc_html__('Course Price (leave blank to make the course free)', 'masterstudy-lms-learning-management-system'),
			'sanitize' => 'stm_lms_save_number'
		),
		'sale_price'           => array(
			'type'     => 'number',
			'label'    => esc_html__('Sale Price', 'masterstudy-lms-learning-management-system'),
			'sanitize' => 'stm_lms_save_number'
		),
		'app_fee'           => array(
			'type'     => 'number',
			'label'    => esc_html__('Application Fee', 'masterstudy-lms-learning-management-system'),
			'sanitize' => 'stm_lms_save_number'
		),
		'saving'           => array(
			'type'     => 'hidden',
			'label'    => esc_html__('Saving', 'masterstudy-lms-learning-management-system'),
		),
		'sale_price_dates'     => array(
			'type'       => 'dates',
			'label'      => esc_html__('Sale Price Dates', 'masterstudy-lms-learning-management-system'),
			'sanitize'   => 'stm_lms_save_dates',
			'dependency' => array(
				'key'   => 'sale_price',
				'value' => 'not_empty'
			),
			'pro'        => true,
		),
		'not_membership'       => array(
			'type'  => 'checkbox',
			'label' => esc_html__('Not included in membership', 'masterstudy-lms-learning-management-system'),
		),
		'affiliate_course'      => array(
			'type'  => 'checkbox',
			'label' => esc_html__('Affiliate course', 'masterstudy-lms-learning-management-system'),
			'pro'   => true,
		),
		'affiliate_course_text' => array(
			'type'  => 'text',
			'label' => esc_html__('Button Text', 'masterstudy-lms-learning-management-system'),
			'dependency' => array(
				'key'   => 'affiliate_course',
				'value' => 'not_empty'
			),
			'pro'   => true,
		),
		'affiliate_course_link' => array(
			'type'  => 'text',
			'label' => esc_html__('Button Link', 'masterstudy-lms-learning-management-system'),
			'dependency' => array(
				'key'   => 'affiliate_course',
				'value' => 'not_empty'
			),
			'pro'   => true,
		),
	)
),
'section_announcement'  => array(
	'name'   => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
	'fields' => array(
		'announcement' => array(
			'type'  => 'editor',
			'label' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
		),
	)
),
'section_faq'           => array(
	'name'   => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
	'fields' => array(
		'faq' => array(
			'type'  => 'faq',
			'label' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
		),
	)
),
),
'stm_courses_teacher' => array(
	'section_teacher' => array(
		'name'   => esc_html__('Teacher', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'course_expert'   => array(
				'label' => __( 'Teacher', 'stm-post-type' ),
				'type'  => 'select',
				'options' => $experts,
				'description' => 'Choose Teacher for course'
			),
		)
	)
),
'stm_courses_gallery' => array(
	'section_gallery' => array(
		'name'   => esc_html__('Gallery', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'course_gallery'   => array(
				'label' => __( 'Gallery', 'stm-post-type' ),
				'type'  => 'text',
				'description' => 'Choose Gallery for course'
			),
		)
	)
),
'stm_lesson_settings'    => array(
	'section_lesson_settings' => array(
		'name'   => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'type'           => array(
				'type'    => 'select',
				'label'   => esc_html__('Lesson type', 'masterstudy-lms-learning-management-system'),
				'options' => array(
					'text'  => esc_html__('Text', 'masterstudy-lms-learning-management-system'),
					'video' => esc_html__('Video', 'masterstudy-lms-learning-management-system'),
					'slide' => esc_html__('Slide', 'masterstudy-lms-learning-management-system'),
				),
				'value'   => 'text'
			),
			'duration'       => array(
				'type'  => 'text',
				'label' => esc_html__('Lesson duration', 'masterstudy-lms-learning-management-system'),
			),
			'preview'        => array(
				'type'  => 'checkbox',
				'label' => esc_html__('Lesson preview', 'masterstudy-lms-learning-management-system'),
			),
			'lesson_excerpt' => array(
				'type'  => 'editor',
				'label' => esc_html__('Lesson Frontend description', 'masterstudy-lms-learning-management-system'),
			),
//						'media'          => array(
//							'type'  => 'multimedia',
//							'label' => esc_html__('Lesson media', 'masterstudy-lms-learning-management-system'),
//						),
		)
	)
),
'stm_quiz_questions'     => array(
	'section_questions' => array(
		'name'   => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'questions' => array(
				'type'      => 'questions',
				'label'     => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
				'post_type' => array('stm-questions')
			),
		)
	)
),
'stm_quiz_settings'      => array(
	'section_quiz_settings' => array(
		'name'   => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'lesson_excerpt'   => array(
				'type'  => 'editor',
				'label' => esc_html__('Quiz Frontend description', 'masterstudy-lms-learning-management-system'),
			),
			'duration'         => array(
				'type'  => 'duration',
				'label' => esc_html__('Quiz duration', 'masterstudy-lms-learning-management-system'),
			),
			'duration_measure' => array(
				'type' => 'not_exist',
			),
			'correct_answer'   => array(
				'type'  => 'checkbox',
				'label' => esc_html__('Show correct answer', 'masterstudy-lms-learning-management-system'),
			),
			'passing_grade'    => array(
				'type'  => 'number',
				'label' => esc_html__('Passing grade (%)', 'masterstudy-lms-learning-management-system'),
			),
			're_take_cut'      => array(
				'type'  => 'number',
				'label' => esc_html__('Points total cut after re-take (%)', 'masterstudy-lms-learning-management-system'),
			),
		)
	)
),
'stm_question_settings'  => array(
	'section_question_settings' => array(
		'name'   => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'type'                 => array(
				'type'    => 'select',
				'label'   => esc_html__('Question type', 'masterstudy-lms-learning-management-system'),
				'options' => array(
					'single_choice' => esc_html__('Single choice', 'masterstudy-lms-learning-management-system'),
					'multi_choice'  => esc_html__('Multi choice', 'masterstudy-lms-learning-management-system'),
					'true_false'    => esc_html__('True or False', 'masterstudy-lms-learning-management-system'),
				),
				'value'   => 'single_choice'
			),
			'answers'              => array(
				'type'         => 'answers',
				'label'        => esc_html__('Answers', 'masterstudy-lms-learning-management-system'),
				'requirements' => 'type'
			),
//						'question'             => array(
//							'type'  => 'editor',
//							'label' => esc_html__('Question', 'masterstudy-lms-learning-management-system'),
//						),
			'question_explanation' => array(
				'type'  => 'textarea',
				'label' => esc_html__('Question result explanation', 'masterstudy-lms-learning-management-system'),
			),
//						'question_hint'        => array(
//							'type'  => 'hint',
//							'label' => esc_html__('Question Hint', 'masterstudy-lms-learning-management-system'),
//						),
		)
	)
),
'stm_reviews'            => array(
	'section_data' => array(
		'name'   => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'review_course' => array(
				'type'    => 'select',
				'label'   => esc_html__('Course Reviewed', 'masterstudy-lms-learning-management-system'),
				'options' => $courses,
			),
			'review_user'   => array(
				'type'    => 'select',
				'label'   => esc_html__('User Reviewed', 'masterstudy-lms-learning-management-system'),
				'options' => $users,
			),
			'review_mark'   => array(
				'type'    => 'select',
				'label'   => esc_html__('User Review mark', 'masterstudy-lms-learning-management-system'),
				'options' => array(
					'5' => '5',
					'4' => '4',
					'3' => '3',
					'2' => '2',
					'1' => '1'
				)
			),
		)
	)
),
'stm_order_info'         => array(
	'order_info' => array(
		'name'   => esc_html__('Order', 'masterstudy-lms-learning-management-system'),
		'fields' => array(
			'order' => array(
				'type' => 'order',
			),
		)
	)
),
));
}

function get_fields($metaboxes)
{
	$fields = array();
	foreach ($metaboxes as $metabox_name => $metabox) {
		foreach ($metabox as $section) {
			foreach ($section['fields'] as $field_name => $field) {
				$sanitize = (!empty($field['sanitize'])) ? $field['sanitize'] : 'stm_lms_save_field';
				$fields[$field_name] = !empty($_POST[$field_name]) ? call_user_func(array($this, $sanitize), $_POST[$field_name], $field_name) : '';
			}
		}
	}

	return $fields;
}

function stm_lms_save_field($value)
{
	return $value;
}

function stm_lms_save_number($value)
{
	return floatval($value);
}

function stm_lms_save_dates($value, $field_name)
{
	global $post_id;

	$dates = explode(',', $value);
	if (!empty($dates) and count($dates) > 1) {
		update_post_meta($post_id, $field_name . '_start', $dates[0]);
		update_post_meta($post_id, $field_name . '_end', $dates[1]);
	}

	return $value;
}

function stm_lms_register_meta_boxes()
{
	$boxes = $this->boxes();
	foreach ($boxes as $box_id => $box) {
		$box_name = $box['label'];
		add_meta_box($box_id, $box_name, array($this, 'stm_lms_display_callback'), $box['post_type'], 'normal', 'high', $this->fields());
	}
}

function stm_lms_display_callback($post, $metabox)
{
	$meta = $this->convert_meta($post->ID);
	foreach ($metabox['args'] as $metabox_name => $metabox_data) {
		foreach ($metabox_data as $section_name => $section) {
			foreach ($section['fields'] as $field_name => $field) {
				$default_value = (!empty($field['value'])) ? $field['value'] : '';
				$value = (isset($meta[$field_name])) ? $meta[$field_name] : $default_value;
				if (!empty($value)) {
					switch ($field['type']) {
						case 'dates' :
						$value = explode(',', $value);
						break;
						case 'answers' :
						$value = unserialize($value);
						break;
					}
				}
				$metabox['args'][$metabox_name][$section_name]['fields'][$field_name]['value'] = $value;
			}
		}
	}
	include STM_LMS_PATH . '/post_type/metaboxes/metabox-display.php';
}

function convert_meta($post_id)
{
	$meta = get_post_meta($post_id);
	$metas = array();
	foreach ($meta as $meta_name => $meta_value) {
		$metas[$meta_name] = $meta_value[0];
	}

	return $metas;
}

function stm_lms_scripts($hook)
{
	$v = time();
	$base = STM_LMS_URL . '/post_type/metaboxes/assets/';
	$assets = STM_LMS_URL . 'assets';
	wp_enqueue_script('vue.js', $base . 'js/vue.min.js', array('jquery'), $v);
	wp_enqueue_script('vue-resource.js', $base . 'js/vue-resource.min.js', array('vue.js'), $v);
	wp_enqueue_script('vue2-datepicker.js', $base . 'js/vue2-datepicker.min.js', array('vue.js'), $v);
	wp_enqueue_script('vue-select.js', $base . 'js/vue-select.js', array('vue.js'), $v);
	wp_enqueue_script('vue2-editor.js', $base . 'js/vue2-editor.min.js', array('vue.js'), $v);
	wp_enqueue_script('vue2-color.js', $base . 'js/vue-color.min.js', array('vue.js'), $v);
	wp_enqueue_script('sortable.js', $base . 'js/sortable.min.js', array('vue.js'), $v);
	wp_enqueue_script('vue-draggable.js', $base . 'js/vue-draggable.min.js', array('sortable.js'), $v);
	wp_enqueue_script('stm_lms_mixins.js', $base . 'js/mixins.js', array('vue.js'), $v);
	wp_enqueue_script('stm_lms_metaboxes.js', $base . 'js/metaboxes.js', array('vue.js'), $v);

	wp_enqueue_style('stm-lms-metaboxes.css', $base . 'css/main.css', array(), $v);
	wp_enqueue_style('stm-lms-icons', STM_LMS_URL . 'assets/icons/style.css', array(), $v);
	wp_enqueue_style('linear-icons', $base . 'css/linear-icons.css', array('stm-lms-metaboxes.css'), $v);
	wp_enqueue_style('font-awesome-min', $assets . '/vendors/font-awesome.min.css', NULL, $v, 'all');

}

function stm_lms_post_types()
{
	return apply_filters('stm_lms_post_types', array(
		'stm-courses',
		'stm-lessons',
		'stm-quizzes',
		'stm-questions',
		'stm-reviews',
	));
}

function stm_lms_save($post_id, $post)
{

	
	$post_type = get_post_type($post_id);

	if (!in_array($post_type, $this->stm_lms_post_types())) return;

	if(!empty($_POST) and !empty($_POST['action']) and $_POST['action'] === 'editpost') {

		$fields = $this->get_fields($this->fields());

		if ($fields['sale_price'] != null) {
			$fields['saving'] = $fields['price'] - $fields['sale_price'];
		}else{
			$fields['saving'] = 0;
		}

		foreach ($fields as $field_name => $field_value) {
			update_post_meta($post_id, $field_name, $field_value);
		}
	}


}

function stm_search_posts()
{
	$r = array();

	$args = array(
		'posts_per_page' => 10,
	);

	if (!empty($_GET['post_types'])) {
		$args['post_type'] = explode(',', sanitize_text_field($_GET['post_types']));
	}

	if (!empty($_GET['s'])) {
		$args['s'] = sanitize_text_field($_GET['s']);
	}

	if (!empty($_GET['ids'])) {
		$args['post__in'] = explode(',', sanitize_text_field($_GET['ids']));
	}

	if (!empty($_GET['exclude_ids'])) {
		$args['post__not_in'] = explode(',', sanitize_text_field($_GET['exclude_ids']));
	}

	if (!empty($_GET['orderby'])) {
		$args['orderby'] = sanitize_text_field($_GET['orderby']);
	}

	if (!empty($_GET['posts_per_page'])) {
		$args['posts_per_page'] = sanitize_text_field($_GET['posts_per_page']);
	}

	$author = STM_LMS_User::get_current_user('', true);

	if(!in_array('administrator', $author['roles'])) {
		$args['author'] = $author['id'];
	}

	if(!empty($_GET['test'])) print_r($args);


	$q = new WP_Query($args);
	if ($q->have_posts()) {
		while ($q->have_posts()) {
			$q->the_post();

			$response = array(
				'id'        => get_the_ID(),
				'title'     => get_the_title(),
				'post_type' => get_post_type(get_the_ID())
			);

			if (in_array('stm-questions', $args['post_type'])) {
				$response = array_merge($response, $this->question_fields($response['id']));
			}

			$r[] = $response;
		}

		wp_reset_postdata();
	}

	$insert_sections = array();
	foreach ($args['post__in'] as $key => $post) {
		if (!is_numeric($post)) {
			$insert_sections[$key] = array('id' => $post, 'title' => $post);
		}
	}

	foreach ($insert_sections as $position => $inserted) {
		array_splice($r, $position, 0, array($inserted));
	}

	wp_send_json($r);
}

function get_question_fields()
{
	return array(
		'type'                 => array(
			'default' => 'single_choice',
		),
		'answers'              => array(
			'default' => array(),
		),
		'question'             => array(),
		'question_explanation' => array(),
		'question_hint'        => array(),
	);
}

function question_fields($post_id)
{
	$fields = $this->get_question_fields();
	$meta = array();

	foreach ($fields as $field_key => $field) {
		$meta[$field_key] = get_post_meta($post_id, $field_key, true);
		$default = (isset($field['default'])) ? $field['default'] : '';
		$meta[$field_key] = (!empty($meta[$field_key])) ? $meta[$field_key] : $default;
	}

	return $meta;
}

function stm_curriculum_create_item()
{
	$r = array();
	$available_post_types = array('stm-lessons', 'stm-quizzes', 'stm-questions');

	if (!empty($_GET['post_type'])) $post_type = sanitize_text_field($_GET['post_type']);
	if (!empty($_GET['title'])) $title = sanitize_text_field($_GET['title']);

	/*Check if data passed*/
	if (empty($post_type) and empty($title)) return;

	/*Check if available post type*/
	if (!in_array($post_type, $available_post_types)) return;

	$item = array(
		'post_type'   => $post_type,
		'post_title'  => wp_strip_all_tags($title),
		'post_status' => 'publish',
	);

	$r['id'] = wp_insert_post($item);
	$r['title'] = get_the_title($r['id']);
	$r['post_type'] = $post_type;

	if ($post_type == 'stm-questions') {
		$r = array_merge($r, $this->question_fields($r['id']));
	}

	wp_send_json($r);

}

function stm_curriculum_get_item()
{
	$post_id = intval($_GET['id']);
	$r = array();

	$r['meta'] = STM_LMS_Helpers::simplify_meta_array(get_post_meta($post_id));
	$r['content'] = get_post_field('post_content', $post_id);

	wp_send_json($r);
}

function stm_save_questions()
{
	$r = array();
	$request_body = file_get_contents('php://input');

	if (!empty($request_body)) {

		$fields = $this->get_question_fields();


		$data = json_decode($request_body, true);

		foreach ($data as $question) {

			if (empty($question['id'])) continue;
			$post_id = $question['id'];

			foreach ($fields as $field_key => $field) {
				if (!empty($question[$field_key])) {
					$r[$field_key] = update_post_meta($post_id, $field_key, $question[$field_key]);
				}
			}
		}
	}
	wp_send_json($r);
}

function stm_save_title()
{
	if (empty($_GET['id']) and !empty($_GET['title'])) return false;

	$post = array(
		'ID'         => intval($_GET['id']),
		'post_title' => sanitize_text_field($_GET['title']),
	);

	wp_update_post($post);

	wp_send_json($post);
}

function manage_posts() {
	$r = array(
		'posts' => array()
	);

	$args = array(
		'posts_per_page' => 10,
	);

	if (!empty($_GET['post_types'])) {
		$args['post_type'] = explode(',', sanitize_text_field($_GET['post_types']));
	}

	$args['post_status'] = (!empty($_GET['post_status'])) ? sanitize_text_field($_GET['post_status']) : 'all';
	$offset = (!empty($_GET['page'])) ? intval($_GET['page'] - 1) : 0;
	if(!empty($offset)) $args['offset'] = $offset * $args['posts_per_page'];

	if(!empty($_GET['meta'])) {
		$args['meta_query'] = array(
			array(
				'key' => sanitize_text_field($_GET['meta']),
				'compare' => 'EXISTS'
			)
		);
	}


	$r['args'] = $args;

	$q = new WP_Query($args);
	$r['total'] = intval($q->found_posts);
	$r['per_page'] = $args['posts_per_page'];
	$r['offset'] = $args['offset'];

	if ($q->have_posts()) {
		while ($q->have_posts()) {
			$q->the_post();

			$response = array(
				'id'        => get_the_ID(),
				'title'     => get_the_title(),
				'url'     => get_the_permalink(),
				'status' => get_post_status(),
				'edit_link' => get_edit_post_link(get_the_ID(), 'value')
			);

			$r['posts'][] = $response;
		}

		wp_reset_postdata();
	}

	wp_send_json($r);
}

function change_status() {

	if(!empty($_GET['post_id']) and !empty($_GET['status'])) {

		remove_action('save_post', array($this, 'stm_lms_save'), 10);
		$post_id = intval($_GET['post_id']);
		$status = sanitize_text_field($_GET['status']);

		$post = array(
			'post_type' => 'stm-courses',
			'ID' => $post_id,
			'post_status' => $status,
		);
		wp_update_post($post);

		add_action('save_post', array($this, 'stm_lms_save'), 10);
		wp_send_json($status);
	}
	die;
}
}

new STM_Metaboxes();

function stm_lms_metaboxes_deps($field, $section_name)
{
	$dependency = '';
	if (empty($field['dependency'])) return $dependency;

	$key = $field['dependency']['key'];
	$compare = $field['dependency']['value'];
	if ($compare === 'not_empty') {
		$dependency = "v-if=data['{$section_name}']['fields']['{$key}']['value']";
	} else {
		$dependency = "v-if=data['{$section_name}']['fields']['{$key}']['value'] == '{$compare}'";
	}

	return $dependency;
}