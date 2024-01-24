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
					<?php if( check_module('Inventory/Delete') ) { ?>
						<div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
							<div class="dropdown-menu dropdown-menu-right">
							</div>
						</div>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php 
			/*if(!empty($result['otp'])) {
				?>
				<div class="alert alert-warning">
					<h4 class="text-center font-weight-bold pb-1">Link with your device</h4>
					<ol>
						<li>Open this link <a href="https://dev.synorexcloud.com/robocube-tuition/home/attendance_pin/162849002945">https://dev.synorexcloud.com/robocube-tuition/home/attendance_pin/162849002945</a> on the device which you like to register.</li>
						<li>Enter your device ID <span class="font-weight-bold">#<?php echo $result['pid']; ?></span> in the page to register your device.</li>
					</ol>
				</div>
				<?php
			}*/
			?>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Type</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="type" required>
									<?php foreach(datalist('device_type') as $k => $v ) { ?>
										<option value="<?php echo $k; ?>" <?php if($result['type'] == $k) echo 'selected'; ?>><?php echo $v['label']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
						
						<?php if($result['type'] == 'web_rfid') { ?>
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Temperature</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="temp_enable" required>
									<?php foreach([ 1 => 'Enable', 0 => 'Disabled' ] as $k => $v ) { ?>
										<option value="<?php echo $k; ?>" <?php if($result['temp_enable'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
						
						<?php } ?>
						
						<?php
						if(!empty($result['otp'])) {
							?>
							<div class="form-group row">
								<label class="col-form-label col-md-3">Link</label>
								<div class="col-md-9">
									<input type="text" class="form-control" Aname="title" value="<?php echo base_url('home/attendance/'.$result['pid']); ?>" onclick="this.select()" readonly>
								</div>
							</div><?php
						} ?>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input <?php if($result['active'] == 1) echo 'checked'; ?> type="checkbox" class="custom-control-input" id="checkbox-active" name="active">
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>

                    </div>
                </div>
               
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>
                    </div>
					<div class="col-6 my-auto text-right">
						<?php if( check_module('Inventory/Delete') ) { ?>
							<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
						<?php } ?>
					</div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Inventory/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>