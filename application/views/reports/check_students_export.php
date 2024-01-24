<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid py-2">
	<div class="row">
		<div class="col-6 my-auto">
			<h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
		</div>
	</div>
</div>

<div class="container-fluid container-wrapper">
	
	<table class="DTable2 table">
		<thead>
			<th style="width:10%">No</th>
			<th style="width:35%">Student</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Address</th>
		</thead>
		<tbody>
			
			<?php $i=0; foreach($result as $e) { $i++; ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $e['fullname_en']; ?></td>
				<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
				<td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
				<td><?php echo empty($e['address']) ? '-' :$e['address']; ?></td>
			</tr>
			<?php } ?>
			
		</tbody>
	</table>
	
</div>