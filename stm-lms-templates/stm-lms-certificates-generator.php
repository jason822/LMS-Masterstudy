<?php
/**
 * @var $course_id
 */

$user = STM_LMS_User::get_current_user();
$username = $user['login'];

$certificate_image_id = STM_LMS_Options::get_option('certificate_image', '');
//$certificate_image = wp_get_attachment_image_url($certificate_image_id, 'img-1120-800');
$certificate_image = get_attached_file($certificate_image_id);

$certificate_stamp_id = STM_LMS_Options::get_option('certificate_stamp', '');

$certificate_title = STM_LMS_Options::get_option('certificate_title', esc_html__('Certificate', 'masterstudy-lms-learning-management-system'));
$certificate_color = STM_LMS_Options::get_option('certificate_title_color', 'rgba(0,0,0,1)');
$certificate_color = str_replace(array('rgba(', ')'), array(''), $certificate_color);
$certificate_color = explode(',', $certificate_color);


$certificate_text = STM_LMS_Options::get_option('certificate_text', '');

$certificate_text = str_replace(
	array('{username}', '{date}', '{course}'),
	array($username, 'date', get_the_title($course_id)),
	$certificate_text
);

$font = 'DejaVu';


require(STM_LMS_PATH .'/libraries/tfpdf/tfpdf.php');

$pdf = new tFPDF('L','pt','A4');

// Add a Unicode font (uses UTF-8)
//$pdf->AddFont('Open Sans','','opensans.php');
//$pdf->AddFont('Open Sans','B','opensansbd.php');
//$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->AddFont($font,'','OpenSans-Regular.ttf',true);
$pdf->SetFont($font,'',14);


$pdf->SetTopMargin(20); $pdf->SetLeftMargin(20); $pdf->SetRightMargin(20);
$pdf->AddPage();

if(!empty($certificate_image)) {
	$pdf->Image($certificate_image, 0, 0, 850, 600);
}


$pdf->SetTextColor($certificate_color[0], $certificate_color[1], $certificate_color[2]);
$pdf->SetFont($font,'',60);
$pdf->SetXY(40,100);
$pdf->Multicell(760,50,$certificate_title,0,'C',0);

$pdf->SetTextColor($certificate_color[0], $certificate_color[1], $certificate_color[2]);
$pdf->SetFont($font,'',40);
$pdf->SetXY(40,205);
$pdf->Multicell(760,50,$username,0,'C',0);

$pdf->SetFont($font,'',17);
$pdf->SetXY(190,290);
$pdf->Multicell(460,24, $certificate_text,0,'C',0);

$pdf->Output();