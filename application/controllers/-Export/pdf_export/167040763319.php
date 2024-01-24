<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// new

error_reporting(E_ALL);
ini_set("display_errors", 1);

$config = [
    'mode' => '-aCJK', 
    "autoScriptToLang" => true,
    "autoLangToFont" => true,
];

$mpdf = new \Mpdf\Mpdf($config);

ob_start();

?>
<html>
<head>
	<meta charset="utf-8">
	<title>Receipt</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC&display=swap" rel="stylesheet">

	<style type="text/css">

	</style>

</head>
<body>
	<table style="width:100%" style="margin-bottom:50px;">
		<tr>
			<td rowspan="2" width="20%"><img src="<?php echo empty($image) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : $image; ?>" style="height:80px; width:80px;"></td>
			<td colspan="" style="font-weight:bold; font-size:18px;"><?php echo $branch['title']; ?></td>
			<td style="font-weight:bold; font-size:18px;" width="30%">OFFICIAL RECEIPT</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="text-align:left">Phone</td>
						<td>: <?php echo empty($branch['phone']) ? '-' : $branch['phone'] ; ?></td>
					</tr>
					<tr>
						<td style="">Email</td>
						<td>: <?php echo empty($branch['email']) ? '-' : $branch['email'] ; ?></td>
					</tr>
					<!--<tr>
						<td style="text-align:left">Website</td>
						<td>:</td>
					</tr>-->
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td style=""></td>
						<td></td>
					</tr>
					<tr>
						<td style="text-align:left">Receipt No</td>
						<td>: <?php echo $payment['payment_no']; ?></td>
					</tr>
					<tr>
						<td style="">Date</td>
						<td>: <?php echo $payment['date']; ?></td>
					</tr>
					<tr>
						<td style=""></td>
						<td></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table style="font-size:15px; width:100%; color:#212121; border-bottom:1px solid black; padding-bottom:20px">
		<tr>
			<td width="15%">Bill To</td>
		</tr>
		<tr>
			<td>Parent</td>
			<td>: <?php echo empty(datalist_Table('tbl_users', 'parent', $student['pid'])) ? '-' : datalist_Table('tbl_users', 'fullname_en', datalist_Table('tbl_users', 'parent', $student['pid'])); ?></td>
		</tr>
		<tr>
			<td>Contact</td>
			<td>: <?php echo empty($student['phone']) ? '-' : $student['phone'] ; ?></td>
		</tr>
		<tr>
			<td>Student</td>
			<td>:&nbsp;
			<?php echo empty($student['fullname_en']) ? '-' : $student['fullname_en'] ; ?>
			<?php echo empty($student['code']) ? '-' : $student['code'] . '&nbsp;&nbsp;' ; ?>
			<?php echo empty($student['form']) ? '-' : datalist_Table('tbl_secondary', 'title', $student['form']) . '<br>' ; ?>
			</td>
		</tr>
		<tr>
			<td>Student IC</td>
			<td>: <?php echo empty($student['nric']) ? '-' : $student['nric'] ; ?></td>
		</tr>
	</table>
	<table style="font-size:16px; font-weight:bold; width:100%; border-bottom:1px solid black">
		<tr>
			<td width="10%">No</td>
			<td width="45%">Description</td>
			<td width="15%" style="text-align:right">Unit Price</td>
			<td width="15%" style="text-align:right">Quantity</td>
			<td width="15%" style="text-align:right">Amount</td>
		</tr>
	</table>
	<table style="font-size:15px; width:100%;">
		<?php $qty = 0; $i=0; foreach($log_payment as $e) { $i++; $qty += $e['qty']; ?>
			<tr>
				<td width="10%" style="text-align:left"><?php echo $i;  ?></td>
				<td width="45%">
					<?php
					echo $e['title'];
					if($e['remark'] != '') {
						echo '<br><span style="font-size: 12px;"><i>'.$e['remark'].'</span></i>';
					}
					?>
				</td>
				<td width="15%" style="text-align:right"><?php echo number_format($e['price_unit'], 2, '.', ','); ?></td>
				<td width="15%" style="text-align:right"><?php echo $e['qty']; ?></td>
				<td width="15%" style="text-align:right"><?php echo number_format($e['price_amount'], 2, '.', ','); ?></td>
			</tr>
		<?php } ?>
	</table>
	<footer style="position:fixed; bottom:0;">
		<table width="100%" style="border-bottom:1px solid black">
			<tr>
				<td width="60%"><br></td>
				<td width="12%" style="font-weight:bold">Total Qty<td>
				<td width="1%">:<td>
				<td width="19%" style="font-weight:bold" style="text-align:right"><?php echo $qty; ?><td>
			</tr>
		</table>
		<table width="100%" style="color:#424242">
			<tr>
				<td width="20%">Payment Method</td>
				<td width="2%">:</td>
				<td width="38%"><?php echo datalist_Table('tbl_secondary', 'title', $payment['payment_method']); ?></td>
				<td width="12%">Subtotal<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right">RM<?php echo number_format($payment['subtotal'], 2, '.', ','); ?><td>
			</tr>
			<tr>
				<td width="20%">Cashier</td>
				<td width="2%">:</td>
				<td width="38%"><?php echo $cashier; ?></td>
				<td width="12%">Discount<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right">
					RM<?php
						if($payment['discount_type'] == '%') {
							echo number_format(($payment['subtotal'] * $payment['discount'] / 100), 2, '.', ',');
						} else {
							echo number_format($payment['discount'], 2, '.', ','); 
						}
					?>
				<td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td width="2%"></td>
				<td width="38%"></td>
				<td width="12%">Deduction<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right">RM<?php echo number_format(abs($payment['adjust']), 2, '.', ','); ?><td>
			</tr>
		</table>
		<table width="100%" style="color:#424242">
			<tr>
				<td width="60%">Computer Generated no signature required.</td>
				<td width="12%" style="font-weight:bold">Net Total<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right; font-weight:bold">RM<?php echo number_format($payment['total'], 2, '.', ','); ?><td>
			</tr>
		</table>
		<p><br>All Payment are <b>NOT REFUNDABLE.</b></p>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle('inv.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output('inv.pdf', 'I');