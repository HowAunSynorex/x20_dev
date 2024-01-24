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
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Tutor</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="teacher">
                                    <option value="">-</option>';

                                    <?php foreach ($teacher as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
                                    };?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Course</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="course">
                                    <option value="">-</option>
                                    <?php foreach ($course as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                                    };?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Fee</label>
                            <div class="col-md-9">
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text">$</div>
									</div>
									<input type="number" step="0.01" class="form-control" name="fee">
								</div>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Type</label>
                            <div class="col-md-9">
								<?php
								foreach(datalist('class_type') as $k => $v) {
									switch($k) {
										case 'check_in':
											if(branch_now('version') == 'shushi') {
												?>
												<div class="custom-control custom-radio custom-control-inline">
													<input type="radio" id="<?php echo $k; ?>" name="type" value="<?php echo $k; ?>" class="custom-control-input" <?php if($k == 'monthly') echo 'checked'; ?>>
													<label class="custom-control-label" for="<?php echo $k; ?>"><?php echo $v['label']; ?></label>
												</div>
												<?php
											}
											break;
										default:
											?>
											<div class="custom-control custom-radio custom-control-inline">
												<input type="radio" id="<?php echo $k; ?>" name="type" value="<?php echo $k; ?>" class="custom-control-input" <?php if($k == 'monthly') echo 'checked'; ?>>
												<label class="custom-control-label" for="<?php echo $k; ?>"><?php echo $v['label']; ?></label>
											</div>
											<?php 
											break;
									}
								} 
								?>
                            </div>
                        </div>
						
						<div class="form-group row credit-sec d-none">
                            <label class="col-form-label col-md-3 text-danger">Credit</label>
                            <div class="col-md-9">
								<input type="number" step="1" class="form-control" name="credit" value="1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
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
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_start">
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_end">
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>