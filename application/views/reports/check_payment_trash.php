<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold">
						<?php echo $thispage['title']; ?>
					</h4>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

				<div class="table-responsive">
					<table class="<?php if(empty($links)) echo 'DTable2'; ?> table">
						<thead>
							<th>Date / Time</th>
							<th>Receipt No</th>
							<th>Date</th>
							<th>Student</th>
							<th>Cashier</th>
							<th>Payment Method</th>
							<th class="text-right">Total ($)</th>
						</thead>
						<tbody>
							<?php $i=0; foreach($result as $e) { $i++; ?>
							<tr>
								<td><?php echo $e['create_on']; ?></td>
								<td><?php echo $e['payment_no']; ?></td>
								<td><?php echo $e['date']; ?></td>
								<td>
									<?php
									
									if ( datalist_Table('tbl_users', 'is_delete', $e['student']) == 0 ){
										
										?><a href="<?php echo base_url('students/edit/'.$e['student']); ?>"><?php echo datalist_Table('tbl_users', 'fullname_en', $e['student']); ?></a><?php
										
									} else {
										
										echo datalist_Table('tbl_users', 'fullname_en', $e['student']);
										
									}
									?>
								</td>
								<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
								<td><?php echo empty($e['payment_method']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['payment_method']) ; ?></td>
								<td class="text-right"><?php echo number_format($e['total'], 2, '.', ','); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>