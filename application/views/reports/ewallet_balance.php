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

            <form method="get" class="mb-3">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Start Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="start_date" value="<?php if(isset($_GET['start_date'])) { echo $_GET['start_date']; } else {
                                    echo date('Y-m-d'); } ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">End Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="end_date" value="<?php if(isset($_GET['end_date'])) { echo $_GET['end_date']; } else {
                                    echo date('Y-m-d'); } ?>">
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
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Student</th>
                    <th>Credit ($)</th>
                    <th>Debit ($)</th>
                    <th>Balance ($)</th>
                </thead>
                <tbody>
                    
                    <?php 
					foreach($result as $e) { $total[$e['user']] = 0; }
					$i=0; foreach($result as $e) {
						if(datalist_Table('tbl_users', 'branch', $e['user']) == branch_now('pid')) { $i++;
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo (datalist_Table('tbl_users', 'fullname_cn', $e['user']) !='')?datalist_Table('tbl_users', 'fullname_cn', $e['user']):datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
								<td><?php echo number_format($e['amount_1'], 2, '.', ','); ?></td>
								<td><?php echo number_format($e['amount_0'], 2, '.', ','); ?></td>
								<td><?php $total[$e['user']] += ($e['amount_1']-$e['amount_0']); echo number_format($total[$e['user']], 2, '.', ',');?>
							</tr>
							<?php
						}
					} ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>