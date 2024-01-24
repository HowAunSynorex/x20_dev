<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-lite', $thispage); ?>

<div style="margin-top: 52px;">

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <div class="row">
                <div class="col-md-8 offset-md-2">
					
					<a href="<?php echo base_url('branches/list'); ?>" class="d-block mb-4 mt-3 small text-muted"><i class="fa fa-fw fa-chevron-left"></i> Back</a>
					
					<div class="media mb-3">
					    <a href="javascript:;" onclick="$('#modal-add').modal('show');" data-toggle="tooltip" title="Change Logo">
							<img src="<?php echo pointoapi_UploadSource($result['image']); ?>" class="mr-3 my-auto rounded border" style="height: 100px; width: 100px; object-fit: cover;">
						</a>
						<div class="media-body">
							<h5 class="font-weight-bold"><?php echo $result['title']; ?><span class="text-muted ml-2 font-weight-normal" style="font-size: 14px"></span></h5>
							<p class="mb-1 small text-muted">Phone: <?php echo empty($result['phone']) ? '-' : $result['phone'] ; ?></p>
							<p class="mb-1 small text-muted">Email: <?php echo empty($result['email']) ? '-' : $result['email'] ; ?></p>
							<p class="mb-1 small text-muted">Address: <?php echo empty($result['address']) ? '-' : $result['address'] ; ?></p>
						</div>
					</div>
					
					<?php echo alert_get(); ?>

					<ul class="nav nav-tabs mt-3">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Details</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Billing</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-3">Advanced</a>
						</li>
					</ul>

					<div class="tab-content py-3">

						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
							<form method="post">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">Title</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-md-3">Company No</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="ssm_no" value="<?php echo $result['ssm_no']; ?>">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-form-label col-md-3">Country</label>
									<div class="col-md-9">
										<select class="form-control select2" name="country">
											<option value="">-</option>
											<?php foreach ($country as $e) {?>
											<option value="<?php echo $e['pid']; ?>" <?php if($result['country'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
											<?php }; ?>

										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-md-3">Currency</label>
									<div class="col-md-9">
										<select class="form-control select2" name="currency">
											<option value="">-</option>
											<?php foreach ($currency as $e) {?>
											<option value="<?php echo $e['pid']; ?>" <?php if($result['currency'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
											<?php }; ?>
										</select>
									</div>
								</div>

								<hr class="mb-4">

								<div class="form-group row">
									<label class="col-form-label col-md-3">Phone</label>
									<div class="col-md-9">
										<input type="tel" class="form-control" name="phone" value="<?php echo $result['phone']; ?>" placeholder="Primary">
										<input type="tel" class="form-control mt-2" name="phone2" value="<?php echo $result['phone2']; ?>">
										<input type="tel" class="form-control mt-2" name="phone3" value="<?php echo $result['phone3']; ?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-md-3">Email</label>
									<div class="col-md-9">
										<input type="email" class="form-control" name="email" value="<?php echo $result['email']; ?>" placeholder="Primary">
										<input type="email" class="form-control mt-2" name="email2" value="<?php echo $result['email2']; ?>">
										<input type="email" class="form-control mt-2" name="email3" value="<?php echo $result['email3']; ?>">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-form-label col-md-3">Address</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="address" value="<?php echo $result['address']; ?>" placeholder="Primary">
										<input type="text" class="form-control mt-2" name="address2" value="<?php echo $result['address2']; ?>">
										<input type="text" class="form-control mt-2" name="address3" value="<?php echo $result['address3']; ?>">
									</div>
								</div>

								<hr class="mb-4">

								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="save" class="btn btn-primary">Save</button>
										<a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
									</div>
								</div>

							</form>
						</div>
					
						<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
							
							<div class="alert alert-success">
								<i class="fa fa-fw fa-briefcase mr-1"></i> <u><?php echo datalist_Table('tbl_secondary', 'title', $result['plan']); ?></u> license is active!
							</div>
							
							<div class="card mb-3">
								<div class="card-body font-weight-bold pb-0">You will be billed <?php echo number_format($result['amount'], 2, '.', ',')." ".datalist_Table('tbl_secondary', 'title', $result['amount_unit']); ?> on <?php echo date('M d, Y', strtotime($result['expired_date'])); ?>.</div>
								<div class="card-body">
									<ul class="mb-1">
										<li><?php echo number_format($result['amount'], 2, '.', ',')." ".datalist_Table('tbl_secondary', 'title', $result['amount_unit']); ?> annually</li>
										<li><?php echo count($this->log_join_model->list('join_branch', $id)); ?> admin account(s)</li>
									</ul>
								</div>
							</div>
							
							<!--<div class="card mb-3">
								<div class="card-body font-weight-bold pb-0">Payment Method</div>
								<div class="card-body">
									<p class="mb-0">Your account is in good standing. We appreciate your support!</p>
								</div>
							</div>-->
							
							<div class="card mb-3">
								<div class="card-body font-weight-bold pb-0">Billing History</div>
								<div class="card-body px-0">
									<table class="table table-sm mb-0">
										<thead>
											<th>Date</th>
											<th>Description</th>
											<th style="width: 20%" class="text-right">Amount ($)</th>
										</thead>
										<tbody>
											<?php 
											
											$i=0; foreach($bill as $e) { $i++; 
												?>
												<tr>
													<td><?php echo $e['date_start']; ?></td>
													<td><?php echo $e['remark']; ?></td>
													<td class="text-right"><?php echo number_format($e['amount'], 2, '.', ','); ?> <?php echo datalist_Table('tbl_secondary', 'title', $e['amount_unit']); ?></td>
												</tr>
												<?php 
											} 
											
											if(count($bill) == 0) echo '<tr><td colspan="3" class="text-center">No result found</td></tr>';
											?>
										</tbody>
									</table>
								</div>
							</div>
							
						</div>
					
						<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
							<div class="alert alert-danger">
								<h5 class="font-weight-bold mb-2">Delete this company?</h5>
								<p class="mb-1A">This will permanently erase everything in <b><?php echo $result['title']; ?></b>.</p>
								<a href="javascript:;" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</a>
							</div>
						</div>
					
					</div>

                </div>
            </div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)" class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Logo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">    

				<div class="form-group">
					<label>Logo</label>
					<div class="media mb-3">
						<img src="<?php echo pointoapi_UploadSource($result['image']); ?>" class="border mr-3" style="height: 85px; width: 85px; object-fit: cover">
						<div class="media-body my-auto">
							<button type="button" class="btn btn-danger btn-sm text-white" onclick="remove_logo(<?php echo $result['pid']; ?>)">Remove</button>
						</div>
					</div>
					<input type="file" class="form-control" name="image">
				</div>
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="add-logo">Save</button>
            </div>

        </div>
    </div>
</form>