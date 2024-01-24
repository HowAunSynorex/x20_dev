<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold">
						<?php 
						
						echo $thispage['title'];

						if( isset($_GET['class']) ) echo '<span class="badge badge-secondary badge-pill badge-sm ml-2"><a href="javascript:;" data-label="return_student" class="text-white"><i class="fa fa-fw fa-times"></i></a> '.datalist_Table('tbl_classes', 'title', $_GET['class']).'</span>';
						if( isset($_GET['parent']) ) echo '<span class="badge badge-secondary badge-pill badge-sm ml-2"><a href="javascript:;" data-label="return_student" class="text-white"><i class="fa fa-fw fa-times"></i></a> '.datalist_Table('tbl_users', 'fullname_en', $_GET['parent']).'</span>';
						
						?>
					</h4>
                </div>
                <div class="col-6 my-auto text-right">
					<a class="btn btn-info" href="<?= base_url('/uploads/files/school_personal_ccident.pdf');?>" download>Policy Content <i class="fas fa-file-download"></i></a>
					<!-- <button type="button" class="btn btn-info" onclick="open();"></button> -->
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			

			
			<form method="post">
				<div class="row mb-3 ">
					<div class="col-md-6">
						<div class="row col-md-3">
							<button type="button" class="btn btn-secondary"  onclick="insurance_ask()" name="to_buy_insurance">Apply Now</button>
						</div>
					</div>
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
							<?php if (count($all) > 0) { ?>
								<?php foreach($all as $k => $v) {?>
									<tr>
										<td>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" id="check-<?php echo $v['pid']; ?>" class="custom-control-input student" name="student[]" onchange="check()" value="<?php echo $v['pid']; ?>" <?php echo $v['insurance'] == 'confirm' ? 'disabled' : '' ?> <?php echo ($v['insurance'] == 'pending' || $v['insurance'] == 'confirm') ? 'checked' : '' ?> >
												<label class="custom-control-label" for="check-<?php echo $v['pid']; ?>"></label>
											</div>
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
							<?php } else { ?>
								<tr><td class="text-center" colspan="8">No result found.</td></tr>
							<?php }?>
						</tbody>
					</table>
				</div>
				<div class="row mb-3 ">
					<div class="col-md-6">
						<div class="row col-md-3">
						<button type="button" class="btn btn-secondary"  onclick="insurance_ask()" name="to_buy_insurance">Apply Now</button>
		
						</div>
					</div>
				</div>
			</form>
			
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	var branch = "<?php echo branch_now('pid'); ?>"; 
	
	function open()
	{
		$("#agreeModal").modal('show');
	}
</script>



<div class="modal " id="agreeModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">School Personal Accident</h5>
			</div>
			<form method="post">  
				<div class="modal-body">
					<embed src="<?= base_url('/uploads/files/school_personal_ccident.pdf');?>" frameborder="0" width="100%" height="500px">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>