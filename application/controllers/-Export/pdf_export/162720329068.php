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
		h2 {font-size: 20px;}

		span {
			display: block;
			padding-top: 0.2rem;
		}
		
		hr {
			margin: 2px 0;
			border: 0;
			color: black;
			height: 1px;
		}

		.content, .content tr, .content td{
			border-collapse: collapse;
			border-spacing: 0;
			border: 1px solid black;
		}
		
		.content td {
			padding: 3px 5px 3px 10px;
		}

		.content-bottom td {
			padding: 10px 0 0;
		}
		
		td {
			vertical-align: top;
		}

	</style>

</head>
<body>
	<header>
		<table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
			<tr>
				<td>
					<h1 style="color: #A7AABB;">SERVICES INVOICE</h1>
				</td>
			</tr>

			<tr><td><br></tr>

			<tr>
				<td colspan="2" style="vertical-align: middle;">
					<img src="<?php echo empty($image) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : $image; ?>" width="90px">
				</td>
				<td colspan="2" style="vertical-align: middle; text-align: right;">
					<!--<h2 style="font-weight: normal;">SERVICES INVOICE</h2>-->
				</td>
			</tr>

			<tr><td></td><br></tr>

			<tr>
				<td style="width: 130px;"></td>
				<td style="width: 100px; vertical-align: bottom; text-align: left; border-bottom: 1px solid black;">
					<span>INVOICE NO.</span>
				</td>
				<td style="width: 120px; vertical-align: bottom; text-align: right; border-bottom: 1px solid black;">
					<span name="invoice-no"><?php echo $payment['payment_no']; ?></span>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					<span name="company-adr"><?php echo $branch['address']; ?></span><br>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					<span name="company-tel">Phone: <?php echo empty($branch['phone']) ? '-' : $branch['phone'] ; ?></span><br>
				</td>
				<td></td>
				<td style="vertical-align: bottom; text-align: left; border-bottom: 1px solid black;">
					<span>CUSTOMER ID</span>
				</td>
				<td style="vertical-align: bottom; text-align: right; border-bottom: 1px solid black	;">
					<span name="customer-id">#<?php echo $payment['student']; ?></span>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					<span name="company-email">Email: <?php echo empty($branch['email']) ? '-' : $branch['email'] ; ?></span><br>
				</td>
			</tr>
			<tr>
				<td>
					<span></span><br>
					<span></span><br>
				</td>
			</tr>
			<tr>
				<td>
					<span><b>BILL TO</b></span><hr>
					<span>
					<b>
					<?php echo $client . '<br>'; ?>
					<?php echo empty(datalist_Table('tbl_users', 'code', $payment['student'])) ? '-' : datalist_Table('tbl_users', 'code', $payment['student']) . '&nbsp;&nbsp;' ; ?>
					<?php echo empty(datalist_Table('tbl_users', 'form', $payment['student'])) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'form', $payment['student'])) . '<br>' ; ?>
					</b>
					</span><br>
					<span name="company-adr"><?php echo $client_address; ?></span><br>
					<span name="company-tel"><?php echo empty($client_phone) ? '-' : $client_phone; ?></span><br>
					<span name="company-tel"><?php echo empty($client_email) ? '-' : $client_email; ?></span>
				</td>
				<td colspan="3">
					
				</td>
			</tr>
		</table>
	</header>
	<br><br>
	<table class="content" style="width: 100%; border: 0;">
		<tr style="border: 0;">
			<th style="width: 10%; text-align: left;"><span>Type</span></th>
			<th style="width: 40%; text-align: left;"><span>Description</span></th>
			<th style="width: 10%; text-align: right;"><span>Qty</span></th>
			<th style="width: 20%; text-align: right;"><span>Unit Price</span></th>
			<th style="width: 20%; text-align: right;"><span>Total</span></th>
		</tr>
		
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
						echo '<br><i><span style="font-size: 12px;">'.$e['remark'].'</span></i>';
					}
					?>
				</td>
				<td style='text-align: right;'><?php echo $e['qty']; ?></td>
				<td style='text-align: right;'><?php echo number_format($e['price_unit'], 2, '.', ','); ?></td>
				<td style='text-align: right;'><?php echo number_format($e['price_amount'], 2, '.', ','); ?></td>
				
			</tr>
			<?php
		}
		
		?>
		
	</table>

	<table class="content-bottom" style="width: 100%;">
		<tr style="border: 0;">
			<td style="width: 50%; text-align: left;"><span><?php if($payment['remark'] != '') { ?>
			<p>Remark: <?php echo $payment['remark']; ?></p>
		<?php } ?></span></td>
			<td style="width: 30%; text-align: right; padding-right: 5px;"><span style="font-weight: bold;">SUBTOTAL</span></td>
			<td style="width: 20%; text-align: right; border-bottom: 1px solid black;"><span><?php echo number_format($payment['subtotal'], 2, '.', ','); ?></span></td>
		</tr>
		<tr style="border: 0;">
			<td style="width: 50%; text-align: left;"></td>
			<td style="width: 30%; text-align: right; padding-right: 5px;"><span style="font-weight: bold;">DISCOUNT</span></td>
			<td style="width: 20%; text-align: right; border-bottom: 1px solid black;">
				<span>
					<?php
						if($payment['discount_type'] == '%') {
							echo number_format(($payment['subtotal'] * $payment['discount'] / 100), 2, '.', ',');
						} else {
							echo number_format($payment['discount'], 2, '.', ','); 
						}
					?>
				</span>
			</td>
		</tr>
		<tr style="border: 0;">
			<td style="width: 50%; text-align: left;"></td>
			<td style="width: 30%; text-align: right; padding-right: 5px;"><span style="font-weight: bold;">SUBTOTAL LESS DISCOUNT</span></td>
			<td style="width: 20%; text-align: right; border-bottom: 1px solid black;"><?php echo number_format($payment['total'], 2, '.', ','); ?></td>
		</tr>
		<tr style="border: 0;">
			<td style="width: 50%; text-align: left;"></td>
			<td style="width: 30%; text-align: right; padding-right: 5px;"><span style="font-weight: bold;">TAX</span></td>
			<td style="width: 20%; text-align: right; border-bottom: 1px solid black;"><span><?php echo number_format($payment['tax'], 2, '.', ','); ?></span></td>
		</tr>
		<tr style="border: 0;">
			<td style="width: 60%; text-align: center;"><span><i>Please make check payable to your name</i></span></td>
			<td style="width: 20%; text-align: right; padding-right: 5px;">
				<?php
				if($payment['adjust'] != 0) {
					if(empty($payment['adjust_label'])) {
						?>
						<span style="font-weight: bold;">ADJUSTMENT</span>
						<?php
					} else {
						echo '<span style="font-weight: bold;">'.$payment['adjust_label'].'</span>';
					}
				}
				?>
			</td>
			<?php if($payment['adjust'] != 0) { ?>
				<td style="width: 20%; text-align: right; border-bottom: 1px solid black;">
					<span><?php echo number_format($payment['adjust'], 2, '.', ','); ?></span>
				</td>
			<?php } else { ?>
				<td></td>
			<?php } ?>
		</tr>
		<tr rowspan="2" style="border: 0;">
			<td style="width: 50%; text-align: center;" rowspan="2"><h1><b>THANK YOU</b></h1></td>
			<td style="width: 30%; text-align: right; padding-right: 5px; vertical-align: top;"><span style="font-weight: bold;">TOTAL</span></td>
			<td style="width: 20%; border-bottom: 1px solid black; text-align: right; vertical-align: top;"><span><?php echo number_format($payment['total'], 2, '.', ','); ?></span></td>
		</tr>
		<tr><td></td><td></td><td></td></tr>
	</table>

	<footer style="position: absolute; bottom: 0; padding: 20mm 20mm 10mm 0;">
		<table style="width: 100%;">
			<tr>
				<td style="text-align: center;"><i>For questions concerning this invoice, please contact <b><?php echo $owner; ?></b>, <?php echo empty($branch['phone']) ? '-' : $branch['phone']; ?>, <?php echo empty($branch['email']) ? '-' : $branch['email']; ?>!</i><br><br>
				<p>** This is auto generated by Robocube Tuition System on <?php echo date('F Y, d'); ?></p>
				</td>
			</tr>
		</table>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle($payment['payment_no'].'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($payment['payment_no'].'.pdf', 'I');