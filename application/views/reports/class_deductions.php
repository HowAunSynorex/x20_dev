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
							<label class="col-form-label col-md-3">Month</label>
							<div class="col-md-9">
								<input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Class</label>
							<div class="col-md-9">
								<select class="form-control select2" name="class">
									<option value="">-</option>
									<?php foreach($class as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['class'])) { if($e['pid'] == $_GET['class']) echo 'selected'; }?>><?php echo $e['title']; ?></option>
									<?php } ?>
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

			<?php echo alert_get(); ?>
			<div class="table-responsive">
				<table class="DTable table table-hover">
					<thead>
						<th style="width: 10%;">No</th>
						<th>Student</th>
						<th>Total Class</th>
						<th>Attended Class</th>
						<th>Class Left</th>
					</thead>
					<tbody>
						<?php $i=0; foreach($result as $e) { $i++; ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo (datalist_Table('tbl_users', 'fullname_cn', $e['student'])!= '')?datalist_Table('tbl_users', 'fullname_cn', $e['student']):datalist_Table('tbl_users', 'fullname_en', $e['student']); ?></td>
								<td><?php echo $e['class_total']; ?></td>
								<td><?php echo $e['attend_total']; ?></td>
								<td><?php echo $e['class_total'] - $e['attend_total']; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				
			</div>
			
		</div>
        <?php $this->load->view('inc/copyright'); ?>
	</div>
</div>