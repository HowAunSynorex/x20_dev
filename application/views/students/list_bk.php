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
							<label class="col-md-3 col-form-label">Card ID</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="rfid_cardid" value="<?php if(isset($_GET['rfid_cardid'])) echo $_GET['rfid_cardid']; ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Phone</label>
							<div class="col-md-9">
								<input type="tel" class="form-control" name="phone" value="<?php if(isset($_GET['phone'])) echo $_GET['phone']; ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Email</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email" value="<?php if(isset($_GET['email'])) echo $_GET['email']; ?>">
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-3 col-form-label">Form</label>
							<div class="col-md-9">
								<select class="form-control select2" name="form">
									<option value="">-</option>
									<?php 
									foreach($form as $e) {
										?>
										<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['form'])) { if($_GET['form'] == $e['pid']) { echo 'selected'; } } ?> ><?php echo $e['title']; ?></option>
										<?php
									} ?>
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
            
			<form method="post" action="<?php echo base_url('students/bulk'); ?>" class="mb-0">
			
				<p class="d-inline-block mb-3">Total Student(s): <?php echo count($all); ?></p>
				
				<?php if(isset($p_section)) { ?>
				<nav class="float-right">
					<ul class="pagination">
						<?php for($p=1; $p<=$max_p; $p++) { ?>
						<li class="page-item <?php if($_GET['p'] == $p) echo 'active'; ?>"><a class="page-link" href="?p=<?php echo $p; ?>&sort_name_click&sort_name=<?php echo $sort_name; ?>"><?php echo $p; ?></a></li>
						<?php } ?>
					</ul>
				</nav>
				<?php } ?>
				
				<div class="table-responsive">
					<table class="table table-hover border">
						<thead>
							<th style="width:7%">No</th>
							<th style="width:10%">
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Code
								<?php } else { ?>
								<a href="?sort_name_click&sort_name=<?php echo $sort_name; ?>">
									Code
									<?php
									
									if($_GET['sort_name'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th style="width:20%">
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Name
								<?php } else { ?>
								<a href="?sort_name_click&sort_name=<?php echo $sort_name; ?>">
									Name
									<?php
									
									if($_GET['sort_name'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th style="width:10%">
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Status
								<?php } else { ?>
								<a href="?sort_status_click&sort_status=<?php echo $sort_status; ?>">
									Status
									<?php
									
									if($_GET['sort_status'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th>
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Gender
								<?php } else { ?>
								<a href="?sort_gender_click&sort_gender=<?php echo $sort_gender; ?>">
									Gender
									<?php
									
									if($_GET['sort_gender'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th>
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Join Date
								<?php } else { ?>
								<a href="?sort_join_click&sort_join=<?php echo $sort_join; ?>">
									Join Date
									<?php
									
									if($_GET['sort_join'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th>
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Phone
								<?php } else { ?>
								<a href="?sort_phone_click&sort_phone=<?php echo $sort_phone; ?>">
									Phone
									<?php
									
									if($_GET['sort_phone'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							
							</th>
							<th>
								<?php if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								School
								<?php } else { ?>
								<a href="?sort_school_click&sort_school=<?php echo $sort_school; ?>">
									School
									<?php
									
									if($_GET['sort_school'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
							<th>
								<?php //if( isset($_GET['search']) || isset($_GET['parent']) || isset($_GET['class']) ) { ?>
								Parent
								<?php /* } else { ?>
								<a href="?sort_parent_click&sort_parent=<?php echo $sort_parent; ?>">
									Parent
									<?php
									
									if($_GET['sort_parent'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } */ ?>
							</th>
							<th>
								<?php if( isset($_GET['search']) || isset($_GET['form']) || isset($_GET['class']) ) { ?>
								Form
								<?php } else { ?>
								<a href="?sort_form_click&sort_form=<?php echo $sort_form; ?>">
									Form
									<?php
									
									if($_GET['sort_form'] == 'ASC') {
										echo '<i class="fa fa-fw fa-sort-alpha-down"></i>';
									} else {
										echo '<i class="fa fa-fw fa-sort-alpha-down-alt"></i>';
									}
									?>
								</a>
								<?php } ?>
							</th>
						</thead>
						<tbody>
							
							<?php 
							$i=$limit * ( $_GET['p'] - 1 ); foreach($result as $e) { $i++; 
								?>
								<tr>
									<?php if(isset($_GET['class'])) { ?>
										<td><?php echo $i; ?></td>
										<td><?php echo $e['code']; ?></td>
										<td>
											<div class="media">
												<?php if( check_module('Students/Update') ) { ?>
													<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['user']); ?>">
												<?php } ?>

													<img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_users', 'image', $e['user'])); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">	
													
												<?php if( check_module('Students/Update') ) { ?>
													</a>
												<?php } ?>
												<div class="media-body my-auto">
												
													<?php if( check_module('Students/Update') ) { ?>
														<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['user']); ?>">
													<?php } ?>
														<?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']) . ' ' . datalist_Table('tbl_users', 'fullname_cn', $e['user']); ?>
													<?php if( check_module('Students/Update') ) { ?>
														</a>
													<?php } ?>
													<div style="font-size: 1.25rem">
														<a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'https://wa.me/'.wa_format($e['phone']).'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="WhatsApp">
															<i class="fab fa-fw fa-whatsapp"></i>
														</a>
														<a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'tel:'.$e['phone']; ?>" class="text-muted" data-toggle="tooltip" title="Call">
															<i class="fa fa-fw fa-phone"></i>
														</a>
														<a href="<?php echo (empty($e['email'])) ? 'javascript:;" style="opacity: .5;' : 'mailto:'.$e['email'].'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Email">
															<i class="fa fa-fw fa-envelope"></i>
														</a>
														<a href="<?php echo (empty($e['address'])) ? 'javascript:;" style="opacity: .5;' : 'https://maps.google.com/?daddr='.urlencode($e['address']).'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Map">
															<i class="fa fa-fw fa-map-marker-alt"></i>
														</a>
													</div>
												</div>
											</div>
										</td>
										<td><?php echo badge(datalist_Table('tbl_users', 'active', $e['user'])); ?></td>
										<td><?php echo ucfirst(datalist_Table('tbl_users', 'gender', $e['user'])); ?></td>
										<td><?php echo empty(datalist_Table('tbl_users', 'date_join', $e['user'])) ? '-' : datalist_Table('tbl_users', 'date_join', $e['user']); ?></td>
										<td><?php echo empty(datalist_Table('tbl_users', 'phone', $e['user'])) ? '-' : datalist_Table('tbl_users', 'phone', $e['user']); ?></td>
										<td><?php echo empty(datalist_Table('tbl_users', 'school', $e['user'])) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'school', $e['user'])) ; ?></td>
										<td><?php echo empty(datalist_Table('tbl_users', 'parent', $e['user'])) ? '-' : datalist_Table('tbl_users', 'fullname_en', datalist_Table('tbl_users', 'parent', $e['user'])) ; ?></td>
										<td><?php echo empty($e['form']) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_users', 'form', $e['user'])); ?></td>
									<?php } else { ?>
										<td class="hover-hide-noA">
											<span class="no"><?php echo $i; ?></span>
											<?php /*<input type="checkbox" class="checkbox-bulk" name="bulk[]" value="<?php echo $e['pid']; ?>">*/ ?>
										</td>
										<td><?php echo $e['code']; ?></td>
										<td>
											<div class="media">
												<?php if( check_module('Students/Update') ) { ?>
													<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
												<?php } ?>
													<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-2 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
												<?php if( check_module('Students/Update') ) { ?>
													</a>
												<?php } ?>
												
												<div class="media-body my-auto">
												
													<?php if( check_module('Students/Update') ) { ?>
														<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
													<?php } ?>
														<?php echo $e['fullname_en'] . ' ' . $e['fullname_cn']; ?>
													<?php if( check_module('Students/Update') ) { ?>
														</a>
													<?php } ?>
													<div style="font-size: 1.25rem">
														<a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'https://wa.me/'.wa_format($e['phone']).'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="WhatsApp">
															<i class="fab fa-fw fa-whatsapp"></i>
														</a>
														<a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'tel:'.$e['phone']; ?>" class="text-muted" data-toggle="tooltip" title="Call">
															<i class="fa fa-fw fa-phone"></i>
														</a>
														<a href="<?php echo (empty($e['email'])) ? 'javascript:;" style="opacity: .5;' : 'mailto:'.$e['email'].'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Email">
															<i class="fa fa-fw fa-envelope"></i>
														</a>
														<a href="<?php echo (empty($e['address'])) ? 'javascript:;" style="opacity: .5;' : 'https://maps.google.com/?daddr='.urlencode($e['address']).'" target="_blank'; ?>" class="text-muted" data-toggle="tooltip" title="Map">
															<i class="fa fa-fw fa-map-marker-alt"></i>
														</a>
													</div>
												</div>
											</div>
										</td>
										<td><?php echo badge($e['active']); ?></td>
										<td><?php echo ucfirst($e['gender']); ?></td>
										<td><?php echo empty($e['date_join']) ? '-' : $e['date_join']; ?></td>
										<td>
											<?php 
											
											if(!empty($e['phone'])) echo $e['phone'].'<br>';
											if(!empty($e['phone2'])) echo $e['phone2'].'<br>';
											if(!empty($e['phone3'])) echo $e['phone3'].'<br>';
											?>
										</td>
										<td><?php echo empty($e['school']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['school']) ; ?></td>
										<td>
										<?php
										$parent = $this->log_join_model->list('join_parent', branch_now('pid'), [ 'user' => $e['pid'], 'active' => 1 ]);
										if(count($parent) > 0) {
											foreach($parent as $p) {
												echo datalist_Table('tbl_users', 'fullname_en', $p['parent']).'<br>';
											}
										} else {
											echo '-';
										}
										?>
										</td>
										<td><?php echo empty($e['form']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['form']); ?></td>
									<?php } ?>
								</tr>
								<?php 
							} 
							
							if($i==0) echo '<tr><td colspan="8">No result found</td></tr>';
							?>
							
						</tbody>
					</table>
				</div>
				
				<p class="d-inline-block mb-2">Total Student(s): <?php echo count($all); ?></p>
				
				<?php if(isset($p_section)) { ?>
				<nav class="float-right">
					<ul class="pagination">
						<?php for($p=1; $p<=$max_p; $p++) { ?>
						<li class="page-item <?php if($_GET['p'] == $p) echo 'active'; ?>"><a class="page-link" href="?p=<?php echo $p; ?>&sort_name_click&sort_name=<?php echo $sort_name; ?>"><?php echo $p; ?></a></li>
						<?php } ?>
					</ul>
				</nav>
				<?php } ?>
				
				<!--<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-bottom navbar-bulk d-none py-2">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item">
							<button class="nav-link btn-danger text-white rounded btn-sm py-1 border-0" name="delete" type="submit" style="cursor: pointer">Delete (<span class="count-checkbox">0</span>)</button>
						</li>
						<li class="nav-item ml-2">
							<button class="nav-link btn-secondary text-white rounded btn-sm py-1 border-0" onclick="selected_all(this)" data-checked="0" type="button" style="cursor: pointer"><i class="far fa-fw fa-check-square"></i> Selected All</button>
						</li>
					</ul>
				</nav>-->
				
			</form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form class="modal fade" id="modal-apply">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Application E-form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
				<a href="<?php echo base_url("landing/apply_form/".branch_now('pid')); ?>" target="_blank">
					<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo base_url("landing/apply_form/".branch_now('pid')); ?>&choe=UTF-8" class="w-50">
				</a>
				<?php if(branch_now('pointoapi_key') == '') { ?>
					<div class="alert alert-warning">
						Setup PointoAPI API Key to use this function. <a href="<?php echo base_url('settings/pointoapi'); ?>" class="text-primary">Setup</a>
					</div>
				<?php } ?>
				<div class="card mb-2">
					<div class="card-body">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="english" name="language" class="custom-control-input" value="english" checked>
							<label class="custom-control-label" for="english">English</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="chinese" name="language" class="custom-control-input" value="chinese">
							<label class="custom-control-label" for="chinese">中文</label>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body align-items-center d-flex justify-content-center">
						<span class="mr-2">Upload receipt in E-form page</span>
						<label class="switch">
							<input type="checkbox" class="receipt-switch" <?php if(branch_now('pointoapi_key') == '') echo 'disabled'; ?>>
							<span class="slider round"></span>
						</label>
					</div>
				</div>
            </div>
        </div>
    </div>
</form>

<script>var branch = "<?php echo branch_now('pid'); ?>"; </script>