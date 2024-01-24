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
	<title><?php echo $payment['payment_no']; ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC&display=swap" rel="stylesheet">

	<style type="text/css">

		body {
			margin: 0;
			padding: 0;
			font-family: 'Arial', 'Noto Sans SC', sans-serif!important;
			width: 210mm;
			min-height: 297mm;
			padding: 20mm;
			margin: 10mm auto;
			border: 1px solid black	;
			position: relative;
		}

		h1 {font-size: 24px;}
		h2 {font-size: 18px;}

		span {
			display: block;
			padding-top: 0.2rem;
			font-size: 14px;
		}
		
		td {
			vertical-align: top;
		}

	</style>
</head>
<body>
	<header>
		<table style="width: 100%;">
			<tr>
				<?php if ($payment['receipt_display'] == 0) { ?>
					<td style="vertical-align: middle; white-space: nowrap; width:90px;"><img src="<?php echo empty($payment['image']) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : $payment['image']; ?>" style="height: 100px; width: 100px;"></td>
					<td style="vertical-align: middle; padding-left: 1rem;">
						<h2 name="company-name"><?php echo $payment['branch_title']; ?></h2>
						<span name="company-email">Email: <?php echo empty($payment['branch_email']) ? '-' : $payment['branch_email'] ; ?></span><br>
						<span name="company-tel">Phone: <?php echo empty($payment['branch_phone']) ? '-' : $payment['branch_phone'] ; ?></span><br>
						<span name="company-email">Address: <?php echo empty($payment['branch_address']) ? '-' : $payment['branch_address'] ; ?></span><br>
					</td>
				<?php } else { ?>
					<td style="vertical-align: middle; white-space: nowrap; width:90px;"></td>
					<td style="vertical-align: middle; padding-left: 1rem;"></td>
				<?php } ?>
				<td style="vertical-align: middle; text-align: right;">
					<h1>Receipt</h1>
					<span name="receipt-no">Receipt No: <?php echo $payment['payment_no']; ?></span><br>
					<span name="date">Date: <?php echo $payment['date']; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<span></span><br>
					<span></span><br>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<span>Bill To</span><br>
					<span>
					<b>
					<?php echo $payment['client'] . '<br>'; ?>
					<?php echo empty($payment['student_code']) ? '-' : $payment['student_code'] . '&nbsp;&nbsp;' ; ?>
					<?php echo empty($payment['student_code']) ? '-' : $payment['form_title'] . '<br>' ; ?>
					</b>
					</span><br>
					<span><?php echo empty($payment['client_phone']) ? '-' : $payment['client_phone']; ?></span>
				</td>
			</tr>
		</table>
		<br><br>
		<table style="width: 100%;">
			<tr>
				<th style="width: 10%; text-align: left;"><span>Type</span></th>
				<th style="width: 40%; text-align: left;"><span>Description</span></th>
				<th style="width: 20%; text-align: right;"><span>Unit Price</span></th>
				<th style="width: 10%; text-align: right;"><span>Qty</span></th>
				<th style="width: 20%; text-align: right;"><span>Amount</span></th>
			</tr>
			<tr><td colspan="5"><hr style="margin: 0;"></td></tr>
			<?php
			
			foreach($log_payment as $e) { 
				?>
				<tr style="border-bottom: 1px solid black;">
					<td>
						<?php
						if(!empty($e['item'])) {
							echo empty($e['period']) ? 'Item' : 'Service';
						} else {
							echo 'Class';
						}
						?>
					</td>
					<td>
						<?php
						echo $e['title'];
						if($e['remark'] != '') {
							echo '<br><span style="font-size: 12px;"><i>'.$e['remark'].'</span></i>';
						}
						?>
					</td>
					<td style='text-align: right;'><?php echo number_format($e['price_unit'], 2, '.', ','); ?></td>
					<td style='text-align: right;'><?php echo $e['qty']; ?></td>
					<td style='text-align: right;'><?php echo number_format($e['price_amount'], 2, '.', ','); ?></td>
					
				</tr>
				<?php
			}
			
			?>
		</table>
	</header>

	<footer style="position: absolute; bottom: 0;  padding: 20mm 20mm 10mm 0;">
		<table style="width: 100%;">
			<tr>
				<td colspan="3"><hr style="margin: 0;"></td>
			</tr>
			<tr>
				<td style="width: 60%;">
					<span>Payment Method: <?php echo $payment['payment_method_title']; ?></span><br>
					<span>Cashier: <?php echo $payment['cashier']; ?></span><br>
					<span>Computer Generated no signature required</span><br>
					<span>All Payments are NOT REFUNDABLE</span><br>
					<?php if($payment['remark'] != '') { ?>
						<span>Remark:<br><?php echo $payment['remark']; ?></span>
					<?php } ?>
				</td>
				<td>
					<span>Sub Total</span><br>
                    <? if($payment['material_fee'] != 0):?>
                            <span>Material Fee</span><br>
                    <? endif;?>
					<span>Discount</span><br>
					<?php
					if($payment['adjust'] != 0) {
						if(empty($payment['adjust_label'])) {
							?>
							<span>Adjustment</span><br>
							<?php
						} else {
							echo '<span>'.$payment['adjust_label'].'</span><br>';
						}
					}
					?>
					<span>Tax</span><br>
					<span>Total</span><br>
                    <span>Receive</span><br>
                    <span>Change</span>
				</td>
				<td style="width: 10%; text-align: right;">
					<span><?php echo number_format($payment['subtotal'], 2, '.', ','); ?></span><br>
                    <? if($payment['material_fee'] != 0):?>
                        <span><?php echo number_format($payment['material_fee'], 2, '.', ','); ?></span><br>
                    <? endif;?>
					<span>
						<?php
						if($payment['discount_type'] == '%') {
							echo "-".number_format(($payment['subtotal'] * $payment['discount'] / 100), 2, '.', ',');
						} else {
							echo "-".number_format($payment['discount'], 2, '.', ',');
						}
						?>
					</span><br>
					<?php if($payment['adjust'] != 0) { ?>
						<span><?php echo number_format($payment['adjust'], 2, '.', ','); ?></span><br>
					<?php } ?>
					<span><?php echo number_format($payment['tax'], 2, '.', ','); ?> </span><br>
					<span><?php echo number_format($payment['total'], 2, '.', ','); ?></span>
                    <span><?php echo number_format($payment['receive'], 2, '.', ','); ?> </span><br>
                    <span><?php echo number_format($payment['change'], 2, '.', ','); ?> </span><br>
				</td>
			</tr>
		</table>
		<p><em>** This is auto generated by Robocube Tuition System on <?php echo date('M d, Y'); ?></em></p>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle($payment['payment_no'].'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($payment['payment_no'].'.pdf', 'I');
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

// $lpr = new PrintSendLPR(); 
// $lpr->setHost("192.168.1.26"); //Put your printer IP here 
// // $lpr->setData("./uploads/pdf/".$payment['pid'].".pdf"); //Path to file, OR string to print
// $lpr->setData("test"); //Path to file, OR string to print
// $lpr->printJob("someQueue");