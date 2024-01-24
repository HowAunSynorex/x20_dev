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
	<title>Abacus</title>
	<!--<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC&display=swap" rel="stylesheet">-->
	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300&display=swap" rel="stylesheet">

	<style type="text/css">
		.table-content {
			border-collapse:collapse;
		}
		.table-content th {
			border:1px solid black;
		}
		.table-content td {
			border-right:1px solid black;
			border-left:1px solid black;
			width:5%
		}
		
	</style>

</head>
<body>
	<table style="width:100%" style="margin-bottom:2px;">
		<tr>
			<td rowspan="2" width="20%" style="text-align:center"><img src='https://www.wanneng.com.my/wp-content/uploads/2017/04/logonew-1.jpg' style=""></td>
			<td style="font-size:20px; text-align:center">萬能杯珠心算模拟题</td>
			<td style="font-size:20px; text-align:center; border:0.5px solid black" width="20%">Marks 得分</td>
		</tr>
		<tr>
			<td style="font-size:20px; text-align:center;">全国心算8岁组 限时5分鐘</td>
			<td style="font-size:35px; text-align:center; border-bottom:0.5px solid black; border-left:0.5px solid black; border-right:0.5px solid black">XX</td>
		</tr>
	</table>
	<table class="table-content" style="font-size:11px; width:100%; margin-bottom:5px; border-right:0px">
		<tr>
			<th width="2%" style="">No.</th>
			<?php $i=0; for($i=1; $i<21; $i++) { ?>
			<th style=""><?php echo $i ?></th>
			<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=6; $i++) { ?>
		<tr>
			<td style="text-align:center; "><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; "><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<th style="text-align:center;">答</th>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<th style="text-align:center" style=""><br></th>
			<?php } ?>
		</tr>
	</table>
	<table class="table-content" style="font-size:11px; width:100%; border:1px solid black; margin-bottom:5px; border-right:0px">
		<tr>
		<th width="2%" style="border-bottom:1px solid black; border-right:1px solid black;">No.</th>
		<?php $i=0; for($i=21; $i<41; $i++) { ?>
		<th style="border-bottom:1px solid black; border-right:1px solid black;"><?php echo $i ?></th>
		<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=7; $i++) { ?>
		<tr>
			<td style="text-align:center; border-right:1px solid black;"><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; border-right:1px solid black;"><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td style="text-align:center; border-top:1px solid black; border-right:1px solid black;">答</td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:center" style="border-top:1px solid black; border-right:1px solid black;"><br></td>
			<?php } ?>
		</tr>
	</table>
	<table class="table-content" style="font-size:11px; width:100%; border:1px solid black; margin-bottom:5px; border-right:0px">
		<tr>
		<th width="2%" style="border-bottom:1px solid black; border-right:1px solid black;">No.</th>
		<?php $i=0; for($i=41; $i<61; $i++) { ?>
		<th style="border-bottom:1px solid black; border-right:1px solid black;"><?php echo $i ?></th>
		<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=7; $i++) { ?>
		<tr>
			<td style="text-align:center; border-right:1px solid black;"><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; border-right:1px solid black;"><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td style="text-align:center; border-top:1px solid black; border-right:1px solid black;">答</td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:center" style="border-top:1px solid black; border-right:1px solid black;"><br></td>
			<?php } ?>
		</tr>
	</table>
	<table class="table-content" style="font-size:11px; width:100%; border:1px solid black; margin-bottom:5px; border-right:0px">
		<tr>
		<th width="2%" style="border-bottom:1px solid black; border-right:1px solid black;">No.</th>
		<?php $i=0; for($i=61; $i<81; $i++) { ?>
		<th style="border-bottom:1px solid black; border-right:1px solid black;"><?php echo $i ?></th>
		<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=8; $i++) { ?>
		<tr>
			<td style="text-align:center; border-right:1px solid black;"><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; border-right:1px solid black;"><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td style="text-align:center; border-top:1px solid black; border-right:1px solid black;">答</td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:center" style="border-top:1px solid black; border-right:1px solid black;"><br></td>
			<?php } ?>
		</tr>
	</table>
	<table class="table-content" style="font-size:11x; width:100%; border:1px solid black; margin-bottom:5px; border-right:0px">
		<tr>
		<th width="2%" style="border-bottom:1px solid black; border-right:1px solid black;">No.</th>
		<?php $i=0; for($i=81; $i<101; $i++) { ?>
		<th style="border-bottom:1px solid black; border-right:1px solid black;"><?php echo $i ?></th>
		<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=9; $i++) { ?>
		<tr>
			<td style="text-align:center; border-right:1px solid black;"><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; border-right:1px solid black;"><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td style="text-align:center; border-top:1px solid black; border-right:1px solid black;">答</td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:center" style="border-top:1px solid black; border-right:1px solid black;"><br></td>
			<?php } ?>
		</tr>
	</table>
	<table class="table-content" style="font-size:11px; width:100%; border:1px solid black; margin-bottom:5px; border-right:0px">
		<tr>
		<th width="2%" style="border-bottom:1px solid black; border-right:1px solid black;">No.</th>
		<?php $i=0; for($i=101; $i<121; $i++) { ?>
		<th style="border-bottom:1px solid black; border-right:1px solid black;"><?php echo $i ?></th>
		<?php } ?>
		</tr>
		<?php $i=1; for($i=1; $i<=9; $i++) { ?>
		<tr>
			<td style="text-align:center; border-right:1px solid black;"><?php echo $i ?></td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:right; border-right:1px solid black;"><i><?php echo(rand(10,100)); ?></i></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr>
			<td style="text-align:center; border-top:1px solid black; border-right:1px solid black;">答</td>
			<?php $j=0; for($j=1; $j<21; $j++) { ?>
				<td style="text-align:center" style="border-top:1px solid black; border-right:1px solid black;"><br></td>
			<?php } ?>
		</tr>
	</table>
	<footer style="position:fixed; bottom:0; width:100%; text-align:center">
		<p><br>Page XX</b></p>
	</footer>
</body>
</html>

<?php

$data = ob_get_clean();

$mpdf->SetTitle('abacus.pdf');
$mpdf->WriteHTML($data);
$mpdf->Output('abacus.pdf', 'I');