<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper py-3">

	<div class="mb-3">
		<a href="<?php echo base_url('webview/points_balance?token='.$_GET['token'].'&type='.$_GET['type']); ?>">
			<i class="fa fa-fw fa-chevron-left"></i> Back
		</a>
	</div>

	<?php echo alert_get(); ?>
	
	<div class="table-responsive">
		<table class="table table-smA table-bordered table-striped">
			<thead>
				<th>Date / Time</th>
				<th>Details</th>
				<th class="text-right">Credit</th>
				<th class="text-right">Debit</th>
				<th class="text-right">Amount</th>
			</thead>
			<tbody>
				<?php 
				
				$i=0; 
				$creditAmt = 0;
				$debitAmt = 0;
				$total = 0;

				if($result != null) {

					foreach($result as $e) { 
					
						$creditAmt += $e['amount_1'];
						$debitAmt += $e['amount_0'];

						?>
						<tr>
							<td><?php echo $e['create_on']; ?></td>
							<td>
								<?php echo $e['title']; ?></a>
								<span class="d-block small text-muted">Remark: <?php echo empty($e['remark']) ? '-' : $e['remark']; ?></span>
								<span class="d-block small text-muted">Create By: <?php echo empty($e['create_by']) ? '-' : datalist_Table('tbl_admins', 'nickname', $e['create_by']) ; ?></span>
							</td>
							<td class="text-right"><?php echo $e['amount_1']; ?>	</td>
							<td class="text-right"><?php echo $e['amount_0']; ?></td> 
							<td class="text-right">
								<?php
								$total += ($e['amount_1']-$e['amount_0']);
								echo $total;
								?>
							</td>                                       
						</tr>
						<?php 
						$i++;
					}

				} else {

					?><tr><td class="text-center" colspan="5">No result found</td></tr></tbody><?php 

				} 
				?>
			<tfoot>
				<th colspan="2">Total</th>
				<th class="text-right"><?php echo $creditAmt; ?>	</th>
				<th class="text-right"><?php echo $debitAmt; ?></th>
				<th class="text-right"><?php echo ($creditAmt - $debitAmt); ?></th>
			</tfoot>
		</table>
	</div>
</div>