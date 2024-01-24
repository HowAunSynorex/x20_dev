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
	<table style="width:100%; margin-bottom:50px;">
		<tr>
			<td width="70%" style="font-weight:bold; font-size:20px;">SYNOREX COMPANY</td>
			<td width="" style="font-weight:bold; font-size:20px; text-align:center">OFFICIAL RECEIPT</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td style="">SSM</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="">Phone</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="">Email</td>
						<td>:</td>
					</tr>
					<tr>
						<td style="">Website</td>
						<td>:</td>
					</tr>
				</table>
			</td>
			<td rowspan="2" style="text-align:center; vertical-align:center"><img src='https://cdn.synorex.link/assets/images/robocube/tuition.png' style="height:90px; width:90px; padding-top:5px">
			</td>
		</tr>
	</table>
	<table style="font-size:15px; width:100%; color:#424242; border-bottom:1px solid black; padding-bottom:10px">
		<tr>
			<td colspan="6">Bill To</td>
		</tr>
		<tr>
			<td width="14%">Parent</td>
			<td width="1%">:</td>
			<td width="50%"></td>
			<td width="20%"><br></td>
			<td width="2%"><br></td>
			<td width=""><br></td>
		</tr>
		<tr>
			<td>Contact</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Student</td>
			<td>:</td>
			<td>student</td>
			<td>Sales Receipt</td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Student IC</td>
			<td>:</td>
			<td>0000000000</td>
			<td>Date</td>
			<td>:</td>
			<td></td>
		</tr>
	</table>
	<table style="width:100%; padding:10px 0px 10px;">
		<tr>
			<td width="65%"><br></td>
			<td width="20%" style="font-weight:bold">Payment Method</td>
			<td width="2%">:</td>
			<td width="" style="font-weight:bold">VISA</td>
		</tr>
	</table>
	<table style="font-size:17px; width:100%; margin-top:20px; border-bottom:1px solid black; border-top:1px solid black; background-color:#E0E0E0">
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
	
	<footer style="position:fixed; bottom:0; width: 100%">
	<table width="100%">
		<tr>
			<td width="50%"></td>
			<td width="50%">
				<table width="100%" style="font-size:13px; padding:10px 0px">
					<tr>
						<td width="40%" style="font-weight:bold">Total</td>
						<td width="10%" style="">:</td>
						<td width="27%" style="text-align:right">3</td>
						<td width="23%" style="text-align:right">RM150.00</td>
					</tr>
					<tr>
						<td style="font-weight:bold">Discount</td>
						<td>:</td>
						<td style="text-align:right">3</td>
						<td style="text-align:right">RM150.00</td>
					</tr>
					<tr>
						<td style="font-weight:bold">Tax</td>
						<td>:</td>
						<td style="text-align:right">3</td>
						<td style="text-align:right">RM150.00</td>
					</tr>
					<tr>
						<td style="font-weight:bold">Total Amount</td>
						<td>:</td>
						<td style="text-align:right">4</td>
						<td style="text-align:right">RM150.00</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
		<table style="width:100%; padding-top:15px; border-top:1px solid black; color:#757575">
			<tr>
				<td>Fees paid are strictly.</td>
			</tr>
			<tr>
				<td><b>NOT REFUNDABLE</b> under any circumstances.</td>
			</tr>
			<tr>
				<td style="font-size:12px; "><i>**This is auto generated by Cashier on Date.</i></td>
			</tr>
		</table>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle('inv.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output('inv.pdf', 'I');