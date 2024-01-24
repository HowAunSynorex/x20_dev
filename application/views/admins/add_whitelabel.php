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
            
            <form method="post">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
							<label class="col-md-3 col-form-label text-danger">Username</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="username" onkeyup="check_username(this.value)" required>
								<small id="username-status" class="help-block text-danger d-none">Username has been taken</small>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-danger">Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="password" required>
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-danger">Nickname</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="nickname" required>
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
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<div class="offset-md-3 col-md-9">
								<button type="submit" class="btn btn-primary" name="save">Save</button>
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