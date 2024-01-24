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
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="end_date" value="<?php echo $_GET['end_date']; ?>">
                            </div>
                        </div>
						
						
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Form</label>
							<div class="col-md-9">
								<select class="form-control select2" name="form[]" multiple>
									<?php
									foreach($form as $e) {
										?>
										<option value="<?php echo $e['pid']; ?>" <?php echo (isset($_GET['form']) ? (in_array($e['pid'], $_GET['form']) ? "selected" : "") : ""); ?>><?php echo $e['title']; ?></option>
										<?php
									} ?>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="remark" value="<?php if(isset($_GET['remark'])) echo $_GET['remark']; ?>">
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

            <div class="table-responsive">
                <table class="DTable2 table table-hover">
                    <thead>
                        <th>No</th>
                        <th>Date / Time</th>
                        <th>Payment No</th>
                        <!--<th>Payment Date</th>-->
                        <th>Form</th>
                        <th>Code</th>
                        <th>Student</th>
                        <th>Cashier</th>
                        <th>Remark</th>
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
								<td><a href="<?php echo base_url('payment/edit/'.$e['pid']); ?>"><?php echo $e['payment_no']; ?></a></td>
								
								<!--<td><?php echo $e['date']; ?></td>-->
								<td><?php echo $e['form_title']; ?></td>
								<td><?php echo $e['student_code']; ?></td>
								<td><a href="<?php echo base_url('students/edit/'.$e['student']); ?>"><?php echo $e['student_fullname_en']; ?></a></td>
								<td><?php echo $e['admin_nickname']; ?></td>
								<td><?php echo empty($e['remark']) ? '-' : $e['remark']; ?></td>
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
									<!-- Tan Jing Suan -->
									<!-- <th class="text-right" colspan="9"><?php echo $e['title']; ?></th> -->
									<th class="text-right" colspan="10"><?php echo $e['title']; ?></th>
									<th class="text-right"><?php echo number_format($total_each[ $e['pid'] ], 2, '.', ','); ?></th>
								</tr>
								<?php 
							} 
						?>
						<?php $total_year_months = group_by('payment_year_month', $result); ?>
						<?php foreach($total_year_months as $k => $v) { 
							$total_year_month = array_sum(array_column($v, 'total'));
						?>
							<tr>
								<!-- Tan Jing Suan -->
								<!-- <th class="text-right" colspan="9">Total [<?php echo $k; ?>]</th> -->
								<th class="text-right" colspan="10">Total [<?php echo $k; ?>]</th>
								<th class="text-right"><?php echo number_format($total_year_month, 2, '.', ','); ?></th>
							</tr>
						<?php } ?>
                        <tr class="table-success">
							<!-- Tan Jing Suan -->
                            <!-- <th class="text-right" colspan="9">Total</th> -->
                            <th class="text-right" colspan="10">Total</th>
                            <th class="text-right"><?php echo number_format($total, 2, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>