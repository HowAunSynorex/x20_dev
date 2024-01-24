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
                        <th>Admin</th>
                        <th class="text-right" style="width: 15%">Total ($)</th>
                    </thead>
                    <tbody>
                        
                        <?php 
						
						$i = 0;
						$grand_total = 0;
						
						foreach($admins as $e) {
							
							$i++;
							
							$sql = '
							
								SELECT * FROM log_payment
								WHERE is_delete = 0
								AND create_by = "' . $e['admin'] . '"
								AND payment IN (
									SELECT pid FROM tbl_payment
									WHERE is_delete = 0
									AND branch = "' . branch_now('pid') . '"
								)
							
							';
							
							$query = $this->db->query($sql)->result_array();
							
							$total = 0;
							
							foreach($query as $e2) {
								
								$total += $e2['price_amount'];
								$grand_total += $e2['price_amount'];
								
							}
						
							?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><a href="<?php echo base_url('admins/edit/'.$e['id']); ?>"><?php echo datalist_Table('tbl_admins', 'nickname', $e['admin']); ?></a></td>
								<td class="text-right"><?php echo number_format($total, 2, '.', ','); ?></td>
							</tr>
							<?php 
						} 
						
						// print_r($total_each);
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