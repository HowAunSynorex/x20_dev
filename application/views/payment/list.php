<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold">
						<?php
						
						echo $thispage['title'];
						if( isset($_GET['student']) ) echo '<span class="badge badge-secondary badge-pill badge-sm ml-2"><a href="javascript:;" data-label="return_payment" class="text-white"><i class="fa fa-fw fa-times"></i></a> '.datalist_Table('tbl_users', 'fullname_en', $_GET['student']).'</span>';
						?>
					</h4>
                </div>
                <div class="col-6 my-auto text-right">
                    <?php
					
					if( check_module('Payment/Create') ) {
						$url = base_url('payment/add');
						if(branch_now('version') == 'shushi') $url = base_url('payment/add2');
						?>
						<div class="btn-group">
							<a href="<?php echo $url; ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
							<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right">
								<a href="<?php echo $url.'?by_class'; ?>" class="dropdown-item">New Payment by Class</a>
							</div>
						</div>
						<?php
					}
					?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<?php /* if( empty(branch_now('pointoapi_key')) ) { ?>
			<div class="alert alert-info">Setup PointoAPI API Key to send receipt via WhatsApp, Email or SMS (SMS will charge extra fees). <a href="<?php echo base_url('settings/pointoapi'); ?>">Setup</a></div>
			<?php } */ ?>
			
			<div id="hidden-pagination" class="d-none"><?php echo $links ?></div>
			
			<?php if(!empty($links) || isset($more)) { ?>
				<div class="d-flex justify-content-between mb-3">
					<nav aria-label="Page navigation example">
						<ul class="pagination m-0">
						</ul>
					</nav>
					<?php
					if(isset($more)) {
						?>
						<form method="get" id="search">
							<div class="d-flex">
								<label class="col-form-label">Search: </label>
								<input type="text" name="q" class="ml-2 form-control" value="<?php if(isset($_GET['q'])) echo $_GET['q']; ?>" onchange="document.getElementById('search').submit()">
							</div>
						</form>
						<?php
					}
					?>
				</div>
			<?php } ?>
			
			<?php if( check_module('Payment/Create') ) { ?>
			
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a href="<?php echo base_url('payment/list'); ?>" class="nav-link <?php if( $_GET['tab'] == '' ) echo 'active'; ?>">Publish</a>
					</li>
					<!-- <li class="nav-item">
						<a href="<?php echo base_url('payment/list?tab=draft'); ?>" class="nav-link <?php if( $_GET['tab'] == 'draft' ) echo 'active'; ?>">Draft</a>
					</li> -->
					<li class="nav-item">
						<a href="<?php echo base_url('payment/list?tab=pending'); ?>" class="nav-link <?php if( $_GET['tab'] == 'pending' ) echo 'active'; ?>">Pending</a>
					</li>
				</ul>
				
				<div class="table-responsive mt-2">
					<table class="<?php if(!isset($more)) echo 'DTable2'; ?> table">
						<thead>
							<th>
								Date / Time
								<?php
								if(isset($more)) {
									$sort = 'desc';
									if(isset($_GET['sort'])) {
										if($_GET['sort'] == 'asc') {
											$sort = 'desc';
										} else {
											$sort = 'asc';
										}
									}
									?>
									<a href="<?php echo isset($_GET['q']) ? base_url('payment/list?sort='.$sort.'&q='.$_GET['q']) : base_url('payment/list?sort='.$sort); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php if(isset($_GET['sort'])) { if($_GET['sort'] == 'asc') { echo 'down'; } else { echo 'up'; } } else { echo 'down'; } ?>"></i></a>
									<?php
								}
								?>
							</th>
							<th>Receipt No</th>
							<th>Date</th>
							<!--162847327573-->
							<th>Student</th>
							<th>Cashier</th>
							<th>Payment Method</th>
							<th class="text-right">Total ($)</th>
							<th></th>
						</thead>
						<tbody>
							<?php $i=0; foreach($result as $e) { $i++; ?>
							<tr>
								<td><?php echo $e['create_on']; ?></td>
								<td><a href="<?php echo base_url('payment/edit/'.$e['pid']); ?>"><?php echo $e['payment_no']; ?></a></td>
								<!--<td><span class="badge badge-<?php echo datalist('payment_status')[ $e['status'] ]['status']; ?>"><?php echo datalist('payment_status')[ $e['status'] ]['title']; ?></span></td>-->
								<td><?php echo $e['date']; ?></td>
								<td>
									<?php
									
									if ( datalist_Table('tbl_users', 'is_delete', $e['student']) == 0 ){
										
										?><a href="<?php echo base_url('students/edit/'.$e['student']); ?>"><?php echo datalist_Table('tbl_users', 'fullname_cn', $e['student']).' '.datalist_Table('tbl_users', 'fullname_en', $e['student']); ?></a><?php
										
									} else {
										
										echo datalist_Table('tbl_users', 'fullname_en', $e['student']);
										
									}
									?>
								</td>
								<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
								<td><?php echo empty($e['payment_method']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['payment_method']) ; ?></td>
								<td class="text-right"><?php echo number_format($e['total'], 2, '.', ','); ?></td>
								<td>
									<?php
									if($e['is_delete'] == 1){?>
										<b style="color:red">Unavailable</b>
									<?php }else{
									
										// edit
										if( check_module('Payment/Update') ) {
											
											?><a href="edit/<?php echo $e['pid']; ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit"><i class="fa fa-fw fa-pen py-1"></i></a> <?php
											
										}
									
										// print
										if( check_module('Payment/Modules/Print') ) {
											
											?>
											<a href="<?php echo base_url('export/pdf_export/'.$e['pid']); ?>" target="_blank" class="btn btn-success btn-sm" data-toggle="tooltip" title="Print"><i class="fa fa-fw fa-print py-1"></i></a>
											<!--<a href="<?php //echo base_url('export/pdf_receipt/'.$e['pid']); ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Print Receipt"><i class="fa fa-fw fa-print py-1"></i></a>-->
											<a onclick="print_recp(<?php echo $e['pid']; ?>)" class="btn btn-success btn-sm" data-toggle="tooltip" title="Print"><i class="fa fa-fw fa-print py-1"></i></a> 
											 <?php
										}
											
										// $empty_pointoapi = empty(branch_now('pointoapi_key')) ? 'Setup PointoAPI API Key to use this function' : '' ;
										
										// send
										if( check_module('Payment/Modules/Send') ) {
											
											// send whatsapp
											$phone = datalist_Table('tbl_users', 'phone', $e['student']);
											
											if(!empty($phone)) {
												
												// if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;
												
												// v1
												// $msg = 'Hey *'.datalist_Table('tbl_users', 'fullname_en', $e['student']).'*, here\'s your confirmation for receipt number *'.$e['payment_no'].'*. Review your receipt by click the link: '.urlencode( base_url('export/pdf_export/'.$e['pid']) ).' %0a %0a ';
												// $msg .= '* This message send via '.app('title');
												
												// v2
												$msg = branch_now('send_msg_whatsapp');
												
												if(!empty($msg)) {
													
													$msg .= ' %0a %0a ';
													$msg .= '* This message send via '.app('title');
													
													$msg = str_replace('%NAME%', datalist_Table('tbl_users', 'fullname_en', $e['student']), $msg);
													$msg = str_replace('%RECEIPT_NO%', $e['payment_no'], $msg);
													$msg = str_replace('%LINK%', urlencode( base_url('export/pdf_export/'.$e['pid']) ), $msg);
													
													?><a href="https://wa.me/<?php echo wa_format($phone); ?>?text=<?php echo $msg; ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												} else {
													
													?><a href="javascript:;" class="btn btn-secondary btn-sm" style="opacity: .5" data-toggle="tooltip" title="WhatsApp content haven't been set"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												}
												
											} else {
												
												?><a href="javascript:;" class="btn btn-secondary btn-sm <?php if( datalist_Table('tbl_users', 'phone', $e['student']) == '') { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
											
											}
											
											?>
											
											<!--<a href="javascript:;" onclick="send_email(<?php echo $e['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( datalist_Table('tbl_users', 'email', $e['student']) == '') { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send Email"><i class="fa fa-fw fa-envelope py-1"></i></a>
											<a href="javascript:;" onclick="send_sms(<?php echo $e['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( datalist_Table('tbl_users', 'phone', $e['student']) == '') { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send SMS"><i class="fa fa-fw fa-comments py-1"></i></a>-->
											<?php

										}
											
										// delete
										if( check_module('Payment/Delete') ) {
											
											?><a href="javascript:;" onclick="del_ask(<?php echo $e['pid']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash py-1"></i></a> <?php
											
										}
									}
									?>
								</td>
							</tr>
							<?php
							}
							if(empty($result)) {
								?>
								<tr><td colspan="8" class="text-center">No result found</td></tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			<?php } else { ?>
			
				<div class="alert alert-info">You only have the permission to create payment</div>
			
			<?php } ?>
				
			<nav aria-label="Page navigation example">
				<ul class="pagination">
				</ul>
			</nav>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>