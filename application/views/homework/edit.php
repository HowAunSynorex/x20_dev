<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

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

            <form method="post">
			
				<div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Class</label>
							<div class="col-md-9">
								<p class="form-control-plaintext"><a href="<?php echo base_url('classes/edit/'.$result['class']); ?>"><?php echo datalist_Table('tbl_classes', 'title', $result['class']); ?></a></p>
							</div>
                        </div>
                    </div>
				</div>
					                    
                <hr class="mb-4">

                <div class="row">
					
					<div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Student</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="student">
									<option value="">-</option>
									<?php foreach($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($result['student'] == $e['pid']) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php echo $result['date']; ?>" required>
                            </div>
                        </div>
						
					</div>

					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status">
									<?php foreach(datalist('homework_status') as $k => $v) { ?>
										<option value="<?php echo $k; ?>" <?php if($result['status'] == $k) echo 'selected'; ?>><?php echo $v['label']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
					</div>
					
				</div>
					
				<hr class="mb-4">

                <div class="row">
					
					<div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Subject</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="subject" value="<?php echo $result['subject']; ?>" required>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Body</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="4" name="body" id="CK"><?php echo $result['body']; ?></textarea>
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
								<?php
								if( check_module('Homework/Delete') ) {
									
									?>
										<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
								<?php
									
								}
								?>
                            </div>
                        </div>
                    </div>
                </div>
				
            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>