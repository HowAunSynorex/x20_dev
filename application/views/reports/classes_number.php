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
                            <label class="col-form-label col-md-3">Month</label>
                            <div class="col-md-9">
                                <input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
                            </div>
                        </div>
						
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
								<select class="form-control select2" name="form[]" multiple>
									<?php
									foreach($form as $e) {
										?>
										<option value="<?php echo $e['pid']; ?>" <?php echo (isset($_GET['form']) ? (in_array($e['pid'], $_GET['form']) ? "selected" : "") : ""); ?>><?php echo $e['title']; ?></option>
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
				<table class="table table-bordered table-sm">
					<?php $classes = group_by('class_title', $result); ?>
					<?php ksort($classes); ?>
					<?php foreach($classes as $k => $v) { ?>
						<?php $courses = group_by('course_title', $v); ?>
						<?php 
							$order = array("K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6", "F1", "F2", "F3", "F4", "F5");
							
							$orderedCourse = array();
							foreach ($order as $key) {
								if (isset($courses[$key]))
								{
									$orderedCourse[$key] = $courses[$key];
								}
							}
						?>
						<?php $teachers = group_by('teacher_pid', $v); ?>
						<thead>
							<tr>
								<th colspan="<?php echo (count($teachers) + 1); ?>" class="text-center"><?php echo $k; ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($orderedCourse as $c_key => $c_value) { ?>
								<?php $course_teachers = group_by('teacher_fullname_en', $c_value); ?>
								<?php ksort($course_teachers); ?>
								<tr>
									<th width="10%" class="text-center" rowspan="<?php echo (count($course_teachers) * 2) ?>"><?php echo $c_key; ?></th>
									<?php foreach($course_teachers as $t_key => $t_value) { ?>
										<th>
											<?php echo $t_key; ?>
										</th>
									<?php } ?>
								</tr>
								<?php foreach($course_teachers as $t_value) { ?>
									<tr>
										<th>
											<?php echo $t_value[0]['student_count']; ?> Student(s)
										</th>
									</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					<?php } ?>
				</table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>