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
                    
                    <table class="table table-smA table-bordered table-striped">
                        <thead>
                            <th style="width:20%">Date / Time</th>
                            <th>Details</th>
                            <th style="width:15%">Action</th>
                        </thead>
                        <tbody>
                            <?php 

                            if($result != null) {

                                $i=0; 

                                foreach($result as $e) { 

                                    $i++; 

                                    ?>
                                    <tr>
                                        <td>
										<?php if ( check_module('Attendance/Update') ) { ?>
											<a href="javascript:;" data-toggle="modal" data-target="#modal-edit" data-id="<?php echo $e['id']; ?>">
										<?php } ?>
												<?php echo $e['datetime']; ?>
										<?php if ( check_module('Attendance/Update') ) { ?>
											</a>
										<?php } ?>
										</td>
										<td>
											<?php echo empty($e['reason']) ? '-' : $e['reason']; ?>
											<span class="d-block small text-muted">Temperature: <?php echo empty($e['temperature']) ? '-' : $e['temperature'].'&deg;C'; ?></span>
											<span class="d-block small text-muted">Remark: <?php echo empty($e['remark']) ? '-' : $e['remark']; ?></span>
										</td>
										<?php
										if($e['action'] == 'in') {
											?>
											<td class="text-success">
												<span class="font-weight-bold"><?php echo ucfirst($e['action']); ?></span>
												<span class="d-block small text-muted">Method: <?php echo empty($e['method']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['method']); ?></span>
											</td>
											<?php
										} else {
											?>
											<td class="text-danger">
												<span class="font-weight-bold"><?php echo ucfirst($e['action']); ?></span>
												<span class="d-block small text-muted">Method: <?php echo empty($e['method']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['method']); ?></span>
											</td>
											<?php
										}
										?>
                                    </tr>
                                    <?php 

                                }

                            } else {

                                ?><tr><td class="text-center" colspan="6">No result found</td></tr></tbody><?php 

                            } 
                            ?>
						</tbody>
						<?php if( isset($_GET['user']) ) { ?>
						<tfoot>
							<td colspan="6" class="text-center">
								<?php if ( check_module('Attendance/Create') ) { ?>
									<a href="javascript:;" data-toggle="modal" data-target="#modal-add">
								<?php } ?>
									<i class="fa fa-fw fa-plus-circle"></i> Add New
								<?php if ( check_module('Attendance/Create') ) { ?>
									</a>
								<?php } ?>
							</td>
						</tfoot>
						<?php } ?>
                    </table>
                    
                </div>

                <div class="col-md-4 mb-4">
                
                    <form method="get">
                    
						<?php if( !isset($_GET['user']) ) { ?>
						<div class="alert alert-warning">Please select a user</div>
						<?php } ?>
						
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">User</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="user" required>
                                    <option value="">-</option>
									<optgroup label="Student">
										<?php foreach ($student as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if( isset($_GET['user']) ) if( $e['pid'] == $_GET['user'] ) echo 'selected'; ?>><?php echo $e['fullname_cn'].' '.$e['fullname_en']; ?></option>
										<?php } ?>
									</optgroup>
									<optgroup label="Teacher">
										<?php foreach ($teacher as $e) { ?>
										<option value="<?php echo $e['pid']; ?>" <?php if( isset($_GET['user']) ) if( $e['pid'] == $_GET['user'] ) echo 'selected'; ?>><?php echo $e['fullname_cn'].' '.$e['fullname_en']; ?></option>
										<?php } ?>
									</optgroup>
                                </select>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') ; ?>" required>
                            </div>
                        </div>
                        
                        <!--<div class="form-group row">
                            <label class="col-md-3 col-form-label">Reason</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="reason" rows="4">Admin adjust</textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>-->
                        
                        <div class="form-group row">
                            <div class="offset-md-3 col-md-9">
                                <button class="btn btn-primary" type="submit" name="save">Search</button>
                            </div>
                        </div>
                    
                    </form>
                    
                </div>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label class="text-danger">Time</label>
					<input type="time" class="form-control" name="time" value="<?php echo date('H:i'); ?>" required>
					<input type="hidden" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') ; ?>">
					<input type="hidden" name="user" value="<?php echo $_GET['user']; ?>">
				</div>
				
				<div class="form-group">
                    <label>Temperature</label>
					<input type="text" class="form-control" name="temperature">
				</div>
				
				<div class="form-group">
					<label>Action</label>
					<select class="form-control" name="action">
						<option value="in">In</option>
						<option value="out">Out</option>
					</select>
				</div>
				
				<div class="form-group">
                    <label>Reason</label>
					<textarea class="form-control" rows="4" name="reason">Manually</textarea>
				</div>
				
				<div class="form-group">
                    <label>Remark</label>
					<textarea class="form-control" rows="4" name="remark"></textarea>
				</div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="add" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>

<form method="post" class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label class="text-danger">Time</label>
					<input type="time" class="form-control" name="time" step="1" required>
                    <!-- if no step="1" error will occur when editing because
                        when time data got second html wouldn't allow without 
                        defining stetp = 1
                        but if don't care then remove step = '1'  -->
					<input type="hidden" name="id">
				</div>
				
				<div class="form-group">
                    <label>Temperature</label>
					<input type="text" class="form-control" name="temperature">
				</div>
				
				<div class="form-group">
					<label>Action</label>
					<select class="form-control" name="action">
						<option value="in">In</option>
						<option value="out">Out</option>
					</select>
				</div>
				
				<div class="form-group">
                    <label>Reason</label>
					<textarea class="form-control" rows="4" name="reason"></textarea>
				</div>
				
				<div class="form-group">
                    <label>Remark</label>
					<textarea class="form-control" rows="4" name="remark"></textarea>
				</div>

            </div>
            <div class="modal-footer">
			    <button type="button" onclick="del_ask()" class="btn btn-link text-danger px-0 mr-auto">Delete</button>
                <button type="submit" name="edit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>