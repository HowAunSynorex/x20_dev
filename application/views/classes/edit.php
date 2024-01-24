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
				<div class="col-6 my-auto text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" href="javascript:;" onclick="clone_ask(<?php echo $result['pid']; ?>)">Clone</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <form method="post">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>
                    
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Tutor</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="teacher">
                                    <option value="">-</option>';
									
                                    <?php foreach ($teacher as $e) { ?>
                                        <option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['teacher'] ) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
                                    <?php } ?>
									
                                </select>
								<a href="javascript:;" class="small" data-toggle="modal" data-target="#modal-swap">
									<i class="fa fa-fw fa-exchange-alt"></i> Swap Class
								</a>
								<?php
								$swapped_class = $this->log_join_model->list('swap_class', branch_now('pid'), [
									'type'		=> 'swap_class',
									'class'		=> $result['pid'],
								]);
								foreach($swapped_class as $e) {
									?>
									<span class="d-block small text-muted">
										<?php
										$class_arr = explode('@', $e['remark']);
										$log_class = $this->log_join_model->view($class_arr[1])[0];
										$time_range = explode('-', $log_class['time_range']);
										$start_time = date('h:i A', strtotime($time_range[0]));
										$end_time = date('h:i A', strtotime($time_range[1]));
										$time_range = $start_time . ' - ' . $end_time;
										echo $class_arr[0] . ' ' . $time_range . ' ' . $log_class['title'] . ' ('.datalist_Table('tbl_users', 'fullname_en', $e['user']).')'; 
										?>
										<a href="javascript:;" onclick="edit_swap(<?php echo $e['id']; ?>)" class="text-warning ml-2">Edit</a>
										<a href="javascript:;" onclick="del_ask_swap(<?php echo $e['id']; ?>)" class="text-danger ml-2">Remove</a>
									</span>
									<?php
								}
								?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Course</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="course">
                                    <option value="">-</option>';

                                    <?php foreach ($course as $e) { ?>
                                        <option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['course'] ) echo 'selected'; ?>><?php echo $e['title']; ?></option>
                                    <?php } ?>
									
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Fee</label>
                            <div class="col-md-9">
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text">$</div>
									</div>
									<input type="number" step="0.01" class="form-control" name="fee" value="<?php echo $result['fee']; ?>">
								</div>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Type</label>
                            <div class="col-md-9">
								<?php
								foreach(datalist('class_type') as $k => $v) {
									switch($k) {
										case 'check_in':
											if(branch_now('version') == 'shushi') {
												?>
												<div class="custom-control custom-radio custom-control-inline">
													<input type="radio" id="<?php echo $k; ?>" name="type" value="<?php echo $k; ?>" class="custom-control-input" <?php if($k == $result['type']) echo 'checked'; ?>>
													<label class="custom-control-label" for="<?php echo $k; ?>"><?php echo $v['label']; ?></label>
												</div>
												<?php
											}
											break;
										default:
											?>
											<div class="custom-control custom-radio custom-control-inline">
												<input type="radio" id="<?php echo $k; ?>" name="type" value="<?php echo $k; ?>" class="custom-control-input" <?php if($k == $result['type']) echo 'checked'; ?>>
												<label class="custom-control-label" for="<?php echo $k; ?>"><?php echo $v['label']; ?></label>
											</div>
											<?php
											break;
									}
								}
								?>
                            </div>
                        </div>
						
						<div class="form-group row credit-sec <?php if($result['type'] != 'check_in') echo 'd-none'; ?>">
                            <label class="col-form-label col-md-3 text-danger">Credit</label>
                            <div class="col-md-9">
								<input type="number" step="1" class="form-control" name="credit" value="<?php echo empty($result['credit']) ? 1 : $result['credit']; ?>" <?php if($result['type'] == 'check_in') echo 'required'; ?>>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>

                    </div>
					
					<div class="col-md-6">
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?> >
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_start" value="<?php echo $result['date_start']; ?>">
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_end" value="<?php echo $result['date_end']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
				
            </form>
            
			<ul class="nav nav-tabs mt-3">
				<!-- Tan Jing Suan -->
				<?php 
					$list_classes_students = [];
					if( isset($result['teacher']) ) {
						$list_classes_students = $this->log_join_model->list_classes_students( $result['pid'] );
					} 
				?>
			
				<?php
				if( check_module('Classes/Modules/Timetable') ) { 
					?>
					<li class="nav-item">
						<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" href="<?php echo base_url('classes/edit/' . $result['pid'] . '?tab=1') ?>">Timetable</a>
					</li><?php
				} 
				
				if( check_module('Classes/Modules/Students') ) { 
					?>
					<li class="nav-item">
						<!-- Tan Jing Suan -->
						<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" href="<?php echo base_url('classes/edit/' . $result['pid'] . '?tab=2') ?>">Student (<?php echo count( $list_classes_students ); ?>)</a>
					</li><?php
				}
				
				if( check_module('Classes/Modules/AddStudent') ) { 
					?>
					<li class="nav-item">
						<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" href="<?php echo base_url('classes/edit/' . $result['pid'] . '?tab=3') ?>">Add Student</a>
					</li><?php
				}
				?>
				
			</ul>
			
			<div class="tab-content py-3">
				
				<?php
				if( check_module('Classes/Modules/Timetable') ) { 
					?>
					<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>">
					
						<form method="post">
						
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-12" style="display: grid; grid-template-columns: repeat(7, 1fr);grid-column-gap: 5px;grid-row-gap: 0px;grid-auto-rows: 1fr;">

										<?php foreach(datalist('day_name') as $k => $v) { ?>
											<div id="dy_<?php echo $k; ?>" class="card card-info mb-1">
												<div class="card-header p-1 d-flex justify-content-between align-items-center">
													<h6 class="mb-0 font-weight-bold"><?php echo $v['name']; ?></h6>
													<button data-day="<?php echo $k; ?>" type="button" class="btn btn-sm btn-primary" onclick="addTime($(this))"><i class="fas fa-plus-square"></i></button>
												</div>
												<div class="list-group">
													<?php $timetable = search($timetables, 'qty', $k);
															$time_range = array_column($timetable, 'time_range');

															array_multisort($time_range, SORT_ASC, $timetable);
														foreach($timetable as $e) { ?>
														<div class="list-group-item list-group-item-action text-dark p-1 border border-dark">
															<div class="d-flex w-100 flex-column">
																<input type="hidden" class="timetable-id" value="<?php echo $e['id']; ?>" />
																<input type="hidden" class="timetable-day" value="<?php echo $k ?>" />
																<input type="hidden" class="timetable-title" value="<?php echo $e['title'] ?>" />
																<input type="hidden" class="timetable-start" value="<?php echo explode('-', $e['time_range'])[0]; ?>" />
																<input type="hidden" class="timetable-end" value="<?php echo explode('-', $e['time_range'])[1]; ?>" />
																<p class="title"><b class="title"><?php echo $result['title'].' '.$e['title']; ?></b></p>
																<p><span class="start-end-time">
																	<?php echo date('h:i A', strtotime(explode('-', $e['time_range'])[0])); ?> - <?php echo date('h:i A', strtotime(explode('-', $e['time_range'])[1])); ?>
																</span></p>
																<div class="btn-group">
																	<button type="button" class="btn btn-warning btn-xs" onclick="editTime($(this))"><i class="fa fa-pen"></i></button>
																	<button type="button" class="btn btn-danger btn-xs" onclick="delTime(<?php echo $e['id'] ?>)"><i class="fa fa-trash"></i></button>
																</div>
															</div>
														</div>
													<?php } ?>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							
							<hr>
							
							<div class="row">
								<?php if( check_module('Classes/Delete') ) { ?>
									<div class="col-12 my-auto text-right">
										<button type="button" class="btn btn-danger" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</button>
									</div>
								<?php } ?>
							</div>
							
						</form>
						
					</div><?php
				} 
				
				if( check_module('Classes/Modules/Students') ) { 
					?>
					<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>">
					
						<table class="DTable2 table">
							<thead>
								<th style="width:7%">No</th>
								<th>Name</th>
								<th>Name (CN)</th>
								<th>Code</th>
								<th>Phone</th>
								<th>Parent</th>
								<th><?php echo ($result['type'] == 'check_in') ? 'Credit Balance' : 'Join Date'; ?></th>
								<th></th>
							</thead>
							<tbody>
								<!-- Tan Jing Suan -->
								<?php $i=0; foreach( $list_classes_students as $e) { $i++; 
									
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><a href="<?php echo base_url('students/edit/'.$e['user']); ?>"><?php echo $e['student']['fullname_en']; ?></a></td>
										<td><?php echo empty($e['student']['fullname_cn']) ? '-' : $e['student']['fullname_cn']; ?></a></td>
										<td><?php echo ucfirst( $e['student']['code'] ); ?></td>
										<td><?php echo empty( $e['student']['phone'] ) ? '-' : $e['student']['phone'] ; ?></td>
										<td><?php echo empty( $e['student']['parent'] ) ? '-' : datalist_Table('tbl_users', 'fullname_cn', $e['student']['parent']).' '.datalist_Table('tbl_users', 'fullname_en', $e['student']['parent']) ; ?></td>
										<td>
											<?php
											//echo ($result['type'] == 'check_in') ? class_credit_balance($result['pid'], $e['user'])['balance'] . '/' . class_credit_balance($result['pid'], $e['user'])['total'] : $e['date'];
											echo ($result['type'] == 'check_in') ? class_credit_balance($result['pid'], $e['user'])['balance'] : $e['date'];
											?>
										</td>
										<td>
											<a href="javascript:;" onclick="disable(<?php echo $e['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
										</td>
									</tr>
									<?php
								}?>
								
							</tbody>
						</table>
						
					</div><?php
				}
				
				if( check_module('Classes/Modules/AddStudent') ) { 
					?>
					<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>">
					
						<table class="DTable2 table">
							<thead>
								<th style="width:7%">No</th>
								<th style="width:20%">Name</th>
								<th>Gender</th>
								<th>Phone</th>
								<th>Parent</th>
								<th>Joined</th>
								<th>Time Slot</th>
								<th><?php echo ($result['type'] == 'check_in') ? 'Credit Balance' : 'Join Date'; ?></th>
							</thead>
							<tbody>

								<?php $i=0; foreach($students as $e) { $i++; 
									$joined = $this->log_join_model->std_class_active_check($result['pid'], $e['pid']);
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo $e['fullname_cn'].' '.$e['fullname_en']; ?></a></td>
										<td><?php echo ucfirst( $e['gender'] ); ?></td>
										<td><?php echo empty( $e['phone'] ) ? '-' : $e['phone'] ; ?></td>
										<td><?php echo empty( $e['parent'] ) ? '-' : datalist_Table('tbl_users', 'fullname_cn', $e['parent']).' '.datalist_Table('tbl_users', 'fullname_en', $e['parent']) ; ?></td>
										<td>
											<label class="switch">
												<input type="checkbox" onclick="class_joined(<?php echo $result['pid']; ?>, <?php echo $e['pid']; ?>)" <?php if( count($joined) == 1 ) echo 'checked'; ?>>
												<span class="slider round"></span>
											</label>
										</td>
										<td>
												<select class="form-control select2" <?php if( count($joined) == 0 ) echo 'disabled'; ?> id="input-timetable-<?php echo $e['pid']; ?>" onchange="change_class_timetable(<?php echo $result['pid'] ?>, <?php echo $e['pid']; ?>, this.value)">
													<?php
													$timetable = $this->log_join_model->list('class_timetable', branch_now('pid'), [
														'class' => $result['pid']
													]);
													$join_class = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $result['pid'], 'user' => $e['pid']]);
													foreach($timetable as $e2) {
														?>
														<option value="<?php echo $e2['id']; ?>" <?php if(count($join_class) > 0 && $e2['id'] == $join_class[0]['sub_class']) echo 'selected'; ?>><?php echo $e2['title'] . ' (' . datalist('day_name')[$e2['qty']]['name'] . ' ' . $e2['time_range'] . ')'; ?></option>
														<?php
													}
													if(count($timetable) == 0) {
														?>
														<option value="">-</option>
														<?php
													}
													?>
												</select>
											</td>
										<td>
											<?php
											if($result['type'] == 'check_in') {
												//echo class_credit_balance($result['pid'], $e['pid'])['balance'] . '/' . class_credit_balance($result['pid'], $e['pid'])['total'];
												echo ($result['type'] == 'check_in') ? class_credit_balance($result['pid'], $e['pid'])['balance'] : $e['date'];
											} else {
												?>
												<input type="date" class="form-control" onchange="change_class_date(<?php echo $result['pid'] ?>, <?php echo $e['pid']; ?>, this.value)" name="class_date" id="input-join_date-<?php echo $e['pid']; ?>" value="<?php if( count($joined) == 1 ) echo $joined[0]['date']; ?>" <?php if( count($joined) == 0 ) echo 'disabled'; ?>>
												<?php
											}
											?>
										</td>
									</tr>
									<?php
								}?>
								
							</tbody>
						</table>
						
					</div><?php
				}
				?>

			</div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<div id="modal-time" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="action_take">
				<input type="hidden" name="id">
				<input type="hidden" name="class_id" value="<?php echo $id; ?>">
				<input type="hidden" name="dy_id">
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Title</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="title" placeholder="title" required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-md-3">Time</label>
					<div class="col-md-9">
						<div class="input-group mb-2">
							<input type="time" class="form-control form-control-timetable" name="time_start" value="">
							<input type="time" class="form-control form-control-timetable" name="time_end" value="">
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" onclick="actionTake();" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<form method="post" id="modal-swap" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Swap Class</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Teacher</label>
					<div class="col-md-9">
						<select class="form-control select2" name="teacher" required>
							<?php
							$swap_teacher = $this->tbl_users_model->list('teacher', branch_now('pid'), ['active' => 1, 'pid !=' => $result['teacher']]);
							foreach($swap_teacher as $e) {
								?>
								<option value="<?php echo $e['pid']; ?>"><?php echo $e['fullname_en']; ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Class</label>
					<div class="col-md-9">
						<select class="form-control select2" name="class" required>
							<?php
							
							$swap_classes = [];
							
							for($i=1; $i<=14; $i++) {
								
								$date = date('Y-m-d', strtotime('+'.$i.'days', time()));
								$day = date('N', strtotime($date));
								$day_name = date('D', strtotime($date));
			
								$sql = '
								
									SELECT
										c.title as class_title,
										l.time_range as time_range,
										l.title as class_subtitle,
										l.id as class_id,
										"' . $day_name . ' ' . $date . '" as date_time
									FROM log_join l
									INNER JOIN tbl_classes c
									ON l.class = c.pid
									AND c.teacher = "' . $result['teacher'] . '"
									AND c.is_delete = 0
									AND c.active = 1
									AND l.is_delete = 0
									AND l.type = "class_timetable"
									AND l.qty = '.$day.'
								
								';
								
								foreach($this->db->query($sql)->result_array() as $e) {
									$swap_classes[] = $e;
								}
								
							}
							
							$check_class = [];
							
							foreach($swapped_class as $e) {
								$check_class[] = $e['remark'];
							}
						
							foreach($swap_classes as $e) {
								if(!in_array($e['date_time'] . '@' . $e['class_id'], $check_class)) {
									?>
									<option value="<?php echo $e['date_time'] . '@' . $e['class_id']; ?>"><?php echo $e['date_time'] . ' ' . $e['time_range'] . ' ' . $e['class_subtitle']; ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</div>
				
			</div>

			<div class="modal-footer">
				<button type="submit" name="save-swap" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>

<form method="post" id="modal-edit-swap" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Swap</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Teacher</label>
					<div class="col-md-9">
						<select class="form-control select2" name="teacher" required>
						</select>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Class</label>
					<div class="col-md-9">
						<select class="form-control select2" name="class" required>
						</select>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<input type="hidden" name="id">
				<button type="submit" name="update_swap" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>

<script>
	var access_denied = <?php echo check_module('Classes/Update') ? 0 : 1 ; ?>;
</script>