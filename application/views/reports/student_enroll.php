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
					<th>Date</th>
					<th>Code</th>
					<th>Name</th>
					<th>Class</th>
					<th>Enroll By</th>
				</thead>
				<tbody>
					<?php $i=1; foreach($result as $e) { ?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $e['date']; ?></td>
							<td><?php echo $e['student_code']; ?></td>
							<td><?php echo $e['student_fullname_cn']; ?> <?php echo $e['student_fullname_en']; ?> </td>
							<td>
								<?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?> <?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?>
							</td>
							<td><?php echo $e['enroll_fullname_en']; ?></td>
						</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
        <?php $this->load->view('inc/copyright'); ?>
	</div>
</div>