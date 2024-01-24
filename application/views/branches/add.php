<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar-lite', $thispage); ?>

<div style="margin-top: 52px;">

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <div class="row">
                <div class="col-md-8 offset-md-2">
				
					<a href="<?php echo base_url('branches/list'); ?>" class="d-block mb-4 mt-3 small text-muted"><i class="fa fa-fw fa-chevron-left"></i> Back</a>
					
					<div class="row">
						<div class="col-md-12">
							<h4 class="mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
						</div>
					</div>
				
					<?php echo alert_get(); ?>

					<form method="post">

						<div class="form-group row">
							<label class="col-form-label col-md-3 text-danger">Title</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="title" required>
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

						<hr class="mb-4">

						<div class="form-group row">
							<label class="col-form-label col-md-3">Phone</label>
							<div class="col-md-9">
								<input type="tel" class="form-control" name="phone">
								<input type="tel" class="form-control mt-2" name="phone2">
								<input type="tel" class="form-control mt-2" name="phone3">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-md-3">Email</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email">
								<input type="email" class="form-control mt-2" name="email2">
								<input type="email" class="form-control mt-2" name="email3">
							</div>
						</div>

						<div class="form-group row">
							<label class="col-form-label col-md-3">Address</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="address">
								<input type="text" class="form-control mt-2" name="address2">
								<input type="text" class="form-control mt-2" name="address3">
							</div>
						</div>

						<hr class="mb-4">

						<div class="row">
							<div class="col-md-6 offset-md-3">
								<div class="form-group">
									<button type="submit" name="save" class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>

					</form>

                </div>
            </div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>