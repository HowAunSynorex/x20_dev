<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Payment</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Send Message (Payment)</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-3">Send Message (Outstanding)</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 4 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-4">Support Box</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 5 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-5">Sync</a>
				</li>
				<!--<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 4 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-4">Login Portal</a>
				</li>-->
			</ul>
			
			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					
					<div class="container"> 
						<?php 
						/* if(empty(branch_now('pointoapi_key'))) {
							?>
							<div class="alert alert-warning">Setup PointoAPI API Key to use this function. <a href="<?php echo base_url('settings/pointoapi'); ?>" class="text-primary">Setup</a></div>
							<?php
						} else { */
							?>
							<div class="accordion" id="accordionExample">
					
							<?php foreach(datalist('app_payment_type') as $k => $v) { ?>
							
								<div class="card">
									<div class="card-body card-body-header font-weight-bold" data-toggle="collapse" data-target="#collapse_<?php echo $k; ?>" aria-expanded="true" aria-controls="collapse_<?php echo $k; ?>" id="heading-<?php echo $k; ?>" style="cursor: pointer;">
										<div class="mb-0">
											<?php echo $v['label']; ?>
										</div>
									</div>
									<div id="collapse_<?php echo $k; ?>" class="collapse <?php if( $_GET['tab'] == $k ) echo 'show'; ?>" data-parent="#accordionExample">
										<div class="card-body">
										
											<div class="row">
												<div class="col-md-9 d-flex align-items-center">
													<?php
													if(branch_now($v['column']) == 1) {
														echo '<span class="text-success font-weight-bold" name="'.$k.'">Enabled</span>';
													} else {
														echo '<span class="text-danger font-weight-bold" name="'.$k.'">Disabled</span>';
													}
													?>
												</div>
												<div class="col-md-3 d-flex align-items-center justify-content-end">
													<div class="form-group mb-0">
														<div class="custom-control custom-checkbox d-flex align-items-center">
															<label class="switch">
																<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $k; ?>" name="gateway_<?php echo $k; ?>">
																<span class="slider round"></span>
															</label>
														</div>
													</div>
												</div>
											</div>
											
											<div class="mt-3 d-none" id="app-form-gateway_<?php echo $k; ?>">
												<div class="dropdown-divider"></div>
												<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)" class="container-fluid p-0 pt-2">
													
													<?php
													
													switch($k) {
														case "transfer":
															?>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group row d-flex align-items-center">
																		<label class="col-md-3 col-form-label text-danger">Bank</label>
																		<div class="col-md-9">
																			<!--<select class="form-control select2" name="gateway_<?php echo $k; ?>_pg">-->
																			<select class="form-control select2" name="bank" required>
																				<option value="">-</option>
																				<?php foreach ($bank_now as $e) { ?>
																					<option value="<?php echo $e['pid']; ?>" <?php if( $e['pid'] == branch_now('bank') ) echo 'selected'; ?>><?php echo $e['title']; ?></option>
																				<?php } ?>
																				<?php foreach ($bank_all as $e) { ?>
																					<option value="<?php echo $e['secondary']; ?>" <?php if( $e['secondary'] == branch_now('bank') ) echo 'selected'; ?>><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
																				<?php } ?>
																			</select>
																		</div>
																	</div>
																	<div class="form-group row d-flex align-items-center">
																		<label class="col-md-3 col-form-label text-danger">Account No</label>
																		<div class="col-md-9">
																			<input type="text" name="acc_no" class="form-control" value="<?php if(!empty(branch_now('acc_no'))) echo branch_now('acc_no');  ?>" required>
																		</div>
																	</div>
																	<div class="form-group row d-flex align-items-center">
																		<label class="col-md-3 col-form-label text-danger">Account Name</label>
																		<div class="col-md-9">
																			<input type="text" name="acc_name" class="form-control" value="<?php if(!empty(branch_now('acc_name'))) echo branch_now('acc_name');  ?>" required>
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
																	<?php foreach($v['input'] as $k2 => $v2) { ?>
																		<div class="form-group row d-flex align-items-center">
																			<label class="col-md-3 col-form-label"><?php echo $v2; ?></label>
																			<div class="col-md-9 d-flex flex-column align-items-start">
																				<div class="media mb-3">
																					<img src="<?php echo pointoapi_UploadSource(branch_now('gateway_qrpay_img_'.$k2)); ?>" class="border mr-3" style="height: 85px; width: 85px; object-fit: cover">
																					<div class="media-body my-auto">
																						<button type="button" class="btn btn-danger btn-sm text-white" onclick="remove_image('<?php echo $k2; ?>')" Aname="remove_<?php echo $k2; ?>">Remove</button>
																					</div>
																				</div>
																				<input type="file" name="gateway_qrpay_img_<?php echo $k2; ?>" class="form-control">
																			</div>
																		</div>
																	<?php } ?>
																</div>
															</div>
															<?php
															break;
														
														default:
															?>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group row d-flex align-items-center">
																		<label class="col-md-3 col-form-label">Payment Gateway</label>
																		<div class="col-md-9">
																			<select class="form-control select2" name="gateway_<?php echo $k; ?>_pg">
																				<option value="">-</option>
																				<?php foreach(datalist('payment_gateway') as $k2 => $v2) { ?>
																					<option value='<?php echo $k2; ?>' <?php if(branch_now('gateway_'.$k.'_pg') == $k2) echo 'selected'; ?>><?php echo $v2['label']; ?></option>
																				<?php } ?>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<?php
															break;
													}
													
													?>
													
													<div class="form-group text-right">
														<button class="btn btn-primary" name="save_<?php echo $k; ?>">Save</button>
													</div>
													
												</form>
												
											</div>
											
										</div>
										
									</div>
									
								</div>
				
							<?php } ?>
							
							</div>
						
							<?php
						// }?>
						
					</div>
						
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
					
					<div class="container">
						<form method="post">
							<div class="row">
								<div class="col-md-12">
								
									<div class="alert alert-info">
										Remark:<br>
										%NAME% : Name<br>
										%RECEIPT_NO% : Receipt No<br>
										%LINK% : Receipt PDF Link<br>
									</div>
								
									<div class="form-group row">
										<label class="col-form-label col-md-3">WhatsApp</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_whatsapp" maxlength="200" onkeyup="limit_text('send_msg_whatsapp', 'count')"><?php echo branch_now('send_msg_whatsapp'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count"><?php echo strlen(branch_now('send_msg_whatsapp')); ?></span>/200 characters</span>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-md-3">SMS</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_sms" maxlength="50" onkeyup="limit_text('send_msg_sms', 'count2')"><?php echo branch_now('send_msg_sms'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count2"><?php echo strlen(branch_now('send_msg_sms')); ?></span>/50 characters</span>
										</div>
									</div>
									
									<!--<div class="form-group row">
										<label class="col-form-label col-md-3">Email</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_email"><?php echo branch_now('send_msg_email'); ?></textarea>
										</div>
									</div>-->
									
									<div class="form-group text-right">
										<button class="btn btn-primary" name="save_msg">Save</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
					
					<div class="container">
						<form method="post">
							<div class="row">
								<div class="col-md-12">
								
									<div class="alert alert-info">
										Remark:<br>
										%NAME% : Name<br>
										%SUBJECT% : Subject<br>
										%ITEM% : Item<br>
										%TOTALOUTSTANDINGAMOUNT% : Total Outstanding Amount<br>
									</div>
								
									<div class="form-group row">
										<label class="col-form-label col-md-3">WhatsApp</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_whatsapp_outstanding" maxlength="200" onkeyup="limit_text('send_msg_whatsapp_outstanding', 'count')"><?php echo branch_now('send_msg_whatsapp_outstanding'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count"><?php echo strlen(branch_now('send_msg_whatsapp_outstanding')); ?></span>/200 characters</span>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-md-3">SMS</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_sms_outstanding" maxlength="50" onkeyup="limit_text('send_msg_sms_outstanding', 'count2')"><?php echo branch_now('send_msg_sms_outstanding'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count2"><?php echo strlen(branch_now('send_msg_sms_outstanding')); ?></span>/50 characters</span>
										</div>
									</div>
									
									<!--<div class="form-group row">
										<label class="col-form-label col-md-3">Email</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_email"><?php echo branch_now('send_msg_email'); ?></textarea>
										</div>
									</div>-->
									
									<div class="form-group text-right">
										<button class="btn btn-primary" name="save_msg_outstanding">Save</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 4 ) echo 'show active'; ?>" id="tab-4">
					
					<div class="container">
						<form method="post">
							<div class="row">
								<div class="col-md-6 offset-md-3">
									
									<div class="form-group">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" name="active_support_box" class="custom-control-input" id="support-box"  <?php if(branch_now('active_support_box')) echo 'checked'; ?>>
											<label class="custom-control-label" for="support-box">Support Box</label>
										</div>
									</div>
									
									<div class="form-group">
										<button class="btn btn-primary" name="save_support_box">Save</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 5 ) echo 'show active'; ?>" id="tab-5">
					
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<a href="<?php echo base_url('api/sync_classes'); ?>" target="_blank" class="btn btn-primary">Sync</a>
							</div>
						</div>
					</div>
					
				</div>
				
				<!--<div class="tab-pane fade <?php if( $_GET['tab'] == 4 ) echo 'show active'; ?>" id="tab-4">
					
					<div class="container">
						<form method="post">
							<div class="row">
								<div class="col-md-12">
								
									<div class="alert alert-info">
										Remark:<br>
										%NAME% : Name
									</div>
								
									<div class="form-group row">
										<label class="col-form-label col-md-3">WhatsApp</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_whatsapp_outstanding" maxlength="200" onkeyup="limit_text('send_msg_whatsapp_outstanding', 'count')"><?php echo branch_now('send_msg_whatsapp_outstanding'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count"><?php echo strlen(branch_now('send_msg_whatsapp_outstanding')); ?></span>/200 characters</span>
										</div>
									</div>
									
									<div class="form-group row">
										<label class="col-form-label col-md-3">SMS</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_sms_outstanding" maxlength="50" onkeyup="limit_text('send_msg_sms_outstanding', 'count2')"><?php echo branch_now('send_msg_sms_outstanding'); ?></textarea>
											<span class="form-text text-muted d-block small">Limit <span data-label="count2"><?php echo strlen(branch_now('send_msg_sms_outstanding')); ?></span>/50 characters</span>
										</div>
									</div>
									
									<!{1}**<div class="form-group row">
										<label class="col-form-label col-md-3">Email</label>
										<div class="col-md-9">
											<textarea class="form-control" rows="4" name="send_msg_email"><?php echo branch_now('send_msg_email'); ?></textarea>
										</div>
									</div>**{1}>
									
									<div class="form-group text-right">
										<button class="btn btn-primary" name="save_msg_outstanding">Save</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					
				</div>-->

			</div>
			
        </div>
        
        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>