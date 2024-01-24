<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// new
error_reporting(E_ALL);
ini_set("display_errors", 1);

$mpdf = new \Mpdf\Mpdf([
	'mode' => '-aCJK', 
	"autoScriptToLang" => true,
	"autoLangToFont" => true,
]);

$mpdf->SetFont('Microsoft YaHei');

$height	= empty($payment['customer']) ? 250 : 270;
$payment_method = ceil(strlen(datalist_Table('tbl_secondary', 'title', $payment['payment_method'])) / 22);
$height += (5.5 * $payment_method);
foreach($log_payment as $e) {
	$height += 6;
	$ori_length = mb_strlen($e['title']);
	$after_length = mb_strlen(preg_replace("/\p{Han}+/u", '', $e['title']));
	$chinese_length = $ori_length - $after_length;
	$row = ceil($chinese_length / 15) + ceil($after_length / 22);
	$height += (6 * $row);
	if(!empty($e['dis_amount'])) {
		$height += 6;
	}
}

ob_start();

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $payment['payment_no']; ?></title>
	<style>
		@page {
			sheet-size: 80mm <?php echo $height; ?>mm;
			margin: 0;
		}
		
		* {
			page-break-inside: avoid;
		}
	</style>
</head>
<body style="margin: 0;">
	<?php if (!$payment['is_draft']) { ?>
		<table style="width: 100%; padding: 10px; margin: 0 auto;">
			<tr>
				<td style="width: 20%;text-align: center;padding-left: 10px;padding-right: 10px;">
					<img style="width: 20%;" src="<?php echo $image; ?>">
				</td>
				<td style="text-align: center;"><?php echo $branch['title'] ?></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;font-size: 14px;padding-left: 20px;padding-right: 20px;"><?php echo $branch['address'] ?></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center;font-size: 14px;padding-left: 20px;padding-right: 20px;">Tel: <?php echo $branch['phone'] ?></td>
			</tr>
		</table>
	<?php } ?>
	<table style="width: 100%; padding: 10px; margin: 0 auto;">
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2" style="text-align: center; font-size: 25px;padding-bottom: 5px;">RECEIPT</td></tr>
		<tr><td colspan="2">Code/单号: <?php echo $payment['payment_no']; ?></td></tr>
		<tr><td colspan="2">Date/日期: <?php echo $payment['date'] ?></td></tr>
		<tr><td colspan="2">Student/学生: <?php echo $payment['student_info']; ?></td></tr>
		<tr><td colspan="2">Created By/开单老师: <?php echo $payment['created_by_teacher']; ?></td></tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2" style="font-weight: bold;">Knock-off Charges(s)/付款项目</td></tr>
		<tr><td colspan="2" style="text-align: center;">------------------------------------------------------------</td></tr>
		<?php $i = 1; 
			foreach($log_payment as $e) { ?>
			<tr>
				<td colspan="2"><?php echo $i; ?>) <?php echo $e['title']; ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">RM <?php echo number_format($e['price_unit'], 2, '.', ','); ?> x <?php echo $e['qty']; ?></td>
				<td style="text-align: right;font-weight: bold;">RM <?php echo number_format($e['price_amount'], 2, '.', ','); ?></td>
			</tr>
			<?php if (count($log_payment) != $i) { ?>
				<tr><td colspan="2" style="text-align: right;">&nbsp;</td></tr>
			<?php } ?>
		<?php $i++;
			} ?>
		<tr><td colspan="2"><br></td></tr>
		<?php if($payment['discount'] >0){ ?>
		<tr>
			<td style="font-weight: bold;">Discount 折扣</td>
			<?php $discount = ($payment['discount_type'] == '$')$payment['discount']?:$payment['subtotal']-($payment['subtotal']*$payment['discount']/100)?>
			<td style="width: 35%; text-align: right;font-weight: bold;">RM -<?php echo number_format($discount, 2, '.', ','); ?></td>
		</tr>
		<?php } ?>
		<?php if($payment['material_fee'] >0){ ?>
		<tr>
			<td style="font-weight: bold;">Material 素材</td>
			<td style="width: 35%; text-align: right;font-weight: bold;">RM <?php echo number_format($payment['material_fee'], 2, '.', ','); ?></td>
		</tr>
		<?php } ?>
		<?php if($payment['transport_fee'] >0){ ?>
		<tr>
			<td style="font-weight: bold;">Transportation 车费</td>
			<td style="width: 35%; text-align: right;font-weight: bold;">RM <?php echo number_format($payment['transport_fee'], 2, '.', ','); ?></td>
		</tr>
		<?php } ?>
		<?php if($payment['childcare_fee'] >0){ ?>
		<tr>
			<td style="font-weight: bold;">Childcare 安亲班</td>
			<td style="width: 35%; text-align: right;font-weight: bold;">RM <?php echo number_format($payment['childcare_fee'], 2, '.', ','); ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td style="font-weight: bold;">TOTAL PAYABLE 总数</td>
			<td style="width: 35%; text-align: right;font-weight: bold;">RM <?php echo number_format($payment['total'], 2, '.', ','); ?></td>
		</tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2" style="font-weight: bold;">Payment(s)/付费</td></tr>
		<tr><td colspan="2" style="text-align: center;">------------------------------------------------------------</td></tr>
		<tr>
			<td colspan="2">1) <?php echo $payment['payment_method_title']; ?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; font-weight: bold;">RM <?php echo number_format($payment['total'], 2, '.', ','); ?></td>
		</tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2"><br></td></tr>
		<tr>
			<td style="font-weight: bold;">TENDER 收到</td>
			<td style="text-align: right; font-weight: bold;">RM <?php echo number_format($payment['receive'], 2, '.', ','); ?></td>
		</tr>
		<tr>
			<td style="font-weight: bold;">CHANGE 找钱</td>
			<td style="text-align: right; font-weight: bold;">RM <?php echo number_format($payment['change'], 2, '.', ','); ?></td>
		</tr>
		<tr>
			<td style="font-weight: bold;">ADV. PAYMENT 预付</td>
			<td style="text-align: right; font-weight: bold;">RM <?php echo number_format(0, 2, '.', ','); ?></td>
		</tr>
		<tr>
			<td style="font-weight: bold;">OUTSTANDING 尚欠款项</td>
			<td style="text-align: right; font-weight: bold;">RM <?php echo number_format(0, 2, '.', ','); ?></td>
		</tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2">Remark/注: BANK IN 3609806374</td></tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2" style="text-align: center; font-size: 30px;padding-bottom: 5px;">Thank You 谢谢</td></tr>
	</table>
</body>
</html>
<?php

$data = ob_get_clean();

$mpdf->SetTitle($payment['payment_no'].'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($payment['payment_no'].'.pdf', 'I'); return;

// $mpdf->Output("./uploads/pdf/".$payment['pid'].".pdf", 'F');

// $file = "./uploads/pdf/".$payment['pid'].".pdf";

// if (file_exists($file)) {
//     // Response
//     // Set the response headers
//     header('Content-Type: application/pdf');
//     header('Content-Disposition: attachment; filename="' . basename($file) . '"');
//     header('Content-Length: ' . filesize($file));
    
//     // Read the file and output its contents
//     readfile($file);
// } else die('File not found');
