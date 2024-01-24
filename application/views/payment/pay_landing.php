<?php defined('BASEPATH') OR exit('No direct script access allowed'); $total = 0; ?>

<div class="container py-5">
	<form method="post">
		<div class="row">
			<div class="col-md-6 offset-md-3">
			
				<a href="<?php echo base_url('payment/std_unpaid_landing/'.$result['pid'].'?token='.$_GET['token']); ?>" class="text-muted d-block mb-3"><i class="fa fa-fw fa-chevron-left"></i> Back</a>
				
				<?php
				
				$std_unpaid_result = std_unpaid_result($result['pid'], $result['branch']);
						
				/* if($std_unpaid_result['count'] > 0) {
					
					if(isset($std_unpaid_result['result']['class'])) {
						
						foreach($std_unpaid_result['result']['class'] as $e) {
							
							$total += $e['amount'];
							
						}
						
					}
					
					if(isset($std_unpaid_result['result']['item'])) {
						
						foreach($std_unpaid_result['result']['item'] as $e) {
							
							$total += $e['amount'];
							
						}
						
					}
					
				} */
				
				$total = $std_unpaid_result['total'] * (datalist_Table('tbl_branches', 'tax', $result['branch']) + 100) / 100;
				
				echo alert_get();
				?>
				
				<div class="card mb-3">
					<div class="card-body">
						<div class="d-flex justify-content-between mb-3">
							<span>
								Total Amount
								<?php if (!empty($currency_code) ) echo '('.$currency_code.')'; ?>
							</span>
							<span><?php echo '$ '.number_format($total, 2, '.', ','); ?></span>
						</div>
						<hr>
						<div class="d-flex justify-content-between">
						
							<h6 class="font-weight-bold text-danger">
								Outstanding Amount
								<?php if (!empty($currency_code) ) echo '('.$currency_code.')'; ?>
							</h6>
							<h6 class="font-weight-bold text-danger">
								<?php echo '$ '.number_format($total, 2, '.', ','); ?>
							</h6>
							
						</div>
					</div>
				</div>
				
					
				<div class="accordion" id="accordionExample">
		
					<?php
					foreach(datalist('app_payment_type') as $k => $v) {
						// if(branch_now('gateway_'.$k) == 1) {
						if(datalist_Table('tbl_branches', 'gateway_'.$k, $result['branch']) == 1) {
							
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
																<div class="alert alert-info">Once the transaction has been done successfully, please send the receipt to <span class="font-weight-bold"><?php echo datalist_Table('tbl_branches', 'title', $result['branch']); ?></span>.</div>
																<div class="form-group row d-flex align-items-center">
																	<label class="col-md-3 col-form-label">Bank</label>
																	<div class="col-md-9">
																		<p class="font-weight-bold form-control-plaintext"><?php echo empty( datalist_Table('tbl_branches', 'bank', $result['branch']) ) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_branches', 'bank', $result['branch']) ) ; ?></p>
																	</div>
																</div>
																<div class="form-group row d-flex align-items-center">
																	<label class="col-md-3 col-form-label">Account No</label>
																	<div class="col-md-9">
																		<p class="font-weight-bold form-control-plaintext"><?php echo empty(datalist_Table('tbl_branches', 'acc_no', $result['branch'])) ? '-' : datalist_Table('tbl_branches', 'acc_no', $result['branch']); ?></p>
																	</div>
																</div>
																<div class="form-group row d-flex align-items-center">
																	<label class="col-md-3 col-form-label">Account Name</label>
																	<div class="col-md-9">
																		<p class="font-weight-bold form-control-plaintext"><?php echo empty(datalist_Table('tbl_branches', 'acc_name', $result['branch'])) ? '-' : datalist_Table('tbl_branches', 'acc_name', $result['branch']); ?></p>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<a href="javascript:;" data-toggle="modal" data-target="#modal-receipt" id="receipt-button" class="btn btn-primary w-100">Continue</a>
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
																		<select class="form-control select2A" onchange="check(this.value)">
																			<option value="">-</option>
																			<?php
																			foreach($v['input'] as $k2 => $v2) {
																				if(!empty( datalist_Table('tbl_branches', 'gateway_qrpay_img_'.$k2, $result['branch']) )) {
																					?>
																					<option value="<?php echo pointoapi_UploadSource( datalist_Table('tbl_branches', 'gateway_qrpay_img_'.$k2, $result['branch']) ); ?>"><?php echo $v2; ?></option>
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
																<form method="post">
																	<input type="hidden" name="total_unpaid" value="<?php echo $total;?>">
																	<input type="hidden" name="gateway" value="<?php echo $k; ?>">
																	<?php if(datalist_Table('tbl_branches', 'pointoapi_key', $result['branch']) == '') { ?>
																	<div class="alert alert-warning">
																		Payment gateway has not been set up yet
																	</div>
																	<?php } ?>
																	<button type="submit" name="pay_pg" class="btn btn-primary w-100" <?php if( datalist_Table('tbl_branches', 'pointoapi_key', $result['branch']) == '') echo 'disabled'; ?>>Continue</button>
																	
																</form>
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
				<h5 class="modal-title" id="modal-qr-titleA">QR</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-info">Once the transaction has been done successfully, please send the receipt to <span class="font-weight-bold"><?php echo datalist_Table('tbl_branches', 'title', $result['branch']); ?></span>.</div>
				<img id="modal-qr-image" src="https://cdn.synorex.link/assets/images/loading/default.gif" class="w-100 d-block">
			</div>
		</div>
	</div>
</div>

<form method="post" onsubmit="Loading(1)" enctype="multipart/form-data" class="modal fade" id="modal-receipt" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<input type="hidden" name="payment_method" required>
				<div class="form-group">
					<label>Receipt</label>
					<input type="file" name="receipt" class="form-control" required>
				</div>
            </div>
			 <div class="modal-footer">
                <button type="submit" name="pay_transfer" class="btn btn-primary">Continue</button>
            </div>
        </div>
    </div>
</form>