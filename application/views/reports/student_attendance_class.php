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
					
						<!--<div class="form-group row">
							<label class="col-form-label col-md-3">Student</label>
							<div class="col-md-9">
								<select class="form-control select2" name="student">
									<option value="">-</option>
									<?php foreach($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['student'])) { if($e['pid'] == $_GET['student']) echo 'selected'; }?>><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>-->
						
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
							<label class="col-form-label col-md-3">Month</label>
							<div class="col-md-9">
								<input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
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
				<table class="DTable2 table table-hover table-bordered" style="width: 200%">
					<thead>
						<th>No</th>
						<th>Class</th>
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
							echo '<th id="day-'.$i.'">'.$i.'</th>';
						}
							
						?>
						<th class="text-center">Total</th>
					</thead>
					<tbody> 
						<?php 
						
						$i=0;
						$existing_user = [];
												
						if(isset($result)) {
							
							foreach($result as $e)  {
							
								$i++;
							
								if(!in_array($e['user'], $existing_user) && datalist_Table('tbl_users', 'is_delete', $e['user']) == 0) {
									
									$existing_user[] = $e['user'];
								
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td>
											<?php
											echo (datalist_Table('tbl_users', 'fullname_cn', $e['user']) !='')?datalist_Table('tbl_users', 'fullname_cn', $e['user']):datalist_Table('tbl_users', 'fullname_en', $e['user']);
											
											$join_date = $this->log_join_model->list('join_class', branch_now('pid'), [
															
												"class" => $e['class'],
												"user" => $e['user']
												
											])[0]['date'];
											
											if(date('Y', strtotime($join_date)) == $year) {
												
												if(date('m', strtotime($join_date)) > $month) {
													
													?>
													<span class="d-block text-muted small">
														Join on
														<?php
														echo $this->log_join_model->list('join_class', branch_now('pid'), [
															
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
													echo $this->log_join_model->list('join_class', branch_now('pid'), [
														
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
										
										$attended = 0;
										$total_class = 0;
										
										for($j=1; $j<=$days_in_month; $j++) {
											
											echo '<td>';
											
											$date = ($j < 10) ? '0'.$j : $j;
											$date = $_GET['month'] . '-' . $date;
											
											$day = date('N', strtotime($date));	
											if(count($this->log_join_model->list_all(['type' => 'class_timetable', 'qty' => $day, 'class'=> $e['class'], 'is_delete'=> 0 ]))){											
											//if(!empty(datalist_Table('tbl_classes', 'dy_'.$day, $e['class']))) {
												
												$total_class++;
																								
												if(!empty($this->log_join_model->list('class_attendance', branch_now('pid'), ["class" => $e['class'], 'active' => 1, "user" => $e['user'], "date" => $date])[0])) {
													
													$attended++;
													echo '<span class="text-success font-weight-bold text-center d-block">&#10003;</span>';
													
												} else {
													
													echo '<span class="text-danger font-weight-bold text-center d-block">&#215;</span>';
													
												}	
												
											}
											
											echo '</td>';
											
										}	
										
										?>
										<td class="text-center"><?php echo $attended . '/' . $total_class; ?></td>
									</tr><?php
									
								}
							
							}
							
						} else {
							
							?>
							<tr>
								<td class="text-center" colspan="<?php echo $days_in_month + 2; ?>">No result found</td>
							</tr>
							<?php
							
						}
						?>
					</tbody>
				</table>
				
			</div>
			
		</div>
        <?php $this->load->view('inc/copyright'); ?>
	</div>
</div>