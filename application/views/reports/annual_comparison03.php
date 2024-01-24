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
                            <label class="col-form-label col-md-3">Year</label>
                            <div class="col-md-9">
								<select class="form-control select2" name="year">
									<?php for($i=2000; $i<=date('Y'); $i++) { ?>
										<option value="<?php echo $i; ?>" <?php if(isset($_GET['year']) && $_GET['year'] == $i) echo 'selected'; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
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
            
			<div class="card">
				<div class="card-header">
					<?php echo $_GET['year'] ?>年 实业绩表
				</div>
				<div class="card-body p-0">
					<div class="table-responsive" >
						<table class="table-hover table-bordered table">
							<thead>
								<th>年级/月份(人数)</th>
								<?php for($i=1; $i<=12; $i++) { ?>
									<th><?php echo $i; ?></th>
								<?php } ?>
								<th>Average</th>
								<th>最低</th>
								<th>最高</th>
							</thead>
							<tbody>
								<?php
								
								$small = ["K1", "K2", "Y1", "Y2", "Y3", "Y4", "Y5", "Y6"];
								$big = ["F1", "F2", "F3", "F4", "F5"];
								
								$small_total = [];
								for($i=1; $i<=12; $i++) {
									$small_total[$i] = 0;
								}
								$big_total = [];
								for($i=1; $i<=12; $i++) {
									$big_total[$i] = 0;
								}
								$total = [];
								for($i=1; $i<=12; $i++) {
									$total[$i] = 0;
								}
								// Tan Jing Suan
								$primarytotal = 0.00;
								$primaryaveragetotal = [];
								
								foreach($form as $e) {
									$summary = [];
									if(in_array($e['title'], $small)) {
										?>
										<tr>
											<td><?php echo $e['title']; ?></td>
											<?php for($i=1; $i<=12; $i++) { ?>
												<td>
												<?php
												
												$query = $this->db->query('
													
													SELECT SUM(l.total) AS total FROM tbl_payment l
													INNER JOIN tbl_users u
													ON u.pid = l.student
													AND l.is_delete = 0
													AND u.is_delete = 0
													AND u.type = "student"
													AND u.form = "'.$e['pid'].'"
													AND MONTH(l.date) = "'.$i.'"
													AND YEAR(l.date) = "'.$_GET['year'].'"
													
												')->result_array();
												// Tan Jing Suan
												// echo number_format($query[0]['total'], 2, '.', ',');
												// $summary[] = $query[0]['total'];
												// $small_total[$i] += $query[0]['total'];
												// $total[$i] += $query[0]['total'];
												$primarytotal += $query[0]['total'];
												echo number_format($primarytotal, 2, '.', ',');
												$summary[] = $primarytotal;
												$small_total[$i] += $primarytotal;
												$total[$i] += $primarytotal;
												?>
												</td>
											<?php } ?>
											<?php 
												$primaryaveragetotal[] = round(array_sum($summary)/count($summary), 2);
											?>
											<td><?php echo number_format(array_sum($summary)/count($summary), 2, '.', ','); ?></td>
											<td><?php echo number_format(min($summary), 2, '.', ','); ?></td>
											<td><?php echo number_format(max($summary), 2, '.', ','); ?></td>
										</tr>
										<?php
									}
									
								}
								?>
								<tr class="table-warning font-weight-bold">
									<td>TOTAL</td>
									<?php for($i=1; $i<=12; $i++) { ?>
										<td><?php echo number_format($small_total[$i], 2, '.', ','); ?></td>
									<?php } ?>
									<!-- <td><?php echo number_format(array_sum($small_total)/count($small_total), 2, '.', ','); ?></td> -->
									<td><?php echo number_format(array_sum($primaryaveragetotal), 2, '.', ','); ?></td>
									<td><?php echo number_format(min($small_total), 2, '.', ','); ?></td>
									<td><?php echo number_format(max($small_total), 2, '.', ','); ?></td>
								</tr>
								<?php
								// Tan Jing Suan
								$secondarytotal = 0;
								$secondaryaveragetotal = [];

								foreach($form as $e) {
									$summary = [];
									if(in_array($e['title'], $big)) {
										?>
										<tr>
											<td><?php echo $e['title']; ?></td>
											<?php for($i=1; $i<=12; $i++) { ?>
												<td>
												<?php
												
												$query = $this->db->query('
													
													SELECT SUM(l.total) AS total FROM tbl_payment l
													INNER JOIN tbl_users u
													ON u.pid = l.student
													AND l.is_delete = 0
													AND u.is_delete = 0
													AND u.type = "student"
													AND u.form = "'.$e['pid'].'"
													AND MONTH(l.date) = "'.$i.'"
													AND YEAR(l.date) = "'.$_GET['year'].'"
													
												')->result_array();
												// Tan Jing Suan
												// echo number_format($query[0]['total'], 2, '.', ',');
												// $summary[] = $query[0]['total'];
												// $big_total[$i] += $query[0]['total'];
												// $total[$i] += $query[0]['total'];
												$secondarytotal += $query[0]['total'];
												echo number_format($secondarytotal, 2, '.', ',');
												$summary[] = $secondarytotal;
												$big_total[$i] += $secondarytotal;
												$total[$i] += $secondarytotal;
												?>
												</td>
											<?php } ?>
											<?php 
												$secondaryaveragetotal[] = round(array_sum($summary)/count($summary), 2);
											?>
											<td><?php echo number_format(array_sum($summary)/count($summary), 2, '.', ','); ?></td>
											<td><?php echo number_format(min($summary), 2, '.', ','); ?></td>
											<td><?php echo number_format(max($summary), 2, '.', ','); ?></td>
										</tr>
										<?php
									}
									
								}
								?>
								<tr class="table-warning font-weight-bold">
									<td>TOTAL</td>
									<?php for($i=1; $i<=12; $i++) { ?>
										<td><?php echo number_format($big_total[$i], 2, '.', ','); ?></td>
									<?php } ?>
									<!-- <td><?php echo number_format(array_sum($big_total)/count($big_total), 2, '.', ','); ?></td> -->
									<td><?php echo number_format(array_sum($secondaryaveragetotal), 2, '.', ','); ?></td>
									<td><?php echo number_format(min($big_total), 2, '.', ','); ?></td>
									<td><?php echo number_format(max($big_total), 2, '.', ','); ?></td>
								</tr>
								<tr class="table-primary font-weight-bold">
									<td>中小总数</td>
									<?php for($i=1; $i<=12; $i++) { ?>
										<td><?php echo number_format($total[$i], 2, '.', ','); ?></td>
									<?php } ?>
									<!-- <td><?php echo number_format(array_sum($total)/count($total), 2, '.', ','); ?></td> -->
									<td><?php echo number_format(array_sum($primaryaveragetotal) + array_sum($secondaryaveragetotal), 2, '.', ','); ?></td>
									<td><?php echo number_format(min($total), 2, '.', ','); ?></td>
									<td><?php echo number_format(max($total), 2, '.', ','); ?></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>