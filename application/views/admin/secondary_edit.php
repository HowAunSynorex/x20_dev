<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

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
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid']; ?>, '<?php echo $result['type']; ?>', '<?php echo strtolower(datalist('secondary_type_admin')[$result['type']]['single']); ?>')">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <form method="post" onsubmit="Loading(1)" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>

                        <?php
                        switch ($thispage['type']) {
                            case 'country':
                                ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Country ID</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="country_id" value="<?php echo $result['country_id']; ?>" required>
                                    </div>
                                </div>
								
								<div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Phone Code</label>
                                    <div class="col-md-9">
                                        <input type="number" class="form-control" name="phone_code" value="<?php echo $result['phone_code']; ?>" required>
                                    </div>
                                </div>
								<?php
                                break;

                            case 'currency':
                                ?>
                                <div class="form-group row">
                                    <label class="col-form-label col-md-3 text-danger">Currency ID</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="currency_id" value="<?php echo $result['currency_id']; ?>" required>
                                    </div>
                                </div>
								<?php
                                break;

                            case 'plan':
                                ?>
								<div class="form-group row">
                                    <label class="col-form-label col-md-3">Fee</label>
                                    <div class="col-md-9">
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">$</div>
											</div>
											<input type="number" step="0.01" class="form-control" name="fee" value="<?php echo $result['fee']; ?>">
										</div>
                                    </div>
                                </div><?php
                                break;

							case 'payment_method':
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">Method ID</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="method_id" value="<?php echo $result['method_id']; ?>" required="">
									</div>
								</div>
								<?php 
								break;
								
							case 'receipt': 
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3">Image</label>
									<div class="col-md-9">
										<img src="<?php echo pointoapi_UploadSource($result['image']); ?>" class="border rounded d-block mb-2" style="height: 100px">
										<input type="file" class="form-control" name="image">
									</div>
								</div>
								<?php 
								break;
							
                        }
                        ?>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($this->group. '/secondary_list/') . $result['type']; ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
			
			<?php if( $thispage['type'] == 'plan' ) { ?>
			
			<ul class="nav nav-tabs mt-3">
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Modules</a>
				</li>
			</ul>
			
			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					
					
					<?php
					
					$arr = [
								
						[
							'label' => 'Home',
							'permission' => [
								'Read' => false,
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
								'Modules' => [],
							],
						],
					
						[
							'label' => 'Calendar',
							'permission' => [
								'Read' => true,
								'Modules' => [],
							],
						],
					
						[
							'label' => 'Attendance',
							'permission' => [
								'Read' => true,
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
								'Modules' => [],
							],
						],
					
						[
							'label' => 'Classes',
							'permission' => [
								'Read' => true,
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
								'Modules' => [
									'Timetable' => true,
								],
							],
						],
					
						[
							'label' => 'Reports',
							'permission' => [
								'Read' => true,
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
									'Sales/Sales by Reference Code' => true,
									'Sales/Advanced Payment' => true,
									'Sales/Teacher Commission' => true,
									'Sales/Received' => true,
									
									'Users/Birthday' => true,
									'Users/Birthday (Parent)' => true,
									'Users/Birthday (Teacher)' => true,
									'Users/WhatsApp Marketing' => true,
									'Users/WhatsApp Marketing (Student)' => true,
									
									'Attendance/Student Attendance' => true,
									'Attendance/Student Attendance (Class)' => true,
									'Attendance/Teacher Attendance' => true,
									'Attendance/Daily Attendance' => true,
									'Attendance/Daily Attendance (Teacher)' => true,
									'Attendance/Monthly Attendance' => true,
									'Attendance/Monthly Attendance (Teacher)' => true,
									'Attendance/Class Deductions' => true,
									'Attendance/Student Enroll' => true,
									'Attendance/Absence Rate' => true,
									
									'Inventory/Stock Movement' => true,
									
									'Data Check/Students' => true,
									'Data Check/Parents' => true,
									'Data Check/Teachers' => true,
									'Data Check/Form Teachers' => true,
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
								'Modules' => [
									'T/P' => true,
									'Form' => true,
									'Banks' => true,
									'Schools' => true,
									'Courses' => true,
									'Class Bundles' => true,
									'Transports' => true,
									'Item Cat.' => true,
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
					
					foreach($arr as $k => $e) {
						foreach($e['permission'] as $k2 => $e2) {
							${$k2} = true;
						}
					}
					
					foreach($arr as $k => $e) {
						foreach($e['permission'] as $k2 => $e2) {
							if($k2 == 'Modules') { 
								foreach($e2 as $k3 => $e3) {
									if( !check_plan_module($e['label'].'/'.$k2.'/'.$k3, $result['pid']) ) {
										${$k2} = false;
										break;
									}
								}
							} else if($e2) { 
								if( !check_plan_module($e['label'].'/'.$k2, $result['pid']) ) {
									${$k2} = false;
									break;
								}
							}
						}
					}
					
					?>
				
					<form method="post">
						
						<table class="table table-bordered table-hover">
							<thead>
								<th>Module</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Read" onclick="select_all(this)" <?php if($Read) echo 'checked'; ?>>
										<label class="custom-control-label" for="Read">Read</label>
									</div>
								</th>
								<th>
									<div class="custom-control custom-checkbox d-flex align-items-center">
										<input type="checkbox" class="custom-control-input" id="Modules" onclick="select_all(this)" <?php if($Modules) echo 'checked'; ?>>
										<label class="custom-control-label" for="Modules">Modules</label>
									</div>
								</th>
							</thead>
							<tbody>
								<?php
								
								foreach($arr as $k => $e) {
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
														<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>-<?php echo str_replace(' ', '', $k3); ?>" value="1" name="modules[<?php echo post_array_replace($e['label'].'/'.$k2.'/'.$k3); ?>]" <?php if( check_plan_module($e['label'].'/'.$k2.'/'.$k3, $result['pid']) ) echo 'checked'; ?>>
														<label class="custom-control-label" for="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>-<?php echo str_replace(' ', '', $k3); ?>"><?php echo $k3; ?></label>
													</div>
													<?php 
												}
											} elseif($e2) { 
												?>
												<div class="custom-control custom-checkbox">
													<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $e['label']; ?>-<?php echo $k2; ?>" value="1" name="modules[<?php echo post_array_replace($e['label'].'/'.$k2); ?>]" <?php if( check_plan_module( $e['label'].'/'.$k2, $result['pid'])) echo 'checked'; ?>>
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
							<button type="submit" class="btn btn-primary" name="save_modules">Save</button>
						</div>
						
					</form>
					
				</div>
				
			</div>
			
			<?php } ?>

        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>