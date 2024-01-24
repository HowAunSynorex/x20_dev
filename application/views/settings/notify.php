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
        
        <div class="container container-wrapper">

            <?php echo alert_get(); ?>
			
			<div class="accordion" id="accordionExample">
				
				<?php foreach(datalist('notify_type') as $k => $v) { ?>
				
					<div class="card">
						<div class="card-body card-body-header font-weight-bold" id="heading-<?php echo $k; ?>">
							<div class="mb-0 d-flex justify-content-between align-items-center">
								<a href="javascript:;" class="text-dark" data-toggle="collapse" data-target="#collapse_<?php echo $k; ?>" aria-expanded="true" aria-controls="collapse_<?php echo $k; ?>">
									<?php echo $v['label']; ?>
								</a>
								<?php
								if(branch_now($v['column']) != null) {
									echo '<span class="text-success font-weight-bold" name="'.$k.'">Enabled</span>';
								} else {
									echo '<span class="text-danger font-weight-bold" name="'.$k.'">Disabled</span>';
								}
								?>
							</div>
						</div>
						<div id="collapse_<?php echo $k; ?>" class="collapse <?php if( $_GET['tab'] == $k ) echo 'show'; ?>" data-parent="#accordionExample">
							<div class="card-body">
							
								<div class="row">
									<div class="col-9 d-flex align-items-center">
										<p class="mb-0"><?php echo $v['title']; ?></p>
									</div>
									<div class="col-3 d-flex align-items-center justify-content-end">
										<div class="form-group mb-0">
											<div class="custom-control custom-checkbox d-flex align-items-center">
			
												<label class="switch">
													<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $k; ?>" name="<?php echo $k; ?>">
													<span class="slider round"></span>
												</label>
												
											</div>
										</div>
									</div>
								</div>
								
								<div class="mt-3 d-none" id="notify-form-<?php echo $k; ?>">
									<div class="dropdown-divider"></div>
									<form method="post" class="container-fluid p-0 pt-2">

										<div class="form-group">
											<label class="text-danger">Notify Method</label>
											<input type="hidden" name="type" value="<?php echo $k; ?>">
											<select class="form-control" name="method" required>
												<?php foreach(datalist('notify_method') as $k2 => $v2 ) { ?>
													<option
														value="<?php echo $k2; ?>"
														<?php 
														switch ($k) {
															case 'payment_success':
																if(branch_now('notify_payment') == $k2) {
																	echo 'selected';
																}
																break;
															
															case 'outstanding':
																if(branch_now('notify_outstanding') == $k2) {
																	echo 'selected';
																}
																break;
															
															default:
																if(branch_now('notify_attendance') == $k2) {
																	echo 'selected';
																}
																break;
															
														}
														?>
													>
														<?php echo $v2; ?>
													</option>
												<?php } ?>
											</select>
										</div>
										
										<div class="form-group">
											<label class="text-danger">Message</label>
											<textarea class="form-control" name="message" rows="4" required><?php
												switch ($k) {
													case 'payment_success':
														echo branch_now('notify_payment_msg');
														break;
													
													case 'outstanding':
														echo branch_now('notify_outstanding_msg');
														break;
														
													default:
														echo branch_now('notify_attendance_msg');
														break;
												}
												?></textarea>
										</div>
										
										<div class="form-group text-right">
											<button class="btn btn-primary" name="save">Save</button>
										</div>
										
									</form>
								</div>
								
							</div>
						</div>
					</div>
	
				<?php } ?>
				
			</div>
			
        </div>
        
        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>