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
                <div class="col-6 my-auto text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
							<?php
							
							if( check_module('Payment/Modules/Print') ) {
								
								?><a class="dropdown-item" href="<?php echo base_url('export/pdf_export/'.$result['pid']); ?>" target="_blank">Print <i class="fa fa-fw fa-external-link-square-alt float-right mt-1"></i></a><?php
								
							}
							
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
			<form method="post" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Student</label>
                            <input type="hidden" name="payment" value="<?php echo $result['pid']; ?>">
							<input type="hidden" name="student" value="<?php echo $result['student']; ?>">
                            <div class="col-md-9">
                                <p class="form-control-plaintext">
									<?php
									if(datalist_Table('tbl_users', 'is_delete', $result['student']) == 0) {
										?>
										<a href="<?php echo base_url('students/edit/'.$result['student']); ?>">
											<?php echo datalist_Table('tbl_users', 'fullname_en', $result['student']); ?>
										</a>
										<?php
									} else {
										echo datalist_Table('tbl_users', 'fullname_en', $result['student']);
									}
									?>
								</p>
                            </div>
                        </div>

                    </div>
                        
                    <div class="col-md-6">

                        <!--<div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status" required>
                                    <?php foreach (datalist('payment_status') as $k => $v) { ?>
                                    <option value="<?php echo $k; ?>" <?php if( $k == $result['status'] ) echo 'selected'; ?>><?php echo $v['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>-->

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Receipt No</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="payment_no" value="<?php echo $result['payment_no']; ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Payment Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php echo $result['date']; ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">    
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                    </div>
					<div class="col-md-6">
					
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Payment Method</label>
                            <div class="col-md-9">
								<select class="form-control select2" name="payment_method">
									<?php foreach ($payment_now as $e) { ?>
										<option <?php if($result['payment_method'] == $e['pid']) echo 'selected'; ?> value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
                                    <?php } ?>
									<?php foreach ($payment_all as $e) { ?>
										<option <?php if($result['payment_method'] == $e['secondary']) echo 'selected'; ?> value="<?php echo $e['secondary']; ?>"><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Receipt</label>
							<div class="col-md-9">
								<p class="form-control-plaintext">
									<input type="hidden" name="receipt" value="<?php echo $result['receipt'];?>">
									<a href="<?php 
										if(!empty($this->tbl_uploads_model->view($result['receipt']))) {
											echo $this->tbl_uploads_model->view($result['receipt'])[0]['file_source'];
										} else {
											echo 'https://cdn.synorexcloud.com/assets/images/blank/1x1.jpg';
										}?>" target="_blank">
										<img src="<?php 
										if(!empty($this->tbl_uploads_model->view($result['receipt']))) {
											echo $this->tbl_uploads_model->view($result['receipt'])[0]['file_source'];
										} else {
											echo 'https://cdn.synorexcloud.com/assets/images/blank/1x1.jpg';
										}?>" class="border rounded bg-white" style="height: 100px;">
										<input type="file" name="image" class="mt-2 form-control">
									</a>
								</p>
							</div>
						</div>
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
								<select class="form-control select2" name="status">
									<?php foreach (datalist('payment_status') as $k => $v) { ?>
										<?php if ($k == 'pending' || $k == 'paid') { ?>
											<option <?php if($result['status'] == $k) echo 'selected'; ?> value="<?php echo $k; ?>"><?php echo $v['title']; ?></option>
										<?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
						
					</div>
                </div>

                <hr class="mb-4">

                <table class="table table-bordered mt-3 mb-4">
                    <thead>
                        <th>Item</th>
                        <th class="text-right" style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 15%;">Unit Price ($)</th>
                        <th class="text-right" style="width: 15%;">Amount ($)</th>
                    </thead>
                    <tbody>
						<input type="hidden" name="removedList">
						<?php
						if(isset($result2)) {
							foreach($result2 as $e2) { 
								$i = time().rand(11, 99);
								?>
								
								<tr class="item<?php echo $i; ?>">
									<td>
										<input type="hidden" name="old[<?php echo $i; ?>][period]" value="<?php echo $e2['period']; ?>">
										<input type="hidden" name="old[<?php echo $i; ?>][type]" value="<?php if ($e2['movement'] != null) {echo 'item';} else {echo 'class';}?>">
										<input type="hidden" name="old[<?php echo $i; ?>][item]" value="<?php echo $e2['item']; ?>">
										<input type="hidden" name="old[<?php echo $i; ?>][log_id]" value="<?php echo $e2['id']; ?>">
										<input type="hidden" name="old[<?php echo $i; ?>][inventory_id]" value="<?php echo $e2['movement']; ?>">
										<input type="hidden" name="old[<?php echo $i; ?>][log_inventory_id]" value="<?php echo $e2['movement_log']; ?>">
										<input type="text" onclick="this.select();" class="form-control" name="old[<?php echo $i; ?>][title]" value="<?php echo $e2['title'];?>" readonly>
										<textarea class="form-control mt-2" onclick="this.select();" name="old[<?php echo $i; ?>][remark]" rows="2"><?php echo $e2['remark']; ?></textarea>
										<!--<div class="mt-2">
											<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
										</div>-->
									</td>
									<td><input type="number" onclick="this.select();" name="old[<?php echo $i; ?>][qty]" class="input-remove_arrow form-control text-right" value="<?php echo $e2['qty']; ?>" required></td>
									<td><input type="number" step="0.01" onclick="this.select();" name="old[<?php echo $i; ?>][price_unit]" class="input-remove_arrow form-control text-right" value="<?php echo number_format($e2['price_unit'], 2, '.', ''); ?>" required></td>
									<td><input type="number" step="0.01" onclick="this.select();" name="old[<?php echo $i; ?>][amount]" class="input-remove_arrow form-control text-right" value="<?php echo number_format($e2['price_amount'], 2, '.', ''); ?>" readonly></td>
								</tr>
							<?php }
						}
					
						?>
					</tbody>
                    <!--<tfoot>
                        <td colspan="4" class="text-center">
                            <a href="javascript:;" data-toggle="modal" data-target="#modal-add"><i class="fa fa-fw fa-plus-circle"></i> Add New</a>
                        </td>
                    </tfoot>-->
                </table>

                <div class="row">
                    <div class="col-md-6 offset-md-6">

                        <div class="form-group row">
                            <input type="hidden" name="subtotal" />
                            <label class="form-control-label col-md-4">Subtotal</label>
                            <label id="subtotal" class="form-control-label col-md-4 offset-md-4 text-right" data-label="subtotal">0.00</label>
                        </div>

                        <div class="form-group row">
                            <label class="form-control-label col-md-4">Discount</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" step="0.01" onclick="this.select();" class="input-remove_arrow form-control" name="discount" placeholder="0" value="<?php echo $result['discount']; ?>">
                                    <select class="form-control" name="discount_type" required>
                                        <option value="%" <?php if($result['discount_type'] == '%') echo 'selected'; ?>>%</option>
                                        <option value="$" <?php if($result['discount_type'] == '$') echo 'selected'; ?>>$</option>
                                    </select>
                                </div>
                            </div>
                            <label class="form-control-label col-md-4 text-right" data-label="discount">0.00</label>
                        </div>

                        <div class="form-group row">
                            <label class="form-control-label col-md-4">Material Fee</label>
                            <div class="col-md-4">
                                <input readonly type="number" step="0.01" onclick="this.select();" class="input-remove_arrow form-control" name="material_fee" placeholder="0" value="<?php if($result['material_fee'] > 0) echo $result['material_fee']; ?>">
                            </div>
                            <label class="form-control-label col-md-4 text-right" data-label="material_fee"><?php echo ($result['material_fee'] > 0) ? number_format($result['material_fee'], 2, '.', ',') : '0.00'; ?></label>
                        </div>

						<? 
						$student_data = $this->tbl_users_model->view($id)[0];
						if ($student_data['childcare_title'] != ""): ?>
							<div class="form-group row">
								<label class="form-control-label col-md-4">Childcare Fee</label>
								<div class="col-md-4">
									<input readonly type="number" step="0.01" onclick="this.select();"
										   class="input-remove_arrow form-control childcare_fee_text" name="childcare_fee"
										   placeholder="0"
										   value="<?=$student_data['childcare_price']?>">
								</div>
								<label class="form-control-label col-md-4 text-right"
									   data-label="childcare_fee"><?=number_format($student_data['childcare_price'], 2, '.', ',')?></label>
							</div>
						<? else:?>
							<input  type="hidden" class="childcare_fee_text" name="childcare_fee" value="0">
						<? endif; ?>

						<? if ($student_data['transport_title'] != ""): ?>
							<div class="form-group row">
								<label class="form-control-label col-md-4">Transport Fee</label>
								<div class="col-md-4">
									<input readonly type="number" step="0.01" onclick="this.select();"
										   class="input-remove_arrow form-control transport_fee_text" name="transport_fee"
										   placeholder="0"
										   value="<?=$student_data['transport_price']?>">
								</div>
								<label class="form-control-label col-md-4 text-right"
									   data-label="transport_fee"><?=number_format($student_data['transport_price'], 2, '.', ',')?></label>
							</div>
						<? else:?>
							<input  type="hidden" class="transport_fee_text" name="transport_fee" value="0">
						<? endif; ?>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <input type="text" id="adjust-title" onclick="this.select();" class="form-control" name="adjust_label" placeholder="Adjustment" value="<?php echo $result['adjust_label']; ?>" />
                            </div>
                            <div class="col-md-4">
                                <input readonly type="number" step="0.01" onclick="this.select();" class="input-remove_arrow form-control" name="adjust" placeholder="0" value="<?php echo $result['adjust']; ?>">
                            </div>
                            <label class="form-control-label col-md-4 text-right" data-label="adjust">0.00</label>
                        </div>

                        <div class="form-group mb-0">
                            <hr class="my-4" />
                        </div>

                        <div class="form-group row">
							<input type="hidden" name="tax-percentage" value="<?php echo branch_now('tax'); ?>" />
                            <input type="hidden" name="tax" />
                            <label class="form-control-label col-md-4">Tax</label>
                            <label class="form-control-label col-md-4 offset-md-4 text-right" data-label="tax">0.00</label>
                        </div>

                        <div class="form-group row">
                            <input type="hidden" id="total" name="total">
							<input type="hidden" id="price-amount" name="price-amount">
                            <label class="form-control-label col-md-4 font-weight-bold">Total</label>
                            <label class="form-control-label col-md-4 offset-md-4 text-right font-weight-bold" data-label="total">0.00</label>
                        </div>

                        <div class="form-group row">
                            <label class="form-control-label col-md-4">Receive </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="input-remove_arrow form-control receive_txt" name="receive" value="<?php echo $result['receive']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="form-control-label col-md-4">Change </label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="input-remove_arrow form-control change_txt" name="change" value="<?php echo $result['receive']-$result['total']; ?>" readonly="">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-12 text-right">

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
								<?php 
									if( check_module('Payment/Delete') ) {
										
										?><button type="button" class="btn btn-danger" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</a><?php
										
									}
								?>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<div class="modal fade" id="modal-add">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			
				<div class="alert alert-warning mt-2 d-none">Please select and enter the required fields!</div>
			   
				<div class="form-group">
					<label class="text-danger d-block">Type</label>
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" id="radio-type-1" name="type" class="custom-control-input" value="class" required>
						<label class="custom-control-label" for="radio-type-1">Class</label>
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" id="radio-type-2" name="type" class="custom-control-input" value="item" required>
						<label class="custom-control-label" for="radio-type-2">Item</label>
					</div>
				</div>
				
				<form method="post" class="d-none section-dynamic section-class">
					<div class="form-group">
						<label class="text-danger">Class</label>
						<input type="hidden" name="user" value="<?php echo $id; ?>">
						<input type="hidden"name="title" >
						<input type="hidden"name="id" >
						<select class="form-control select2" name="class" data-required="true">
							<option value="">-</option>';
							<?php foreach ($classes as $e) {
								echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
							};?>
						</select>
					</div>
					
					 <div class="form-group">
						<label class="text-danger">Period</label>
						<input type="month" class="form-control" name="period" data-required="true" value="<?php echo date('Y-m'); ?>">
					</div>
					
					<div class="form-group">
						<label>Qty</label>
						<input type="text" onclick="this.select();" class="form-control" name="class-qty" data-required="true" value="1">
					</div>

					<div class="form-group">
						<label>Unit Price</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" style="font-size: 14px;">$</span>
							</div>
							<input type="amount" step="0.01" class="form-control" name="class-price_unit">
						</div>
					</div>

					<div class="form-group">
						<label>Amount</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" style="font-size: 14px;">$</span>
							</div>
							<input type="amount" step="0.01" class="form-control" name="class-amount" readonly>
						</div>
					</div>

					<div class="form-group">
						<label>Remark</label>
						<textarea class="form-control" onclick="this.select();" name="class-remark" rows="4"></textarea>
					</div>
					
				</form>

				<form method="post" class="d-none section-dynamic section-item">
					<div class="form-group">
						<label class="d-flex justify-content-between align-items-end">
							<span class="text-danger">Item</span>
							<a href="javascript:;" onclick="show_category()" class="small">Filter by category</a>
						</label>
						<input type="hidden" name="user" value="<?php echo $id; ?>">
						<input type="hidden"name="title" >
						<input type="hidden"name="id" >
						<div class="category-sec mb-2 d-none">
							<hr>
							<div class="form-group mb-0">
								<label>Category</label>
								<select class="form-control select2 category">
									<?php foreach($item_cat as $e) { ?>
										<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>	
									<?php } ?>
								</select>
							</div>
							<hr>
						</div>
						<select class="form-control select2" name="item" data-required="true">
							<?php foreach($item_cat as $e) { ?>
								<optgroup label="<?php echo $e['title']; ?>">
									<?php
									$items= $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1, 'category' => $e['pid']]);
									foreach ($items as $e2) {
										?>
										<option value="<?php echo $e2['pid']; ?>"><?php echo $e2['title']; ?></option>
										<?php
									}
									?>
								</optgroup>
							<?php } ?>
						</select>
					</div>
					
					<div class="form-group">
						<label>Qty</label>
						<input type="text" class="form-control" onclick="this.select();" name="item-qty" data-required="true" value="1">
					</div>

					<div class="form-group">
						<label>Unit Price</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" style="font-size: 14px;">$</span>
							</div>
							<input type="amount" step="0.01" class="form-control" name="item-price_unit">
						</div>
					</div>

					<div class="form-group">
						<label>Amount</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" style="font-size: 14px;">$</span>
							</div>
							<input type="amount" step="0.01" class="form-control" name="item-amount" readonly>
						</div>
					</div>

					<div class="form-group">
						<label>Remark</label>
						<textarea class="form-control" name="item-remark" rows="4"></textarea>
					</div>

				</form>

			</div>

			<div class="modal-footer">
				<button type="button" name="add" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<script>
	<?php if( !check_module('Payment/Update') || branch_now('version') == 'receipt' ) { ?>
	var access_denied = true;
	<?php } ?>
</script>