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
							<label class="col-form-label col-md-3">Parent</label>
							<div class="col-md-9">
								<select class="form-control select2" name="parent">
									<option value="">-</option>
									<?php foreach($parents as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if($_GET['parent'] == $e['pid']) echo 'selected'; ?>><?php echo $e['fullname_en']; ?> (<?php echo $e['phone']; ?>)</option>
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
                    <th style="width:12%">Code</th>
                    <th style="width:25%">Parent</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Total Outstanding Student</th>
                    <th class="text-right">Total Outstanding Amount ($)</th>
                    <th></th>
                    <th></th>
                </thead>
                <tbody>
					<?php $i=$row; foreach($result as $k => $stds) { $i++; 
						$parent = search($parents, 'pid', $k);
						$students = search($student_result, 'parent_pid', $k);
						
						if (count($students) > 0) {
							?>
								<tr class="table-danger main-tr" data-toggle="collapse" data-target="#collapse<?php echo $k; ?>" aria-expanded="true" aria-controls="collapse<?php echo $k; ?>">
									<td><?php echo $i; ?></td>
									<td>-</td>
									<td><?php echo count($parent) > 0 ? $parent[0]['fullname_en'] .' '. $parent[0]['fullname_cn'] : '-'; ?></td>
									<td><?php echo count($parent) > 0 ? $parent[0]['phone'] : '-'; ?></td>
									<td><?php echo count($parent) > 0 ? (empty($parent[0]['email']) ? '-' : $parent[0]['email']) : '-'; ?></td>
									<td><?php echo array_sum(array_column($students, 'count')); ?></td>
									<td class="text-right"><?php echo number_format(array_sum(array_column($students, 'total')), 2, '.', ','); ?></td>
									<td></td>
									<td></td>
								</tr>
							<?
							foreach($students as $ev) {
								if(isset($ev['pid'])) {
								?>
								<tr class="collapse p-0 table-light" id="collapse<?php echo $k; ?>">
									<td></td>
									<td><?php echo $ev['code']; ?></td>
									<td><a href="<?php echo base_url('students/edit/'.$ev['pid']); ?>"> <?php echo $ev['fullname_en']; ?> <?php echo $ev['fullname_cn']; ?></a></td>
									<td><?php echo empty($ev['phone']) ? '-' : $ev['phone']; ?></td>
									<td><?php echo empty($ev['email']) ? '-' : $ev['email']; ?></td>
									<!--<td>x <?php echo $ev['count'] ?></td>
									<td class="text-right"><?php echo number_format($ev['total'], 2, '.', ','); ?></td>-->
									<td>
										<?php foreach($ev['details'] as $k2 => $v2) {
											echo $k2 . '<br>';
										} ?>
									</td>
									<td class="text-right">
										<?php foreach($ev['details'] as $k2 => $v2) {
											echo number_format($v2, 2, '.', ',') . '<br>';
										} ?>
									</td>
									<td class="font-weight-bold"><a href="<?php echo base_url('payment/add/'.$ev['pid']); ?>"><i class="fa fa-fw fa-file-invoice"></i> Make a Payment</a></td>
									<td>
										<?php
										
										if( !empty($branch_pointoapi_key) ) {
											
											// send whatsapp
											$phone = $ev['phone'];
											
											if(!empty($phone)) {
												
												if(substr($phone, 0, 1) != '+') $phone = '6'.$phone;
												
												// v1
												// $msg = 'Hey *'.datalist_Table('tbl_users', 'fullname_en', $ev['student']).'*, here\'s your confirmation for receipt number *'.$ev['payment_no'].'*. Review your receipt by click the link: '.urlencode( base_url('export/pdf_export/'.$ev['pid']) ).' %0a %0a ';
												// $msg .= '* This message send via '.app('title');
												
												// v2
												$msg = $branch_send_msg_whatsapp_outstanding;
												
												if(!empty($msg)) {
													
													$msg .= ' %0a %0a ';
													$msg .= '* This message send via '.app('title');
													
													$msg = str_replace('%NAME%', $ev['fullname_en'], $msg);
													$msg = str_replace('%PHONE%', $branch_phone, $msg);
													
													?><a href="https://wa.me/<?php echo $phone; ?>?text=<?php echo $msg; ?>" target="_blank" class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												} else {
													
													?><a href="javascript:;" class="btn btn-secondary btn-sm" style="opacity: .5" data-toggle="tooltip" title="WhatsApp content haven't been set"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
												
												}
												
											} else {
												
												?><a href="javascript:;" class="btn btn-secondary btn-sm <?php if( empty($ev['phone']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send WhatsApp"><i class="fab fa-fw fa-whatsapp py-1"></i></a><?php
											
											}
												
											?>
											
											<a href="javascript:;" onclick="send_email(<?php echo $ev['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( empty($ev['email']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send Email"><i class="fa fa-fw fa-envelope py-1"></i></a>
											
											<a href="javascript:;" onclick="send_sms(<?php echo $ev['pid']; ?>)" class="btn btn-secondary btn-sm d-lg-inline-blockA d-noneA <?php if( empty($ev['phone']) ) { echo 'disabled'; } ?>" data-toggle="tooltip" title="Send SMS"><i class="fa fa-fw fa-comments py-1"></i></a>
											
											<?php
										}
										?>
									</td>
								</tr>
								<?php
								}
							}
						}
					?>
					<?php } ?>
                </tbody>
				<tfoot>
					<tr><td colspan="8"><?= $pagination; ?></td></tr>
				</tfoot>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>