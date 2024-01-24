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
							<label class="col-form-label col-md-3">Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['teacher'])) {if($e['pid'] == $_GET['teacher']) echo 'selected';} ?>><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></option>
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

			<?php echo alert_get();  ?>
			
			<table class="DTable table table-bordered my-2">
				<thead>
					<th style="width:10%">No</th>
					<th style="width:15%">Class</th>
					<th style="width:15%">Teacher</th>
					<th style="width:15%">Date</th>
					<th style="width:15%">Total</th>
					<th style="width:15%">None</th>
					<th style="width:15%">Present</th>
					<th style="width:15%">Absent</th>
				</thead>
				<tbody>
					<?php 
						$i=0;
							
						foreach($result as $e) {
							$i++;
						?>
							<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $e['class_day']; ?> <?php echo $e['time_range']; ?><br/><?php echo $e['class_title']; ?> <?php echo $e['sub_class_title']; ?></td>
							<td><?php echo ($e['teacher_fullname_cn'] != '')?$e['teacher_fullname_cn']:$e['teacher_fullname_en']; ?></td>
							<td><?php echo $e['class_date']; ?></td>
							<td><?php echo $e['join_count']; ?></td>
							<td><?php echo $e['none_count'] < 0 ? 0 : $e['none_count']; ?></td>
							<td><?php echo $e['present_count']; ?></td>
							<td><?php echo $e['absent_count']; ?></td>
						</tr><?php
						}
					?>
				</tbody>
			</table>

		</div>

        <?php $this->load->view('inc/copyright'); ?>
		
	</div>
</div>