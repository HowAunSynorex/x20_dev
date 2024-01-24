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
            
            <form method="post" enctype="multipart/form-data">
				<div class="row">
                    <div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3 text-danger">Title</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" required>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Logo</label>
                            <div class="col-md-9">
								<img src="https://cdn.synorex.link/assets/images/blank/4x3.jpg" class="mb-3 rounded border" style="height: 85px; width: 85px; object-fit: cover">
								<input type="file" class="form-control" name="image">
                            </div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-md-3">Company No</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="ssm_no">
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-form-label col-md-3">Country</label>
							<div class="col-md-9">
								<select class="form-control select2" name="country">
									<option value="">-</option>';

									<?php foreach ($country as $e) {?>
									<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
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
									<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
									<?php }; ?>

								</select>
							</div>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" checked>
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
								<input type="tel" class="form-control" name="phone" placeholder="Primary">
								<input type="tel" class="form-control mt-2" name="phone2">
								<input type="tel" class="form-control mt-2" name="phone3">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-md-3">Email</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email" placeholder="Primary">
								<input type="email" class="form-control mt-2" name="email2">
								<input type="email" class="form-control mt-2" name="email3">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Address</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="address" placeholder="Primary">
								<input type="text" class="form-control mt-2" name="address2">
								<input type="text" class="form-control mt-2" name="address3">
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
				</div>

			</form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>