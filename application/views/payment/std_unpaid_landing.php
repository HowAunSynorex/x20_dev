<?php defined('BASEPATH') OR exit('No direct script access allowed'); $subtotal = 0; $total = 0; ?>

<div class="container py-5">
	<form method="post">
		<div class="row">
			<div class="col-md-6 offset-md-3">
			
				<img src="<?php echo (empty($image)) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : pointoapi_UploadSource($image)?>" class="d-block w-25 mx-auto mb-4">
				
				<div class="card mb-3">
					<div class="card-body">
						<div class="d-flex justify-content-between mb-3">
							<span class="text-secondary"><?php echo date('F Y'); ?></span>
							<span class="text-secondary"></span>
						</div>
						<h4 class="text-center">Outstanding Payment</h4>
						<span class="font-italic"><?php echo $result['fullname_en']; ?></span>
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
								<?php
								
								$std_unpaid_result = std_unpaid_result($result['pid']);
								
								if($std_unpaid_result['count'] > 0) {
									
									if(isset($std_unpaid_result['result']['class'])) {
										
										foreach($std_unpaid_result['result']['class'] as $e) {
																						
											?>
											<tr>
												<td>
													<?php echo isset($e['period']) ? '['. $e['period'] .'] '. $e['title'] : $e['title']; ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic">Discount</span>
													<?php } ?>
													<?php if(!empty($e['remark'])) { ?>
														<em class="d-block text-muted small">Remark: <?php echo $e['remark'] ?></em>
													<?php } ?>
												</td>
												<td class="text-center"><?php echo $e['qty']; ?></td>
												<td class="text-right">
													<?php echo '$ ' . number_format($e['amount'], 2, '.', ','); ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic"><?php echo '$ ' . number_format($e['discount'], 2, '.', '');  ?></span>
													<?php } ?>
												</td>
											</tr>
											<?php
											
										}
										
									}
									
									if(isset($std_unpaid_result['result']['item'])) {
										
										foreach($std_unpaid_result['result']['item'] as $e) {
																						
											?>
											<tr>
												<td>
													<?php echo $e['title']; ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic">Discount</span>
													<?php } ?>
													<?php if(!empty($e['remark'])) { ?>
														<em class="d-block text-muted small">Remark: <?php echo $e['remark'] ?></em>
													<?php } ?>
												</td>
												<td class="text-center"><?php echo $e['qty']; ?></td>
												<td class="text-right">
													<?php echo '$ ' . number_format($e['amount'], 2, '.', '');  ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic"><?php echo '$ ' . number_format($e['discount'], 2, '.', '');  ?></span>
													<?php } ?>
												</td>
											</tr>
											<?php
											
										}
										
									}
									
									if(isset($std_unpaid_result['result']['service'])) {
										
										foreach($std_unpaid_result['result']['service'] as $e) {
																						
											?>
											<tr>
												<td>
													<?php echo $e['title']; ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic">Discount</span>
													<?php } ?>
													<?php if(!empty($e['remark'])) { ?>
														<em class="d-block text-muted small">Remark: <?php echo $e['remark'] ?></em>
													<?php } ?>
												</td>
												<td class="text-center"><?php echo $e['qty']; ?></td>
												<td class="text-right">
													<?php echo '$ ' . number_format($e['amount'], 2, '.', '');  ?>
													<?php if($e['discount'] > 0) { ?>
														<span class="d-block font-italic"><?php echo '$ ' . number_format($e['discount'], 2, '.', '');  ?></span>
													<?php } ?>
												</td>
											</tr>
											<?php
											
										}
										
									}
									
								} else {
									?>
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
									<?php 
								}
								?>
							</tbody>
						</table>
						
						<hr>
						<div class="d-flex justify-content-between align-items-center">
							<span>Subtotal</span>
							<h6 class="font-weight-bold"><?php echo '$ ' . number_format($std_unpaid_result['subtotal'], 2, '.', ','); ?></h6>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<span>Discount</span>
							<h6 class="font-weight-bold"><?php echo '$ ' . number_format($std_unpaid_result['discount'], 2, '.', ','); ?></h6>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<span>Tax</span>
							<h6 class="font-weight-bold"><?php echo '$ ' . number_format($std_unpaid_result['subtotal'] * datalist_Table('tbl_branches', 'tax', $result['branch']) / 100, 2, '.', ','); ?></h6>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<span>
								Total
							</span>
							<h6 class="font-weight-bold"><?php echo '$ ' . number_format($std_unpaid_result['total'], 2, '.', ','); ?></h6>
						</div>
					</div>
				</div>
				
				<div class="card mb-3">
					<div class="card-body">
						<div class="d-flex justify-content-between mb-3">
							<span>
								Total Amount
								<?php if (!empty($currency_code) ) echo '('.$currency_code.')'; ?>
							</span>
							<span><?php echo '$ ' . number_format($std_unpaid_result['total'], 2, '.', ','); ?></span>
						</div>
						<hr>
						<div class="d-flex justify-content-between">
							<h6 class="font-weight-bold text-danger">
								Outstanding Amount
								<?php if (!empty($currency_code) ) echo '('.$currency_code.')'; ?>
							</h6>
							<h6 class="font-weight-bold text-danger">
								<?php echo '$ ' . number_format($std_unpaid_result['total'], 2, '.', ','); ?>
							</h6>
						</div>
					</div>
				</div>
				
				<a href="<?php echo base_url('payment/pay_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-primary w-100">Pay Now</a>
				
				<!--<a href="<?php echo base_url('payment/pay_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-block btn-warning">Pay Now</a>-->
				
				<!--<a href="<?php echo base_url('export/pdf_export/'.$result['pid'].'?token='.$_GET['token']); ?>" class="btn btn-lg btn-block btn-secondary">View Receipt</a>-->
				
				<p class="text-muted small text-center mt-4"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.<br>Powered by <b>Synorex</b></p>
			
			</div>
		</div>
	</form>
</div>