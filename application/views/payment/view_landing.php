<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php

	if( $result['status'] == 'paid' ) {
		?>
		<embed src="https://drive.google.com/viewerng/viewer?embedded=true&url=<?php echo base_url('export/pdf_export/' . $result['pid']); ?>" style="width: 100%; height: 100vh;"></embed>
		<?php
	} else {
		?>
		<div class="container py-5">
			<form method="post">
				<div class="row">
					<div class="col-md-6 offset-md-3">
					
						<img src="<?php echo (empty($image)) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : pointoapi_UploadSource($image)?>" class="d-block w-25 mx-auto mb-4">
						
						<div class="card mb-3">
							<div class="card-body">
								<div class="d-flex justify-content-between mb-3">
									<span class="text-secondary"><?php echo date('F Y'); ?></span>
									<span class="text-secondary"><?php echo $result['payment_no']; ?></span>
								</div>
								<h4 class="text-center">Account</h4>
								<span class="font-italic"><?php echo datalist_Table('tbl_users', 'fullname_en', $result['student']); ?></span>
								<hr>
								
								<table class="w-100">
									<thead>
										<tr>
											<th style="width: 60%;">Item / Class</th>
											<th class="text-center">Qty</th>
											<th class="text-right">Amount</th>
										</tr>
									</thead>
									<tbody>
									<?php foreach($result2 as $e) { ?>
									
										<tr>
											<td>
												<?php
												if($e['item'] == null) {
													echo empty($e['period']) ? datalist_Table('tbl_classes', 'title', $e['class']) : '[' . $e['period'] . '] ' . datalist_Table('tbl_classes', 'title', $e['class']);
												} else {
													echo datalist_Table('tbl_inventory', 'title', $e['item']);
												}
												?>
												<?php if($e['dis_amount'] > 0) { ?>
													<span class="d-block font-italic">Discount</span>
												<?php } ?>
												<?php if(!empty($e['remark'])) { ?>
													<em class="d-block text-muted small">Remark: <?php echo $e['remark'] ?></em>
												<?php } ?>
											</td>
											<td class="text-center"><?php echo $e['qty']; ?></td>
											<td class="text-right">
												<?php echo $unit.' '.number_format($e['price_amount'], 2, '.', ','); ?>
												<?php if($e['dis_amount'] > 0) { ?>
													<span class="d-block font-italic"><?php echo $unit.' '.number_format($e['dis_amount'], 2, '.', '');  ?></span>
												<?php } ?>
											</td>
										</tr>
										
									<?php } ?>
									</tbody>
								</table>
								
								<hr>
								<div class="d-flex justify-content-between align-items-center">
									<span>Total</span>
									<h6 class="font-weight-bold"><?php echo $unit.' '.number_format($result['total'], 2, '.', ','); ?></h6>
								</div>
							</div>
						</div>
						
						<div class="card mb-3">
							<div class="card-body">
								<?php if(!empty($result['discount'])) { ?>
								<div class="d-flex justify-content-between mb-3">
									<span>Discount <?php if($result['discount_type'] == '%') echo '('.$result['discount'] . '%)'; ?></span>
									<span>
										<?php
										echo ($result['discount_type'] == '%') ? '&#8211; '.$unit.' '.number_format(($result['subtotal'] * $result['discount'] / 100), 2, '.', ',') : '&#8211; '.$unit.' '.number_format($result['discount'], 2, '.', ',');
										?>
									</span>
								</div>
								<?php } ?>
								<?php if(!empty($result['adjust'])) { ?>
								<div class="d-flex justify-content-between mb-3">
									<span><?php echo empty($result['adjust_label']) ? 'Adjustment' : $result['adjust_label']; ?></span>
									<span class=><?php echo $unit.' '.number_format($result['adjust'], 2, '.', ','); ?></span>
								</div>
								<?php } ?>
								<div class="d-flex justify-content-between mb-3">
									<span>Total Amount</span>
									<span><?php echo $unit.' '.number_format($result['total'], 2, '.', ','); ?></span>
								</div>
								<hr>
								<div class="d-flex justify-content-between">
									
									<?php
									switch($result['status']) {
										case 'paid':
											?>
											<h6 class="font-weight-bold text-success"> Total Paid</h6>
											<h6 class="font-weight-bold text-success">
												<?php echo $unit.' '.number_format($result['total'], 2, '.', ','); ?>
											</h6><?php
											break;
										default:
											?>
											<h6 class="font-weight-bold text-danger">Outstanding Amount</h6>
											<h6 class="font-weight-bold text-danger">
												<?php echo $unit.' '.number_format($result['total'], 2, '.', ','); ?>
											</h6><?php
											break;
									}
									?>
									
									
								</div>
							</div>
						</div>
						
						<!--<a href="<?php echo base_url('payment/pay_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-primary w-100">Pay Now</a>-->
						
						<!--<a href="<?php echo base_url('payment/pay_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-block btn-warning">Pay Now</a>-->
						
						<!--<a href="<?php echo base_url('export/pdf_export/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-block btn-secondary">View Receipt</a>-->
						
						<p class="text-muted small text-center mt-4"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.<br>Powered by <b>Synorex</b></p>
					
					</div>
				</div>
			</form>
		</div>
		<?php
	}

?>