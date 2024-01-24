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
						<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" href="<?php echo base_url('classes/edit/' . $result['pid'] . '?tab=2') ?>">Student (<?php echo count( $this->log_join_model->list_classes_students( $result['pid'] ) ); ?>)</a>
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
								<div class="col-md-6">
							   
									<?php
									
									foreach(datalist('day_name') as $k => $v) { 
										?>
										<div class="form-group row" id="group-timetable-<?php echo $k; ?>">
											<label class="col-md-3 col-form-label"><?php echo $v['name']; ?></label>
											<div class="col-md-9">
												<div class="input-group mb-2">
													<input type="time" class="form-control form-control-timetable" name="dy_<?php echo $k; ?>[1]" value="<?php echo (!empty($result['dy_' . $k])) ? explode('-', $result['dy_' . $k])[0] : ''; ?>" <?php if (empty($result['dy_' . $k]) ) echo 'readonly'; ?>>
													<input type="time" class="form-control form-control-timetable" name="dy_<?php echo $k; ?>[2]" value="<?php echo (!empty($result['dy_' . $k])) ? explode('-', $result['dy_' . $k])[1] : ''; ?>" <?php if (empty($result['dy_' . $k]) ) echo 'readonly'; ?>>
												</div>
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input checkbox-rest" id="checkbox-timetable-<?php echo $k; ?>" onclick="timetable_rest(<?php echo $k; ?>)" <?php if (empty($result['dy_' . $k])) echo 'checked'; ?>>
													<label class="custom-control-label" for="checkbox-timetable-<?php echo $k; ?>">Rest</label>
												</div>
											</div>
										</div>
										<?php
									}
									?>

								</div>
								<div class="col-md-6">
									
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="offset-md-3 col-md-9">
											<button type="submit" class="btn btn-primary" name="save_working_hrs">Save</button>
										</div>
									</div>
								</div>
								<?php if( check_module('Classes/Delete') ) { ?>
									<div class="col-6 my-auto text-right">
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
								<th>Gender</th>
								<th>Phone</th>
								<th>Parent</th>
								<th><?php echo ($result['type'] == 'check_in') ? 'Credit Balance' : 'Join Date'; ?></th>
								<th></th>
							</thead>
							<tbody>
								
								<?php $i=0; foreach( $this->log_join_model->list_classes_students( $result['pid'] ) as $e) { $i++; 
									
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><a href="<?php echo base_url('students/edit/'.$e['user']); ?>"><?php echo $e['student']['fullname_en']; ?></a></td>
										<td><?php echo empty($e['student']['fullname_cn']) ? '-' : $e['student']['fullname_cn']; ?></a></td>
										<td><?php echo ucfirst( $e['student']['gender'] ); ?></td>
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

<script>
	var access_denied = <?php echo check_module('Classes/Update') ? 0 : 1 ; ?>;
</script>