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
		
			<!--<form method="get">
				<div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Student</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="student">
                                    <option value="">-</option>';
                                    <?php foreach ($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['student'])) {if($e['pid'] == $_GET['student']) echo 'selected';} ?>><?php echo $e['fullname_en']; ?></option>
                                    <?php };?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php if(isset($_GET['date'])) { echo $_GET['date']; } else { echo date('Y-m-d'); } ?>">
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-md-6">
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>-->

            <?php echo alert_get(); ?>
            
            <table class="table table table-hover">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:40%">Student</th>
                    <th>Total Unpaid Item</th>
                    <th class="text-right">Total Unpaid Amount ($)</th>
                    <th></th>
                </thead>
                <tbody>
                    
                    <?php
					
					// v1 by soon
					/*$i=0;
					if(isset($result)) {
						foreach($result as $e) { $i++; ?>
						<tr class="table-danger" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse<?php echo $i; ?>">
							<td><?php echo $i; ?></td>
							<td><a href="<?php echo base_url('students/edit/'.$e['user']); ?>"><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></a></td>
							<td>x
								<?php
								
								$total_unpaid = 0;
								$total_item = 0;
								foreach($this->log_join_model->list('unpaid_item', branch_now('pid'), ['user' => $e['user']]) as $e2) {
									$total_item += $e2['qty'];
									$total_unpaid += $e2['amount'];
								}
								
								echo $total_item;
								?>
							</td>
							<td class="text-right">
								<?php
								
								echo number_format($total_unpaid, 2, '.', ',');
								?>
							</td>
							<td class="font-weight-bold"><a href="<?php echo base_url('payment/add/'.$e['user']); ?>"><i class="fa fa-fw fa-file-invoice"></i> Make a Payment</a></td>
						</tr>
						<?php
						foreach($this->log_join_model->list('unpaid_item', branch_now('pid'), ['user' => $e['user']]) as $e3) {
							?>
							<tr class="collapse p-0 table-light" id="collapse<?php echo $i; ?>">
								<td></td>
								<td><?php echo datalist_Table('tbl_inventory', 'title', $e3['item']); ?></td>
								<td>x <?php echo $e3['qty']; ?></td>
								<td class="text-right"><?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e3['item']), 2, '.', ',') ; ?></td>
								<td></td>
							</tr>
							<?php
						}
						}
					}*/
					
					// v2 by steve
					$i=0; 
					
					foreach($result_std as $e) { 
					
						$std_unpaid_result = std_unpaid_result2($e['pid'], '', 'item');
						
						// total unpaid item
						if(count($std_unpaid_result['result']['item']) > 0) {
							
							$i++;
							
							// summary
							$total_i = array_sum(array_column($std_unpaid_result['result']['item'], 'qty'));
							$total_amount = array_sum(array_column($std_unpaid_result['result']['item'], 'amount'));
							$total_amount = 0;
							/* 
							foreach($std_unpaid_result['result']['item'] as $e2) {
								$total_i += $e2['qty'];
								$total_amount += $e2['amount'];
							} */
							
							?>
							<tr class="table-danger" data-toggle="collapse" data-target="#collapse<?php echo $e['pid']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $e['pid']; ?>">
								<td><?php echo $i; ?></td>
								<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo $e['fullname_en']; ?></a></td>
								<td>x <?php echo $total_i ?></td>
								<td class="text-right"><?php echo number_format($total_amount, 2, '.', ','); ?></td>
								<td class="font-weight-bold"><a href="<?php echo base_url('payment/add/'.$e['pid']); ?>"><i class="fa fa-fw fa-file-invoice"></i> Make a Payment</a></td>
							</tr>
							<?php
							
							// unpaid item
							if(isset($std_unpaid_result['result']['item'])) {
								
								foreach($std_unpaid_result['result']['item'] as $e2) {
									?>
									<tr class="collapse p-0 table-light" id="collapse<?php echo $e['pid']; ?>">
										<td></td>
										<td><?php echo $e2['title']; ?></td>
										<td>x <?php echo $e2['qty']; ?></td>
										<td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',') ; ?></td>
										<td>
											<a href="javascript:;" onclick="del_ask_item(<?php echo $e2['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
										</td>
									</tr>
									<?php
								}
								
							}
							
						}
					
					}
					
					if($i == 0) echo '<tr><td colspan="5" class="text-center">No result found</td></th>';
					?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>