<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <?php echo alert_get(); ?>

			<?php 
			
			if( !empty(app_sys('marquee')) ) { 
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
			}
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
								<h4 class="font-weight-bold mb-1"><?php echo $outstanding_student; ?></h4>
								<span class="d-block">Outstanding Students</span>
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
                    <?php
					
					if( check_module('Home/Modules/Monthly Joined') ) {
						?>
						<div class="card mb-3">
							<div class="card-body font-weight-bold pb-0">Monthly Joined</div>
							<div class="card-body">
								<canvas id="chart-joined" height="150"></canvas>
							</div>
						</div>
						<?php
					}
					?>
					<div class="card mb-3">
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
                    </div>
					
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
															
															?><a href='students/edit/<?php echo $e['pid']; ?>'><?php echo $e['fullname_en']; ?></a><?php
															
														} else {
															
															echo $e['fullname_en'];
															
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
            </div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<div class="custom-feedback">
	<a class="custom-feedback-btn d-block text-white py-2 px-4" href="javascript:;" onclick="intro()"><i class="fa fa-fw fa-question-circle mr-1"></i> Guide</a>
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
</script>