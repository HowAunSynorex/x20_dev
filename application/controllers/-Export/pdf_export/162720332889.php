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
		
		.content td {
			padding: 3px 0;
		}
		
		td {
			vertical-align: start;
		}

	</style>

</head>
<body>
	<header>
		<table style="width: 100%; text-align: center; border-bottom: 1px solid black; padding-bottom: 10px;">
			<tr>
				<td><?php echo $branch['address']; ?></td>
			</tr>
			<tr>
				<td><?php echo empty($branch['phone']) ? '-' : $branch['phone']; ?></td>
			</tr>
			<tr>
				<td><?php echo empty($branch['email']) ? '-' : $branch['email']; ?></td>
			</tr>
		</table>
		<table style="width: 100%;">
			<tr><td><br></td></tr>
			<tr>
				<td colspan="2" style="text-align: right;"><h1>TAX INVOICE</h1></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr><td><br></td></tr>
			<tr>
				<td style="width: 35%;"><b>
				<?php echo $client . '<br>'; ?>
				<?php echo empty(datalist_Table('tbl_users', 'code', $payment['student'])) ? '-' : datalist_Table('tbl_users', 'code', $payment['student']) . '&nbsp;&nbsp;' ; ?>
				<?php echo empty(datalist_Table('tbl_users', 'form', $payment['student'])) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'form', $payment['student'])) . '<br>' ; ?>
				</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr style="vertical-align: middle;">
				<td rowspan="3"><?php echo $client_address; ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td style="text-align: right;">No</td>
				<td>:</td>
				<td><?php echo $payment['payment_no']; ?></td>
			</tr>
			<tr>
				<td></td>
				<td style="text-align: right;">Date</td>
				<td>:</td>
				<td><?php echo date('Y-m-d', strtotime($payment['date'])); ?></td>
			</tr>
			<tr>
				<td style="text-align: left;">Phone: <?php echo empty($client_phone) ? '-' : $client_phone ; ?></td>
				<td style="width: 180px;"></td>
				<td style="text-align: right;">Page</td>
				<td>:</td>
				<td>1 of 1</td>
			</tr>	
		</table>
	</header>

	<table class="content" style="width: 100%; margin: 20px 0; border-collapse: collapse;">
		<tr style="border: 1px solid black; border-left: 0; border-right: 0;">
			<th style="text-align: left; vertical-align: top; width: 7%; padding: 5px 0;">No</th>
			<th style="text-align: left;  vertical-align: top; width: 10%; padding: 5px 0;">Type</th>
			<th style="text-align: left;  vertical-align: top; padding: 5px 0;">Description</th>
			<th style="text-align: center; vertical-align: top; width: 7%; padding: 5px 0;">Qty</th>
			<th style="text-align: right; vertical-align: top; width: 15%; padding: 5px 0;">Unit Price</th>
			<th style="text-align: right; vertical-align: top; width: 15%; padding: 5px 0;">Total</th>
		</tr>
		
		<?php
			
		$i=1;
		foreach($log_payment as $e) { 
			?>
			<tr style="border-bottom: 1px solid black;">
				
				<td style='text-align: left; padding: 5px 0;'><?php echo $i; ?></td>
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
				<td style='text-align: center; padding: 5px 0;'><?php echo $e['qty']; ?></td>
				<td style='text-align: right; padding: 5px 0;'><?php echo number_format($e['price_unit'], 2, '.', ','); ?></td>
				<td style='text-align: right; padding: 5px 0; '>
					<?php echo number_format($e['price_amount'], 2, '.', ','); ?>
				</td>
				
			</tr>
			<?php
			$i++;
		}
		
		?>
		
	</table>

	<footer style="position: absolute; width: 180mm; bottom: 0; padding: 1mm 0 5mm 0;">
		<table style="width: 100%; border: 1px solid black; border-left: 0; border-right: 0; padding: 1mm 0 5mm;">
			<tr>
				<td rowspan="2" style="width: 50%;">
				<?php
				$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
				echo strtoupper($f->format($payment['total'])).' ONLY';
				?>
				</td>
				<td style="width: 30%; text-align: right;"><b>Subtotal</b></td>
				<td style="width: 20%; text-align: right; border: 1px solid;"><b><?php echo number_format($payment['subtotal'], 2, '.', ','); ?></b></td>
			</tr>
			<tr>
				<td style="text-align: right;"><b>Discount</b></td>
				<td style="text-align: right; border: 1px solid;">
					<b>
					<?php
						if($payment['discount_type'] == '%') {
							echo number_format(($payment['subtotal'] * $payment['discount'] / 100), 2, '.', ',');
						} else {
							echo number_format($payment['discount'], 2, '.', ','); 
						}
					?>
					</b>
				</td>
			</tr>
			<?php if($payment['adjust'] != 0) { ?>
			<tr>
				<td></td>
				<td style="text-align: right;">
					<b>
					<?php
					if(empty($payment['adjust_label'])) {
						echo 'ADJUSTMENT';
					} else {
						echo $payment['adjust_label'];
					}
					?>
					</b>
				</td>
				<td style="text-align: right; border: 1px solid;"><b><?php echo number_format($payment['adjust'], 2, '.', ','); ?></b></td>
			</tr>
			<?php } ?>
			<tr>
				<td></td>
				<td style="text-align: right;"><b>Tax</b></td>
				<td style="text-align: right; border: 1px solid;"><b><?php echo number_format($payment['tax'], 2, '.', ','); ?></b></td>
			</tr>
			<tr>
				<td></td>
				<td style="text-align: right;"><b>Total</b></td>
				<td style="text-align: right; border: 1px solid;"><b><?php echo number_format($payment['total'], 2, '.', ','); ?></b></td>
			</tr>
		</table>
		<table style="width: 100%; padding: 1mm 0 0;">
			<tr>
				<td>
				<?php if($payment['remark'] != '') {
					echo 'Remark: '.$payment['remark'];
				} ?>
				</td>
				<td style="width: 50%;"></td>
			</tr>

			<tr><td><br></td></tr>
			<tr><td><br></td></tr>
			<tr><td><br></td></tr>
			<tr><td><br></td></tr>
			<tr>
				<td style="border-bottom: 1px solid black;"></td>
			</tr>
			<tr>
				<td style="text-align: center;"><b>Authorised Signature</b></td>
			</tr>
		</table>
		<p>** This is auto generated by Robocube Tuition System on <?php echo date('F Y, d'); ?></p>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle($payment['payment_no'].'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($payment['payment_no'].'.pdf', 'I');