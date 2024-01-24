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
                                <select class="form-control select2" onchange="window.location.href='<?php echo base_url('homework/add/'); ?>'+this.value" name="class" required>
                                    <option value="">-</option>
                                    <?php foreach ($class as $e) { ?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if( isset($id) ) {if($id == $e['pid'] ) { echo 'selected'; } } ?>><?php echo $e['title']; ?></option>
                                    <?php } ?>
                                </select>
                                <?php if(empty($id)) {?>
                                <div class="alert alert-warning mt-2">Please select a class</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
				</div>
					
				<?php if(isset($id) && !empty($id)) { ?>
                    
                <hr class="mb-4">

                <div class="row">
					
					<div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Student</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="student">
									<option value="">-</option>
									<?php foreach($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>"><?php echo $e['fullname_en']; ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
						
					</div>

					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status">
									<?php foreach(datalist('homework_status') as $k => $v) { ?>
										<option value="<?php echo $k; ?>"><?php echo $v['label']; ?></option>
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
                                <input type="text" class="form-control" name="subject" required>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Body</label>
                            <div class="col-md-9">
                                <textarea class="form-control" rows="4" name="body" id="CK"></textarea>
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
                </div>
				
				<?php } ?>

            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>