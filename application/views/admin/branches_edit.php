<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

if( $result['is_delete'] == 1 ) echo '<script>var readonly_input = true;</script>';
?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
                <div class="col-6 my-auto text-right <?php if($result['is_delete'] == 1) echo 'd-none'; ?>">
                    <div class="dropdown">  
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item text-danger" href="javascript:;" onclick="del_ask(<?php echo $result['pid'];?>)">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<?php if( $result['is_delete'] == 1 ) { ?>
			<div class="alert alert-warning">
				This branch has been deleted.
				<a class="ml-2 btn btn-success btn-sm text-light" href="javascript:;" onclick="restore_ask(<?php echo $result['pid']; ?>)">
					Restore Now
				</a>
			</div>
			<?php } ?>

            <form method="post" enctype="multipart/form-data" id="form-primary">

                <div class="row">
                    <div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">SSM No</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="ssm_no" value="<?php echo $result['ssm_no']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Country</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="country">
                                    <option value="">-</option>
                                    <?php foreach ($country as $e) {?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if($result['country'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
                                    <?php }; ?>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Currency</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="currency">
                                    <option value="">-</option>
                                    <?php foreach ($currency as $e) {?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if($result['currency'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>
						
                    </div>
                    <div class="col-md-6">
					
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Owner</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="owner" required>
                                    <option value="">-</option>
                                    <?php foreach ($admins as $e) {?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if($result['owner'] == $e['pid']) echo 'selected'; ?>><?php echo $e['nickname']; ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>
						
						<div class="form-group row">
							<label class="col-form-label col-md-3">Logo</label>
							<div class="col-md-9">
								<img src="<?php echo pointoapi_UploadSource($result['image']); ?>" class="mb-2 rounded border" style="height: 100px; width: 100px; object-fit: cover;">
								<input type="file" class="form-control" name="image">
							</div>
						</div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Phone</label>
                            <div class="col-md-9">
                                <input type="tel" class="form-control" name="phone" value="<?php echo $result['phone']; ?>">
                                <input type="tel" class="form-control mt-2" name="phone2" value="<?php echo $result['phone2']; ?>">
                                <input type="tel" class="form-control mt-2" name="phone3" value="<?php echo $result['phone3']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Email</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" name="email" value="<?php echo $result['email']; ?>">
                                <input type="email" class="form-control mt-2" name="email2" value="<?php echo $result['email2']; ?>">
                                <input type="email" class="form-control mt-2" name="email3" value="<?php echo $result['email3']; ?>">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Address</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="address" value="<?php echo $result['address']; ?>">
                                <input type="text" class="form-control mt-2" name="address2" value="<?php echo $result['address2']; ?>">
                                <input type="text" class="form-control mt-2" name="address3" value="<?php echo $result['address3']; ?>">
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Plan</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="plan">
                                    <?php foreach ($plan as $e) {?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if($result['plan'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>
					
					</div>
					
					<div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Amount ($)</label>
                            <div class="col-md-9">
                                <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $result['amount']; ?>">
                            </div>
                        </div>

                    </div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
					
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Expired Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="expired_date" value="<?php echo $result['expired_date']; ?>">
                            </div>
                        </div>
					
					</div>
					<div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Currency</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="amount_unit">
                                    <option value="">-</option>';

                                    <?php foreach ($currency as $e) {?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if($result['amount_unit'] == $e['pid']) echo 'selected'; ?>><?php echo $e['title']; ?></option>
                                    <?php }; ?>

                                </select>
                            </div>
                        </div>

                    </div>
				</div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($this->group . '/branches_list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Version</label>
                            <div class="col-md-9">
								<select class="form-control select2" name="version">
									<?php foreach(datalist('branch_version') as $k => $v) { ?>
										<option value="<?php echo $k; ?>" <?php if($result['version'] == $k) echo 'selected'; ?>><?php echo $v; ?></option>
									<?php } ?>
								</select>
							</div>
                        </div>
                    </div>
                </div>

            </form>
			
			<ul class="nav nav-tabs mt-3">
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Admin(s)</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">Bill(s)</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-3">Overview</a>
				</li>
			</ul>

			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					<div class="text-right mb-3">
						<a href="javascript:;" data-toggle="modal" data-target="#modal-add" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> Add New</a>
					</div>
					<table class="DTable table">
						<thead>
							<th style="width:10%">No</th>
							<th>Name</th>
							<th>Permission</th>
							<th></th>
						</thead>
						<tbody>
							
							<?php
							
							$i=0;
							
							foreach($join_admins as $e) {
								
								$i++;
							
								$e['permission'] = json_decode($e['permission'], true);
								if(!is_array($e['permission'])) $e['permission'] = [];
							
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo datalist_Table('tbl_admins', 'nickname', $e['admin']); ?></td>
									<td><?php echo $result['owner'] == $e['admin'] ? 'Owner' : 'Total '.count( $e['permission'] ).' permission(s)' ; ?></td>
									<td>
										<a href="javascript:;" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Remove" onclick="remove_ask(<?php echo $e['id']; ?>)"><i class="fa fa-fw fa-trash py-1"></i></a>
									</td>
								</tr>
								<?php
								
							}
							
							?>
							
						</tbody>
					</table>
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
					<div class="text-right mb-3">
						<a href="javascript:;" data-toggle="modal" data-target="#modal-add-bill" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> Add New</a>
					</div>
					<table class="DTable table">
						<thead>
							<th style="width:10%">No</th>
							<th>Plan</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Amount</th>
							<th>Currency</th>
							<th>Description</th>
							<th></th>
						</thead>
						<tbody>
							
							<?php $i=0; foreach($bill as $e) { $i++; ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo datalist_Table('tbl_secondary', 'title', $e['plan']); ?></td>
								<td><?php echo $e['date_start']; ?></td>
								<td><?php echo $e['date_end']; ?></td>
								<td><?php echo $e['amount']; ?></td>
								<td><?php echo datalist_Table('tbl_secondary', 'title', $e['amount_unit']); ?></td>
								<td><?php echo $e['remark']; ?></td>
								<td>
									<a href="javascript:;" class="btn btn-sm btn-warning" data-id="<?php echo $e['id']; ?>" data-target="#modal-edit-bill" data-toggle="modal" title="Edit"><i class="fa fa-fw fa-pen py-1"></i></a>
									<a href="javascript:;" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Remove" onclick="remove_ask_bill(<?php echo $e['id']; ?>)"><i class="fa fa-fw fa-trash py-1"></i></a>
								</td>
							<?php } ?>
							
						</tbody>
					</table>
				</div>
				
				<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
					<table class="DTable table">
						<thead>
							<th>Data</th>
							<th>Total</th>
							<th>Active</th>
							<th>Inactive</th>
							<th>Trash</th>
						</thead>
						<tbody>
							
							<?php foreach(['students', 'parents', 'teachers', 'classes', 'items', 'movement', 'attendance'] as $e) {?>
								<tr>
									<td><?php echo ucfirst($e); ?></td>
									<td>
										<?php echo count(${$e}); ?>
										<a href="javascript:;" class="d-block text-danger" onclick="del_ask_overview(<?php echo $result['pid']; ?>, '<?php echo $e; ?>')">Delete</a>
									</td>
									<?php  
									
										switch($e) {
											
											case 'movement':
											case 'attendance':
												?>
												<td>N/A</td>
												<td>N/A</td>
												<?php
												break;
												
											default:
												?>
												<td>
													<?php

														$active = 0;
														foreach(${$e} as $e2) if($e2['active'] == 1 && $e2['is_delete'] == 0) $active++;
														echo $active;
													
													?>
													<a href="javascript:;" class="d-block text-danger" onclick="del_ask_active(<?php echo $result['pid']; ?>, '<?php echo $e; ?>')">Delete</a>
												</td>
												<td>
													<?php

														$inactive = 0;
														foreach(${$e} as $e2) if($e2['active'] == 0 && $e2['is_delete'] == 0) $inactive++;
														echo $inactive;
													
													?>
													<a href="javascript:;" class="d-block text-danger" onclick="del_ask_inactive(<?php echo $result['pid']; ?>, '<?php echo $e; ?>')">Delete</a>
												</td>
												<?php
												break;
											
										}
									?>
									<td>
										<?php

											$deleted = 0;
											foreach(${$e} as $e2) if($e2['is_delete'] == 1) $deleted++;
											echo $deleted;
										
										?>
									</td>
									<!--<td>
										<a href="javascript:;" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Remove" onclick="del_ask_bill(<?php echo $e['id']; ?>)"><i class="fa fa-fw fa-trash py-1"></i></a>
									</td>-->
								</tr>
							<?php } ?>
							
						</tbody>
					</table>
				</div>
				
			</div>
				
		</div>

    </div>
		
    <?php $this->load->view('inc/copyright_admin'); ?>

</div>

<form method="post" class="modal fade" id="modal-add">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">New Admin</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
				<div class="form-group">
					<label>Admin</label>
					<select class="form-control select2" name="admin" required>
						<option value="">-</option>
						<?php foreach ($admins as $e) {?>
						<option value="<?php echo $e['pid']; ?>"><?php echo $e['nickname']; ?></option>
						<?php }; ?>
					</select>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="add_user" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>

<form method="post" class="modal fade" id="modal-add-bill">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">New Bill</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
				<div class="form-group">
					<label class="text-danger">Plan</label>
					<select class="form-control select2" name="plan" required>
						<option value="">-</option>
						<?php foreach ($plan as $e) {?>
						<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
						<?php }; ?>
					</select>
				</div>
				
				<div class="form-group">
					<label class="text-danger">Currency</label>
					<select class="form-control select2" name="amount_unit" required>
						<option value="">-</option>
						<?php foreach ($currency as $e) {?>
						<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
						<?php }; ?>
					</select>
				</div>
				
				<div class="form-group">
					<label>Start Date</label>
					<input type="date" class="form-control" name="date_start" value="<?php echo date('Y-m-d'); ?>" required>
				</div>
				
				<div class="form-group">
					<label>End Date</label>
					<input type="date" class="form-control" name="date_end" value="<?php echo date('Y-m-d'); ?>" required>
				</div>
				
				<div class="form-group">
					<label>Amount ($)</label>
					<input type="number" step="0.01" class="form-control" name="amount" >
				</div>	
				
				<div class="form-group">
					<label>Description</label>
					<textarea class="form-control" rows="4" name="remark"></textarea>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="add_bill" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>

<form method="post" class="modal fade" id="modal-edit-bill">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Bill</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="text-danger">Plan</label>
					<input type="hidden" name="id">
					<select class="form-control select2" name="plan" required>
						<option value="">-</option>
						<?php foreach ($plan as $e) {?>
						<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
						<?php }; ?>
					</select>
				</div>
				
				<div class="form-group">
					<label class="text-danger">Currency</label>
					<select class="form-control select2" name="amount_unit">
						<option value="">-</option>
						<?php foreach ($currency as $e) {?>
						<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
						<?php }; ?>
					</select>
				</div>
				
				<div class="form-group">
					<label>Start Date</label>
					<input type="date" class="form-control" name="date_start">
				</div>
				
				<div class="form-group">
					<label>End Date</label>
					<input type="date" class="form-control" name="date_end">
				</div>
				
				<div class="form-group">
					<label>Amount ($)</label>
					<input type="number" step="0.01" class="form-control" name="amount">
				</div>	
				
				<div class="form-group">
					<label>Description</label>
					<textarea class="form-control" rows="4" name="remark">=</textarea>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="save_bill" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</form>