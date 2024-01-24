<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// new

error_reporting(E_ALL);
ini_set("display_errors", 1);

$config = [
    'mode' => '+aCJK', 
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
			<td rowspan="2" width="20%"><img src='https://cdn.synorex.link/assets/images/robocube/tuition.png' style="height:80px; width:80px;"></td>
			<td colspan="" style="font-weight:bold; font-size:18px;">SYNOREX COMPANY</td>
			<td style="font-weight:bold; font-size:18px;" width="30%">OFFICIAL RECEIPT</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="">SSM</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="text-align:left">Phone</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="">Email</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="text-align:left">Website</td>
						<td>:</td>
					</tr>
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
						<td>:</td>
					</tr>
					<tr>
						<td style="">Date</td>
						<td>:</td>
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
			<td>:</td>
		</tr>
		<tr>
			<td>Contact</td>
			<td>:</td>
		</tr>
		<tr>
			<td>Student</td>
			<td>:</td>
		</tr>
		<tr>
			<td>Student IC</td>
			<td>:</td>
		</tr>
	</table>
	<table style="font-size:16px; font-weight:bold; width:100%; border-bottom:1px solid black">
		<tr>
			<td width="10%">No</td>
			<td width="60%">Description</td>
			<td width="15%" style="text-align:right">Unit Price</td>
			<td width="15%" style="text-align:right">Quantity</td>
			<td width="15%" style="text-align:right">Amount</td>
		</tr>
	</table>
	<table style="font-size:15px; width:100%; padding-top:20px;">
		<tr>
			<td width="10%" style="text-align:left">1</td>
			<td width="60%">Class</td>
			<td width="15%" style="text-align:right">RM150.00</td>
			<td width="15%" style="text-align:right">2</td>
			<td width="15%" style="text-align:right">RM300.00</td>
		</tr>
	</table>
	<footer style="position:fixed; bottom:0;">
		<table width="100%" style="border-bottom:1px solid black">
			<tr>
				<td width="60%"><br></td>
				<td width="12%" style="font-weight:bold">Total Qty<td>
				<td width="1%">:<td>
				<td width="19%" style="font-weight:bold" style="text-align:right">5<td>
			</tr>
		</table>
		<table width="100%" style="color:#424242">
			<tr>
				<td width="20%">Payment Method</td>
				<td width="2%">:</td>
				<td width="38%">Visa</td>
				<td width="12%">Subtotal<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right">5<td>
			</tr>
			<tr>
				<td width="20%">Cashier</td>
				<td width="2%">:</td>
				<td width="38%">COOL</td>
				<td width="12%">Discount<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right">5<td>
			</tr>
		</table>
		<table width="100%" style="color:#424242">
			<tr>
				<td width="60%">Computer Generated no signature required.</td>
				<td width="12%" style="font-weight:bold">Net Total<td>
				<td width="1%">:<td>
				<td width="19%" style="text-align:right; font-weight:bold">RM150.00<td>
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