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
							<a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</a>
						</div>
					</div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <form method="post">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
							<label class="col-md-3 col-form-label">Username</label>
							<div class="col-md-9">
								<p class="form-control-plaintext"><?php echo $result['username']; ?></p>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="password" placeholder="Set new password">
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-md-3 col-form-label text-danger">Nickname</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="nickname" value="<?php echo $result['nickname']; ?>" required>
							</div>
						</div>
                    </div>
					<div class="col-md-6">
					
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php if($result['active']) echo 'checked'; ?>>
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