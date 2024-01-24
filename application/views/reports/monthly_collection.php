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

            <form id="search_form" method="get">
				<div id="draft_key"><?php echo isset($_GET['is_draft']) ? '<input type="hidden" name="is_draft" value="1" />' : '' ?></div>
                <div class="row">
                    <div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Month</label>
                            <div class="col-md-9">
                                <input type="month" class="form-control" name="month" value="<?php if(isset($_GET['month'])) { echo $_GET['month']; } else { echo date('Y-m'); } ?>">
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
			
			<form method="post">
				<div class="pb-2">
					<button name="pdf" type="submit" class="btn btn-primary">PDF</button>
				</div>
			</form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>No</th>
                        <th>Date / Time</th>
                        <th>Payment No</th>
                        <th>Date</th>
						<th>Form</th>
                        <th>Code</th>
                        <th>Student</th>
                        <th>Cashier</th>
                        <th>Payment Method</th>
                        <th class="text-right">Total ($)</th>
                    </thead>
                    <tbody>
                        
                        <?php 
						
						$total = 0;
						
						$i=0; foreach($result as $e) { $i++; 
						
							$total += $e['total'];
							
							if(isset($total_each[ $e['payment_method'] ])) {
								
								$total_each[ $e['payment_method'] ] += $e['total'];
								
							} else {
								
								$total_each[ $e['payment_method'] ] = $e['total'];

							}
							
						
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['create_on']; ?></td>
								<td><a href="<?php echo base_url('payment/edit/'.$e['pid']); ?>"><?php echo $e['payment_no']; ?>
								</a></td>
								<td><?php echo $e['date']; ?></td>
								<td><?php echo datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'form', $e['student'])); ?></td>
								<td><?php echo datalist_Table('tbl_users', 'code', $e['student']); ?></td>
								<td>
									<?php
									if(datalist_Table('tbl_users', 'is_delete', $e['student']) == 0) {
										?>
										<a href="<?php echo base_url('students/edit/'.$e['student']); ?>">
											<?php echo (datalist_Table('tbl_users', 'fullname_cn', $e['student']) !='' )?datalist_Table('tbl_users', 'fullname_cn', $e['student']):datalist_Table('tbl_users', 'fullname_en', $e['student']); ?>
										</a>
										<?php
									} else {
										echo (datalist_Table('tbl_users', 'fullname_cn', $e['student']) !='' )?datalist_Table('tbl_users', 'fullname_cn', $e['student']):datalist_Table('tbl_users', 'fullname_en', $e['student']);
									}
									?>
								</td>
								<td>
								<?php echo $e['create_by_nickname']; ?></td>
								<td><?php echo $e['payment_method_title'] ; ?></td>
								<td class="text-right"><?php echo number_format($e['total'], 2, '.', ','); ?></td>
							</tr>
							<?php 
						} 
						
						// print_r($total_each);
						?>
                        
                    </tbody>
					<tfoot>
						<?php 
						
						foreach($result_payment_method as $e) { 
						
							if(!isset($total_each[ $e['pid'] ])) $total_each[ $e['pid'] ] = 0;
							
							?>
							<tr>
								<th class="text-right" colspan="9"><?php echo $e['title']; ?></th>
								<th class="text-right"><?php echo number_format($total_each[ $e['pid'] ], 2, '.', ','); ?></th>
							</tr>
							<?php 
						} 
						?>
                        <tr class="table-success">
                            <th class="text-right" colspan="9">Total</th>
                            <th class="text-right"><?php echo number_format($total, 2, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>