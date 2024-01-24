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
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="start_date" value="<?php echo $_GET['start_date']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="end_date" value="<?php echo $_GET['end_date']; ?>">
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
            </form>
            
            <table class="table-hover table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:30%">
						Reference Code
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
						<a href="<?php echo base_url('reports/sales_by_ref_code?sort='.$sort); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php if(isset($_GET['sort'])) { if($_GET['sort'] == 'asc') { echo 'down'; } else { echo 'up'; } } else { echo 'down'; } ?>"></i></a>
					</th>
                    <th>Student</th>
                    <th style="width:25%">Class</th>
                    <th class="text-right">Fees ($)</th>
                </thead>
                <tbody>
				
					<?php 
					
					$i=0;
					$total = 0;
					
					foreach($ref_code as $e) {
						
						if(!empty($e['ref_code'])) {
							
							$i++;
							
							$student_query = $this->tbl_users_model->list('student', branch_now('pid'), [
								'active' => 1,
								'ref_code'	=> $e['ref_code']
							]);
							
							$students = [];
							
							foreach($student_query as $sq) {
								$classes = $this->log_join_model->list('join_class', branch_now('pid'), [
									'user'		=> $sq['pid'],
									'active'	=> 1
								]);
								
								foreach($classes as $c) {
									$students[$sq['pid']]['class'][] = [
										'pid' 		=> $c['class'],
										'title' 	=> datalist_Table('tbl_classes', 'title', $c['class']),
										'amount' 	=> datalist_Table('tbl_classes', 'fee', $c['class']),
									];
								}
								
							}
							
							$class_each_qty = 0;
							$class_each_fee = 0;
							
							foreach($students as $s) {
								$class_each_qty += count($s['class']);
								foreach($s['class'] as $c) {
									$class_each_fee += $c['amount'];
									$total += $c['amount'];
								}
							}
														
							?>
							<tr class="table-info main-tr" data-toggle="collapse" data-target="#collapse<?php echo $e['ref_code']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $e['ref_code']; ?>" style="cursor: pointer;">
								<td><?php echo $i; ?></td>
								<td><?php echo $e['ref_code']; ?></td>
								<td>x <?php echo count($students); ?></td>
								<td>x <?php echo $class_each_qty; ?></td>
								<td class="text-right"><?php echo number_format($class_each_fee, 2, '.', ','); ?></td>
							</tr>
							<?php
							
							foreach($students as $k => $s) {
								?>
							<tr class="collapse p-0 table-light collapse<?php echo $e['ref_code']; ?>" id="collapse<?php echo $e['ref_code']; ?>" data-toggle="collapse" data-target="#collapse<?php echo $k.$e['ref_code']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $k.$e['ref_code']; ?>">
									<td></td>
									<td></td>
									<td><?php echo datalist_Table('tbl_users', 'fullname_en', $k); ?></td>
									<td>x <?php echo count($s['class']); ?></td>
									<td class="text-right">
										<?php
										$sub_amount = 0;
										foreach($s['class'] as $c) {
											$sub_amount += $c['amount'];
										}
										echo number_format($sub_amount, 2, '.', ',');
										?>
									</td>
								</tr>
								<?php
								foreach($s['class'] as $c) {
									?>
									<tr class="collapse p-0 table-light collapse<?php echo $k.$e['ref_code']; ?>" id="collapse<?php echo $k.$e['ref_code']; ?>">
										<td></td>
										<td></td>
										<td></td>
										<td><?php echo $c['title']; ?></td>
										<td class="text-right"><?php echo number_format($c['amount'], 2, '.', ','); ?></td>
									</tr>
									<?php
								}
								
							}
							
						}
					
					}
					
					if($i == 0) echo '<tr><td colspan="5" class="text-center">No result found</td></th>';
					?>
					
                </tbody>
                <tfoot>
					<tr>
						<th colspan="4" class="text-right">Total Fees</th>
						<th class="text-right"><?php echo number_format($total, 2, '.', ','); ?></th>
					</tr>
                </tfoot>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>