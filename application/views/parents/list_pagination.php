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
					<?php if( check_module('Students/Create') ) { ?>
						<div class="btn-group">
							<a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
							<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item d-flex justify-content-between align-items-center" href="javascript:;" data-toggle="modal" data-target="#modal-apply" >
									<span>E-form Apply</span>
									<i class="fa fa-fw fa-external-link-square-alt"></i>
								</a>
							</div>
						</div>
					<?php } ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<form method="get">
				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Fullname</label>
							<div class="col-md-9">
								<div class="row">
									<div class="col-md-6">
										<input type="text" class="form-control" name="fullname_en" value="<?php if(isset($_GET['fullname_en'])) echo $_GET['fullname_en']; ?>" placeholder="English">
									</div>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fullname_cn" value="<?php if(isset($_GET['fullname_cn'])) echo $_GET['fullname_cn']; ?>" placeholder="中文 (Optional)">
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Phone</label>
							<div class="col-md-9">
								<input type="tel" class="form-control" name="phone" value="<?php if(isset($_GET['phone'])) echo $_GET['phone']; ?>">
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
            
			<!--<form method="post" action="<?php echo base_url('students/bulk'); ?>" class="mb-0">-->
			
				<div class="row">
					<div class="col-md-6">
						<p class="d-inline-block mb-2">Total Student(s): <?php echo $total_count; ?></p>
					</div>
					<div class="col-md-6">
						<?= $pagination; ?>
					</div>
				</div>
				
				<div class="table-responsive">
					<table class="table table-hover border">
						<thead>
    						<th style="width:7%">No</th>
    						<th style="width:20%">Name</th>
    						<th style="width:10%">Status</th>
    						<th>Gender</th>
    						<th>Email</th>
    						<th>Phone</th>
    						<th>Child(s)</th>
						</thead>
						<tbody>
							
							<?php 
								$i=$row; foreach($result as $e) { $i++; 
								?>
								<tr>
        							<td><?php echo $i; ?></td>
        							<td>
        								<div class="media">
        									<?php if( check_module('Parents/Update') ) { ?>
        										<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
        											<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
        										</a>
        										
        										<div class="media-body my-auto">
        											<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
        												<?php echo $e['fullname_en']; ?>
        												<br>
        												<?php echo $e['fullname_cn']; ?>
        											</a>
        										</div>
        									<?php } else { ?>
        										<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
        										
        										<div class="media-body my-auto">
        											<?php echo $e['fullname_en']; ?>
        											<br>
        											<?php echo $e['fullname_cn']; ?>
        										</div>
        									<?php } ?>
        									
        								</div>
        							</td>
        							<td><?php echo badge($e['active']); ?></td>
        							<td><?php echo ucfirst($e['gender']); ?></td>
        							<td><?php echo empty($e['email']) ? '-' : $e['email']; ?></td>
        							<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
        							<td>
        								<a href="<?php echo base_url('students/list/?parent='.$e['pid']); ?>">
        									<?php
    										$child = $this->log_join_model->list('join_parent', branch_now('pid'), [ 'parent' => $e['pid'], 'active' => 1 ]);
    										echo count($child);
    										?>
        								</a>
        							</td>
        						</tr>
								<?php 
							} 
							?>
							
						</tbody>
					</table>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<p class="d-inline-block mb-2">Total Student(s): <?php echo $total_count; ?></p>
					</div>
					<div class="col-md-6 text-right">
						<?= $pagination; ?>
					</div>
				</div>
				

			<!--</form>-->
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>


<script>var branch = "<?php echo branch_now('pid'); ?>"; </script>