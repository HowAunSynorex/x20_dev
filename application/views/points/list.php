<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
			         
            <div class="row">
                <div class="col-md-8 mb-4">
                    
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <th style="width:20%">Date / Time</th>
                            <th>Details</th>
                            <th class="text-right" style="width:15%">Credit <?php if($thispage['type']=='ewallet') echo '($)';?></th>
                            <th class="text-right" style="width:15%">Debit <?php if($thispage['type']=='ewallet') echo '($)';?></th>
                            <th class="text-right" style="width:15%">Amount <?php if($thispage['type']=='ewallet') echo '($)';?></th>
                        </thead>
                        <tbody>
                            <?php 
							
							$i=0; 
							$creditAmt = 0;
							$debitAmt = 0;
							$total = 0;

                            if($result != null) {

                                foreach($result as $e) { 
								
									$creditAmt += $e['amount_1'];
									$debitAmt += $e['amount_0'];

                                    ?>
                                    <tr>
                                        <td><?php echo $e['create_on']; ?></td>
                                        <td>
											<?php 
											if( check_module('Points/Update') ) {
												?>
											<a href="javascript:;" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $e['id']; ?>"><?php 
											} 
											echo $e['title']; 
											
											if( check_module('Points/Update') ) {
												?></a><?php 
											} ?>
                                            <span class="d-block small text-muted">Remark: <?php echo empty($e['remark']) ? '-' : $e['remark']; ?></span>
                                            <span class="d-block small text-muted">Create By: <?php echo empty($e['create_by']) ? '-' : datalist_Table('tbl_admins', 'nickname', $e['create_by']) ; ?></span>
                                            <a target="_blank" href="<?php echo base_url('export/pdf_ewallet_each/'.$e['id']); ?>" class="small">Print</a>
                                        </td>
                                        <td class="text-right">
                                            <?php
                                            if ($thispage['type']=='ewallet') {
                                                echo number_format($e['amount_1'], 2, '.', ',');
                                            } else {
                                                echo $e['amount_1'];
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <?php
                                            if ($thispage['type']=='ewallet') {
                                                echo number_format($e['amount_0'], 2, '.', ',');
                                            } else {
                                                echo $e['amount_0'];
                                            }
                                            ?>
                                        </td> 
                                        <td class="text-right">
											<?php
											$total += ($e['amount_1']-$e['amount_0']);
											if ($thispage['type']=='ewallet') {
                                                echo number_format($total, 2, '.', ',');
                                            } else {
                                                echo $total;
                                            }
											?>
										</td>                                       
                                    </tr>
                                    <?php 
									$i++;
                                }

                            } else {

                                ?><?php 

                            } 
                            ?>
						</tbody>
                        <tfoot>
                            <th colspan="2">Total</th>
                            <th class="text-right" style="width:15%">
								<?php 
									if ($thispage['type']=='ewallet') {
										echo number_format($creditAmt, 2, '.', ',');
									} else {
										echo $creditAmt; 
									}
								?>
							</th>
                            <th class="text-right" style="width:15%">
								<?php 
									if ($thispage['type']=='ewallet') {
										echo number_format($debitAmt, 2, '.', ',');
									} else {
										echo $debitAmt; 
									}
								?>
							</th>
                            <th class="text-right" style="width:15%">
								<?php 
									if ($thispage['type']=='ewallet') {
										echo number_format(($creditAmt - $debitAmt), 2, '.', ',');
									} else {
										echo ($creditAmt - $debitAmt); 
									}
								?>
							</th>
                        </tfoot>
                    </table>
                    
                </div>

                <div class="col-md-4 mb-4">
                
                    <form method="post">
                    
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Student</label>
                            <div class="col-md-9">
                                <select onchange="select(this, '<?php echo $thispage['type'];?>');" class="form-control select2" id="student" name="student" required>
                                    <option value="">-</option>
                                    <?php foreach ($student as $e) { ?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if( $e['pid'] == $id ) echo 'selected'; ?>><?php echo $e['fullname_cn'].' '.$e['fullname_en'].' ('.$e['code'].')'; ?></option>
                                    <?php } ?>
                                </select>
                                <?php if( empty($id) ) { ?>
                                <div class="alert alert-warning mt-2">Please select a student</div>
                                <?php } ?>
                            </div>
                        </div>
						
						<?php 
						if( !empty($id) ) { 
							if( check_module('Points/Create') ) { ?>
						
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Credit</label>
                            <div class="col-md-9">
								<?php if($thispage['type']=='ewallet') { ?>
									<div class="input-group">
										<div class="input-group-prepend">	
											<span class="input-group-text" style="font-size: 14px;">$</span>
										</div>
										<input type="number" class="form-control" name="amount_1" step="0.01">
									</div>
								<?php } else { ?>
										<input type="number" class="form-control" name="amount_1" step="0.01" >
								<?php }	?>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Debit</label>
                            <div class="col-md-9">
								<?php if($thispage['type']=='ewallet') { ?>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" style="font-size: 14px;">$</span>
										</div>
										<input type="number" class="form-control" name="amount_0" step="0.01">
									</div>
								<?php } else { ?>
									<input type="number" class="form-control" name="amount_0" step="0.01">
								<?php } ?>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>
                        
						<div class="form-group row">
							<div class="offset-md-3 col-md-9">
								<button class="btn btn-primary" type="submit" name="save">Save</button>
						
								<?php /*if ($thispage['type'] == 'ewallet' && isset($id)) { ?>
									<a class="btn btn-info" href="<?php echo base_url('export/pdf_ewallet/'.$id); ?>" target="_blank">Print <i class="fa fa-fw fa-print"></i></a>
								<?php }*/ ?>
								
							</div>
						</div>
						<?php 
							}
						} ?>

                    </form>
                    
                </div>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Point</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
			
				<input type="hidden" name="id">
                
				<div class="form-group">
					<label>Credit</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" style="font-size: 14px;">$</span>
						</div>
						<input type="number" class="form-control" name="amount_1" step="0.01">
						<input type="hidden" class="form-control" name="id">
					</div>
				</div>
				
				<div class="form-group">
					<label>Debit</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" style="font-size: 14px;">$</span>
						</div>
						<input type="number" class="form-control" name="amount_0" step="0.01">
					</div>
				</div>
				
				<div class="form-group payment-sec d-none">
                    <label>Payment No</label>
					<p>23123</p>
				</div>
				
				<div class="form-group">
					<label>Reason</label>
					<textarea class="form-control" name="title" rows="4"></textarea>
				</div>
				
				<div class="form-group">
					<label>Remark</label>
					<textarea class="form-control" name="remark" rows="4"></textarea>
				</div>
				
            </div>
            <div class="modal-footer">
				<?php if( check_module('Points/Delete') ) { ?>
					<input type="hidden" name="id">
					<a onclick="del_ask('<?php echo $thispage['type']; ?>')" name="delete" class="btn btn-link text-danger px-0 mr-auto">Delete</a>
				<?php } ?>
                <button type="submit" name="edit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
