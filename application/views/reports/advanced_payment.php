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
			
			<form method="get" id="my-form">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
								<label class="col-form-label col-md-3">Period</label>
								<div class="col-md-9">
										<input type="month" class="form-control" name="period" value="<?php if(isset($_GET['period'])) { echo $_GET['period']; } else {
												echo date('Y-m'); } ?>" required>
								</div>
						</div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Status</label>
							<div class="col-md-9">
								<select class="form-control" name="status" required>
									<option value="paid" <?php if(isset($_GET['status'])) { if($_GET['status'] == 'paid') { echo 'selected'; } } ?> >Paid</option>
									<option value="unpaid" <?php if(isset($_GET['status'])) { if($_GET['status'] == 'unpaid') { echo 'selected'; } } ?> >Unpaid</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="offset-md-3 col-md-9">
								<button type="submit" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			
			<div id="hidden-pagination" class="d-none"><?php echo $links ?></div>
				
			<nav aria-label="Page navigation example">
				<ul class="pagination">
				</ul>
			</nav>
            
            <table class="table-hover table">
                <thead>
                    <th style="width:10%">No</th>
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
						
						if(isset($_GET['period'])) {
							$period = $_GET['period'];
						} else {
							$period = date('Y-m');
						}
						
						
						if(isset($_GET['status'])) {
							$status = $_GET['status'];
						} else {
							$status = 'paid';
						}
						?>
						<a href="<?php echo base_url('reports/advanced_payment?sort='.$sort.'&period='.$period.'&status='.$status); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php echo $_GET['sort'] == 'asc' ? 'down' : 'up'; ?>"></i></a>
					</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Total <?php if(isset($_GET['status'])) { echo ucfirst($_GET['status']); } else { echo 'Paid'; } ?> Item / Class</th>
                    <th class="text-right">Total <?php if(isset($_GET['status'])) { echo ucfirst($_GET['status']); } else { echo 'Paid'; } ?> Amount ($)</th>
										<?php if(isset($_GET['status']) == 'unpaid') { ?>
											<th></th>
										<?php } ?>
                    <th></th>
                </thead>
                <tbody>
				
					<?php 
					
					// v3 by steve
					/* $count=0;
					$total = 0;
					
					foreach($result_std as $e) { 
					
						$std_unpaid_result = std_unpaid_result($e['pid']);
						
						if($std_unpaid_result['count'] > 0) {
							
							$count++;
							$total+=$std_unpaid_result['total'];
						
							?>
							<tr class="table-danger main-tr" data-toggle="collapse" data-target="#collapse<?php echo $e['pid']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $e['pid']; ?>">
								<td><?php echo $count; ?></td>
								<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo $e['fullname_en']; ?></a></td>
								<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
								<td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
								<td>x <?php echo $std_unpaid_result['count'] ?></td>
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
												$msg = str_replace('%SUBJECT%', $e['fullname_en'], $msg);
												$item = '';
												
												$std_unpaid_result = std_unpaid_result($e['pid'], $e['branch']);
												
												if($std_unpaid_result['count'] > 0) {
													$j = 0;
													$i = $std_unpaid_result['count'];
													if(isset($std_unpaid_result['result']['class'])) {
														foreach($std_unpaid_result['result']['class'] as $e2) {
															$j++;
															if($j < $i) {
																$item .= $e2['title'] . ' x ' . $e2['qty'] . ', ';
															} else {
																$item .= $e2['title'] . ' x ' . $e2['qty'];
															}
														}
													}
													if(isset($std_unpaid_result['result']['item'])) {
														foreach($std_unpaid_result['result']['item'] as $e2) {
															$j++;
															if($j < $i) {
																$item .= $e2['title'] . ' x ' . $e2['qty'] . ', ';
															} else {
																$item .= $e2['title'] . ' x ' . $e2['qty'];
															}
														}
													}
												}
												
												$msg = str_replace('%ITEM%', $item, $msg);
												$msg = str_replace('%TOTALOUTSTANDINGAMOUNT%', number_format($std_unpaid_result['total'], 2, '.', ','), $msg);
												
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
										<td><?php echo $e2['title']; ?></td>
										<td></td>
										<td></td>
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
								
								foreach($std_unpaid_result['result']['class'] as $e2) {
									?>
									<tr class="collapse p-0 table-light" id="collapse<?php echo $e['pid']; ?>">
										<td></td>
										<td><?php echo isset($e2['period']) ? '[' . $e2['period'] . '] ' . $e2['title'] : $e2['title']; ?></td>
										<td></td>
										<td></td>
										<td>x <?php echo $e2['qty']; ?></td>
										<td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',') ; ?></td>
										<td>
											<?php if(isset($e2['period'])) { ?>
												<a href="javascript:;" onclick="del_ask_class(<?php echo $e['pid'] ?>, <?php echo $e2['class']; ?>, '<?php echo $e2['period']; ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
											<?php } else { ?>
												<a href="javascript:;" onclick="del_ask_class(<?php echo $e['pid'] ?>, <?php echo $e2['class']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
											<?php } ?>
										</td>
										<td></td>
									</tr>
									<?php
								}
								
							}

						}
					
					}
					
					if($count == 0) echo '<tr><td colspan=8" class="text-center">No result found</td></th>'; */
					?>
					
					<tr id="no_result_found" style="display: none;"><td colspan="8" class="text-center">No result found<td></tr>
                    
                </tbody>
                <tfoot>
					<tr>
						<th colspan="5" class="text-right">Total <?php if(isset($_GET['status'])) { echo ucfirst($_GET['status']); } else { echo 'Paid'; } ?> Payment</th>
						<th class="text-right">
							<input type="hidden" name="total_amount" value="0">
							<span id="total_amount"></span>
							<?php //echo number_format($total, 2, '.', ','); ?>
						</th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th colspan="5" class="text-right">Total <?php if(isset($_GET['status'])) { echo ucfirst($_GET['status']); } else { echo 'Paid'; } ?> Student(s)</th>
						<th class="text-right">
							<input type="hidden" name="total_amount_std" value="0">
							<input type="hidden" name="page" value="0">
							<input type="hidden" name="branch" value="<?php echo branch_now('pid'); ?>">
							<span id="total_amount_std"></span>
							<?php //echo number_format($count, 0, '.', ''); ?>
						</th>
						<th></th>
						<th></th>
					</tr>
                </tfoot>
            </table>
			
			<nav aria-label="Page navigation example">
				<ul class="pagination">
				</ul>
			</nav>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>