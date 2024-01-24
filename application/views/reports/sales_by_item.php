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
			
            <div class="table-responsive">
                <table class="DTable table table-hover">
                    <thead>
                        <th style="width: 10%">No</th>
                        <th>Title</th>
                        <th style="width: 10%">Quantity</th>
                        <th class="text-right" style="width: 15%">Total ($)</th>
                    </thead>
                    <tbody>
                        
                        <?php 
						
						$i = 0;
						$grand_total = 0;
						
						foreach($result as $e) {
							$i++;
							$grand_total += $e['total'];
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><a href="<?php echo base_url('items/edit/'.$e['item']); ?>"><?php echo $e['item_title']; ?></a></td>
								<td><?php echo $e['qty']; ?></td>
								<td class="text-right"><?php echo number_format($e['total'], 2, '.', ','); ?></td>
							</tr>
							<?php 
						}
						?>
                        
                    </tbody>
					<tfoot>
                        <tr class="table-success">
                            <th class="text-right" colspan="3">Total</th>
                            <th class="text-right"><?php echo number_format($grand_total, 2, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>