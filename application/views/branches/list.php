<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-lite', $thispage); ?>

<div style="margin-top: 52px;">

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <div class="row">
                        <div class="col-md-8">
                            <h4>Hi, <?php echo auth_data('nickname'); ?></h4>
                            <p class="text-muted">You belong to the following branches. Please select the branch you wish to access now.</p>
                        </div>
                        <!--<div class="col-md-4 text-right my-auto">
							<a href="<?php echo base_url('branches/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
                        </div>-->
                    </div>
                    
					<?php echo alert_get(); ?>
					
					<!--<div class="alert alert-warning">
						Click <a href="#!" data-toggle="modal" data-target="#modal-renew">here</a> to renew your account. You also can <a href="https://one.synorexcloud.com/client/account?pg=payment_card" target="_blank">add</a> your credit/debit card to auto renew the licesne
					</div>-->
					
					<!--<div class="alert alert-success">
						To purchase or renew the license please contact our billing department (WhatsApp: 012-5468 517)
					</div>-->

                    <ul class="list-group mb-3">
                        <?php
						
						$i = 0;

						$my_branches = my_branches();

						if(count($my_branches) > 0) {
							
							foreach($my_branches as $e) {
								
								if( datalist_Table('tbl_branches', 'is_delete', $e['branch']) == 0 ) { $i++;
								
									?>
									<li class="list-group-item py-3">
										<div class="media">
											<img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_branches', 'image', $e['branch'])); ?>" class="mr-3 my-auto rounded border" style="height: 100px; width: 100px; object-fit: cover;">
											<div class="media-body">
												<div class="row" style="height: 100px;">
													<div class="col-md-8 d-flex justify-content-center flex-column">
														<h6 class="mt-0 mb-1 text-dark" style="font-size: 1rem;"><?php echo datalist_Table('tbl_branches', 'title', $e['branch']); ?></h6>
														<span class="text-muted d-block" style="font-size: 90%;">Branch created on <?php echo date('d M Y', strtotime($e['create_on'])); ?></span>
														<span class="text-muted d-block" style="font-size: 90%;">Branch ID: #<?php echo $e['branch']; ?></span>
														<span class="text-muted d-block" style="font-size: 90%;">
															User(s): <?php echo count($this->log_join_model->list('join_branch', $e['branch'])); ?>
														</span>
														<!--<span class="text-muted d-block" style="font-size: 90%;">
															Expired Date: 
															<?php 
															
															/* $expired_date = datalist_Table('tbl_branches', 'expired_date', $e['branch']);
															// echo $expired_date;
															
															unset($Expired);
															
															if( strtotime(date('Y-m-d')) >= strtotime($expired_date) ) {
																
																$Expired = 1;
																
																echo '<b class="text-danger">'.$expired_date.' - License expired</b>'; 
																
															} elseif( strtotime(date('Y-m-d', strtotime('+14 days'))) >= strtotime($expired_date) ) {
																
																echo '<b class="text-warning">'.$expired_date.' - Expiring</b>'; 
																
															} else {
																
																echo empty($expired_date) ? '-' : $expired_date; 
																
															} */
															?>
														</span>-->
													</div>
													<div class="col-md-4 text-right my-auto">
														<?php
														
														if( datalist_Table('tbl_branches', 'active', $e['branch']) == 1 ) {
															
															/* if(datalist_Table('tbl_branches', 'owner', $e['branch']) == auth_data('pid')) { 
																
																?><a href="<?php echo base_url($thispage['group'].'/edit/'.$e['branch']); ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-fw fa-pen py-1"></i></a><?php 
															
															} */
															
															?><a href="<?php echo base_url('home/branch_access/'.$e['branch']); ?>" class="btn btn-success btn-sm ml-1 <?php /* if( isset($Expired) ) echo 'disabled'; */?>" data-toggle="tooltip" title="Access"><i class="fa fa-fw fa-sign-in-alt py-1"></i></a><?php
															
														} else {
															
															?><em class="text-muted">Inactive</em><?php
															
														}
														?>
													</div>
												</div>
											</div>
										</div>
									</li>
									<?php
								
								}
								
							}
							
						}
						
						if($i == 0) {
							?>
							<li class="list-group-item py-3">
								No result found
							</li>
							<?php
						}
                        ?>
                    </ul>
                    
                </div>
            </div>

        </div>
		
		<form method="post" class="modal fade" id="modal-renew" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Renew License</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
					
						<div class="alert alert-info">
							Please select a branch to renew
						</div>
						
						<div class="form-group">
							<label>Branch</label>
							<select class="form-control select2" name="branch" required>
								<option value="">-</option>
								<?php 
								
								foreach($my_branches as $e) { 
									if( datalist_Table('tbl_branches', 'is_delete', $e['branch']) == 0 ) {
										?><option value="<?php echo $e['branch']; ?>"><?php echo datalist_Table('tbl_branches', 'title', $e['branch']); ?></option><?php 
									} 
								}
								?>
							</select>
						</div>
						
						<div class="form-group">
							<label>Plan</label>
							<select class="form-control select2" name="plan" required>
								<?php 
								
								foreach($this->tbl_secondary_model->list2([ 'type' => 'plan', 'active' => 1, 'pid !=' => 162593333372 ]) as $e) { 
									?><option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option><?php 
								}
								?>
							</select>
						</div>
						
					</div>
					<div class="modal-footer">
						<button type="submit" name="pay_now" class="btn btn-primary">Pay Now</button>
					</div>
				</div>
			</div>
		</form>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<!--<a href="//wa.me/60125468517" target="_blank">
	<img src="<?php echo base_url('uploads/site/wa.png'); ?>" class="fixed-bottom ml-auto mb-2 mr-2">
</a>-->