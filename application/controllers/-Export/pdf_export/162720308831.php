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
		h2 {font-size: 16px;}

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
				<td style="vertical-align: middle; white-space: nowrap; width:90px;"><img src="<?php echo empty($image) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' :$image ; ?>" width="90px"></td>
				<td colspan="5" style="vertical-align: middle; padding-left: 1.5rem;">
					<h1 name="company-name"><?php echo $branch["title"]; ?></h1>
					<span name="company-email">Email: <?php echo empty($branch['email']) ? '-' : $branch['email'] ; ?></span><br>
					<span name="company-tel">Phone: <?php echo empty($branch['phone']) ? '-' : $branch['phone'] ; ?></span><br>
					<span name="company-email">Address: <?php echo empty($branch['address']) ? '-' : $branch['address'] ; ?></span><br>
				</td>
			</tr>
			<tr>
				<td>
					<span></span><br>
					<span></span><br>
				</td>
			</tr>
			<tr>
				<td colspan="3" rowspan="3">
					<span>Bill To</span><br>
					<span>
					<b>
					<?php echo $client . '<br>'; ?>
					<?php echo empty(datalist_Table('tbl_users', 'code', $payment['student'])) ? '-' : datalist_Table('tbl_users', 'code', $payment['student']) . '&nbsp;&nbsp;' ; ?>
					<?php echo empty(datalist_Table('tbl_users', 'form', $payment['student'])) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'form', $payment['student'])) . '<br>' ; ?>
					</b>
					</span><br>
					<span><?php echo empty($client_phone) ? '-' : $client_phone; ?></span>
				</td>
			<tr>
				<td colspan="5"></td>
				<td style="background-color: #48565F; color: white; vertical-align: middle; padding: 0 5px;">
					<h2>SALES RECEIPT <?php echo $payment['payment_no']; ?></h2>
				</td>
			</tr>
			<tr>
				<td colspan="5"></td>
				<td style="background-color: #48565F; color: white; vertical-align: middle; padding: 0 5px;">
					<h2>DATE <?php echo $payment['date']; ?></h2>
				</td>
			</tr>
			<tr>
				<td colspan="8" style="padding-right: 10px; vertical-align: middle;"><hr style="margin: 0; padding: 0; height: 5px; color: #48565F;"></td>
				<td style="background-color: #48565F; color: white; vertical-align: middle; padding: 0 5px;">
					<h2>PYMT TYPE <?php echo datalist_Table('tbl_secondary', 'title', $payment['payment_method']); ?></h2>
				</td>
			</tr>
		</table>
		<br><br>
		<table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
			<tr style="background-color: #48565F;">
				<th style="width: 18%; text-align: left; color: white; padding: 2px 5px;"><span>DATE</span></th>
				<th style="width: 12%; text-align: left; color: white; padding: 2px 5px;"><span>TYPE</span></th>
				<th style="width: 33%; text-align: left; color: white; padding: 2px 5px;"><span>ACTIVITY</span></th>
				<th style="width: 7%; text-align: right; color: white; padding: 2px 5px;"><span>QTY</span></th>
				<th style="width: 15%; text-align: right; color: white; padding: 2px 5px;"><span>RATE</span></th>
				<th style="width: 15%; text-align: right; color: white; padding: 2px 5px;"><span>AMOUNT</span></th>
			</tr>
			<tr><td colspan="5"></td></tr>
			
			<?php
			
			foreach($log_payment as $e) { 
				?>
				<tr style="border-bottom: 1px solid black;">
				
					<td><?php echo $payment['date']; ?></td>
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
			
			<tr><td colspan="5"><br></td></tr>
			<tr><td colspan="5"><br></td></tr>
			<tr><td colspan="5"><br></td></tr>
			<tr>
				<td colspan="3">
				</td>
				<td style="padding: 3px 5px;">
					<span>TOTAL</span><br><br>
					<span>DISCOUNT</span><br><br>
					<?php
					if($payment['adjust'] != 0) {
						if(empty($payment['adjust_label'])) {
							?>
							<span>ADJUSTMENT</span><br><br>
							<?php
						} else {
							echo '<span>'.$payment['adjust_label'].'</span><br><br>';
						}
					}
					?>
					<span>TAX</span><br><br>
					<span>RECEIVED</span><br><br>
				</td>
				<td style="width: 20%; text-align: right; padding: 3px 5px;">
					<span><?php echo number_format($payment['subtotal'], 2, '.', ','); ?></span><br><br>
					<span>
					<?php
						if($payment['discount_type'] == '%') {
							echo number_format(($payment['subtotal'] * $payment['discount'] / 100), 2, '.', ',');
						} else {
							echo number_format($payment['discount'], 2, '.', ','); 
						}
					?>
					</span><br><br>
					<?php if($payment['adjust'] != 0) { ?>
						<span><?php echo number_format($payment['adjust'], 2, '.', ','); ?></span><br><br>
					<?php } ?>
					<span><?php echo number_format($payment['tax'], 2, '.', ','); ?> </span><br><br>
					<span><?php echo number_format($payment['total'], 2, '.', ','); ?></span><br><br>
				</td>
			</tr>
			<tr><td colspan="5"><br></td></tr>
			<tr>
				<td colspan="3"></td>
				<td style="color: white; background-color: #48565F; padding: 2px 5px; font-weight: bold;">
					<span>TOTAL DUE</span><br><br>
				</td>
				<td style="width: 20%; text-align: right; color: white; background-color: #48565F; padding: 3px 5px; font-weight: bold;">
					<span><?php echo number_format($payment['total'], 2, '.', ','); ?></span><br><br>
				</td>
			</tr>	
		</table>
	</header>

	<footer style="position: absolute; bottom: 0;  padding: 20mm 20mm 10mm 0;">
		<?php if($payment['remark'] != '') { ?>
			<p>Remark:<br><?php echo $payment['remark']; ?></p>
		<?php } ?>
		<br>
		<p>Fees or goods paid are strictly. NON REFUNDABLE under any circumstances.</p>
		<p><em>** This is auto generated by Robocube Tuition System on <?php echo date('M d, Y'); ?></em></p>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle($payment['payment_no'].'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($payment['payment_no'].'.pdf', 'I');