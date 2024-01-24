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
            
            <form method="post" onsubmit="Loading(1)">

                <div class="row">
                    <div class="col-md-6">

                        <?php if(isset($_GET['by_class'])) { ?>
							<div class="form-group row">
								<label class="col-form-label col-md-3 text-danger">Class</label>
								<div class="col-md-9">
									<select class="form-control select2" onchange="window.location.href='<?php echo base_url('payment/add2'); ?>'+'?by_class&class='+this.value" name="class" required>
										<option value="">-</option>
										<?php foreach ($classes as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if( isset($_GET['class']) ) {if($_GET['class'] == $e['pid'] ) { echo 'selected'; } } ?>><?php echo $e['title']; ?></option>
										<?php } ?>
									</select>
									<?php if(!isset($_GET['class']) || empty($_GET['class'])) {?>
									<div class="alert alert-warning mt-2">Please select a class</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
						
						<?php
						if(isset($_GET['by_class']) && isset($_GET['class']) && !empty($_GET['class'])) {
							?>
							<div class="form-group row">
								<label class="col-form-label col-md-3 text-danger">Student</label>
								<div class="col-md-9">
									<select class="form-control select2" onchange="window.location.href='<?php echo base_url('payment/add2/'); ?>'+this.value+'?by_class&class=<?php echo $_GET['class']; ?>'" name="student" required>
										<option value="">-</option>
										<?php foreach ($student as $e) { ?>
										<option value="<?php echo $e['user']; ?>" <?php if( isset($id) ) {if($id == $e['user'] ) { echo 'selected'; } } ?>><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></option>
										<?php } ?>
									</select>
									<?php if(empty($id)) {?>
									<div class="alert alert-warning mt-2">Please select a student</div>
									<?php } ?>
								</div>
							</div>
							<?php
						} elseif(!isset($_GET['by_class'])) {
							?>
							<div class="form-group row">
								<label class="col-form-label col-md-3 text-danger">Student</label>
								<div class="col-md-9">
									<select class="form-control select2" onchange="window.location.href='<?php echo base_url('payment/add2/'); ?>'+this.value" name="student" required>
										<option value="">-</option>
										<?php foreach ($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if( isset($id) ) {if($id == $e['pid'] ) { echo 'selected'; } } ?>><?php echo $e['fullname_en']; ?></option>
										<?php } ?>
									</select>
									<?php if(empty($id)) {?>
									<div class="alert alert-warning mt-2">Please select a student</div>
									<?php } ?>
								</div>
							</div>
							<?php
						}
						?>

                    </div>
                    <div class="col-md-6">

                        <?php /*if(isset($id) && !empty($id)) {?>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status" required>
                                    <?php foreach (datalist('payment_status') as $k => $v) { ?>
                                    <option value="<?php echo $k; ?>"><?php echo $v['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php }*/ ?>

                    </div>
                </div>

                <?php if(isset($id) && !empty($id)) {?>
                    
                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Receipt No</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="payment_no" value="<?php echo new_receipt_no(); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Payment Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">    
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>

                    </div>
					<div class="col-md-6">
					
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Payment Method</label>
                            <div class="col-md-9">
								<select class="form-control select2" name="payment_method">
                                    <?php foreach ($payment_now as $e) { ?>
										<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
                                    <?php } ?>
									<?php foreach ($payment_all as $e) { ?>
										<option value="<?php echo $e['secondary']; ?>"><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
						
					</div>
                </div>

                <table class="table table-bordered mt-3 mb-4">
                    <thead>
                        <th>Item</th>
                        <th class="text-right" style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 15%;">Unit Price ($)</th>
                        <th class="text-right" style="width: 15%;">Amount ($)</th>
                    </thead>
                    <tbody>
						<?php
						
						$i = 0;
						
						$std_unpaid_result = std_unpaid_result($id);
						
						// print_r($std_unpaid_result); exit;
						
						if($std_unpaid_result['count'] > 0) {
							
							if(isset($std_unpaid_result['result']['class'])) {
								
								foreach($std_unpaid_result['result']['class'] as $e) {
									
									$i++;
									
									?>
									<tr class="item<?php echo $i; ?>">
										<td>
											<input type="hidden" name="item[<?php echo $i; ?>][type]" value="class"> 
											<input type="hidden" name="item[<?php echo $i; ?>][period]" value="<?php echo isset($e['period']) ? $e['period'] : ''; ?>"> 
											<input type="hidden" name="item[<?php echo $i; ?>][item]" value="<?php echo $e['class']; ?>"> 
											<input type="hidden" name="item[<?php echo $i; ?>][dis_amount]" value="<?php echo $e['discount']; ?>"> 
											<input type="text" onclick="this.select();" class="form-control" name="item[<?php echo $i; ?>][title]" value="<?php
												if(isset($e['period'])) {
													echo '[' . $e['period'] . '] ' . $e['title'];
												} else {
													echo $e['title'];
												}
											?>" readonly>
											<textarea class="form-control mt-2" onclick="this.select();" name="item[<?php echo $i; ?>][remark]" rows="4"></textarea>
											<div class="mt-2">
												<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
											</div>
										</td>
										<td><input type="number" onclick="this.select();" name="item[<?php echo $i; ?>][qty]" class="input-remove_arrow form-control text-right" value="<?php echo isset($e['qty']) ? $e['qty'] : 1; ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="item[<?php echo $i; ?>][price_unit]" class="input-remove_arrow form-control text-right" value="<?php echo number_format(datalist_Table('tbl_classes', 'fee', $e['class']), 2, '.', ''); ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="item[<?php echo $i; ?>][amount]" class="input-remove_arrow form-control text-right" value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" readonly></td>
									</tr>
									<?php
									
								}
								
							}
							
							if(isset($std_unpaid_result['result']['item'])) {
								
								foreach($std_unpaid_result['result']['item'] as $e) {
									
									$i++;
									
									?>
									<tr class="item<?php echo $i; ?>">
										<td>
											<input type="hidden" name="unpaid[<?php echo $i; ?>][id]" value="<?php echo $e['id']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][item]" value="<?php echo $e['item']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][dis_amount]" value="<?php echo $e['discount']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][movement]" value="<?php echo datalist_Table('log_join', 'movement', $e['id'], 'id'); ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][movement_log]" value="<?php echo datalist_Table('log_join', 'movement_log', $e['id'], 'id'); ?>">
											<input type="text" onclick="this.select();" class="form-control" name="unpaid[<?php echo $i; ?>][title]" value="<?php echo $e['title']; ?>" readonly>
											<textarea class="form-control mt-2" onclick="this.select();" name="unpaid[<?php echo $i; ?>][remark]" rows="4"><?php echo datalist_Table('log_join', 'remark', $e['id'], 'id'); ?></textarea>
											<div class="mt-2">
												<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
											</div>
										</td>
										<td><input type="number" onclick="this.select();" name="unpaid[<?php echo $i; ?>][qty]" class="input-remove_arrow form-control text-right" value="<?php echo $e['qty']; ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="unpaid[<?php echo $i; ?>][price_unit]" class="input-remove_arrow form-control text-right" value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']), 2, '.', ''); ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="unpaid[<?php echo $i; ?>][amount]" class="input-remove_arrow form-control text-right" value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']) * $e['qty'], 2, '.', ''); ?>" readonly></td>
										</td>
									</tr>
									<?php
									
								}
								
							}
							
							if(isset($std_unpaid_result['result']['service'])) {
								
								foreach($std_unpaid_result['result']['service'] as $e) {
									
									$i++;
									
									?>
									<tr class="item<?php echo $i; ?>">
										<td>
											<input type="hidden" name="item[<?php echo $i; ?>][type]" value="service"> 
											<input type="hidden" name="item[<?php echo $i; ?>][period]" value="<?php echo isset($e['period']) ? $e['period'] : ''; ?>"> 
											<input type="hidden" name="item[<?php echo $i; ?>][item]" value="<?php echo $e['item']; ?>"> 
											<input type="hidden" name="item[<?php echo $i; ?>][dis_amount]" value="<?php echo $e['discount']; ?>"> 
											<input type="text" onclick="this.select();" class="form-control" name="item[<?php echo $i; ?>][title]" value="<?php echo isset($e['period']) ? '[' . $e['period'] . '] ' . $e['title'] : $e['title']; ?>" readonly>
											<textarea class="form-control mt-2" onclick="this.select();" name="item[<?php echo $i; ?>][remark]" rows="4"></textarea>
											<div class="mt-2">
												<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)" class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
											</div>
										</td>
										<td><input type="number" onclick="this.select();" name="item[<?php echo $i; ?>][qty]" class="input-remove_arrow form-control text-right" value="<?php echo $e['qty']; ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="item[<?php echo $i; ?>][price_unit]" class="input-remove_arrow form-control text-right" value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();" name="item[<?php echo $i; ?>][amount]" class="input-remove_arrow form-control text-right" value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" readonly></td>
										</td>
									</tr>
									<?php
									
								}
								
							}
							
						}
							
						?>
					</tbody>
                    <tfoot>
                        <td colspan="4" class="text-center">
                            <a href="javascript:;" data-toggle="modal" data-target="#modal-add"><i class="fa fa-fw fa-plus-circle"></i> Add New</a>
                        </td>
                    </tfoot>
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
                                    <input type="number" step="0.01" onclick="this.select();" class="input-remove_arrow form-control" name="discount" value="<?php echo $std_unpaid_result['discount']; ?>" placeholder="0">
                                    <select class="form-control" name="discount_type" required>
                                        <option value="%">%</option>
                                        <option value="$" <?php if($std_unpaid_result['discount'] > 0) echo 'selected'; ?>>$</option>
                                    </select>
                                </div>
                            </div>
                            <label class="form-control-label col-md-4 text-right" data-label="discount">0.00</label>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <input type="text" id="adjust-title" onclick="this.select();" class="form-control" name="adjust_label" placeholder="Adjustment" />
                            </div>
                            <div class="col-md-4">
                                <input type="number" step="0.01" onclick="this.select();" class="input-remove_arrow form-control" name="adjust" placeholder="0">
                            </div>
                            <label class="form-control-label col-md-4 text-right" data-label="adjust">0.00</label>
                        </div>
						
						<div class="form-group">
							<div class="alert alert-info d-flex justify-content-between">
								<span>Ewallet balance: <b><?php echo user_point('ewallet', $id); ?></b></span>
								<input type="hidden" name="ewallet_value" value="<?php echo user_point('ewallet', $id); ?>">
								<div class="form-check">
									<input class="form-check-input m-0" type="checkbox" name="ewallet" id="ewallet" style="height: 16px; width: 16px; top: 50%; transform: translateY(-50%); left: -.3rem;">
									<label class="form-check-label" for="ewallet">Use ewallet</label>
								</div>
							</div>
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

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-12 text-right">

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                            </div>
                        </div>

                    </div>
                </div>

                <?php } ?>

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
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" id="radio-type-3" name="type" class="custom-control-input" value="package" required>
						<label class="custom-control-label" for="radio-type-3">Package</label>
					</div>
				</div>
				
				<form method="post" class="d-none section-dynamic section-class">
					<div class="form-group">
						<label class="text-danger">Class</label>
						<input type="hidden" name="user" value="<?php echo $id; ?>">
						<input type="hidden" name="title" >
						<input type="hidden" name="id" >
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
				
				<form method="post" class="d-none section-dynamic section-package">
					<div class="form-group">
						<label class="text-danger">Package</label>
						<input type="hidden" name="user" value="<?php echo $id; ?>">
						<input type="hidden" name="title" >
						<input type="hidden" name="id" >
						<select class="form-control select2" name="package" data-required="true">
							<option value="">-</option>';
							<?php foreach ($package as $e) {
								echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
							};?>
						</select>
					</div>
					
				</form>

			</div>

			<div class="modal-footer">
				<button type="button" name="add" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>