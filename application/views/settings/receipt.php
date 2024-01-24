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
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Receipt No</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Print</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-3">Account</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 4 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-4">Payment</a>
				</li>
			</ul>

			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					<form method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-4 offset-md-1">
							
								<?php if( count($result) > 0 ) echo '<div class="alert alert-info">Please delete all receipts before modifying the number format</div>'; ?>
								
								<div class="form-group">
									<label>Format</label>
									<input type="text" class="form-control" name="receipt_no" value="<?php echo $branch_data[0]['receipt_no']; ?>" onchange="return_sample()" <?php if( count($result) > 0 ) echo 'readonly'; ?>>
									<span class="form-text text-muted small">
										Remark:<br>
										%DD%: Day<br>
										%MM%: Month<br>
										%YY%: Year<br>
										%YYYY%: Full year<br>
									</span>
									<!--<select class="form-control" name="receipt_no" required>
										<?php foreach (datalist('receipt_no_format') as $e) { ?>
										<option value="<?php echo $e; ?>" <?php if( $e == branch_now('receipt_no') ) echo 'selected'; ?>><?php echo $e; ?></option>
										<?php } ?>
									</select>-->
								</div>
								
								<div class="form-group">
									<label>Max No</label>
									<input type="number" class="form-control" name="receipt_no_max" value="<?php echo $branch_data[0]['receipt_no_max']; ?>" onchange="return_sample()" <?php if( count($result) > 0 ) echo 'readonly'; ?>>
								</div>
								
								<div class="form-group">
									<label>Sample Receipt No</label>
									<input type="text" class="form-control" name="sample" value="<?php echo new_receipt_no(); ?>" readonly>
								</div>
								
								<div class="form-group">
									<button type="submit" class="btn btn-primary" name="save">Save</button>
								</div>
								
							</div>
							
							<div class="col-md-4 offset-md-1">
							
								<?php if( count($result) > 0 ) echo '<div class="alert alert-info">Please delete all receipts before modifying the number format</div>'; ?>
								
								<div class="form-group">
									<label>Format (Draft)</label>
									<input type="text" class="form-control" name="receipt_no_draft" value="<?php echo $branch_data[0]['receipt_no_draft']; ?>" onchange="return_sample()" <?php if( count($result) > 0 ) echo 'readonly'; ?>>
									<span class="form-text text-muted small">
										Remark:<br>
										%DD%: Day<br>
										%MM%: Month<br>
										%YY%: Year<br>
										%YYYY%: Full year<br>
									</span>
									<!--<select class="form-control" name="receipt_no_draft" required>
										<?php foreach (datalist('receipt_no_format') as $e) { ?>
										<option value="<?php echo $e; ?>" <?php if( $e == branch_now('receipt_no_draft') ) echo 'selected'; ?>><?php echo $e; ?></option>
										<?php } ?>
									</select>-->
								</div>
								
								<div class="form-group">
									<label>Max No (Draft)</label>
									<input type="number" class="form-control" name="receipt_no_max_draft" value="<?php echo $branch_data[0]['receipt_no_max_draft']; ?>" onchange="return_sample()" <?php if( count($result) > 0 ) echo 'readonly'; ?>>
								</div>
								
								<div class="form-group">
									<label>Sample Receipt No (Draft)</label>
									<input type="text" class="form-control" name="sample" value="<?php echo new_receipt_no('', 'draft'); ?>" readonly>
								</div>
								
								<div class="form-group">
									<button type="submit" class="btn btn-primary" name="save">Save</button>
								</div>
								
							</div>
						</div>
					</form>
				</div>

				<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
					<div class="container">
						<div class="row">
						
							<?php if($branch_data[0]['receipt_print'] != null) { ?>
								<input type="hidden" name="selected-receipt" value="<?php echo $branch_data[0]['receipt_print']; ?>">
								<div class="col-md-4 mb-4" name="first-receipt" id="selected-receipt-<?php echo $branch_data[0]['receipt_print']; ?>">
									<div class="card">
										<img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_secondary', 'image', $branch_data[0]['receipt_print'])); ?>" class="border-bottom card-img-top">
										<div class="card-body">
											<h6 class="card-text font-weight-bold mb-3"><?php echo datalist_Table('tbl_secondary', 'title', $branch_data[0]['receipt_print']); ?></h6>
											<div class="receipt-group">
												<label class="btn btn-secondary" id="<?php echo $branch_data[0]['receipt_print']; ?>">
													<span>Active</span>
													<input class="d-none" type="radio" value="<?php echo $branch_data[0]['receipt_print']; ?>" name="receipt">
												</label>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<?php
							foreach($receipt as $e) {
								?>
								<div class="col-md-4 mb-4" id="receipt-<?php echo $e['pid']; ?>">
									<div class="card">
										<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="border-bottom card-img-top">
										<div class="card-body">
											<h6 class="card-text font-weight-bold mb-3"><?php echo $e['title']; ?></h6>
											<div class="receipt-group">
												<label class="btn btn-secondary" id="<?php echo $e['pid']; ?>">
													<span>Active</span>
													<input class="d-none" type="radio" value="<?php echo $e['pid']; ?>" name="receipt">
												</label>
											</div>
										</div>
									</div>
								</div>
								<?php 
							} 
							?>
						</div>
					</div>
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
				 
					<form method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-4 offset-md-4">
							
								<div class="form-group">
									<label>Tax</label>
									<div class="input-group">
										<input type="number" class="form-control" name="tax" value="<?php echo branch_now('tax') ?>">
										<div class="input-group-append">
											<div class="input-group-text">%</div>
										</div>
									</div>
								</div>
								
								<!--<div class="form-group">
									<label>Rounding</label>
									<input type="number" class="form-control" name="rounding" value="<?php echo branch_now('rounding') ?>">
								</div>-->
								
								<div class="form-group">
									<button type="submit" class="btn btn-primary" name="save-account">Save</button>
								</div>
								
							</div>
						</div>
					</form>
					
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 4 ) echo 'show active'; ?>" id="tab-4">
				  <div class="col-md-6">
					<div class="table-responsive mt-2">
						<form method="post">
						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="payment">Save</button>
						</div>
						<table class="DTable2 table">
							<thead>
								<th>No</th>
								<th>Code</th>
								<th>Name</th>
								<th></th>
							</thead>
							<tbody>
							  
								<?php $i=0; foreach($students as $e) { $i++; ?>
								<tr>
								  <td><?php echo $i;?></td>
								  <td><?php echo $e['code']; ?></td>
								  <td><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></td>
								  <td class="text-right">
								  <input type="hidden" name="student_id[<?php echo $e['pid']; ?>]" value="<?php echo $e['pid']; ?>">
								  <input type="checkbox" id="studentcheck" name="studentcheck[<?php echo $e['pid']; ?>]" value="1" <?php echo ($e['payment'] == 1)?'checked':''; ?>>
								  </td>
								</tr>
								<?php
								}
								if(empty($result)) {
									?>
									<tr><td colspan="8" class="text-center">No result found</td></tr>
									<?php
								}
								?>
							</tbody>
						 </table>
						 <div class="form-group">
							<button type="submit" class="btn btn-primary" name="payment">Save</button>
						</div>
						</form>
					</div>
				  </div>
				</div>

			</div>
        </div>
        
        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Settings/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>