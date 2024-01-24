<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>
<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>
	<div id="page-content-wrapper">
		<div class="container-fluid py-2">
			<div class="row">
				<div class="col-6 my-auto">
					<h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?>
					</h4>
				</div>
			</div>
		</div>
		<div class="container-fluid container-wrapper">
		
			<form method="get">
				<div class="row">
                    <div class="col-md-6">
				
						<div class="form-group row">
							<label class="col-form-label col-md-3">Start Date</label>
							<div class="col-md-9">
								<input type="date" class="form-control" name="start_date" value="<?php if(isset($_GET['start_date'])) { echo $_GET['start_date']; } else {
									echo date('Y-m-d'); } ?>">
							</div>
						</div>
					
						<div class="form-group row">
							<label class="col-form-label col-md-3">End Date</label>
							<div class="col-md-9">
								<input type="date" class="form-control" name="end_date" value="<?php if(isset($_GET['end_date'])) { echo $_GET['end_date']; } else {
									echo date('Y-m-d'); } ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Student</label>
							<div class="col-md-9">
								<select class="form-control select2" name="student">
									<option value="">-</option>';
									<?php foreach ($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['student'])) {if($e['pid'] == $_GET['student']) echo 'selected';} ?>><?php echo $e['code']; ?> <?php echo $e['fullname_cn']; ?> (<?php echo $e['fullname_en']; ?>)</option>
									<?php };?>
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
			
			<table class="DTable table table-bordered">
				<thead>
					<th>No</th>
					<th>Code</th>
					<th>Name</th>
					<th>Total Subject</th>
					<th>Total Class</th>
					<th>Present</th>
					<th>Absent</th>
					<th>%</th>
				</thead>
				<tbody>
					<?php $i=1; foreach(group_by('student_pid', $result) as $k => $v) { 
						$student = search($v, 'student_pid', $k);
						$class = group_by('class_id', $student);
						
						$count_sub_class = 0;
						foreach(array_keys($class) as $e)
						{
							$sub_class = search($student, 'class_id', $e);
							$count_sub_class += count($sub_class) >= 5 ? 4 : count($sub_class);
						}
					?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $student[0]['student_code']; ?></td>
							<td>
								<?php echo $student[0]['student_fullname_cn']; ?> <?php echo $student[0]['student_fullname_en']; ?> 
							</td>
							<td>
								<?php echo count($class); ?>
							</td>
							<td>
								<?php echo $count_sub_class; ?>
							</td>
							<td>
								<?php echo array_sum(array_column($v, 'present_count')); ?>
							</td>
							<td>
								<?php echo array_sum(array_column($v, 'absent_count')); ?>
							</td>
							<td>
								<?php echo number_format($count_sub_class > 0 ? ((array_sum(array_column($v, 'absent_count')) / $count_sub_class) * 100) : 0, 0, '.', ''); ?>%
							</td>
						</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
        <?php $this->load->view('inc/copyright'); ?>
	</div>
</div>