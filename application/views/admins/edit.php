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
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['id']; ?>)">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
			<?php if(defined('WHITELABEL') && branch_now('owner') != $result['admin']) { ?>
				<form method="post">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-md-3 col-form-label text-danger">Name</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="nickname" value="<?php echo $admin['nickname']; ?>" required>
								</div>
							</div>

                            <!--
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Email</label>
								<div class="col-md-9">
									<input type="email" class="form-control" name="username" onkeyup="check_username(this.value, '<?php echo $admin['pid']; ?>')" value="<?php echo $admin['username']; ?>" required>
									<small id="username-status" class="help-block text-danger d-none">Email has been taken</small>
								</div>
							</div>
							-->
							
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" name="password" placeholder="Set whitelabel login portal password">
								</div>
							</div>
						</div>
						<div class="col-md-6">
						
							<div class="form-group mb-4">
								<div class="custom-control custom-checkbox mt-1">
									<input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php if($admin['active']) echo 'checked'; ?>>
									<label class="custom-control-label" for="checkbox-active">Active</label>
								</div>
							</div>
							
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group row">
								<div class="offset-md-3 col-md-9">
									<button type="submit" class="btn btn-primary" name="save">Save</button>
									<a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				</form>
			<?php } else { ?>
				<div class="row">
					<div class="col-md-6">

						<div class="form-group row">
							<label class="col-form-label col-md-3">Name</label>
							<div class="col-md-9">
								<label class="form-control-plaintext">
									<?php 
									echo datalist_Table('tbl_admins', 'nickname', $result['admin']); 
									if( branch_now('owner') == $result['admin'] ) echo ' (Owner)';
									?>
								</label>
							</div>
						</div>

					</div>
				</div>
			<?php } ?>
			
			<ul class="nav nav-tabs mt-3">
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Permission</a>
				</li>
				<?php if(defined('WHITELABEL') && branch_now('owner') != $result['admin'] && branch_now('owner') == auth_data('pid')) { ?>
					<li class="nav-item">
						<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Branch</a>
					</li>
				<?php } ?>
			</ul>
			
			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					<form method="post">
						
						<?php if( branch_now('owner') == $result['admin'] ) { ?>
						<div class="alert alert-info">This user is the owner and cannot customize permissions</div>
						<?php } ?>
						
						<?php
						
						$permission = [
							
							[
								'label' => 'Home',
								'permission' => [
									'Read' => false,
									'Create' => false,
									'Update' => false,
									'Delete' => false,
									'Modules' => [
										'Total Students' => true,
										'Outstanding Students' => true,
										'New Students (Monthly)' => true,
										'Check In (Today)' => true,
										'Monthly Joined' => true,
										'Button Group' => true,
										'Birthday (This Month)' => true,
									],
								],
							],
						
							[
								'label' => 'Payment',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Print' => true,
										'Send' => true,
									],
								],
							],
						
							[
								'label' => 'Points',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Epoint' => true,
										'Ewallet' => true,
									],
								],
							],
						
							[
								'label' => 'Students',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Outstanding Payment' => true,
										'Actived Classes' => true,
										'Parents' => true,
										'Services' => true,
										'Unpaid Items' => true,
									],
								],
							],
						
							[
								'label' => 'Parents',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [],
								],
							],
						
							[
								'label' => 'Calendar',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [],
								],
							],
						
							[
								'label' => 'Attendance',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Daily Attendance' => true,
										'Class Attendance' => true,
										'Manually Attendance' => true,
										'New Tab Attendance' => true,
									],
								],
							],
						
							[
								'label' => 'Inventory',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Items' => true,
										'Movement' => true,
									],
								],
							],
						
							[
								'label' => 'Content',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Announcement' => true,
										'Slideshow' => true,
									],
								],
							],
						
							[
								'label' => 'Homework',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [],
								],
							],
						
							[
								'label' => 'Classes',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Timetable' => true,
										'Students' => true,
										'Add Student' => true,
									],
								],
							],
						
							[
								'label' => 'Teachers',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'Timetable' => true,
									],
								],
							],
						
							[
								'label' => 'Reports',
								'permission' => [
									'Read' => true,
									'Create' => false,
									'Update' => false,
									'Delete' => false,
									'Modules' => [
										'Sales/Unpaid Items' => true,
										'Sales/Daily Collection' => true,
										'Sales/Monthly Collection' => true,
										'Sales/Outstanding Payment' => true,
										'Sales/Outstanding Payment (Class)' => true,
										'Sales/Outstanding Payment (Parent)' => true,
										'Sales/Deleted Receipts' => true,
										'Sales/Epoint Balance' => true,
										'Sales/Ewallet Balance' => true,
										'Sales/Sales by Item' => true,
										'Sales/Sales by Item Cat.' => true,
										'Sales/Sales by School' => true,
										'Sales/Sales by Course' => true,
										'Sales/Sales by Admin' => true,
										'Sales/Advanced Payment' => true,
										'Sales/Teacher Commission' => true,
										'Sales/Received' => true,
										
										'Users/Birthday' => true,
										'Users/Birthday (Parent)' => true,
										'Users/Birthday (Teacher)' => true,
										
										'Attendance/Student Attendance' => true,
										'Attendance/Student Attendance (Class)' => true,
										'Attendance/Teacher Attendance' => true,
										'Attendance/Daily Attendance' => true,
										'Attendance/Daily Attendance (Teacher)' => true,
										'Attendance/Monthly Attendance' => true,
										'Attendance/Monthly Attendance (Teacher)' => true,
										'Attendance/Student Enroll' => true,
										'Attendance/Absence Rate' => true,
										
										'Inventory/Stock Movement' => true,
										
										'Data Check/Students' => true,
										'Data Check/Parents' => true,
										'Data Check/Teachers' => true,
										'Data Check/Items' => true,
										'Data Check/Classes' => true,
										'Data Check/Payment (Trash)' => true,
										'Data Check/Classes Number' => true,
										'Data Check/Babysitter' => true,
										
										'Summary/Annual Comparison' => true,
									],
								],
							],
						
							[
								'label' => 'Secondary',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'T/P' => true,
										'Form' => true,
										'Banks' => true,
										'Schools' => true,
										'Courses' => true,
										'Class Bundles' => true,
										'Item Cat.' => true,
										'Transports' => true,
										'Childcare' => true,
										'Payment Methods' => true,
										'Exams' => true,
									],
								],
							],
						
							[
								'label' => 'Settings',
								'permission' => [
									'Read' => true,
									'Create' => true,
									'Update' => true,
									'Delete' => true,
									'Modules' => [
										'General' => true,
										'Admins' => true,
										'Branches' => true,
										'Devices' => true,
										'Migrate' => true,
										'Receipt' => true,
										'PointoAPI' => true,
										'Reset Student' => true,
										'Reset Parent' => true,
									],
								],
							],
						
						];
						
						foreach($permission as $k => $e) {
							foreach($e['permission'] as $k2 => $e2) {
								${$k2} = true;
							}
						}
						
						foreach($permission as $k => $e) {
							foreach($e['permission'] as $k2 => $e2) {
								if($k2 == 'Modules') { 
									foreach($e2 as $k3 => $e3) {
										if( !check_permission($e['label'].'/'.$k2.'/'.$k3, $result['admin']) && branch_now('owner') != $result['admin']) {
											${$k2} = false;
											break;
										}
									}
								} else if($e2) { 
									if( !check_permission($e['label'].'/'.$k2, $result['admin']) && branch_now('owner') != $result['admin'] ) {
										${$k2} = false;
										break;
									}
								}
							}
						}
						
						?>
						
						<table class="table table-bordered table-hover">
							<thead>
								<th>Permission</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Read" onclick="select_all(this)" <?php if($Read) echo 'checked'; ?> <?php if( branch_now('owner') == $result['admin'] ) echo 'disabled'; ?>>
										<label class="custom-control-label" for="Read">Read</label>
									</div>
								</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Create" onclick="select_all(this)" <?php if($Create) echo 'checked'; ?> <?php if( branch_now('owner') == $result['admin'] ) echo 'disabled'; ?>>
										<label class="custom-control-label" for="Create">Create</label>
									</div>
								</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Update" onclick="select_all(this)" <?php if($Update) echo 'checked'; ?> <?php if( branch_now('owner') == $result['admin'] ) echo 'disabled'; ?>>
										<label class="custom-control-label" for="Update">Update</label>
									</div>
								</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Delete" onclick="select_all(this)" <?php if($Delete) echo 'checked'; ?> <?php if( branch_now('owner') == $result['admin'] ) echo 'disabled'; ?>>
										<label class="custom-control-label" for="Delete">Delete</label>
									</div>
								</th><th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Modules" onclick="select_all(this)" <?php if($Modules) echo 'checked'; ?> <?php if( branch_now('owner') == $result['admin'] ) echo 'disabled'; ?>>
										<label class="custom-control-label" for="Modules">Modules</label>
									</div>
								</th>
							</thead>
							<tbody>
								<?php
								
								foreach($permission as $k => $e) {
									?>
									<tr>
										<td><?php echo $e['label']; ?></td>
										<?php foreach($e['permission'] as $k2 => $e2) { ?>
										<td>
											<?php 
											
											if($k2 == 'Modules') { 
												foreach($e2 as $k3 => $e3) {
													?>
													<div class="custom-control custom-checkbox mb-1">
														<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>-<?php echo post_array_replace($k3); ?>" value="1" name="permission[<?php echo post_array_replace($e['label'].'/'.$k2.'/'.$k3); ?>]" <?php if( check_permission($e['label'].'/'.$k2.'/'.$k3, $result['admin']) ) echo 'checked'; if( branch_now('owner') == $result['admin'] ) echo ' disabled'; ?>>
														<label class="custom-control-label" for="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>-<?php echo post_array_replace($k3); ?>"><?php echo $k3; ?></label>
													</div>
													<?php 
												}
											} else if($e2) { 
												?>
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>" value="1" name="permission[<?php echo post_array_replace($e['label'].'/'.$k2); ?>]" <?php if( check_permission($e['label'].'/'.$k2, $result['admin']) ) echo 'checked'; if( branch_now('owner') == $result['admin'] ) echo ' disabled'; ?>>
													<label class="custom-control-label" for="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>"><?php echo $k2; ?></label>
												</div>
												<?php 
											} 
											?>
										</td>
										<?php } ?>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					
						<div class="text-right">
							<button type="submit" class="btn btn-primary" name="save_permission">Save</button>
						</div>
						
					</form>
					
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
					<form method="post">
						<div class="row">
							<div class="col-md-6">
								<ul class="list-group">
									<?php
									
									$query = $this->log_join_model->list_admin([ 'admin' => $result['admin'], 'type' => 'join_branch' ]);
									$branch_id = [];
									
									foreach($query as $e) {
										$branch_id[] = $e['branch'];
									}

									foreach(my_branches() as $e) {

										if( datalist_Table('tbl_branches', 'active', $e['branch']) == 1 && datalist_Table('tbl_branches', 'is_delete', $e['branch']) == 0 ) {
											
											?>
											<li class="list-group-item d-flex justify-content-between align-items-center">
												<?php echo datalist_Table('tbl_branches', 'title', $e['branch']); ?>
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input" name="branch[]" value="<?php echo $e['branch']; ?>" id="<?php echo $e['branch']; ?>" <?php if(in_array($e['branch'], $branch_id)) echo 'checked'; ?>>
													<label class="custom-control-label" for="<?php echo $e['branch']; ?>"></label>
												</div>
											</li>
											<?php 
									
										}

									} 
									?>
								</ul>
								<div class="text-right mt-3">
									<button type="submit" class="btn btn-primary" name="save_branch">Save</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>