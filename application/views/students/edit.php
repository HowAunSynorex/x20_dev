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
					<a class="btn btn-primary mr-2" href="<?php echo base_url('payment/add/'.$result['pid']); ?>">New Payment</a>
					<div class="dropdown d-inline-block">
						<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" href="javascript:;" onclick="print()">Print <i class="fa fa-fw fa-external-link-square-alt float-right ml-2 mt-1"></i></a>
							<a class="dropdown-item" href="<?php echo base_url('payment/list/?student='.$result['pid']); ?>" target="_blank">Payment History <i class="fa fa-fw fa-external-link-square-alt float-right ml-2 mt-1"></i></a>
							<a class="dropdown-item" href="<?php echo base_url('reports/student_attendance?start_date='.date('Y-m-d').'&end_date='.date('Y-m-d').'&student='.$result['pid']); ?>" target="_blank">Attendance <i class="fa fa-fw fa-external-link-square-alt float-right mt-1"></i></a>
							<a class="dropdown-item" href="<?php echo base_url('payment/add/'.$result['pid']); ?>">New Payment</a>
						</div>
					</div>
				</div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<?php
			
			$std_unpaid_result = std_unpaid_result2($result['pid']);
			$new_unpaid_class = [];
			$material_fee = [];
			$subsidy_fee = [];
			$with_class_bundle = [];
			$without_class_bundle = [];
			$total_material_fee = 0;
			$total_subsidy_fee = 0;
			
			if($std_unpaid_result['count'] > 0) {
				
				if(isset($std_unpaid_result['result']['class'])) {
					
					foreach($std_unpaid_result['result']['class'] as $e) {
						$check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
							'course' => datalist_Table('tbl_classes', 'course', $e['class'])
						]);
						if(count($check_class_bunlde) > 0) {
							$check_class_bunlde = $check_class_bunlde[0];
							$with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e;
						} else {
							$without_class_bundle[]['data'] = $e;
						}
					}



					foreach($with_class_bundle as $k => $v) {

                        //get actual class qty , array is combine with multi outstanding month now
                        $actual_class_lists = array();
                        foreach($v['data'] as $v_row)
                        {
                            $actual_class_lists[$v_row['id']] = $v_row;
                        }
                        $actual_class_count = count($actual_class_lists);
                        $actual_material_count = count($v['data']) / $actual_class_count;
                        $actual_subsidy_count = count($v['data']) / $actual_class_count;

						$check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
							'parent' => $k,
							'qty' => $actual_class_count
						]);
						if(count($check_bundle_price) > 0) {

							$check_bundle_price = $check_bundle_price[0];
							$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
                       
                            for($a =0 ; $a < $actual_material_count; $a++)
                            {
                                $material_fee[] = [
                                    'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                    'fee' => $check_bundle_price['material']
                                ];
                            }


							foreach($v['data'] as $k2 => $v2) {
								$class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
								$with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
								$with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
								$with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';

							}
						} else {
							$check_bundle_price = $this->db->query('
								SELECT * FROM log_join
								WHERE is_delete = 0
								AND type = "class_bundle_price"
								AND branch = "' . branch_now('pid') . '"
								AND parent = "' . $k . '"
								AND qty < '.$actual_class_count.'
								ORDER BY qty DESC
							')->result_array();
							if(count($check_bundle_price) > 0) {
								$check_bundle_price = $check_bundle_price[0];
								$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
								for($i=0; $i<floor($actual_class_count / $check_bundle_price['qty']); $i++) {
                                    for($a =0 ; $a < $actual_material_count; $a++)
                                    {
                                        $material_fee[] = [
                                            'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                            'fee' => $check_bundle_price['material']
                                        ];
                                    }
								}
								foreach($v['data'] as $k2 => $v2) {
									if($k2 >= $check_bundle_price['qty'] * floor(count($v['data']) / $check_bundle_price['qty'])) {
										$without_class_bundle[0]['data'][] = $v2;
										unset($with_class_bundle[$k]['data'][$k2]);
									} else {
										$class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
										$with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
										$with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
										$with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
									}
								}
							}
						} 
					}

					foreach($with_class_bundle as $k => $v) {

                        //get actual class qty , array is combine with multi outstanding month now
                        $actual_class_lists = array();
                        foreach($v['data'] as $v_row)
                        {
                            $actual_class_lists[$v_row['id']] = $v_row;
                        }
                        $actual_class_count = count($actual_class_lists);

						$bundle_subsidy = $this->db->query('
							SELECT * FROM log_join
							WHERE is_delete = 0
							AND type = "class_bundle_price"
							AND branch = "' . branch_now('pid') . '"
							AND parent = "' . $k . '"
							AND subsidy > 0
							AND qty <= ' . $actual_class_count . '
							ORDER BY qty DESC
						')->result_array();
						if(count($bundle_subsidy) > 0) {
							$bundle_subsidy = $bundle_subsidy[0];
							for($i=0; $i<floor($actual_class_count / $bundle_subsidy['qty']); $i++) {
                                for($a =0 ; $a < $actual_subsidy_count; $a++)
                                {
                                    $subsidy_fee[] = [
                                        'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                        'fee' => $bundle_subsidy['subsidy']
                                    ];
                                }
							}
						}
					}
					
					foreach($material_fee as $e) {
						$total_material_fee += $e['fee'];
					}
					
					foreach($subsidy_fee as $e) {
						$total_subsidy_fee += $e['fee'];
					}

				}


			}
			
			$total_payment = 0;
			$total_discount = 0;
			$total = 0;
			
			$new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
			foreach($new_unpaid_class as $e) {
				foreach($e['data'] as $e2) {
					if( isset($e2['amount']) && isset($e2['discount']) ) {
						//$total_payment += $e2['amount'] - $e2['discount'];
                        $total_payment += $e2['amount'];
                        $total_discount += $e2['discount'];
					}
				}

				if( isset($e['data']['amount']) && isset($e2['data']['discount']) ) {
					//$total_payment += $e['data']['amount'] - $e['data']['discount'];
                    $total_payment += $e['data']['amount'];
                    $total_discount += $e2['data']['discount'];
				}
			}



            $childcare_fee = 0;
            $transport_fee = 0;
            if ($result['childcare_title'] != "")
                $childcare_fee = $result['childcare_price'];

            if ($result['transport_title'] != "")
                $transport_fee = $result['transport_price'];

			$total_payment += $total_material_fee;
            $total_payment += $childcare_fee;
            $total_payment += $transport_fee;


			$total_discount += $total_subsidy_fee;
			$total = $total_payment - $total_discount;

			if($std_unpaid_result['count'] > 0) {
				?>
				<div class="alert alert-info d-flex justify-content-between align-items-center">
					<span>
                        <? echo branch_now('currency');?>
						A payment for <b><?php echo datalist_Table('tbl_secondary', 'currency_id', branch_now('currency')). number_format($total, 2, '.', ','); ?></b> is still outstanding on <b><?php echo $result['fullname_en']; ?></b>'s account.
					</span>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-payment">View Details</button>
				</div>
				<?php
			}
				
			?>
            
            <form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">
			
				<div class="section-print">
					<div class="row">
						<div class="col-md-6">

							<div class="form-group row">
								<label class="col-form-label col-md-3 text-danger">Full Name</label>
								<div class="col-md-9">
									<div class="row">
										<div class="col-md-6">
											<input type="hidden" name="student" value="<?php echo $result['pid']; ?>">
											<input type="text" class="form-control" name="fullname_en" value="<?php echo $result['fullname_en']; ?>" required>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control" name="fullname_cn" value="<?php echo $result['fullname_cn']; ?>">
										</div>
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Nickname</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="nickname" value="<?php echo $result['nickname']; ?>">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-md-3">NRIC / Birth Cert No.</label>
								<div class="col-md-9">
									<input type="text" class="form-control" id="nric" name="nric" value="<?php echo $result['nric']; ?>">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Birthday</label>
								<div class="col-md-9">
									<input type="date" class="form-control" name="birthday" value="<?php echo $result['birthday']; ?>">
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

							<div class="form-group row mb-3 pb-1">
								<label class="col-md-3 col-form-label">Gender</label>
								<div class="col-md-9 my-auto">
									
									<?php foreach(datalist('gender') as $k => $v) { ?>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio-gender-<?php echo $k; ?>" name="gender" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $result['gender'] == $k ) { echo 'checked'; }  ?>>
										<label class="custom-control-label" for="radio-gender-<?php echo $k; ?>"><?php echo $v; ?></label>
									</div>
									<?php } ?>

								</div>
							</div>

						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-form-label col-md-3">Avatar</label>
								<div class="col-md-9">
									<img src="<?php echo empty($result['image']) ? 'https://cdn.synorex.link/assets/images/blank/4x3.jpg' : datalist_Table('tbl_uploads', 'file_source', $result['image']); ?>" class="mb-3 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
									<input type="file" class="form-control" name="image">
								</div>
							</div>
						</div>
					</div>

					<hr class="mb-4">

					<div class="row">
						<div class="col-md-6">

							<div class="form-group row section-portal">
								<label class="col-form-label col-md-3 <?php if($result['username'] != '') echo 'text-danger'; ?>" id="username-text">Username</label>
								<div class="col-md-9">
									<input type="text" class="form-control" id="username" name="username" value="<?php echo ($result['username'] == '')?$result['nric']:$result['username']; ?>" >
									<span id="check_username" class="d-none form-text font-weight-bold small text-danger">Username has been taken</span>
								</div>
							</div>

							<div class="form-group row section-portal">
								<label class="col-form-label col-md-3" id="password-text">Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" id="password" name="password" value="123">
								<span id="default_password" class="d-none form-text font-weight-bold small text-muted">Default password: 123</span>
								</div>
							</div>

						</div>
						<!--<div class="col-md-6">

							<div class="form-group mb-4">
								<div class="custom-control custom-checkbox mt-1">
									<input onchange="check(this);" type="checkbox" class="custom-control-input" id="checkbox-portal" <?php if($result['username'] != '') echo 'checked'; ?>>
									<label class="custom-control-label" for="checkbox-portal">Allow app access for this student</label>
								</div>
							</div>

						</div>-->
					</div>

					<hr class="mb-4">

					<div class="row">
						<div class="col-md-6">

							<div class="form-group row">
								<label class="col-form-label col-md-3">Phone</label>
								<div class="col-md-9">
									<input type="tel" class="form-control" name="phone" placeholder="Primary" value="<?php echo $result['phone']; ?>">
									<input type="tel" class="form-control mt-2" name="phone2" value="<?php echo $result['phone2']; ?>">
									<input type="tel" class="form-control mt-2" name="phone3" value="<?php echo $result['phone3']; ?>">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Email</label>
								<div class="col-md-9">
									<input type="email" class="form-control" name="email" placeholder="Primary" value="<?php echo $result['email']; ?>">
									<input type="email" class="form-control mt-2" name="email2" value="<?php echo $result['email2']; ?>">
									<input type="email" class="form-control mt-2" name="email3" value="<?php echo $result['email3']; ?>">
								</div>
							</div>

						</div>
						<div class="col-md-6">

							<div class="form-group row">
								<label class="col-form-label col-md-3">Address</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="address" placeholder="Primary" value="<?php echo $result['address']; ?>">
									<input type="text" class="form-control mt-2" name="address2" value="<?php echo $result['address2']; ?>">
									<input type="text" class="form-control mt-2" name="address3" value="<?php echo $result['address3']; ?>">
								</div>
							</div>

						</div>
					</div>

					<hr class="mb-4">

					<div class="row">
						<div class="col-md-6">

							<div class="form-group row">
								<label class="col-form-label col-md-3">Code</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="code" value="<?php echo $result['code']; ?>">
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-form-label col-md-3">Join Date</label>
								<div class="col-md-9">
									<input type="date" class="form-control" name="date_join" value="<?php echo $result['date_join']; ?>">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Card ID</label>
								<div class="col-md-9">
									<input type="text" class="form-control" id="rfid_cardid" name="rfid_cardid" value="<?php echo $result['rfid_cardid']; ?>">
									<span id="check_cardid" class="d-none form-text font-weight-bold small text-danger">Card ID has been taken</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">

							<div class="form-group row">
								<label class="col-form-label col-md-3">School</label>
								<div class="col-md-9">
									<select class="form-control select2" name="school">
										<option value="">-</option>';
										<?php foreach ($school as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['school'] ) echo 'selected'; ?>><?php echo $e['title']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label class="col-form-label col-md-3">Form</label>
								<div class="col-md-9">
									<select class="form-control select2" name="form">
										<option value="">-</option>';
										<?php foreach ($form as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['form'] ) echo 'selected'; ?>><?php echo $e['title']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<!--<div class="form-group row">
								<label class="col-form-label col-md-3">Parent</label>
								<div class="col-md-9">
									<select class="form-control select2" name="parent">
										<option value="">-</option>';
										<?php foreach ($parent as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['parent'] ) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>-->
							
							<div class="form-group row">
								<label class="col-form-label col-md-3">Guardian</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="guardian" value="<?php echo $result['guardian']; ?>">
								</div>
							</div>
						</div>
					</div>
				</div>

				<hr class="mb-4">

				<div class="row">
					<div class="col-md-6">
					
						<div class="form-group row">
							<label class="col-form-label col-md-3">Transportation</label>
							<div class="col-md-9">
								<select class="form-control select2" name="transport">
									<option value="">-</option>';

									<?php foreach ($transport as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($result['transport'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>';
									<?php } ?>
									
									 <?php foreach ($branch_transport as $e) { ?>
										<option value="<?php echo $e['secondary']; ?>" <?php if($result['transport'] == $e['secondary']) echo 'selected'; ?>><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
									<?php } ?>

								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Car Plate No</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="car_plate_no" value="<?php echo $result['car_plate_no']; ?>">
							</div>
						</div>
					</div>
					<div class="col-md-6">
							
						<div class="form-group row">
							<label class="col-form-label col-md-3">Address Pick-Up</label>
							<div class="col-md-9">
								<textarea class="form-control" name="address_pickup" rows="3"><?php echo $result['address_pickup']; ?></textarea>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Address Drop-Off</label>
							<div class="col-md-9">
								<textarea class="form-control" name="address_dropoff" rows="3"><?php echo $result['address_dropoff']; ?></textarea>
							</div>
						</div>
					</div>
                </div>

				<hr class="mb-4">
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Childcare</label>
							<div class="col-md-9">
								<select class="form-control select2" name="childcare">
									<option value="">-</option>';
									<?php foreach ($childcare as $e) { ?>
									<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['childcare'] ) echo 'selected'; ?>><?php echo $e['title']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Childcare Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="childcare_teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['childcare_teacher'] ) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
									<?php };?>
								</select>
							</div>
                        </div>
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Form Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="form_teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($e['pid'] == $result['form_teacher'] ) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
									<?php };?>
								</select>
							</div>
                        </div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Date Call</label>
							<div class="col-md-9">
								<input type="date" class="form-control" name="date_call" value="<?php echo $result['date_call']; ?>">
							</div>
                        </div>
					</div>
				</div>
				
				<hr class="mb-4">

                <div class="row">
					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark/Attention</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark (Active Subject)</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark_active" rows="4"><?php echo $result['remark_active']; ?></textarea>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark (Important/Urgent)</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark_important" rows="4"><?php echo $result['remark_important']; ?></textarea>
                            </div>
                        </div>
					</div>
                </div>
				
				<hr class="mb-4">
				
				<div class="row">
					<?php foreach(datalist('student_question') as $k => $v) { 
						$question = search($questions, 'title', $k);
						?>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-form-label col-md-3"><?php echo $v; ?></label>
								<div class="col-md-9">
									<textarea class="form-control" rows="4" name="question[<?php echo $k; ?>]"><?php echo count($question) > 0 ? $question[0]['remark'] : ''; ?></textarea>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>
                    </div>
					<div class="col-6 my-auto text-right">
						<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>)" class="btn btn-danger">Delete</button>
                    </div>
                </div>

				<ul class="nav nav-tabs mt-3">
				
					<?php
					
					if( check_module('Students/Modules/OutstandingPayment') ) { ?>
						<!--<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Outstanding Payment</a>
						</li>--><?php	
					}

					if( check_module('Students/Modules/ActivedClasses') ) { ?>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Actived Classes</a>
						</li><?php
					}
					
					if( check_module('Students/Modules/ActivedClasses') ) { ?>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-3">Parents</a>
						</li><?php
					}

					if( check_module('Students/Modules/ActivedClasses') ) { ?>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 4 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-4">Services</a>
						</li><?php
					}
					
					if( check_module('Students/Modules/UnpaidItems') ) { ?>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 5 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-5">Unpaid Items</a>
						</li><?php
					} 
					
					?>
					<li class="nav-item">
						<a class="nav-link <?php if( $_GET['tab'] == 6 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-6">Exam</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" target="_blank" href="<?php echo base_url('payment/list?student=' . $result['pid']) ?>">Payment History <i class="fa fa-fw fa-external-link-square-alt"></i></a>
					</li>
					
				</ul>

				<div class="tab-content py-3">
				
					<?php
					
					if( check_module('Students/Modules/OutstandingPayment') ) { ?>
						<!--<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
							sd
						</div>--><?php	
					}

					if( check_module('Students/Modules/ActivedClasses') ) { ?>
						<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-form-label col-md-3">Course</label>
										<div class="col-md-9">
											<select class="form-control select2 course-filter" onchange="filterTableClass($(this));">
												<option value="">All</option>';
												<?php foreach ($course as $e) { ?>
												<option value="<?php echo $e['title']; ?>"><?php echo $e['title']; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<table id="tableClass" class="table">
								<thead>
									<th style="width:10%">No</th>
									<th style="width:25%">Title</th>
									<th>Tutor</th>
									<th>Course</th>
									<th>Fee ($)</th>
									<th>Type</th>
									<th>Student(s)</th>
									<th>Joined</th>
									<th>Time Slot</th>
									<th>Join Date</th>
									<th>Credit Balance</th>
								</thead>
								<tbody>
									
									<?php 
									
									$i=0; foreach($class as $e) { $i++; 
									
										$joined = $this->log_join_model->std_class_active_check($e['pid'], $result['pid']);
										
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('classes/edit/'.$e['pid']); ?>"><?php echo $e['title']; ?></a></td>
											<td><?php echo empty($e['teacher']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['teacher']); ?></td>
											<td><?php echo empty($e['course']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['course']); ?></td>
											<td><?php echo empty($e['fee']) ? '-' : number_format($e['fee'], 2, '.', ','); ?></td>
											<td><?php echo datalist('class_type')[$e['type']]['label']; ?></td>
											<td>
												<?php
												
												// active_class_std
												$active_class_std = 0;
												
												foreach( $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $e['pid'], 'active' => 1 ]) as $e2 ) {
													if( datalist_Table('tbl_users', 'active', $e2['user']) == 1 && datalist_Table('tbl_users', 'is_delete', $e2['user']) == 0 ) { 
														$active_class_std++;
													}
												}
												
												echo $active_class_std;
												?>
											</td>
											<td>
												<label class="switch">
													<input type="checkbox" onclick="class_joined(<?php echo $e['pid']; ?>, <?php echo $result['pid']; ?>)" <?php if( count($joined) == 1 ) echo 'checked'; ?>>
													<span class="slider round"></span>
												</label>
											</td>
											<td>
												<select class="form-control select2" <?php if( count($joined) == 0 ) echo 'disabled'; ?> id="input-timetable-<?php echo $e['pid']; ?>" onchange="change_class_timetable(<?php echo $e['pid'] ?>, <?php echo $result['pid']; ?>, this.value)">
													<?php
													$timetable = $this->log_join_model->list('class_timetable', branch_now('pid'), [
														'class' => $e['pid']
													]);
													$join_class = $this->log_join_model->list('join_class', branch_now('pid'), [ 'class' => $e['pid'], 'user' => $result['pid']]);
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
												<?php if($e['type'] != 'check_in') { ?>
													<input type="date" class="form-control" onchange="change_class_date(<?php echo $e['pid'] ?>, <?php echo $result['pid']; ?>, this.value)" name="class_date" id="input-join_date-<?php echo $e['pid']; ?>" value="<?php if( count($joined) == 1 ) echo $joined[0]['date']; ?>" <?php if( count($joined) == 0 ) echo 'disabled'; ?>>
												<?php } ?>
											</td>
											<td class="text-center">
											<?php
											//if($e['type'] == 'check_in') echo class_credit_balance($e['pid'], $result['pid'])['balance'] . '/' . class_credit_balance($e['pid'], $result['pid'])['total'];
											if($e['type'] == 'check_in') echo class_credit_balance($e['pid'], $result['pid'])['balance'];
											?></td>
										</tr>
										<?php 
									} 
									?>
									
								</tbody>
							</table>
						</div><?php
					}
					
					if( check_module('Students/Modules/Parents') ) { ?>
						<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
							<div class="container-fluid mb-3">
								<div class="row d-flex justify-content-end">
									<div class="my-auto">
										<a href="javascript:;" data-toggle="modal" data-target="#modal-add-parent" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
									</div>
								</div>
							</div>
							<table class="DTable table">
								<thead>
									<th style="width:10%">No</th>
									<th style="width:35%">Name</th>
									<th style="width:30%">Relationship</th>
									<th>Create On</th>
									<th></th>
								</thead>
								<tbody>
									<?php $i=0; foreach($join_parent as $e) { $i++; ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('parents/edit/'.$e['parent']); ?>"><?php echo datalist_Table('tbl_users', 'fullname_en', $e['parent']); ?></a></td>
											<td><input type="text" class="form-control" onchange="update_relationship($(this));" data-id="<?php echo $e['id']; ?>" value="<?php echo $e['title']; ?>" /></td>
											<td><?php echo $e['create_on']; ?></td>
											<td>
												<a href="javascript:;" onclick="del_ask_parent(<?php echo $e['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div><?php
					}
					
					if( check_module('Students/Modules/Services') ) { ?>
						<div class="tab-pane fade <?php if( $_GET['tab'] == 4 ) echo 'show active'; ?>" id="tab-4">
							<table class="DTable table">
								<thead>
									<th style="width:10%">No</th>
									<th style="width:35%">Title</th>
									<th>Fee ($)</th>
									<th>Joined</th>
									<th>Join Date</th>
									<th>Credit Balance</th>
								</thead>
								<tbody>
									
									<?php 
									
									$i=0; foreach($services as $e) { $i++; 
										
										$joined = $this->log_join_model->std_service_active_check($e['pid'], $result['pid']);
										
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('items/edit/'.$e['pid']); ?>"><?php echo $e['title']; ?></a></td>
											<td><?php echo empty($e['price_sale']) ? '-' : number_format($e['price_sale'], 2, '.', ','); ?></td>
											<td>
												<label class="switch">
													<input type="checkbox" onclick="service_joined(<?php echo $e['pid']; ?>, <?php echo $result['pid']; ?>)" <?php if( count($joined) == 1 ) echo 'checked'; ?>>
													<span class="slider round"></span>
												</label>
											</td>
											<td>
												<?php if($e['type'] != 'check_in') { ?>
													<input type="date" class="form-control" onchange="change_service_date(<?php echo $e['pid'] ?>, <?php echo $result['pid']; ?>, this.value)" name="service_date" id="input-join_date-<?php echo $e['pid']; ?>" value="<?php if( count($joined) == 1 ) echo $joined[0]['date']; ?>" <?php if( count($joined) == 0 ) echo 'disabled'; ?>>
												<?php } ?>
											</td>
											<td class="text-center">
											<?php
											//if($e['type'] == 'check_in') echo class_credit_balance($e['pid'], $result['pid'])['balance'] . '/' . class_credit_balance($e['pid'], $result['pid'])['total'];
											if($e['type'] == 'check_in') echo class_credit_balance($e['pid'], $result['pid'])['balance'];
											?></td>
										</tr>
										<?php 
									} 
									?>
									
								</tbody>
							</table>
						</div><?php
					}
					
					if( check_module('Students/Modules/UnpaidItems') ) { ?>
						<div class="tab-pane fade <?php if( $_GET['tab'] == 5 ) echo 'show active'; ?>" id="tab-5">
							<div class="container-fluid mb-3">
								<div class="row d-flex justify-content-end">
									<div class="my-auto">
										<a href="javascript:;" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
									</div>
								</div>
							</div>
							
							<table class="DTable table">
								<thead>
									<th style="width:10%">No</th>
									<th style="width:35%">Item</th>
									<th>Date</th>
									<th>Qty</th>
									<th>Unit Price ($)</th>
									<th>Amount ($)</th>
									<th></th>
								</thead>
								<tbody>
									
								   <?php $i=0; foreach($unpaid_items as $e) { $i++; ?>
									<tr>
										<td><?php echo $i; ?></td>
										<td>
											<?php echo datalist_Table('tbl_inventory', 'title', $e['item']); ?>
											<?php if(!empty($e['remark'])) { ?>
												<em class="d-block text-muted small">Remark: <?php echo $e['remark'] ?></em>
											<?php } ?>
										</td>
										<td><?php echo $e['date']; ?></td>
										<td><?php echo $e['qty']; ?></td>
										<td><?php echo number_format($e['amount'] / $e['qty'], 2, '.', ','); ?></td>
										<td><?php echo number_format($e['amount'], 2, '.', ','); ?></td>
										<td>
											<!--<a href="javascript:;" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-item" data-id="<?php echo $e['id']; ?>" title="Edit"><i class="fa fa-fw fa-pen py-1"></i></a>-->
											<a href="javascript:;" onclick="del_ask_item(<?php echo $e['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
										</td>
									</tr>
									<?php } ?>
									
								</tbody>
							</table>
						</div><?php
					}
					
					?>
					
					<div class="tab-pane fade <?php if( $_GET['tab'] == 6 ) echo 'show active'; ?>" id="tab-6">
						<div class="table-responsive">
						    
						    <table class="table table-hover border">
        						<thead>
        							<th>No</th>
        							<th>Exam</th>
        							<th>Date</th>
        							<th>Score</th>
        						</thead>
        						<tbody>
        						    <?php 
        						    
        						    $i=0; foreach( $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND type=? AND user=? GROUP BY date ORDER BY date ASC ', [ 'exam_score', $result['pid'] ])->result_array() as $e1 ) { $i++; 
            						    $subject = datalist_Table('tbl_secondary', 'subject', $e1['secondary']);
            						    $subject = json_decode($subject, true);
            						    if(!is_array($subject)) $subject = [];
            						    ?>
                						    <tr>
                							    <td><?php echo $i; ?></td>
                							    <td><?php echo datalist_Table('tbl_secondary', 'title', $e1['secondary']); ?></td>
                							    <td><?php echo $e1['date']; ?></td>
                							    <td>
                							        <table class="table table-hover border">
                                						<thead>
                                							<th>No</th>
                                							<th>Exam</th>
                                							<th>Score</th>
                                						</thead>
                                						<tbody>
                                						    <?php $i2=0; foreach($subject as $e) { $i2++; ?>
                                    						    <tr>
                                    							    <td><?php echo $i2; ?></td>
                                    							    <td><?php echo $e; ?></td>
                                    							    <td>
                                    							        <?php
                                    							        
                                    							        $score = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND type=? AND secondary=? AND user=? AND subject=? AND date=? ', [ 'exam_score', $e1['secondary'], $result['pid'], $e, $e1['date'] ])->result_array();
                                    							        if(count($score) > 0) {
                                    							            $score = $score[0];
                                    							            echo $score['score'];
                                    							        }
                                    							        ?>
                                    							    </td>
                                    							</tr>
                                						    <?php } ?>
                                						</tbody>
                                					</table>
                							    </td>
                							</tr>
            						    <?php 
        						    }
        						    ?>
        						</tbody>
        					</table>
						    
					    </div>
					</div>

				</div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-add-parent" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Parent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<div class="form-group">
					<label class="text-danger">Parent</label>
					<select class="form-control select2" name="parent" onchange="redirect_parent_page(this.value)" required>
						<option value="">-</option>
						<option value="@NEW_PARENT">- Create new customer -</option>
						<?php
						foreach($parent as $e) {
							echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
						};?>
					</select>
				</div>
				<div class="form-group">
					<label>Relationship</label>
					<input type="text" class="form-control" name="relationship">
				</div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="add-parent">Save</button>
            </div>

        </div>
    </div>
</form>

<form method="post" class="modal fade" id="modal-add-item" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Unpaid Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th style="width: 60%;">Item</th>
							<th>Qty</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php $id = time(); ?>
						<tr id="<?php echo $id; ?>">
							<td>
								<select class="form-control select2" name="item[]" required>
									<option value="">-</option>
									<?php
									foreach($items as $e) {
										echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
									};?>
								</select>
							</td>
							<td>
								<input type="number" class="form-control" name="qty[]" value="1" required>
							</td>
							<td class="align-middle">
								<a href="javascript:;" onclick="del_row(<?php echo $id; ?>)" class="text-danger"><i class="fa fa-fw fa-times-circle"></i></a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td class="text-right" colspan="100%">
								<a href="javascript:;" onclick="append_row()">
									<i class="fa fa-fw fa-plus-circle"></i> Add New
								</a>
							</td>
						</tr>
					</tfoot>
				</table>
				
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>">
                </div>
				
				<div class="form-group">
                    <label>Remark</label>
                    <textarea class="form-control" name="remark" rows="4"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="add-item">Save</button>
            </div>

        </div>
    </div>
</form>

<form method="post" class="modal fade" id="modal-edit-item" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Unpaid Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label class="text-danger">Item</label>
					<input type="hidden" id="i_id" name="inventory[<?php echo time().rand(11, 99); ?>][inventory_id]">
					<input type="hidden" id="log_i_id" name="inventory[<?php echo time().rand(11, 99); ?>][log_inventory_id]">
                    <select class="form-control select2" name="item" required>
                        <option value="">-</option>';
                        <?php foreach ($items as $e) {
                            echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                        };?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Qty</label>
                    <input type="number" class="form-control" name="qty">
					<input type="hidden" name="price">
					<input type="hidden" name="amount">
                </div>
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="date">
                    <input type="hidden" name="id">
                </div>
				
				<div class="form-group">
                    <label>Remark</label>
                    <textarea class="form-control" name="remark" rows="4"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="save-item">Save</button>
            </div>

        </div>
    </div>
</form>

<div class="modal fade" id="modal-payment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Outstanding Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <div class="modal-body">
                <table class="table">
					<thead>
						<tr>
							<th>No</th>
							<th>Item / Class</th>
							<th>Qty</th>
							<th class="text-right">Discount</th>
							<th class="text-right">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
						if(isset($std_unpaid_result['result']['item'])) {
							foreach($std_unpaid_result['result']['item'] as $e) {
								$i++;
								?>
								<tr class="">
									<td><?php echo $i; ?></td>
									<td>
										<a href="javascript:;" onclick="edit_outstanding(<?php echo $e['id']; ?>)"><?php echo $e['title']; ?></a>
										<?php if(!empty($e['remark'])) { ?>
											<span class="d-block small text-muted">Remark: <?php echo $e['remark']; ?></span>
										<?php } ?>
									</td>
									<td>x <?php echo $e['qty']; ?></td>
									<td class="text-right"><?php echo number_format($e['discount'], 2, '.', ',') ; ?></td>
									<td class="text-right"><?php echo number_format($e['amount'], 2, '.', ',') ; ?></td>
								</tr>
								<?php
							}
						}
						
						if(isset($std_unpaid_result['result']['class'])) {
							foreach($new_unpaid_class as $e) {
								foreach($e['data'] as $e2) {
									if(isset($e2['amount'])) {
										$i++;
										?>
										<tr class="">
											<td><?php echo $i; ?></td>
											<td>
												<a href="javascript:;" onclick="<?php echo isset($e2['period']) ? 'edit_outstanding('.$e2['id'].', \''.$e2['period'].'\')' : 'edit_outstanding('.$e2['id'].')'; ?>">
													<?php
														if(isset($e2['period'])) {
															echo '[' . $e2['period'] . '] ' . $e2['title'];
														} else {
															echo $e2['title'];
														}
													?>
												</a>
												<?php if(!empty($e2['remark'])) { ?>
													<span class="d-block small text-muted">Remark: <?php echo $e2['remark']; ?></span>
												<?php } ?>
											</td>
											<td>x <?php echo $e2['qty']; ?></td>
											<td class="text-right"><?php echo number_format($e2['discount'], 2, '.', ',') ; ?></td>
											<td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',') ; ?></td>
										</tr>
										<?php
									}
									
								}
								if(isset($e['data']['amount'])) {
									$i++;
									?>
									<tr class="">
										<td><?php echo $i; ?></td>
										<td>
											<a href="javascript:;" onclick="<?php echo isset($e['data']['period']) ? 'edit_outstanding('.$e['data']['id'].', \''.$e['data']['period'].'\')' : 'edit_outstanding('.$e['data']['id'].')'; ?>">
												<?php
													if(isset($e['data']['period'])) {
														echo '[' . $e['data']['period'] . '] ' . $e['data']['title'];
													} else {
														echo $e['data']['title'];
													}
												?>
											</a>
											<?php if(!empty($e['data']['remark'])) { ?>
												<span class="d-block small text-muted">Remark: <?php echo $e['data']['remark']; ?></span>
											<?php } ?>
										</td>
										<td>x <?php echo $e['data']['qty']; ?></td>
										<td class="text-right"><?php echo number_format($e['data']['discount'], 2, '.', ',') ; ?></td>
										<td class="text-right"><?php echo number_format($e['data']['amount'], 2, '.', ',') ; ?></td>
									</tr>
									<?php
								}
							}
						}
						
						if(isset($std_unpaid_result['result']['service'])) {
							foreach($std_unpaid_result['result']['service'] as $e) {
								$i++;
								?>
								<tr class="">
									<td><?php echo $i; ?></td>
									<td>
										<a href="javascript:;" onclick="<?php echo isset($e['period']) ? 'edit_outstanding('.$e['id'].', \''.$e['period'].'\')' : 'edit_outstanding('.$e['id'].')'; ?>">
											<?php
												if(isset($e['period'])) {
													echo '[' . $e['period'] . '] ' . $e['title'];
												} else {
													echo $e['title'];
												}
											?>
										</a>
										<?php if(!empty($e['remark'])) { ?>
											<span class="d-block small text-muted">Remark: <?php echo $e['remark']; ?></span>
										<?php } ?>
									</td>
									<td>x <?php echo $e['qty']; ?></td>
									<td class="text-right"><?php echo number_format($e['discount'], 2, '.', ',') ; ?></td>
									<td class="text-right"><?php echo number_format($e['amount'], 2, '.', ',') ; ?></td>
								</tr>
								<?php
							}
						}
						
						foreach($material_fee as $e) {
							if($e['fee'] > 0) {
								$i++;
								?>
								<tr class="">
									<td><?php echo $i; ?></td>
									<td>
										<?php
										echo $e['title'];
										?>
									</td>
									<td>x 1</td>
									<td class="text-right"><?php echo number_format(0, 2, '.', ','); ?></td>
									<td class="text-right"><?php echo number_format($e['fee'], 2, '.', ',') ; ?></td>
								</tr>
								<?php
							}
						}
						
						foreach($subsidy_fee as $e) {
							if($e['fee'] > 0) {
								$i++;
								?>
								<tr class="">
									<td><?php echo $i; ?></td>
									<td>
										<?php
										echo $e['title'];
										?>
									</td>
									<td>x 1</td>
									<td class="text-right"><?php echo number_format($e['fee'], 2, '.', ',') ; ?></td>
									<td class="text-right"><?php echo number_format(0, 2, '.', ','); ?></td>
								</tr>
								<?php
							}
						}
						?>

                        <? if ($result['childcare_title'] != ""): ?>
                            <?
                            $i++;
                            ?>
                            <tr class="">
                                <td><?php echo $i; ?></td>
                                <td>
                                    <?php
                                    echo $result['childcare_title'];
                                    ?>
                                </td>
                                <td>x 1</td>
                                <td class="text-right"><?php echo number_format($result['childcare_price'], 2, '.', ',') ; ?></td>
                                <td class="text-right"><?php echo number_format($result['childcare_price'], 2, '.', ','); ?></td>
                            </tr>
                        <? endif; ?>

                        <? if ($result['transport_title'] != ""): ?>
                            <?
                            $i++;
                            ?>
                            <tr class="">
                                <td><?php echo $i; ?></td>
                                <td>
                                    <?php
                                    echo $result['transport_title'];
                                    ?>
                                </td>
                                <td>x 1</td>
                                <td class="text-right"><?php echo number_format($result['transport_price'], 2, '.', ',') ; ?></td>
                                <td class="text-right"><?php echo number_format($result['transport_price'], 2, '.', ','); ?></td>
                            </tr>
                        <? endif; ?>

                    </tbody>
					<tfoot class="font-weight-bold">
						<tr>
							<td colspan="4">
								<p>Subtotal</p>
								<p>Discount</p>
								<p class="m-0">Total</p>
							</td>
							<td class="text-right">
								<p><?php echo number_format($total_payment, 2, '.', ',') ; ?></p>
								<p><?php echo number_format($total_discount, 2, '.', ',') ; ?></p>
								<p class="m-0"><?php echo number_format($total, 2, '.', ',') ; ?></p>
							</td>
						</tr>
					</tfoot>
				</table>
            </div>

            <div class="modal-footer">
				<a class="btn btn-success mr-2" href="<?php echo base_url('payment/add/'.$result['pid']); ?>">New Payment</a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<form method="post" class="modal fade" id="modal-edit-outstanding" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Unpaid Item / Class</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
				<div class="form-group row">
					<label class="col-md-3 col-form-label">Title</label>
					<div class="col-md-9">
						<p name="title" class="font-weight-bold form-control-plaintext"></p>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-md-3 col-form-label">Amount</label>
					<div class="col-md-9">
						<p name="amount" class="font-weight-bold form-control-plaintext"></p>
					</div>
				</div>
				
				<div class="form-group row">
					<label class="col-md-3 col-form-label">Qty</label>
					<div class="col-md-9">
						<p name="qty" class="font-weight-bold form-control-plaintext"></p>
					</div>
				</div>
				
				<hr>
			
				<div class="form-group">
					<label>Discount</label>
					<input type="number" step="0.01" class="form-control" name="discount">
					<input type="hidden" name="id">
					<input type="hidden" name="month">
				</div>
				
				<div class="form-group">
					<label>Remark</label>
					<textarea rows="4" class="form-control" name="remark"></textarea>
				</div>
			</div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="edit_outstanding">Save</button>
            </div>
        </div>
    </div>
</form>


<form method="post" class="modal fade" id="modal-add_new_parent" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add New Parent</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
			
				<div class="form-group row">
					<label class="col-form-label col-md-3 text-danger">Full Name</label>
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-6">
								<input type="text" class="form-control" name="fullname_en" placeholder="English" required>
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fullname_cn" placeholder="中文 (Optional)">
							</div>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-md-3">Relationship</label>
					<div class="col-md-9">
						<input type="text" class="form-control" name="relationship" required>
					</div>
				</div>
				
				<a href="<?php echo base_url('parents/add'); ?>" target="_blank">Show more options <i class="fas fa-fw fa-external-link-square-alt"></i></a>

			</div>

			<div class="modal-footer">
				<button type="submit" name="add_new_parent" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>


<script>
	<?php if( !check_module('Students/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
	var items_options = '<?php echo json_encode($items); ?>'
	var formTitle = '<?php echo datalist_Table('tbl_secondary', 'title', $result['form']); ?>'
</script>