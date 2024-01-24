<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper py-3">

	<?php echo alert_get(); ?>
	
	<div class="table-responsive">
		<table class="table table-smA table-bordered table-striped">
			<thead>
				<th style="width:7%">No</th>
				<th>Child</th>
				<th style="width:15%">Balance</th>
			</thead>
			<tbody>
				<?php $i=0; foreach($result as $e) { $i++; 
						?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $e['fullname_en']; ?></td>
							<td class="text-right"><a href="<?php echo base_url('webview/history/'.$e['pid'].'?token='.$_GET['token'].'&type='.$_GET['type']); ?>"><?php echo ($_GET['type'] == 'ewallet') ? number_format(user_point($_GET['type'], $e['pid']), 2, '.', ',') : user_point($_GET['type'], $e['pid']); ?></a></td>
						</tr>
				<?php } ?>

				<?php if(empty($result)) { ?>
					<tr><td class="text-center" colspan="3">No result found</td></tr></tbody>
				<?php  } ?>
			</tbody>
		</table>
	</div>
	
</div>