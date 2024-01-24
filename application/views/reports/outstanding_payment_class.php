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
							<label class="col-form-label col-md-3">Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="teacher" onchange="form.submit();">
									<option value="">-</option>
									<?php foreach($teachers as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($_GET['teacher'] == $e['pid']) echo 'selected'; ?>><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Class</label>
							<div class="col-md-9">
								<select class="form-control select2" name="class">
									<option value="">-</option>
									<?php foreach($classes as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($_GET['class'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
									<?php } ?>
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
            
            <table class="table-hover table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:10%">Code</th>
                    <th style="width:25%">
						Student
						<?php
						$sort = 'desc';
						if(isset($_GET['sort'])) {
							if($_GET['sort'] == 'asc') {
								$sort = 'desc';
							} else {
								$sort = 'asc';
							}
						}
						?>
						<a href="<?php echo base_url('reports/outstanding_payment_class?class='.$_GET['class'].'&sort='.$sort); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php if(isset($_GET['sort'])) { if($_GET['sort'] == 'asc') { echo 'down'; } else { echo 'up'; } } else { echo 'down'; } ?>"></i></a>
					</th>
                    <th>Phone</th>
                    <th class="text-right">Total Outstanding Amount ($)</th>
                    <th></th>
                    <th></th>
                </thead>
                <tbody>
				
					<?php
					
					if(isset($_GET['class'])) {
						
						$j=0;
					
						foreach($result_std as $e) { 
						
							$std_unpaid_result = std_unpaid_result2($e['pid']);
							$new_unpaid_class = [];
							$material_fee = [];
							$subsidy_fee = [];
							$with_class_bundle = [];
							$without_class_bundle = [];
							$total_material_fee = 0;
							$total_subsidy_fee = 0;
							
							if($std_unpaid_result['count'] > 0) {
								
								if(isset($std_unpaid_result['result']['class'])) {
									
									foreach($std_unpaid_result['result']['class'] as $e2) {
										$check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
											'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
										]);
										if(count($check_class_bunlde) > 0) {
											$check_class_bunlde = $check_class_bunlde[0];
											$with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
										} else {
											$without_class_bundle[]['data'] = $e2;
										}
									}
									
									foreach($with_class_bundle as $k => $v) {
										$check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
											'parent' => $k,
											'qty' => count($v['data'])
										]);
										if(count($check_bundle_price) > 0) {
											$check_bundle_price = $check_bundle_price[0];
											$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
											$material_fee[] = [
												'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
												'fee' => $check_bundle_price['material']
											];
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
												AND qty < '.count($v['data']).'
												ORDER BY qty DESC
											')->result_array();
											if(count($check_bundle_price) > 0) {
												$check_bundle_price = $check_bundle_price[0];
												$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
												for($i=0; $i<floor(count($v['data']) / $check_bundle_price['qty']); $i++) {
													$material_fee[] = [
														'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
														'fee' => $check_bundle_price['material']
													];
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
										$bundle_subsidy = $this->db->query('
											SELECT * FROM log_join
											WHERE is_delete = 0
											AND type = "class_bundle_price"
											AND branch = "' . branch_now('pid') . '"
											AND parent = "' . $k . '"
											AND subsidy > 0
											AND qty < ' . count($v['data']) . '
											ORDER BY qty DESC
										')->result_array();
										if(count($bundle_subsidy) > 0) {
											$bundle_subsidy = $bundle_subsidy[0];
											for($i=0; $i<floor(count($v['data']) / $bundle_subsidy['qty']); $i++) {
												$subsidy_fee[] = [
													'title'	=> 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
													'fee' => $bundle_subsidy['subsidy']
												];
											}
										}
									}
									
									foreach($material_fee as $e2) {
										$total_material_fee += $e2['fee'];
									}
									
									foreach($subsidy_fee as $e2) {
										$total_subsidy_fee += $e2['fee'];
									}
								}
							}
							
							$total_payment = 0;
							$total_discount = 0;
							$total = 0;
							
							$new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
							
							foreach($new_unpaid_class as $e2) {
								foreach($e2['data'] as $e3) {
									if(isset($e3['amount'])) {
										$total_payment += $e3['amount'] - $e3['discount'];
									}
								}
								if(isset($e2['data']['amount'])) {
									$total_payment += $e2['data']['amount'] - $e2['data']['discount'];
								}
							}
							
							if(isset($std_unpaid_result['result']['item'])) {
								foreach($std_unpaid_result['result']['item'] as $e2) {
									$total_payment += $e2['amount'];
									$total_discount += $e2['discount'];
								}
							}
							
							if(isset($std_unpaid_result['result']['service'])) {
								foreach($std_unpaid_result['result']['service'] as $e2) {
									$total_payment += $e2['amount'];
									$total_discount += $e2['discount'];
								}
							}
							
							$total_payment += $total_material_fee;
							$total_discount += $total_subsidy_fee;
							$total = $total_payment - $total_discount;
							
							if($std_unpaid_result['count'] > 0) {
								
								$j++;
						
								?>
								<tr class="table-danger main-tr" data-toggle="collapse" data-target="#collapse<?php echo $e['pid']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $e['pid']; ?>">
									<td><?php echo $j; ?></td>
									<td><?php echo $e['code']; ?></td>
									<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></a></td>
									<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
									<td class="text-right"><?php echo number_format($std_unpaid_result['total'], 2, '.', ','); ?></td>
									<td class="font-weight-bold"><a href="<?php echo base_url('payment/add/'.$e['pid']); ?>"><i class="fa fa-fw fa-file-invoice"></i> Make a Payment</a></td>
									<td>
										<?php
										
										if( !empty(branch_now('pointoapi_key')) ) {
											// send whatsapp
											$phone = $e['phone'];
											
											if(!empty($phone)) {
												
												if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;
												
												// v1
												// $msg = 'Hey *'.datalist_Table('tbl_users', 'fullname_en', $e['student']).'*, here\'s your confirmation for receipt number *'.$e['payment_no'].'*. Review your receipt by click the link: '.urlencode( base_url('export/pdf_export/'.$e['pid']) ).' %0a %0a ';
												// $msg .= '* This message send via '.app('title');
												
												// v2
												$msg = branch_now('send_msg_whatsapp_outstanding');
												
												if(!empty($msg)) {
													
													$msg .= ' %0a %0a ';
													$msg .= '* This message send via '.app('title');
													
													$msg = str_replace('%NAME%', $e['fullname_en'], $msg);
													$msg = str_replace('%PHONE%', branch_now('phone'), $msg);
													
													?><a href="https://wa.me/<?php echo $phone; ?>?text=<?php echo $msg; ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												} else {
													
													?><a href="javascript:;" class="btn btn-secondary btn-sm" style="opacity: .5" data-toggle="tooltip" title="WhatsApp content haven't been set"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												}
												
											} else {
												
												?><a href="javascript:;" class="btn btn-secondary btn-sm <?php if( empty($e['phone']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
											
											}
												
											?>
											
											<a href="javascript:;" onclick="send_email(<?php echo $e['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( empty($e['email']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send Email"><i class="fa fa-fw fa-envelope py-1"></i></a>
											
											<a href="javascript:;" onclick="send_sms(<?php echo $e['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( empty($e['phone']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send SMS"><i class="fa fa-fw fa-comments py-1"></i></a>
											
											<?php
										}
										?>
									</td>
								</tr>
								<?php
								
								// unpaid item
								if(isset($std_unpaid_result['result']['item'])) {
									
									foreach($std_unpaid_result['result']['item'] as $e2) {
										?>
										<tr class="collapse p-0 table-light" id="collapse<?php echo $e['pid']; ?>">
											<td></td>
											<td></td>
											<td><?php echo $e2['title']; ?></td>
											<td>x <?php echo $e2['qty']; ?></td>
											<td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',') ; ?></td>
											<td>
												<a href="javascript:;" onclick="del_ask_item(<?php echo $e2['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
											</td>
											<td></td>
										</tr>
										<?php
									}
									
								}
								
								// unpaid class
								if(isset($std_unpaid_result['result']['class'])) {
									
									$single_arr = (singledimensional('data', $new_unpaid_class));
									$group_by = group_by('period', $single_arr);
									unset($group_by['']);
											
									foreach($group_by as $k => $e22) {
									?>
										<tr class="collapse p-0 table-light" id="collapse<?php echo $e['pid']; ?>">
											<td></td>
											<td></td>
											<td><?php echo $k ?></td>
											<td></td>
											<td class="text-right"><?php echo number_format(array_sum(array_column($e22, 'amount')), 2, '.', ',') ; ?></td>
											<td></td>
											<td></td>
										</tr>
									<?php foreach($e22 as $e3) {
											if(isset($e3['amount']) && ($e3['amount'] > 0)) {
												?>
												<tr class="collapse p-0 table-light" id="collapse<?php echo $e['pid']; ?>">
													<td></td>
													<td></td>
													<td><?php echo '[' . $e3['period'] . '] ' . $e3['title']; ?></td>
													<td>x 1</td>
													<td class="text-right"><?php echo number_format($e3['amount'], 2, '.', ',') ; ?></td>
													<td>
														<a href="javascript:;" onclick="del_ask_class(<?php echo $e['pid'] ?>, <?php echo $e3['class']; ?>, '<?php echo $e3['period']; ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
													</td>
													<td></td>
												</tr>
												<?php
											}
										}
									}
								}

							}
						
						}
						
						//if($i == 0) echo '<tr><td colspan=8" class="text-center">No result found</td></th>';
						
					}
					
					?>
					
					<tr id="no_result_found" <?php if(isset($_GET['class'])) echo 'style="display: none;"'; ?>><td colspan="8" class="text-center">No result found<td></tr>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>