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
			
            <div class="table-responsive">
                <table class="DTable table table-hover">
                    <thead>
                        <th style="width: 10%">No</th>
                        <th>Title</th>
                        <!--<th style="width: 10%">Quantity</th>-->
                        <th class="text-right" style="width: 15%">Total ($)</th>
                    </thead>
                    <tbody>
                        
                        <?php 
						
						$i = 0;
						$grand_total = 0;
						
						foreach($school as $e) {
							
							$i++;
							
							// $quantity = 0;
							$total = 0;
							
							foreach($students as $e2) {
			
								if($e2['school'] == $e['pid']) {
									
									$query = $this->log_payment_model->list2([
									
										'user' => $e2['pid'],
										'is_delete' => 0,
									]);
									
									foreach($query as $e3) {
										
										// $quantity += $e3['qty'];
										$total += $e3['price_amount'];
										$grand_total += $e3['price_amount'];
										
									}
								}
								
							}
						
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['title']; ?></td>
								<!--<td><?php echo $quantity; ?></td>-->
								<td class="text-right"><?php echo number_format($total, 2, '.', ','); ?></td>
							</tr>
							<?php 
						} 
						
						?>
                        
                    </tbody>
					<tfoot>
                        <tr class="table-success">
                            <th class="text-right" colspan="2">Total</th>
                            <th class="text-right"><?php echo number_format($grand_total, 2, '.', ','); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>