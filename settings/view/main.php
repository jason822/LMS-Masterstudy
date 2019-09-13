<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

?>


<?php $title = (!empty($title)) ? $title : esc_html__('LMS Settings', 'masterstudy-lms-learning-management-system'); ?>
<h1><?php echo sanitize_text_field($title); ?></h1>

<?php
$id = $metabox['id'];
$sections = $metabox['args'][$id];

$data_vue = "data-vue='" . str_replace('\'', '`',json_encode($sections)) . "'";
?>

<div class="stm-lms-settings" <?php echo ($data_vue); ?>>

    <?php require_once(STM_LMS_PATH . '/post_type/metaboxes/metabox-display.php'); ?>


    <div class="stm_metaboxes_grid">
        <div class="stm_metaboxes_grid__inner">
            <a href="#"
               @click.prevent="saveSettings('<?php echo esc_attr($id); ?>')"
               v-bind:class="{'loading': loading}"
               class="button load_button">
                <span><?php esc_html_e('Save Settings', 'masterstudy-lms-learning-management-system'); ?></span>
                <i class="lnr lnr-sync"></i>
            </a>
        </div>
    </div>

</div>