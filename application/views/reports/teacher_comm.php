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
                    <div class="col-md-7">
                        <div class="form-group row">
							<label class="col-form-label col-md-3">Branch</label>
							<div class="col-md-9">
								<select class="form-control select2" name="branch[]" multiple>
									<?php
									foreach($branch as $b) {
										?>
										<option value="<?php echo $b['pid']; ?>" <?php echo (isset($_GET['branch']) ? (in_array($b['pid'], $_GET['branch']) ? "selected" : "") : ""); ?>><?php echo $b['title']; ?></option>
										<?php
									} ?>
								</select>
							</div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Month</label>
                            <div class="col-md-9">
                                <input type="month" class="form-control" name="month" value="<?php echo $_GET['month']; ?>">
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
						Teacher
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
						<a href="<?php echo base_url('reports/teacher_comm?sort='.$sort); ?>" class="float-right sort-link"><i class="fa fa-fw fa-sort-alpha-<?php if(isset($_GET['sort'])) { if($_GET['sort'] == 'asc') { echo 'down'; } else { echo 'up'; } } else { echo 'down'; } ?>"></i></a>
					</th>
                    <th>Branch</th>
                    <!--<th class="text-right">Total Commission ($)</th>-->
                </thead>
                <tbody>
				
					<?php 
					
					$i=0;
					$total = 0;
					
					foreach($teachers as $e) {
						$i++;
						?>
						<tr>
							<td><?php echo $i; ?></td>
							<td>
								<a href="<?php echo base_url('reports/teacher_comm_det?teacher='.$e['pid'].'&month='.$_GET['month']); ?>"><?php echo ($e['fullname_cn'] != '')?$e['fullname_cn']:$e['fullname_en']; ?></a>
							</td>
							<td><?php echo datalist_Table('tbl_branches', 'title', $e['branch']); ?></td>
							<!--<td class="text-right">
								<?php
								/* $sql = '
								
									SELECT SUM(l.price_amount) AS total FROM log_payment l
									INNER JOIN tbl_classes c
									ON l.class = c.pid
									AND l.is_delete = 0
									AND c.teacher = "'.$e['pid'].'"
									AND l.period = "'.$_GET['month'].'"
								
								';
								$query = $this->db->query($sql)->result_array();
								$each = 0;
								if(count($query) == 1) {
									$each += $query[0]['total'];
								}
								$total += $each;
								echo number_format ($each, 2, '.', ',');
								*/
								?>
							</td>-->
						</tr>
						<?php
					}
					
					if($i == 0) echo '<tr><td colspan="100%" class="text-center">No result found</td></th>';
					?>
					
                </tbody>
                <!--<tfoot>
					<tr>
						<th colspan="3" class="text-right">Total Commission</th>
						<th class="text-right"><?php //echo number_format($total, 2, '.', ','); ?></th>
					</tr>
                </tfoot>-->
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>