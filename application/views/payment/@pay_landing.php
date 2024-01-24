<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container py-5">
	<form method="post">
		<div class="row">
			<div class="col-md-6 offset-md-3">
			
				<a href="<?php echo base_url('payment/view_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="text-muted d-block mb-3"><i class="fa fa-fw fa-chevron-left"></i> Back</a>
				
				<div class="card mb-3">
					<div class="card-body">
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
				
					
							<div class="accordion" id="accordionExample">
					
								<?php
								foreach(datalist('app_payment_type') as $k => $v) {
									if(branch_now('gateway_'.$k) == 1) {
										?>
										<div class="card">
										
											<div class="card-header font-weight-bold" data-toggle="collapse" data-target="#collapse_<?php echo $k; ?>" aria-expanded="true" aria-controls="collapse_<?php echo $k; ?>" id="heading-<?php echo $k; ?>" style="cursor: pointer;">
												<div class="mb-0">
													<?php echo $v['label']; ?>
												</div>
											</div>
											
											<div id="collapse_<?php echo $k; ?>" class="collapse" data-parent="#accordionExample">
												<div class="card-body">
													<div id="app-form-gateway_<?php echo $k; ?>">
														<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)" class="container-fluid">
															<?php
															
															switch($k) {
																case "transfer":
																	?>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="alert alert-info">Once the transaction has been done successfully, please send the receipt to <span class="font-weight-bold"><?php echo branch_now('title'); ?></span>.</div>
																			<div class="form-group row d-flex align-items-center">
																				<label class="col-md-3 col-form-label">Bank</label>
																				<div class="col-md-9">
																					<p class="font-weight-bold form-control-plaintext"><?php echo empty(branch_now('bank')) ? '-' : branch_now('bank'); ?></p>
																				</div>
																			</div>
																			<div class="form-group row d-flex align-items-center">
																				<label class="col-md-3 col-form-label">Account No</label>
																				<div class="col-md-9">
																					<p class="font-weight-bold form-control-plaintext"><?php echo empty(branch_now('acc_no')) ? '-' : branch_now('acc_no'); ?></p>
																				</div>
																			</div>
																			<div class="form-group row d-flex align-items-center">
																				<label class="col-md-3 col-form-label">Account Name</label>
																				<div class="col-md-9">
																					<p class="font-weight-bold form-control-plaintext"><?php echo empty(branch_now('acc_name')) ? '-' : branch_now('acc_name'); ?></p>
																				</div>
																			</div>
																		</div>
																	</div>
																	<?php
																	break;
																	
																case "qrpay":
																	?>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group row">
																				<label class="col-md-3 col-form-label">QR Pay</label>
																				<div class="col-md-9">
																					<select class="form-control select2A" onchange="check(this)">
																						<option value="">-</option>
																						<?php
																						foreach($v['input'] as $k2 => $v2) {
																							if(!empty(branch_now('gateway_qrpay_img_'.$k2))) {
																								?>
																								<option value="<?php echo $v2.'-'.$k2; ?>"><?php echo $v2; ?></option>
																								<?php
																							}
																						}
																						?>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="row">
																		<div class="col-md-12">
																			<a href="javascript:;" data-toggle="modal" data-target="#modal-qr" id="qr-button" class="disabled btn btn-primary w-100">Continue</a>
																		</div>
																	</div>
																	<?php
																	break;
																
																default:
																	?>
																	<div class="row">
																		<div class="col-md-12">
																			<a href="" class="btn btn-primary w-100">Continue</a>
																		</div>
																	</div>
																	<?php
																	break;
															}
															
															?>
															
														</form>
														
													</div>
													
												</div>
												
											</div>
											
										</div>
										
										<?php
									}
								} ?>
							</div>
	
				
				<p class="text-muted small text-center mt-4"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.<br>Powered by <b>Synorex</b></p>
			
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="modal-qr" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-qr-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<img id="modal-qr-image" src="https://cdn.synorex.link/assets/images/loading/default.gif" class="w-100 d-block">
			</div>
		</div>
	</div>
</div>