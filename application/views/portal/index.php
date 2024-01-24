<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_portal', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_portal'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
               
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">
			<form method="get">
				<div class="row">
					<div class="col-md-6">

						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Branch</label>
							<div class="col-md-9">
								 <select class="form-control select2" name="branch_id">
                                    <option value="">-</option>
									<?php foreach($branches as $k => $v) {?>
									<option value="<?= $v['pid']?>" <?php if($this->input->get('branch_id') == $v['pid']) echo "selected"?>><?= $v['title']?></option>
									<?php } ?>
                                 </select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Status</label>
							<div class="col-md-9">
								 <select class="form-control select2" name="status">
									<?php foreach(datalist('insurance_status') as $k => $v) {?>
										<?php if ($k != 'reject') { ?>
											<option value="<?= $k ?>" <?php if($this->input->get('status') == $k) echo "selected"?>><?= $v['label']?></option>
										<?php } ?>
									<?php } ?>
                                 </select>
							</div>
						</div>
						
						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
				</div>
			</form>
            <?php echo alert_get(); ?>
            
			<form method="post">
				<div class="row mb-3 ">
					<div class="col-md-6">
						<button type="button" class="btn btn-success" data-action="approve" name="insurance_action" onclick="take_action($(this))">Approve</button>
						<button type="button" class="btn btn-danger" data-action="reject" name="insurance_action" onclick="take_action($(this))">Reject</button>
					</div>
					<input type="hidden" name="action_take" />
				</div>
							
				<div class="table-responsive">
					<table class="table  table-hover">
						<thead>
							<th>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" id="bulk_check"  class="custom-control-input" onchange="check_all()">
									<label class="custom-control-label" for="bulk_check"></label>
								</div>
							</th>
							<th style="width:20%">Name</th>
							<th>Gender</th>
							<th>Join Date</th>
							<th>Phone</th>
							<th>School</th>
							<th>Parent</th>
							<th>Insurance</th>
						</thead>
						<tbody>
						<?php foreach($students_result as $k => $v) {?>
						<tr>
							<td>
								<?php if ($v['insurance'] == 'pending') { ?>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" id="check-<?php echo $v['pid']; ?>" class="custom-control-input student" name="student[]" onchange="check()" value="<?php echo $v['pid']; ?>">
										<label class="custom-control-label" for="check-<?php echo $v['pid']; ?>"></label>
									</div>
								<?php } ?>
							</td>
							<td>
							<?php echo $v['fullname_en']; ?>
							</td>
							<td><?php echo empty($v['gender']) ? '-' : datalist('gender')[$v['gender']]; ?></td>
							<td><?php echo empty($v['date_join']) ? '-' : $v['date_join'] ?></td>
							<td><?php echo empty($v['phone']) ? '-' : $v['phone']; ?></td>
							<td><?php echo empty($v['school']) ? '-' : datalist_Table('tbl_secondary', 'title', $v['school']); ?></td>
							<td><?php echo empty($v['parent']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $v['parent']); ?></td>
							<td>
								<?php if (!empty($v['insurance'])) { ?>
									<span class="badge badge-<?php echo datalist('insurance_status')[$v['insurance']]['badge'] ?>"><?php echo datalist('insurance_status')[$v['insurance']]['label'] ?></span>
								<?php } ?>
							</td>
						</tr>
						<?php }?>
						
							

							
						</tbody>
					</table>
				</div>
				<div class="row mb-3 ">
					<div class="col-md-6">
						<div class="row col-md-3">
						
		
						</div>
					</div>
				</div>
			</form>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>