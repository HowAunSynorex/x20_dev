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
					<?php echo $_GET['year'] ?>年 人数表
				</div>
				<div class="card-body p-0">
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
							$primarytotal = 0;
							$primaryaveragetotal = [];
							
							foreach($form as $e) {
								$summary = [];
								// Tan Jing Suan
								$primarytotal = 0;
								if(in_array($e['title'], $small)) {
									?>
									<tr>
										<td><?php echo $e['title']; ?></td>
										<?php for($i=1; $i<=12; $i++) { ?>
											<td>
											<?php
											$query = $this->db->query('
												
												SELECT COUNT(*) AS total FROM tbl_users
												WHERE is_delete = 0
												AND type = "student"
												AND form = "'.$e['pid'].'"
												AND MONTH(date_join) = "'.$i.'"
												AND YEAR(date_join) = "'.$_GET['year'].'"
												
											')->result_array();
											// Tan Jing Suan
											// echo $query[0]['total'];
											// $summary[] = $query[0]['total'];
											// $small_total[$i] += $query[0]['total'];
											// $total[$i] += $query[0]['total'];
											$primarytotal += $query[0]['total'];
											echo $primarytotal;
											$summary[] = $primarytotal;
											$small_total[$i] += $primarytotal;
											$total[$i] += $primarytotal;
											?>
											</td>
										<?php } ?>
										<?php 
											$primaryaveragetotal[] = round(array_sum($summary)/count($summary));
										?>
										<td><?php echo round(array_sum($summary)/count($summary)); ?></td>
										<td><?php echo min($summary); ?></td>
										<td><?php echo max($summary); ?></td>
									</tr>
									<?php
								}
								
							}
							?>
							<tr class="table-warning font-weight-bold">
								<td>TOTAL</td>
								<?php for($i=1; $i<=12; $i++) { ?>
									<td><?php echo $small_total[$i]; ?></td>
								<?php } ?>
								<!-- <td><?php echo round(array_sum($small_total)/count($small_total)); ?></td> -->
								<td><?php echo array_sum($primaryaveragetotal); ?></td>
								<td><?php echo min($small_total); ?></td>
								<td><?php echo max($small_total); ?></td>
							</tr>
							<?php
							// Tan Jing Suan
							$secondarytotal = 0;
							$secondaryaveragetotal = [];

							foreach($form as $e) {
								$summary = [];
								// Tan Jing Suan
								$secondarytotal = 0;
								if(in_array($e['title'], $big)) {
									?>
									<tr>
										<td><?php echo $e['title']; ?></td>
										<?php for($i=1; $i<=12; $i++) { ?>
											<td>
											<?php
											$query = $this->db->query('
												
												SELECT COUNT(*) AS total FROM tbl_users
												WHERE is_delete = 0
												AND type = "student"
												AND form = "'.$e['pid'].'"
												AND MONTH(date_join) = "'.$i.'"
												AND YEAR(date_join) = "'.$_GET['year'].'"
												
											')->result_array();
											// Tan Jing Suan
											// echo $query[0]['total'];
											// $summary[] = $query[0]['total'];
											// $big_total[$i] += $query[0]['total'];
											// $total[$i] += $query[0]['total'];
											$secondarytotal += $query[0]['total'];
											echo $secondarytotal;
											$summary[] = $secondarytotal;
											$big_total[$i] += $secondarytotal;
											$total[$i] += $secondarytotal;
											?>
											</td>
										<?php } ?>
										<?php 
											$secondaryaveragetotal[] = round(array_sum($summary)/count($summary));
										?>
										<td><?php echo round(array_sum($summary)/count($summary)); ?></td>
										<td><?php echo min($summary); ?></td>
										<td><?php echo max($summary); ?></td>
									</tr>
									<?php
								}
								
							}
							?>
							<tr class="table-warning font-weight-bold">
								<td>TOTAL</td>
								<?php for($i=1; $i<=12; $i++) { ?>
									<td><?php echo $big_total[$i]; ?></td>
								<?php } ?>
								<!-- <td><?php echo round(array_sum($big_total)/count($big_total)); ?></td> -->
								<td><?php echo array_sum($secondaryaveragetotal); ?></td>
								<td><?php echo min($big_total); ?></td>
								<td><?php echo max($big_total); ?></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<tr class="table-primary font-weight-bold">
									<td>中小总数</td>
									<?php for($i=1; $i<=12; $i++) { ?>
										<td><?php echo $total[$i]; ?></td>
									<?php } ?>
									<!-- <td><?php echo round(array_sum($total)/count($total)); ?></td> -->
									<td><?php echo array_sum($primaryaveragetotal) + array_sum($secondaryaveragetotal); ?></td>
									<td><?php echo min($total); ?></td>
									<td><?php echo max($total); ?></td>
								</tr>
							</tr>
						</tfoot>
					</table>
				</div>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>