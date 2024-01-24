<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container">
	
	<?php
	
	if($thispage['type'] == 'student') {
		
		?>
		<div class="py-2">
			<h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
		</div>
		
		<form method="get">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-form-label col-md-3">Month</label>
						<div class="col-md-9">
							<?php 
							if(isset($_GET['token'])) {
								
								?>
								<input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
								<?php
								
							}
							
							if(isset($_GET['student'])) {
								
								?>
								<input type="hidden" name="student" value="<?php echo $_GET['student']; ?>">
								<?php
								
							}
							?>
							<input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group text-right">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form>

		<?php echo alert_get(); ?>
		<div class="table-responsive">
			<table class="DTableA table table-hover table-bordered" style="width: 200%">
				<thead>
					<th style="width: 10px; height: 30px;">No</th>
					<th class="pr-5" style="width: 200px;">Class</th>
					<?php
					
					$date;
					
					if(isset($_GET['month'])) {
						$month = date('m', strtotime($_GET['month']));
						$year = date('Y', strtotime($_GET['month']));
						$date = $_GET['month'];
					} else {
						$month = date('m');
						$year = date('Y');
						$date = date('Y-m');
					}
					
					$days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);
					
					for($i=1; $i<=$days_in_month; $i++) {
						echo '<th style="width: 20px; height: 50px;" id="day-'.$i.'">'.$i.'</th>';
					}
						
					?>
				</thead>
				<tbody> 
					<?php 
					$i=0; $dayList = '';
					if(isset($result)) {
						
						foreach($result as $e) { $i++;
						
							if(datalist_Table('tbl_classes', 'active', $e['class']) == 1) {
								
								$class = $this->tbl_classes_model->view($e['class']);
								$classDays= [];
								foreach([
									'dy_1' => '1',
									'dy_2' => '2',
									'dy_3' => '3',
									'dy_4' => '4',
									'dy_5' => '5',
									'dy_6' => '6',
									'dy_7' => '0',
									] as $k => $v) {
									if($class[0][$k] != null) {
										$classDays[] = $v;
									}
								}
								
								$month; $year;
								
								if(isset($_GET['month'])) {
									$month = date('m', strtotime($_GET['month']));
									$year = date('Y', strtotime($_GET['month']));
								} else {
									$month = date('m');
									$year = date('Y');
								}
								
								
								$days = array();
								$firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));
								$addDays = (8 - $firstDay) % 7;
								
								foreach($classDays as $e2) {
									$days[] = date('d', mktime(0, 0, 0, $month, $e2 + $addDays, $year));
									$nextMonth = mktime(0, 0, 0, $month + 1, 1, $year);
									for ($week = 1, $time = mktime(0, 0, 0, $month, $e2 + $addDays + $week * 7, $year);
										$time < $nextMonth;
										++$week, $time = mktime(0, 0, 0, $month, $e2 + $addDays + $week * 7, $year))
									{
										$days[] = date('d', $time);
									}
								}
								
								$result2 = $days;
							
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td>
									<?php
									echo datalist_Table('tbl_classes', 'title', $e['class']);
																		
									$join_date = $this->log_join_model->list('join_class', $e['branch'], [
																
										"class" => $e['class'],
										"user" => $e['user']
										
									])[0]['date'];
									
									if(date('Y', strtotime($join_date)) == $year) {
										
										if(date('m', strtotime($join_date)) > $month) {
											
											?>
											<span class="d-block text-muted small">
												Join on
												<?php
												echo $this->log_join_model->list('join_class', $e['branch'], [
													
													"class" => $e['class'],
													"user" => $e['user'],
													
												])[0]['date'];
												?>
											</span>
											<?php
											
										}
										
									} else {
										
										?>
										<span class="d-block text-muted d-block">
											Join on
											<?php
											echo $this->log_join_model->list('join_class', $e['branch'], [
												
												"class" => $e['class'],
												"user" => $e['user'],
												
											])[0]['date'];
											?>
										</span>
										<?php
										
									}
									?>
									</td>
									<?php 
										for($j=1; $j<=$days_in_month; $j++) {
											echo '<td class="font-weight-bold day-'.$j.'">';
																				
											foreach($result2 as $e2){
												$x=time().rand(11,99);
												if($e2 == $j) {
													$dayList .= $j."+";
													
													if(!empty($this->log_join_model->list('class_attendance', $branch, ["class" => $e['class'], "user" => $e['user'], "date" => $date."-".$j])[0])) {
														echo '<i class="fa fa-check-circle fa-fw text-success"></i>';
													} else {
														echo '<i class="fa fa-times-circle fa-fw text-danger"></i>';
													}	
													
												}
											}
											echo '</td>';
										}												
									?>
								</tr><?php
								
							}
						
						}
					}
					?>
				</tbody>
			</table>
			<input type="hidden" name="removed_list">
			<input type="hidden" name="days" value="<?php echo $dayList; ?>"/>
		</div>
		<?php
		
	} else {
		
		?>
	
		<div class="container-fluid container-wrapper pt-5">
			<div class="row">
				<div class="offset-md-3 col-md-6">
					<div class="alert alert-info">
						Please select the student to check the attendance.
					</div>
					<div class="form-group row">
						<label class="col-form-label col-md-3">Student</label>
						<div class="col-md-9">
							<select class="form-control select2" onchange="class_landing(this.value)" id="student">
								<option value="">-</option>
								<?php
								foreach($this->tbl_users_model->list_v2([
									'type' => 'student',
									'active' => 1,
									'parent' => $parent,
								]) as $e ) {
									?>
									<option value="<?php echo $e['pid']; ?>"><?php echo $e['fullname_en']; ?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php
		
	} ?>

	<p class="text-muted small text-center mt-4"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.<br>Powered by <b>Synorex</b></p>

</div>