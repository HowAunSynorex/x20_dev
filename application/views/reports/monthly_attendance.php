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
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</div>
						
					</div>
				</div>
			</form>
            
			<div class="table-responsive">
				<table class="DTable2 table table-bordered">
					<thead>
						<th>No</th>
						<th>Student</th>
						
						<?php
													
							if(isset($_GET['month'])) {
								
								$month = date('m', strtotime($_GET['month']));
								$year = date('Y', strtotime($_GET['month']));
								
							} else {
								
								$month = date('m');
								$year = date('Y');
								
							}
							
							$days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);

							for($i=1; $i<=$days_in_month; $i++) {
								
								echo '<th>'.$i.'</th>';
								
							}

						?>
						
					</thead>
					<tbody> 
						<?php
						
						$i = 0;
						
						foreach($student as $s) {
							
							$i++;
							
							?>
							
							<tr>
								<td><?php echo $i; ?></td>
								<td>
									<a href="<?php echo base_url('students/edit/'.$s['pid']); ?>">
										<?php echo ($s['fullname_cn'] !="")?$s['fullname_cn']:$s['fullname_en']; ?>
									</a>
									<?php 
									
									$join_date = datalist_Table('tbl_users', 'date_join', $s['pid']);
																											
									if( !empty($join_date) ) {
										
										if( date('Y', strtotime($join_date)) >= $year ) {
																				
											if( floatval(date('m', strtotime($join_date))) > floatval($month) ) {
												
												?>
												<span class="text-muted small d-block">
													Join on <?php echo $join_date; ?>
												</span>
												<?php
												
											}
											
										} else {
											
											?>
											<span class="text-muted small d-block">
												Join on <?php echo $join_date; ?>
											</span>
											<?php
											
										}
										
									}

									?>
								</td>
								
								<?php 
										
									for($j=1; $j<=$days_in_month; $j++) {
										
										echo '<td class="font-weight-bold">';
										
										foreach($result as $e) {
											
											if($e['user'] == $s['pid']){
												
												// check whether student check in at that day
												if(date('d', strtotime($e['datetime'])) == $j) {
													
													if($e['action'] == 'in') {
														
														?>
														<span class="text-success">
															<?php echo date('H:i', strtotime($e['datetime'])); ?>
														</span>
														
														<br>
														<?php
																												
													} else {
														
														?>
														<span class="text-danger">
															<?php echo date('H:i', strtotime($e['datetime']));?>
														</span>
														
														<br>
														<?php
														
													}
													
												} else {
													
													echo '';
													
												}
												
											}
											
										}
										
										echo '</td>';
										
									}	
									
								?>
								
							</tr>
							
							<?php
							
						}
						
						/*$i=1;
						
						if(isset($result)) {

							foreach($result as $e) {

								$type = datalist_Table('tbl_users', 'type', $e['user']);
								
								if ($type == 'student' && check_user_availability($e['user']) ) { $i++;
								
									?>

									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
										
										<?php 
										
											for($j=1; $j<=$days_in_month; $j++) {
												
												echo '<td class="font-weight-bold">';
												
												foreach($result2 as $e2) {
													
													if($e2['user'] == $e['user']){
														
														if(date('d', strtotime($e2['datetime'])) == $j) {
															
															if($e2['action'] == 'in') {
																
																echo '<span class="text-success">'.date('H:i', strtotime($e2['datetime'])).'</span><br>';
																
															} else {
																
																echo '<span class="text-danger">'.date('H:i', strtotime($e2['datetime'])).'</span><br>';
																
															}
															
														} else {
															
															echo '';
															
														}
														
													}
													
												}
												
												echo '</td>';
												
											}	
											
										?>
										
									</tr><?php

								} else {}
							}

						}*/

						?>
					</tbody>
				</table>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>