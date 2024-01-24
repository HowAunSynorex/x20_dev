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
					<?php if( check_module('Secondary/Delete') ) { ?>
						<div class="dropdown">  
							<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
							<div class="dropdown-menu dropdown-menu-right">
							</div>
						</div>
					<?php } ?>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <form method="post">

                <div class="row">
                    <div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Title</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="title" value="<?php echo $result['title']; ?>" required>
                            </div>
                        </div>

						<?php
						
						switch($thispage['type']) {
							
							case 'payment_method':
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">Method ID</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="method_id" value="<?php echo $result['method_id']; ?>" required="">
									</div>
								</div>
								<?php 
								break;
							
							case 'childcare':
							case 'transport':
								?>
								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">Price</label>
									<div class="col-md-9">
										<input type="number" step="0.0001" class="form-control" name="price" value="<?php echo $result['price']; ?>" required="">
									</div>
								</div>
								<?php 
								break;
							
						}
						?>
						
                    </div>
                    <div class="col-md-6">
					
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>
						
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
						
						<?php if($thispage['type'] == 'class_bundle') { ?>
							<div class="form-group row">
								<label class="col-form-label col-md-3">Course</label>
								<div class="col-md-9 pt-2">
									<?php foreach($course as $e) { ?>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input class-check" id="checkbox-<?php echo $e['pid']; ?>" value="<?php echo $e['pid']; ?>" name="courses[]" <?php echo count(search($courses, 'course', $e['pid'])) > 0 ? 'checked' : '' ;?>>
											<label class="custom-control-label" for="checkbox-<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></label>
										</div>
									</td>
									<?php } ?>
								</div>
							</div>
						<?php } else { ?>
							
							<div class="form-group row">
								<label class="col-form-label col-md-3">Remark</label>
								<div class="col-md-9">
									<textarea class="form-control" name="remark" rows="4"></textarea>
								</div>
							</div>
						<?php } ?>
					</div>
                    <div class="col-md-6">
						<?php if($thispage['type'] == 'class_bundle') { ?>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Remark</label>
								<div class="col-md-9">
									<textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
								</div>
							</div>
						<?php } ?>
						
						<?php if($thispage['type'] == 'exam') { ?>

							<div class="form-group row">
								<label class="col-form-label col-md-3">Subject</label>
								<div class="col-md-9">
									<div id="subject-div">
										<?php if (count($subject) == 0) { ?>
											<div class="input-group mb-2">
												<input type="text" class="form-control" name="subject[]" value="" />
												<div class="input-group-append">
													<button class="btn btn-danger" type="button" onclick="removeSubject($(this));"><i class="fas fa-trash"></i></button>
												</div>
											</div>
										<?php } ?>
										<?php foreach($subject as $e) { ?>
											<div class="input-group mb-2">
												<input type="text" class="form-control" name="subject[]" value="<?php echo $e; ?>" />
												<div class="input-group-append">
													<button class="btn btn-danger" type="button" onclick="removeSubject($(this));"><i class="fas fa-trash"></i></button>
												</div>
											</div>
										<?php } ?>
									</div>
									<div class="float-right">
										<button class="btn btn-info btn-xs" type="button" onclick="addSubject();"><i class="fas fa-plus"></i></button>
									</div>
								</div>
							</div>
						<?php } ?>
						
					</div>
				</div>

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <?php if( check_module('Secondary/Delete') ) { ?>
        							<button type="button" onclick="del_ask(<?php echo $result['pid']; ?>, '<?php echo $result['type']; ?>', '<?php echo strtolower(datalist('secondary_type')[$result['type']]['single']); ?>')" class="btn btn-danger">Delete</button>
        						<?php } ?>
                                <a href="<?php echo base_url($thispage['group'] . '/list/') . $result['type']; ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>
				
				<?php if($result['type'] == 'school') { ?>
					<ul class="nav nav-tabs mt-3">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" href="<?php echo base_url('secondary/edit/' . $result['pid'] . '?tab=1') ?>">Class Summary</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" href="<?php echo base_url('secondary/edit/' . $result['pid'] . '?tab=2') ?>">Student(s)</a>
						</li>
					</ul>

					<div class="tab-content py-3">
						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>">
							<table class="DTable table">
								<thead>
									<th style="width:7%">No</th>
									<th style="width:35%">Title</th>
									<th style="width:10%">Status</th>
									<th>Tutor</th>
									<th>Course</th>
									<th>Fee ($)</th>
									<th>Student(s)</th>
								</thead>
								<tbody>
									<?php $i=0; foreach($classes as $e) { $i++; ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td>
												<?php if( check_module('Classes/Update') ) { ?>
													<a href="<?php echo base_url('classes/edit/'.$e); ?>">
												<?php } ?>
													<?php echo datalist_Table('tbl_classes', 'title', $e); ?>
												<?php if( check_module('Classes/Update') ) { ?>
													</a>
												<?php } ?>
											</td>
											<td><?php echo badge(datalist_Table('tbl_classes', 'active', $e)); ?></td>
											<td><?php echo empty(datalist_Table('tbl_classes', 'teacher', $e)) ? '-' : datalist_Table('tbl_users', 'fullname_en', datalist_Table('tbl_classes', 'teacher', $e)); ?></td>
											<td><?php echo empty(datalist_Table('tbl_classes', 'course', $e)) ? '-' : datalist_Table('tbl_secondary', 'title', datalist_Table('tbl_classes', 'course', $e)); ?></td>
											<td><?php echo empty(datalist_Table('tbl_classes', 'fee', $e)) ? '-' : number_format(datalist_Table('tbl_classes', 'fee', $e), 2, '.', ','); ?></td>
											<td>
												<a href="<?php echo base_url('classes/edit/'.$e.'?tab=2'); ?>"><?php echo count( $this->log_join_model->list_classes_students($e)); ?></a>
											</td>
										</tr>
									<?php }	?>
								</tbody>
							</table>
						</div>
						
						<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>">
							<table class="DTable table">
								<thead>
									<th style="width:7%">No</th>
									<th style="width:20%">Name</th>
									<th>Gender</th>
									<th>Phone</th>
									<th>Parent</th>
									<th>Join Date</th>
								</thead>
								<tbody>
									
									<?php $i=0; foreach($students as $e) { $i++; ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo $e['fullname_cn'].' '.$e['fullname_en']; ?></a></td>
											<td><?php echo ucfirst( $e['gender'] ); ?></td>
											<td><?php echo empty( $e['phone'] ) ? '-' : $e['phone'] ; ?></td>
											<td><?php echo empty( $e['parent'] ) ? '-' : datalist_Table('tbl_users', 'fullname_cn', $e['parent']).' '.datalist_Table('tbl_users', 'fullname_en', $e['parent']) ; ?></td>
											<td><?php echo $e['date_join']; ?></td>
										</tr>
										<?php
									}?>
									
								</tbody>
							</table>
						</div>
					</div>
				<?php } elseif($result['type'] == 'course') {
					
					$sql = '
						SELECT u.*, j.date AS date, j.id AS id FROM tbl_users u
						INNER JOIN log_join j
						ON j.user = u.pid
						AND u.type = "student"
						AND u.is_delete = 0
						AND j.is_delete = 0
						AND j.type = "join_class"
						AND j.active = 1
						AND j.branch = "'.branch_now('pid').'"
						AND u.branch = "'.branch_now('pid').'"
						INNER JOIN tbl_classes c
						ON c.pid = j.class
						AND c.is_delete = 0
						AND c.branch = "'.branch_now('pid').'"
						INNER JOIN tbl_secondary s
						ON s.pid = c.course
						AND s.is_delete = 0
						AND s.branch = "'.branch_now('pid').'"
						GROUP BY j.user
					';
					
					$users = $this->db->query($sql)->result_array();
					
					?>
					<ul class="nav nav-tabs mt-3">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" href="<?php echo base_url('secondary/edit/' . $result['pid'] . '?tab=1') ?>">Student (<?php echo count($users); ?>)</a>
						</li>
					</ul>
					
					<div class="tab-content py-3">
						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>">
						
							<table class="DTable2 table">
								<thead>
									<th style="width:7%">No</th>
									<th>Name</th>
									<th>Name (CN)</th>
									<th>Gender</th>
									<th>Phone</th>
									<th>Parent</th>
									<th><?php echo ($result['type'] == 'check_in') ? 'Credit Balance' : 'Join Date'; ?></th>
									<th></th>
								</thead>
								<tbody>
									
									<?php
									$i=0;
									foreach($users as $e) {
										$i++; 
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('students/edit/'.$e['pid']); ?>"><?php echo $e['fullname_en']; ?></a></td>
											<td><?php echo empty($e['fullname_cn']) ? '-' : $e['fullname_cn']; ?></a></td>
											<td><?php echo ucfirst( $e['gender'] ); ?></td>
											<td><?php echo empty( $e['phone'] ) ? '-' : $e['phone'] ; ?></td>
											<td><?php echo empty( $e['parent'] ) ? '-' : datalist_Table('tbl_users', 'fullname_cn', $e['parent']).' '.datalist_Table('tbl_users', 'fullname_en', $e['parent']) ; ?></td>
											<td>
												<?php
												//echo ($result['type'] == 'check_in') ? class_credit_balance($result['pid'], $e['user'])['balance'] . '/' . class_credit_balance($result['pid'], $e['user'])['total'] : $e['date'];
												echo ($result['type'] == 'check_in') ? class_credit_balance($result['pid'], $e['pid'])['balance'] : $e['date'];
												?>
											</td>
											<td>
												<a href="javascript:;" onclick="disable(<?php echo $e['id']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times py-1"></i></a>
											</td>
										</tr>
										<?php
									}?>
									
								</tbody>
							</table>
							
						</div>
					</div>
				<?php } else if($result['type'] == 'class_bundle') { ?>
				
					<ul class="nav nav-tabs mt-3">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" href="<?php echo base_url('secondary/edit/' . $result['pid'] . '?tab=1') ?>">Price List</a>
						</li>
					</ul>
					
					<div class="tab-content py-3">
						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>">
						
							<form method="post">
								<table class="DTable2 table">
									<thead>
										<th width="20%">Qty</th>
										<th width="35%">Price</th>
										<th>Material Fee</th>
										<th>Subsidy</th>
									</thead>
									<tbody>
										
										<?php for($i=1;$i<=15;$i++) {?>
											<tr>
												<input type="hidden" name="bundle[<?php echo $i; ?>][qty]" value="<?php echo $i; ?>" />
												<td>X <?php echo $i; ?></td>
												<td><input type="number" class="form-control" name="bundle[<?php echo $i; ?>][amount]" value="<?php echo count(search($prices, 'qty', $i)) > 0 ? search($prices, 'qty', $i)[0]['amount'] : ''; ?>" /></td>
												<td><input type="number" class="form-control" name="bundle[<?php echo $i; ?>][material]" value="<?php echo count(search($prices, 'qty', $i)) > 0 ? search($prices, 'qty', $i)[0]['material'] : ''; ?>" /></td>
												<td><input type="number" class="form-control" name="bundle[<?php echo $i; ?>][subsidy]" value="<?php echo count(search($prices, 'qty', $i)) > 0 ? search($prices, 'qty', $i)[0]['subsidy'] : ''; ?>" /></td>
											</tr>
											<?php
										}?>
									</tbody>
								</table>
								
								
								<div class="row">
									<div class="col-md-6">
										<div class="row">
											<div class="offset-md-3 col-md-9">
												<button type="submit" class="btn btn-primary" name="save_bundle_price">Save</button>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					
				<?php } elseif($result['type'] == 'exam') { ?>
					
					<ul class="nav nav-tabs mt-3">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" href="<?php echo base_url('secondary/edit/' . $result['pid'] . '?tab=1') ?>">Student List</a>
						</li>
					</ul>
					
					<div class="tab-content py-3">
						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>">
						    <div class="table-responsive">
            					<table class="table table-hover border">
            						<thead>
            							<th>No</th>
            							<th>Student</th>
            							<th>Date</th>
            							<?php foreach($subject as $e) { ?>
            							    <th><?php echo $e; ?></th>
            							<?php } ?>
            							<th></th>
            						</thead>
            						<tbody>
            						    <?php $i=0; foreach( $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND type=? AND secondary=? GROUP BY user, date ORDER BY user ASC, date ASC ', [ 'exam_score', $result['pid'] ])->result_array() as $e1 ) { $i++; ?>
                						    <tr>
                							    <td><?php echo $i; ?></td>
                							    <td><?php echo datalist_Table('tbl_users', 'fullname_en', $e1['user']); ?></td>
                							    <td><?php echo $e1['date']; ?></td>
                    							<?php foreach($subject as $e) { ?>
                    							    <td>
                    							        <?php
                    							        
                    							        $score = $this->db->query(' SELECT * FROM log_join WHERE is_delete=0 AND type=? AND secondary=? AND user=? AND subject=? AND date=? ', [ 'exam_score', $result['pid'], $e1['user'], $e, $e1['date'] ])->result_array();
                    							        if(count($score) > 0) {
                    							            $score = $score[0];
                    							            echo $score['score'];
                    							        }
                    							        ?>
                    							    </td>
                    							<?php } ?>
                    							<td>
                    							    <a href="javascript:;" class="text-danger" onclick="if(confirm('Are you sure want to delete?')) { window.location.href='?reset&date=<?php echo $e1['date']; ?>&user=<?php echo $e1['user']; ?>&type=exam_score&secondary=<?php echo $result['pid']; ?>' }">Reset</a>
                    							</td>
                							</tr>
            						    <?php } ?>
            						</tbody>
            					</table>
						    </div>
						</div>
					</div>
					
				<?php } ?>
				
            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Secondary/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>