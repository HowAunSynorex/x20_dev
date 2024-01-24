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

ob_start();

?>

<html>
<head>
	<meta charset="utf-8">
	<title>Ewallet Receipt</title>
	<style>
		@page {
			sheet-size: 80mm 200mm;
			margin: 0;
		}
		
		* {
			page-break-inside: avoid;
		}
	</style>
</head>
<body style="margin: 0;">

	<table style="width: 100%;">
		<tr>
			<td style="text-align:center">
				<img style="width: 20%;" src="<?php echo pointoapi_UploadSource($branch['image']); ?>"><br>
                <?php echo $branch['title'] ?><br>
                <?php echo $branch['address'] ?><br>
                <?php echo $branch['phone'] ?><br>
                <br>
                RECEIPT<br>
			</td>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<td>
				Code/单号: #<?php echo $id; ?>
			</td>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<td style="text-align:center">
				-----------------------------------------------------------------
			</td>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<td><?php echo $point['title']; ?></td>
			<td style="text-align: right">
			    <?php
			    
			    if($point['amount_1'] > 0) {
			        echo '+'.number_format($point['amount_1'],2,'.',',');
			    } else {
			        echo '-'.number_format($point['amount_0'],2,'.',',');
			    }
			    ?>
			</td>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<td style="text-align:center">
				-----------------------------------------------------------------
			</td>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<th style="text-align: left">BALANCE</th>
			<th style="text-align: right"><?php echo number_format(user_point('ewallet', $point['user']),2,'.',','); ?></th>
		</tr>
	</table>
	
	<table style="width: 100%;">
		<tr>
			<td style="text-align:center">
			    Thank You 谢谢
			</td>
		</tr>
	</table>
	
</body>
</html>
<?php

$data = ob_get_clean();

$mpdf->SetTitle($id.'.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output($id.'.pdf', 'I');