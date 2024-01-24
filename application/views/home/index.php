<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <?php echo alert_get(); ?>

			<?php 
			
			/* if( !empty(app_sys('marquee')) ) { 
				?>
				<div class="card mb-3">
					<div class="card-body py-0 mt-2">
						<div class="d-flex">
							<div style="width: 5%"><i class="fa fa-fw fa-bullhorn"></i></div>
							<div style="width: 95%"><marquee class="m-0"><?php echo app_sys('marquee'); ?></marquee></div>
						</div>
					</div>
				</div>
				<?php 
			}
			
			$branch_duration = round(((strtotime(date('Y-m-d')) - strtotime(branch_now('create_on'))) / (60 * 60 * 24))) + 1;
			$admin_duration = round(((strtotime(date('Y-m-d')) - strtotime(auth_data('create_on'))) / (60 * 60 * 24))) + 1;
			
			if( $branch_duration < 30 && $admin_duration < 30 ) {
				?>
				<div class="alert alert-info">
					<h5 class="font-weight-bold mb-2 mt-1">Booking a FREE remotely training</h5>
					<p class="mb-1"><a target="_blank" href="https://help.synorexcloud.com/book-an-appointment/">Book an Appointment <i class="fa fa-fw fa-external-link-square-alt"></i></a></p>
				</div>
				<?php
			}
			
			if( branch_now('plan') == '162593333372' ) {
				?>
				<div class="alert alert-warning">
					<h5 class="font-weight-bold mb-2 mt-1">FREE license actived!</h5>
					<p class="mb-1">The FREE version license is currently activated. Before you start, please subscribe to one of the <a href="https://synorexcloud.com/robocube-tuition/" target="_blank">plans <i class="fa fa-fw fa-external-link-square-alt"></i></a></p>
				</div>
				<?php
			}
			
			$check_student; $check_class; $check_join_class; $check_payment;
			
			$check_all_complete = 0;
			
			if(count($this->tbl_users_model->setup_list(branch_now('pid'), ['type' => 'student'])) > 0) {
				$check_student = 'check-circle text-success';
			} else {
				$check_student = 'times-circle text-danger';
			}
			
			if(count($this->tbl_classes_model->setup_list(branch_now('pid'))) > 0) {
				$check_class = 'check-circle text-success';
			} else {
				$check_class = 'times-circle text-danger';
			}
			
			if(count($this->log_join_model->setup_list(branch_now('pid'), ['type' => 'join_class'])) > 0) {
				$check_join_class = 'check-circle text-success';
			} else {
				$check_join_class = 'times-circle text-danger';
			}
			
			if(count($this->tbl_payment_model->setup_list(branch_now('pid'))) > 0) {
				$check_payment = 'check-circle text-success';
			} else {
				$check_payment = 'times-circle text-danger';
			}
			
			if(	$check_student == 'check-circle text-success' &&
				$check_class == 'check-circle text-success' &&
				$check_join_class == 'check-circle text-success' &&
				$check_payment == 'check-circle text-success' ) {
				
				$check_all_complete = 1;
				
			}
			
			if($check_all_complete == 0) {
				
				?>
				<div class="alert alert-warning">
					<h5 class="font-weight-bold mb-2 mt-1">Please complete your setup</h5>
					<p class="mb-2">These easy steps will help you take full advantage of your dashboard, turning all your paperwork with Robocube Tuition.</p>
					<?php
					
					foreach([
						[
							'link' => base_url('students/add'),
							'icon' => $check_student,
							'title' => 'Create or import your students data',
						],
						[
							'link' => base_url('classes/add'),
							'icon' => $check_class,
							'title' => 'Create your first class',
						],
						[
							'link' => base_url('students/list'),
							'icon' => $check_join_class,
							'title' => 'Setup student subscription tuition class',
						],
						[
							'link' => base_url('payment/add'),
							'icon' => $check_payment,
							'title' => 'One click to generated receipt',
						],
					] as $e) {
						echo '<a href="'.$e['link'].'?intro" style="color: inherit !important"><p class="mb-1"><i class="fa mr-2 fa-fw fa-'.$e['icon'].'"></i> '.$e['title'].' <i class="fa fa-fw fa-caret-square-right"></i></p></a>';
					}
					
					?>
					
				</div>
				<?php
			} */
			?>
			
            <div class="row">
				<?php
				
				if( check_module('Home/Modules/Total Students') ) {
					?>
					<div class="col-md-3 mb-3">
						<div class="card">
							<div class="card-body">
								<h4 class="font-weight-bold mb-1"><?php echo $total_student; ?></h4>
								<span class="d-block">Total Students (Active)</span>
							</div>
						</div>
					</div>
					<?php
				}
				
				if( check_module('Home/Modules/Outstanding Students') ) {
					?>
					<div class="col-md-3 mb-3">
						<div class="card">
							<div class="card-body">
								<h4 class="font-weight-bold mb-1" id="outstanding_count"><i class="fa fa-fw fa-spinner fa-spin"></i></h4>
								<span class="d-block">Outstanding Students<a href="<?php echo base_url('reports/outstanding_payment'); ?>" target="_blank" class="ml-2"><i class="fa fa-fw fa-sync" data-toggle="tooltip" data-html="true" title="Last update on: <br><?php echo time_elapsed_string(branch_now('outstanding_update')); ?>"></i></a></span>
							</div>
						</div>
					</div>
					<?php
				}
				
				if( check_module('Home/Modules/New Students (Monthly)') ) {
					?>
					<div class="col-md-3 mb-3">
						<div class="card">
							<div class="card-body">
								<h4 class="font-weight-bold mb-1"><?php echo $monthly_student; ?></h4>
								<span class="d-block">New Students (This month)</span>
							</div>
						</div>
					</div>
					<?php
				}
				
				if( check_module('Home/Modules/Check In (Today)') ) {
					?>
					<div class="col-md-3 mb-3">
						<div class="card">
							<div class="card-body">
								<h4 class="font-weight-bold mb-1"><?php echo $check_in; ?></h4>
								<span class="d-block">Check In (Today)</span>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
                
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php if( check_module('Home/Modules/Monthly Joined') ) { ?>
						<div class="card">
							<div class="card-body font-weight-bold pb-0">Monthly Joined 
							<select onchange="window.location.href='?month='+this.value">
							<?php for($x=1; $x <= 12; $x++) { ?>
								<option value="<?php echo $x ?>" <?php echo($month == $x)?'selected':'' ?>> <?php echo date_format(date_create(date('Y').'-'.sprintf("%02d", $x)),"M") ?></option>
							<?php } ?>
							</select></div>
							<div class="card-body px-0">
								<div class="row">
									<!-- Tan Jing Suan -->
									<div class="col-md-12">
										<table class="table table-sm table-sm2 mb-0">
											<thead>
												<th>幼儿</th>
												<th>新生</th>
												<th>加科</th>
												<th>停補</th>
												<th>总</th>
											</thead>
											<tbody>
												<?php 
												$kindergarden_new_student = 0;
												$kindergarden_new_join_student = 0;
												$kindergarden_new_unjoin_student = 0;
												$kindergarden_total_student = 0; 
												if(count($kindergarden_student_joined) > 0) {
												?>
												<?php foreach($kindergarden_student_joined AS $kindergarden_student) { ?>
													<tr>
														<td><?php echo $kindergarden_student['title']; ?></td>
														<td data-new-en="<?php echo $kindergarden_student['new_student_fullname_en']; ?>" data-new-cn="<?php echo $kindergarden_student['new_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="new">
																<?php echo $kindergarden_student['new_student']; ?>
															</a>
														</td>
														<td data-en-join="<?php echo $kindergarden_student['new_join_student_fullname_en']; ?>" data-cn-join="<?php echo $kindergarden_student['new_join_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="join">
																<?php echo $kindergarden_student['new_join_student']; ?>
															</a>
														</td>
														<td data-en-unjoin="<?php echo $kindergarden_student['new_unjoin_student_fullname_en']; ?>" data-cn-unjoin="<?php echo $kindergarden_student['new_unjoin_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="unjoin">
																<?php echo $kindergarden_student['new_unjoin_student']; ?>
															</a>
														</td>
														<td><?php echo $kindergarden_student['total_student']; ?></td>
													</tr>
													<?php
														$kindergarden_new_student = $kindergarden_new_student + $kindergarden_student['new_student'];
														$kindergarden_new_join_student = $kindergarden_new_join_student + $kindergarden_student['new_join_student'];
														$kindergarden_new_unjoin_student = $kindergarden_new_unjoin_student + $kindergarden_student['new_unjoin_student'];
														$kindergarden_total_student = $kindergarden_total_student + $kindergarden_student['total_student'];
													?>
													<?php } ?>
												<?php 
												} else {
													?><tr><td class="text-center" colspan="4">No result found</td></tr><?php
												}
												
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total</th>
													<th><?php echo $kindergarden_new_student ?></th>
													<th><?php echo $kindergarden_new_join_student ?></th>
													<th><?php echo $kindergarden_new_unjoin_student ?></th>
													<th><?php echo $kindergarden_total_student ?></th>
												</tr>
											</tfoot>
										</table>
									</div>
									<div class="col-md-12">
										<table class="table table-sm table-sm2 mb-0">
											<thead>
												<th>小学</th>
												<th>新生</th>
												<th>加科</th>
												<th>停補</th>
												<th>总</th>
											</thead>
											<tbody>
												<?php 
												$pri_new_student = 0;
												$pri_new_join_student = 0;
												$pri_new_unjoin_student = 0;
												$pri_total_student = 0; 
												if(count($primary_student_joined) > 0) {
												?>
												<?php foreach($primary_student_joined AS $primary_student) { ?>
													<tr>
														<td><?php echo $primary_student['title']; ?></td>
														<td data-new-en="<?php echo $primary_student['new_student_fullname_en']; ?>" data-new-cn="<?php echo $primary_student['new_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="new">
																<?php echo $primary_student['new_student']; ?>
															</a>
														</td>
														<td data-en-join="<?php echo $primary_student['new_join_student_fullname_en']; ?>" data-cn-join="<?php echo $primary_student['new_join_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="join">
																<?php echo $primary_student['new_join_student']; ?>
															</a>
														</td>
														<td data-en-unjoin="<?php echo $primary_student['new_unjoin_student_fullname_en']; ?>" data-cn-unjoin="<?php echo $primary_student['new_unjoin_student_fullname_cn']; ?>">
															<a href="javascript:;" data-toggle="modal" data-target="#modal-student" data-type="unjoin">
																<?php echo $primary_student['new_unjoin_student']; ?>
															</a>
														</td>
														<td><?php echo $primary_student['total_student']; ?></td>
													</tr>
													<?php
														$pri_new_student = $pri_new_student + $primary_student['new_student'];
														$pri_new_join_student = $pri_new_join_student + $primary_student['new_join_student'];
														$pri_new_unjoin_student = $pri_new_unjoin_student + $primary_student['new_unjoin_student'];
														$pri_total_student = $pri_total_student + $primary_student['total_student'];
													?>
													<?php } ?>
												<?php 
												} else {
													?>
													<tr><td class="text-center" colspan="4">No result found</td></tr>
													<?php
												}
												
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total</th>
													<th><?php echo $pri_new_student ?></th>
													<th><?php echo $pri_new_join_student ?></th>
													<th><?php echo $pri_new_unjoin_student ?></th>
													<th><?php echo $pri_total_student ?></th>
												</tr>
											</tfoot>
										</table>
									</div>
									<div class="col-md-12">
										<table class="table table-sm table-sm2 mb-0">
											<thead>
												<th>中学</th>
												<th>新生</th>
												<th>加科</th>
												<th>停補</th>
												<th>总</th>
											</thead>
											<tbody>
												<?php 
												$se_new_student = 0;
												$se_new_join_student = 0;
												$se_new_unjoin_student = 0;
												$se_total_student = 0;
												if(count($secondary_student_joined) > 0) {
												?>
												<?php foreach($secondary_student_joined AS $secondary_student) { ?>
													<tr>
														<td><?php echo $secondary_student['title']; ?></td>
														<td data-student-en="<?php echo $secondary_student['new_student_fullname_en']; ?>" data-student-cn="<?php echo $secondary_student['new_student_fullname_cn']; ?>">
															<a href="#" onclick="previewStudent($(this))">
																<?php echo $secondary_student['new_student']; ?>
															</a>
														</td>
														<td data-student-en="<?php echo $secondary_student['new_join_student_fullname_en']; ?>" data-student-cn="<?php echo $secondary_student['new_join_student_fullname_cn']; ?>">
															<a href="#" onclick="previewStudent($(this))">
																<?php echo $secondary_student['new_join_student']; ?>
															</a>
														</td>
														<td data-student-en="<?php echo $secondary_student['new_unjoin_student_fullname_en']; ?>" data-student-cn="<?php echo $secondary_student['new_unjoin_student_fullname_cn']; ?>">
															<a href="#" onclick="previewStudent($(this))">
																<?php echo $secondary_student['new_unjoin_student']; ?>
															</a>
														</td>
														<td><?php echo $secondary_student['total_student']; ?></td>
													</tr>
													<? 
														$se_new_student = $se_new_student + $secondary_student['new_student'];
														$se_new_join_student = $se_new_join_student + $secondary_student['new_join_student'];
														$se_new_unjoin_student = $se_new_unjoin_student + $secondary_student['new_unjoin_student'];
														$se_total_student = $se_total_student + $secondary_student['total_student'];
													?>
													<?php } ?>
												<?php 
												} else {
													?>
													<tr><td class="text-center" colspan="4">No result found</td></tr>
													<?php
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th>Total</th>
													<th><?php echo $se_new_student ?></th>
													<th><?php echo $se_new_join_student ?></th>
													<th><?php echo $se_new_unjoin_student ?></th>
													<th><?php echo $se_total_student ?></th>
												</tr>
											</tfoot>
										</table>
									</div>
									<div class="col-md-6 mt-3">
										<table class="table table-sm table-sm2 mb-0">
											<thead>
												<tr>
													<th>总加科</th>
													<!-- Tan Jing Suan -->
													<!-- <th><?php echo $pri_new_join_student + $se_new_join_student ?></th> -->
													<th><?php echo $kindergarden_new_join_student + $pri_new_join_student + $se_new_join_student ?></th>
												</tr>
												<tr>
													<th>总停科</th>
													<!-- Tan Jing Suan -->
													<!-- <th><?php echo $pri_new_unjoin_student + $se_new_unjoin_student ?></th> -->
													<th><?php echo $kindergarden_new_unjoin_student + $pri_new_unjoin_student + $se_new_unjoin_student ?></th>
												</tr>
												<tr>
													<th>Total</th>
													<!-- Tan Jing Suan -->
													<!-- <th><?php echo ($pri_new_join_student + $se_new_join_student) - ($pri_new_unjoin_student + $se_new_unjoin_student) ?></th> -->
													<th><?php echo ($kindergarden_new_join_student + $pri_new_join_student + $se_new_join_student) - ($kindergarden_new_unjoin_student + $pri_new_unjoin_student + $se_new_unjoin_student) ?></th>
												</tr>
											</thead>
										</table>
									</div>
								</div>
							</div>
						</div>
						<!--
						<div class="card mb-3">
							<div class="card-body font-weight-bold pb-0">Monthly Joined</div>
							<div class="card-body">
								<canvas id="chart-joined" height="150"></canvas>
							</div>
						</div>-->
					<?php } ?>
					<!--<div class="card mb-3">
                        <div class="card-body font-weight-bold pb-2">What's News</div>
                        <div class="card-body p-0">
							<ul class="list-group list-group-flush" id="append-news">
								<li class="list-group-item">Loading...</li>
								<?php
								
								/*foreach([
									
									[
										'date' => '2021-08-01',
										'title' => 'Robocube Tuition is Live',
										'description' => 'The new version of Robocube Tuition will provide a new experience.',
									],
									
								] as $k => $e) {
									?>
									<li class="list-group-item">
										<p class="mb-0 font-weight-bold"><?php echo $e['title']; ?> <em class="float-right small text-muted"><?php echo date('M j, Y', strtotime($e['date'])); ?></em></p>
										<span class="text-muted d-block text-truncate"><?php echo $e['description']; ?></span>
									</li>
									<?php
									if($k > 5) break;
								}*/
								?>
							</ul>
                        </div>
                    </div>-->
					
					<!--<div class="card mb-3">
                        <div class="card-body font-weight-bold pb-0">Classes</div>
                        <div class="card-body">
                            <canvas id="chart-classes" height="150"></canvas>
                        </div>
                    </div>-->
					
                </div>
                <div class="col-md-6 mb-3">
                    <?php
					
					if( check_module('Home/Modules/Button Group') ) {
						?>
						<div class="row">
							<?php
							
							if( check_module('Payment/Read') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('payment/list'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-money-check-alt d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">Sales Payment</span>
									</a>
								</div>
								<?php
							}
							
							if( check_module('Students/Read') && check_module('Students/Create') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('students/add'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-user-graduate d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">New Student</span>
									</a>
								</div>
								<?php
							}
							
							if( check_module('Classes/Read') && check_module('Classes/Create') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('classes/add'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-chalkboard-teacher d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">New Class</span>
									</a>
								</div>
								<?php
							}
							
							if( check_module('Calendar/Read') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('calendar'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-calendar-alt d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">Calendar</span>
									</a>
								</div>
								<?php
							}
							
							if( check_module('Content/Create') && check_module('Content/Modules/Announcement') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('content/add/announcement'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-bullhorn d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">New Ann.</span>
									</a>
								</div>
								<?php
							}

							if( check_module('Reports/Read') ) {
								?>
								<div class="col-md-4 mb-4">
									<a href="<?php echo base_url('reports'); ?>" class="btn btn-block btn-secondary py-3">
										<i class="fa fa-fw fa-chart-pie d-block mx-auto fa-3x mb-2"></i>
										<span class="d-block">Reports</span>
									</a>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					
					if( check_module('Home/Modules/Birthday (This Month)') ) {
						?>
						<div class="card">
							<div class="card-body font-weight-bold pb-0">Birthday (This Month)</div>
							<div class="card-body px-0">
								<table class="table table-sm table-sm2 mb-0">
									<thead>
										<th>Student</th>
										<th>Birthday</th>
									</thead>
									<tbody>
										<?php 
										
										if(count($birthday_student) > 0) {
											foreach($birthday_student as $e) {
												?>
												<tr>
													<td>
														<?php
														
														if( check_module('Students/Read') ) {
															
															?><a href='students/edit/<?php echo $e['pid']; ?>'><?php echo $e['fullname_cn']; ?></a><?php
															
														} else {
															
															echo $e['fullname_cn'];
															
														}
														?>
													</td>
													<td><?php echo $e['birthday']; ?></td>
												</tr>
												<?php
											}
										} else {
											?><tr><td class="text-center" colspan="2">No result found</td></tr><?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<?php
					}
					?>
					
                </div>
                <div class="col-12">
					<?php
					$atd_sql = '
						SELECT
							l.id,
							l.date,
							l2.title AS sub_class_title,
							l2.id AS sub_class_id,
							u.fullname_cn AS student,
							u.pid AS student_id,
							c.title AS subject,
							c.pid AS class_id,
							s.title AS form,
							u2.fullname_en AS teacher,
							u2.pid AS teacher_id,
							l.remark
						FROM log_join l
						INNER JOIN log_join l2
						ON l.sub_class = l2.id
						AND l.is_delete = 0
						AND l2.is_delete = 0
						AND l.branch = "'.branch_now('pid').'"
						AND l2.branch = "'.branch_now('pid').'"
						AND l.type = "class_attendance"
						AND l2.type = "class_timetable"
						AND l.date = "'.date('Y-m-d').'"
						AND l.active = 0
						INNER JOIN tbl_classes c
						ON c.pid = l2.class
						AND c.is_delete = 0
						AND c.branch = "'.branch_now('pid').'"
						INNER JOIN tbl_secondary s
						ON s.pid = c.course
						AND s.branch = "'.branch_now('pid').'"
						AND s.is_delete = 0
						AND s.type = "course"
						INNER JOIN tbl_users u
						ON u.pid = l.user
						AND u.is_delete = 0
						AND u.branch = "'.branch_now('pid').'"
						INNER JOIN tbl_users u2
						ON u2.pid = c.teacher
						AND u2.is_delete = 0
						AND u2.branch = "'.branch_now('pid').'"
					';
					?>
					<div class="card mt-3">
						<div class="card-body font-weight-bold pb-0">Daily Absent (小学)</div>
						<div class="card-body px-0">
							<table class="table table-sm table-sm2 mb-0">
								<thead>
									<th>#</th>
									<th>Date</th>
									<th>Time Range</th>
									<th>Student</th>
									<th>Subject</th>
									<th>Form</th>
									<th>Teacher</th>
									<th>Reason</th>
									<th>Parent</th>
									<th>Attendance</th>
									<th>Edit Attendance Reason</th>
								</thead>
								<tbody>
									<?php
									$sql = $atd_sql . ' AND s.title IN ("K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5")';
									$absent = $this->db->query($sql)->result_array();
									$i=0;
									foreach($absent as $e) {
										$i++;
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $e['date']; ?></td>
											<td><?php echo $e['sub_class_title']; ?></td>
											<td>
												<?php
												
												if( check_module('Students/Read') ) {
													
													?><a href='students/edit/<?php echo $e['student_id']; ?>'><?php echo $e['student']; ?></a><?php
													
												} else {
													
													echo $e['student'];
													
												}
												?>
											</td>
											<td><?php echo $e['subject']; ?></td>
											<td><?php echo $e['form']; ?></td>
											<td><?php echo $e['teacher']; ?></td>
											<td><?php echo empty($e['remark']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['remark']); ?></td>
											<td>
												<?php
												$sql = '
													SELECT * FROM log_join
													WHERE is_delete = 0
													AND type = "join_parent"
													AND user = "'.$e['student_id'].'"
												';
												$parent = $this->db->query($sql)->result_array();
												foreach($parent as $e2) {
													echo datalist_Table('tbl_users', 'fullname_en', $e2['parent']) . '<br>';
												}
												if(count($parent) == 0) echo '-';
												?>
											</td>
											<td>
												<a href="<?php echo base_url('attendance/classes?teacher='.$e['teacher_id'].'&class='.$e['class_id'].'&sub_class='.$e['sub_class_id'].'&month='.date('Y-m', strtotime($e['date']))) ?>">View</a>
											</td>
											<td>
												<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $e['id']; ?>" data-reason="<?php echo $e['remark']; ?>">Edit</button>
											</td>
										</tr>
										<?php
									}
									
									if(count($absent) == 0) {
										?><tr><td class="text-center" colspan="100%">No result found</td></tr><?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
					
					<div class="card mt-3">
						<div class="card-body font-weight-bold pb-0">Daily Absent (中学)</div>
						<div class="card-body px-0">
							<table class="table table-sm table-sm2 mb-0">
								<thead>
									<th>#</th>
									<th>Date</th>
									<th>Time Range</th>
									<th>Student</th>
									<th>Subject</th>
									<th>Form</th>
									<th>Teacher</th>
									<th>Reason</th>
									<th>Parent</th>
									<th>Attendance</th>
									<th>Edit Attendance Reason</th>
								</thead>
								<tbody>
									<?php
									// Tan Jing Suan
									$sql = $atd_sql . ' AND s.title IN ("F1", "F2", "F3", "F4", "F5")';
									$absent = $this->db->query($sql)->result_array();
									$i=0;
									foreach($absent as $e) {
										$i++;
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $e['date']; ?></td>
											<td><?php echo $e['sub_class_title']; ?></td>
											<td>
												<?php
												
												if( check_module('Students/Read') ) {
													
													?><a href='students/edit/<?php echo $e['student_id']; ?>'><?php echo $e['student']; ?></a><?php
													
												} else {
													
													echo $e['student'];
													
												}
												?>
											</td>
											<td><?php echo $e['subject']; ?></td>
											<td><?php echo $e['form']; ?></td>
											<td><?php echo $e['teacher']; ?></td>
											<td><?php echo empty($e['remark']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['remark']); ?></td>
											<td>
												<?php
												$sql = '
													SELECT * FROM log_join
													WHERE is_delete = 0
													AND type = "join_parent"
													AND user = "'.$e['student_id'].'"
												';
												$parent = $this->db->query($sql)->result_array();
												foreach($parent as $e2) {
													echo datalist_Table('tbl_users', 'fullname_en', $e2['parent']) . '<br>';
												}
												if(count($parent) == 0) echo '-';
												?>
											</td>
											<td>
												<a href="<?php echo base_url('attendance/classes?teacher='.$e['teacher_id'].'&class='.$e['class_id'].'&sub_class='.$e['sub_class_id'].'&month='.date('Y-m', strtotime($e['date']))) ?>">View</a>
											</td>
											<td>
												<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $e['id']; ?>" data-reason="<?php echo $e['remark']; ?>">Edit</button>
											</td>
										</tr>
										<?php
									}
									
									if(count($absent) == 0) {
										?><tr><td class="text-center" colspan="100%">No result found</td></tr><?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
					
					<div class="card mt-3">
						<div class="card-body font-weight-bold pb-0">Daily Absent (Childcare)</div>
						<div class="card-body px-0">
							<table class="table table-sm table-sm2 mb-0">
								<thead>
									<th>#</th>
									<th>Date</th>
									<th>Time Range</th>
									<th>Student</th>
									<th>Subject</th>
									<th>Form</th>
									<th>Teacher</th>
									<th>Reason</th>
									<th>Parent</th>
									<th>Attendance</th>
									<th>Edit Attendance Reason</th>
								</thead>
								<tbody>
									<?php
									$sql = '
										SELECT
											l.id,
											l.date,
											l.remark,
											u.fullname_cn AS student,
											u.pid AS student_id,
											u2.fullname_en AS teacher,
											u2.pid AS teacher_id,
											ch.title AS childcare,
											f.title AS form
											FROM log_join l
											INNER JOIN tbl_users u
											ON u.pid = l.user
											AND l.is_delete = 0
											AND l.branch = "'.branch_now('pid').'"
											AND l.type = "childcare_attendance"
											AND l.date = "'.date('Y-m-d').'"
											AND l.active = 0
											AND u.is_delete = 0
											AND u.branch = "'.branch_now('pid').'"
											INNER JOIN tbl_users u2
											ON u2.pid = u.childcare_teacher
											AND u2.is_delete = 0
											AND u2.branch = "'.branch_now('pid').'"
											INNER JOIN tbl_secondary ch
											ON ch.pid = u.childcare
											INNER JOIN tbl_secondary f
											ON f.pid = u.form
											AND ch.branch = "'.branch_now('pid').'"
										';
									$absent = $this->db->query($sql)->result_array();
									$i=0;
									foreach($absent as $e) {
										$i++;
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $e['date']; ?></td>
											<td>Whole Day</td>
											<td>
												<?php
												
												if( check_module('Students/Read') ) {
													
													?><a href='students/edit/<?php echo $e['student_id']; ?>'><?php echo $e['student']; ?></a><?php
													
												} else {
													
													echo $e['student'];
													
												}
												?>
											</td>
											<td><?php echo $e['childcare']; ?></td>
											<td><?php echo $e['form']; ?></td>
											<td><?php echo $e['teacher']; ?></td>
											<td><?php echo empty($e['remark']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['remark']); ?></td>
											<td>
												<?php
												$sql = '
													SELECT * FROM log_join
													WHERE is_delete = 0
													AND type = "join_parent"
													AND user = "'.$e['student_id'].'"
												';
												$parent = $this->db->query($sql)->result_array();
												foreach($parent as $e2) {
													echo datalist_Table('tbl_users', 'fullname_cn', $e2['parent']) . '<br>';
												}
												if(count($parent) == 0) echo '-';
												?>
											</td>
											<td>
												<a href="<?php echo base_url('attendance/classes?teacher='.$e['teacher_id'].'&class='.$e['class_id'].'&sub_class='.$e['sub_class_id'].'&month='.date('Y-m', strtotime($e['date']))) ?>">View</a>
											</td>
											<td>
												<button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $e['id']; ?>" data-reason="<?php echo $e['remark']; ?>">Edit</button>
											</td>
										</tr>
										<?php
									}
									
									if(count($absent) == 0) {
										?><tr><td class="text-center" colspan="100%">No result found</td></tr><?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
                </div>
            </div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<div class="custom-feedback">
	<a class="custom-feedback-btn d-block text-white py-2 px-4" href="javascript:;" onclick="intro()"><i class="fa fa-fw fa-question-circle mr-1"></i> Guide</a>
</div>

<form method="post" class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Attendance Reason</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Reason</label>
					<select class="form-control select2" name="reason">
						<option value="">-</option>
						<?php
						foreach($this->tbl_secondary_model->list('reason', branch_now('pid'), ['active' => 1]) as $e) {
							?>
							<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="id">
				<button type="submit" name="save_reason" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>


<div class="modal fade" id="modal-student" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Student Name(s)</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	function intro_index() {
		<?php 
		
		if( !isset($_COOKIE[md5('@intro')]) ) {
			echo 'intro();';
			setcookie(md5('@intro'), 'intro', time() + (86400 * 90), "/");
		} 
		?>
	}
	var Branch = "<?php echo branch_now('pid'); ?>"
</script>