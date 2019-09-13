<?php
/**
 * Template for displaying course filter within the loop.
 *
 * @author  Chenglu
 * @package MasterStudy/Templates
 * @version 3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$postid   = get_the_ID();


?>

<article class="search-result row" id="post-<?php the_ID(); ?>">
	<div class="col-xs-12 col-sm-12 col-md-3">
		<a class="" href="<?php the_permalink() ?>"><?php STM_LMS_Templates::show_lms_template('courses/parts/image', array('id' => $id)); ?></a>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-9 excerpet">
		<span style="font-size: 20px; font-weight: 600;"><?php the_title() ?></span>
		<h3 id="total_price">
			<?php if (isset($_GET['weeks']) && ($_GET['weeks'] != '') && $price != '') {
				if ($has_sale_price == 'has-sale') {
					echo ($sale_price * intval($_GET['weeks']));
				}else{
					echo ($price * intval($_GET['weeks']));
				}
			}else if (isset($_GET['weeks']) && $_GET['weeks'] == '' && $price != ''){
				echo $sale_price;
			} else {
				echo $sale_price;
			}
			 ?>
		</h3>
		<div class="price_box">
			<table class="course_detail_by_search">
				<thead >
					<tr style="border: none;">
						<?php if ($has_weeks_option == 'has-weeks') {
							?>
						<th>Usual weekly Price</th>
						<th>Our weekly Price</th>
						<?php
						}else{
							?>
							<th>Usual Price</th>
							<th>Our Price</th>
							<?php
						} 
						?>
						<th>Your total saving</th>
					</tr>
				</thead>
				<tbody>
					<tr style="border: none;">
						<td>
							<?php if (empty($price) and empty($sale_price)): ?>
					        <strong><?php esc_html_e('Free', 'masterstudy-lms-learning-management-system'); ?></strong>
					    <?php elseif (!empty($price) and !empty($sale_price)): ?>
					        <span><?php echo STM_LMS_Helpers::display_price($price); ?></span>
					    <?php else: ?>
					        <strong><?php echo STM_LMS_Helpers::display_price($price); ?></strong>
					    <?php endif; ?>
						</td>
						<td>
							<?php if (empty($price) and empty($sale_price)): ?>
					        <strong><?php esc_html_e('Free', 'masterstudy-lms-learning-management-system'); ?></strong>
					    <?php elseif (!empty($price) and !empty($sale_price)): ?>
					        <strong><?php echo STM_LMS_Helpers::display_price($sale_price); ?></strong>
					    <?php else: ?>
					        <strong><?php echo STM_LMS_Helpers::display_price($price); ?></strong>
					    <?php endif; ?>
						</td>
						<td style="font-size: 20px; color: #f52611">
							<?php if (empty($price) and empty($sale_price)): ?>
					        <strong><?php esc_html_e('Free', 'masterstudy-lms-learning-management-system'); ?></strong>
					    <?php elseif (!empty($price) and !empty($sale_price)): ?>
					        <strong><?php echo STM_LMS_Helpers::display_price($price- $sale_price); ?></strong>
					    <?php else: ?>
					        <strong><?php echo STM_LMS_Helpers::display_price($price); ?></strong>
					    <?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>						

	</div>
	<span class="clearfix borda"></span>
	<div class="course_desc" style="color: #575c61; font-weight: 400; margin: 1em;">
		<?php
		echo stm_lms_minimize_word(wp_kses_post(strip_shortcodes(get_the_excerpt())), 150, '...');
		?>
	</div>
	<a href="<?php the_permalink() ?>" target="blank"><input type="button" class="btn btn-default btn-sm" style="font-weight: 650" value="READ MORE" /></a>
</article>
