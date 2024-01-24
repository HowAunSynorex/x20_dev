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
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['student'])) {if($e['pid'] == $_GET['student']) echo 'selected';} ?>><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></option>
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
			
			<table class="table table-bordered">
				<thead>
					<th>Student</th>
					<th>Total Days</th>
					<th>Total Attendance</th>
				</thead>
				<tbody>
					<?php
					
					if(isset($_GET['start_date'])) { 
						$start_date = $_GET['start_date']; 
					} else { 
						$start_date = date('Y-m-d'); 
					}
					
					if(isset($_GET['end_date'])) { 
						$end_date = $_GET['end_date']; 
					} else { 
						$end_date = date('Y-m-d'); 
					}
					
					$diff = strtotime($end_date) - strtotime($start_date);
					$diff = round($diff / (60 * 60 * 24)) + 1;
					
					$total_attendance = 0;
					
					if(isset($result)) {
						foreach($std_result as $e) {
							$type = datalist_Table('tbl_users', 'type', $e['user']);
							if ( $type == 'student' && check_user_availability($e['user']) ) {
								if(count($this->log_attendance_model->list([
									'user' => $e['user'],
									'DATE(datetime)' => date('Y-m-d', strtotime($e['datetime'])),
								])) > 0) {
									$total_attendance++;
								}
								?>
								<tr>
									<td><?php echo (datalist_Table('tbl_users', 'fullname_cn', $e['user']) !='')?datalist_Table('tbl_users', 'fullname_cn', $e['user']):datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
									<td><?php echo $diff; ?></td>
									<td><?php echo number_format(($total_attendance / $diff * 100), 2, '.', '') . '%'; ?></td>
								</tr>
								<?php
							}
						}
					} else {
						?>
						<tr><td colspan="3" class="text-center">No result found</td></tr>
						<?php
					}
					
					if(isset($_GET['student']) && count($std_result) == 0) {
						?>
						<tr>
							<td><?php echo (datalist_Table('tbl_users', 'fullname_cn', $_GET['student']) !="")?datalist_Table('tbl_users', 'fullname_cn', $_GET['student']):datalist_Table('tbl_users', 'fullname_en', $_GET['student']); ?></td>
							<td><?php echo $diff; ?></td>
							<td><?php echo number_format(0, 2, '.', '') . '%'; ?></td>
						</tr>
						<?php
					}
										
					?>
				</tbody>
			</table>

            <?php echo alert_get();  ?>
			<div class="table-responsive">
				<table class="DTable2 table table-bordered my-2">
					<thead>
						<th style="width:10%">No</th>
						<th>Date</th>
						<th>Student</th>
						<?php
					
						if(isset($_GET['start_date'])) { 
							$start_date = $_GET['start_date']; 
						} else { 
							$start_date = date('Y-m-d'); 
						}
						
						if(isset($_GET['end_date'])) { 
							$end_date = $_GET['end_date']; 
						} else { 
							$end_date = date('Y-m-d'); 
						}
						
						$dataList = [];
						
						if(isset($result)) {
														
							foreach($result as $e) {
								
								$type = datalist_Table('tbl_users', 'type', $e['user']);
								
								if ( $type == 'student' && check_user_availability($e['user']) ) {
									
									$query = $this->log_attendance_model->list([
											
										'user' => $e['user'],
										'DATE(datetime)' => date('Y-m-d', strtotime($e['datetime'])),
									
									]);
									
									foreach( $query as $e2 ) {
										
										array_push(
										
											$dataList,
											count($query).','.$e['user'].','.date('Y-m-d', strtotime($e['datetime']))
											
										);
									}
									
								}
							}
							
							$countList = array(); $userList = array(); $dateList = array();
							
							foreach($dataList as $k => $v) {
								
								$countList[] = explode(',', $v)[0];
								$userList[] = explode(',', $v)[1];
								$dateList[] = explode(',', $v)[2];
								
							}
							
							if($countList != null) {
							
								$max = array_search(max($countList), $countList);
								$user = $userList[$max];
								$date = $dateList[$max];
								$j = 0; $x = 1;
								$data = $this->log_attendance_model->list([
											
									'user' => $user,
									'DATE(datetime)' => $date,
								
								]);
								
								foreach($data as $k => $v) {	
								
									$j++;
									
									if($v['action'] == 'in') {
										echo '<th>'.ucfirst($v['action']).' #'.$x.'</th>';
									} else {
										echo '<th>'.ucfirst($v['action']).' #'.$x.'</th>';
										$x++;
									}
									
								}
								
							}
							
						}
						
					?>
					</thead>
					<tbody>
					<?php 
						$i=0;
						
						if(isset($result)) {
							
							foreach($result as $e) {
							
								$type = datalist_Table('tbl_users', 'type', $e['user']);
								
								if ( $type == 'student' && check_user_availability($e['user']) ) {
									
									$i++;
									
									?>
									
									<tr>
										<td><?php echo $i; ?></td>
										<td>
											<a href="<?php echo base_url('attendance/manually?user='.$e['user'].'&date='.date('Y-m-d', strtotime($e['datetime'])).'&save='); ?>">
												<?php echo date('Y-m-d', strtotime($e['datetime'])); ?>
											</a>
										</td>
										<td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
										
										<?php
										
											foreach( $this->log_attendance_model->list([
											
												'user' => $e['user'],
												'DATE(datetime)' => date('Y-m-d', strtotime($e['datetime'])),
											
											]) as $e2 ) {
												
												if ($e2['action'] == 'in') {
													
													?>
													<td class="text-success">
														<b><?php echo date('H:i', strtotime($e2['datetime'])); ?></b>
													</td>
													<?php
													
												} else {
													
													?>
													<td class="text-danger">
														<b><?php echo date('H:i', strtotime($e2['datetime'])); ?></b>
													</td>
													<?php
													
												}
												
											}
											
											$query = $this->log_attendance_model->list([
											
												'user' => $e['user'],
												'DATE(datetime)' => date('Y-m-d', strtotime($e['datetime'])),
											
											]);
											
											if(count($query) != $j) {
												
												for( $x = count($query); $x < $j; $x++ ) {
													
													echo '<td>-</td>';
													
												}
												
											}
										
										?>
										
									</tr><?php
									
								}
								
							}
							
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
        <?php $this->load->view('inc/copyright'); ?>
	</div>
</div>