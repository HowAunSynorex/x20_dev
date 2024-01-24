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

            <form id="search_form" method="get">
                <div class="row">
                    <div class="col-md-6">
					
						<div class="form-group row">
							<label class="col-form-label col-md-3">Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['teacher'])) {if($e['pid'] == $_GET['teacher']) echo 'selected';} ?>><?php echo $e['fullname_en']; ?></option>
									<?php };?>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Form</label>
							<div class="col-md-9">
								<select class="form-control select2" name="form">
									<option value="">-</option>
									<?php
									foreach($form as $e) {
										?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['form'])) { if($_GET['form'] == $e['pid']) { echo 'selected'; } } ?> ><?php echo $e['title']; ?></option>
										<?php
									} ?>
								</select>
							</div>
						</div>
						
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
						
                    </div>
                </div>
            </form>
			
            <div class="table-responsive">
				<table class="DTable table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>Teacher</th>
							<th>Student Code</th>
							<th>Student Name</th>
							<th>IC</th>
							<th>School</th>
							<th>Subject</th>
							<th>Parent Tel</th>
							<th>Date Call</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=0; foreach($result AS $e) { $i++; ?>
							<tr>
								<td>
									<?php echo $i; ?>
								</td>
								<td>
									<?php echo $e['form_teacher_fullname_en']; ?> <?php echo $e['form_teacher_fullname_cn']; ?>
								</td>
								<td>
									<a href="<?php echo base_url('students/edit/' . $e['student_pid']); ?>">
										<?php echo $e['student_code']; ?>
									</a>
								</td>
								<td>
									<?php echo $e['student_fullname_en']; ?> <?php echo $e['student_fullname_cn']; ?>
								</td>
								<td>
									<?php echo $e['student_nric']; ?>
								</td>
								<td>
									<?php echo $e['school_title']; ?>
								</td>
								<td>
									<?php echo $e['join_class_title']; ?>
								</td>
								<td>
									<?php echo $e['parent_fullname_en']; ?>
								</td>
								<td>
									<input onchange="update_student_date_call($(this))" data-id="<?php echo $e['student_pid']; ?>" type="date" class="form-control student-date-call" name="date_call" value="<?php echo empty($e['student_date_call']) ? "" : $e['student_date_call'] ?>">
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>