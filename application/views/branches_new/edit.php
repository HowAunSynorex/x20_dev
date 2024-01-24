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
				<div class="col-6 my-auto text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <form method="post" enctype="multipart/form-data">
				<div class="row">
                    <div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3 text-danger">Title</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Logo</label>
                            <div class="col-md-9">
								<div class="mb-3 d-flex align-items-end">
									<img src="<?php echo empty($result['image']) ? 'https://cdn.synorex.link/assets/images/blank/4x3.jpg' : datalist_Table('tbl_uploads', 'file_source', $result['image']); ?>" class="rounded border" style="height: 85px; width: 85px; object-fit: cover">
									<div class="ml-3">
										<button type="button" class="btn btn-danger btn-sm text-white" onclick="remove_logo(<?php echo $result['pid']; ?>)">Remove</button>
									</div>
								</div>
								<input type="file" class="form-control" name="image">
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
									<option value="">-</option>';
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
									<option value="">-</option>';
									<?php foreach ($currency as $e) {?>
										<option value="<?php echo $e['pid']; ?>" <?php if($result['currency'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
									<?php }; ?>
								</select>
							</div>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php IF($result['active'] == 1) echo 'checked'; ?>>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
					</div>
				</div>
				
				<hr class="mb-4">
				
				<div class="row">
                    <div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Phone</label>
							<div class="col-md-9">
								<input type="tel" class="form-control" name="phone" placeholder="Primary" value="<?php echo $result['phone']; ?>">
								<input type="tel" class="form-control mt-2" name="phone2" value="<?php echo $result['phone2']; ?>">
								<input type="tel" class="form-control mt-2" name="phone3" value="<?php echo $result['phone3']; ?>">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-md-3">Email</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email" placeholder="Primary" value="<?php echo $result['email']; ?>">
								<input type="email" class="form-control mt-2" name="email2" value="<?php echo $result['email2']; ?>">
								<input type="email" class="form-control mt-2" name="email3" value="<?php echo $result['email3']; ?>">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Address</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="address" placeholder="Primary" value="<?php echo $result['address']; ?>">
								<input type="text" class="form-control mt-2" name="address2" value="<?php echo $result['address2']; ?>">
								<input type="text" class="form-control mt-2" name="address3" value="<?php echo $result['address3']; ?>">
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<div class="col-md-6 offset-md-3">
								<button type="submit" name="save" class="btn btn-primary">Save</button>
								<a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
							</div>
						</div>
					</div>
					<div class="col-6 my-auto text-right">
						<?php if( check_module('Classes/Delete') ) { ?>
							<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
						<?php } ?>
					</div>
				</div>

			</form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>