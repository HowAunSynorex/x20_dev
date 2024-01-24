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
			
			<form method="post">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Title</label>
                            <div class="col-md-9">
                                <p class="form-control-plaintext"><?php echo empty($result['title']) ? '-' : '<a href="'.base_url('classes/edit/'.$result['pid']).'" target="_blank">'.$result['title'].' <i class="fa fa-fw fa-external-link-square-alt"></i></a>' ;  ?></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Teacher</label>
                            <div class="col-md-9">
                                <p class="form-control-plaintext"><?php echo empty($result['teacher']) ? '-' : '<a href="'.base_url('teachers/edit/'.$result['teacher']).'" target="_blank">'.datalist_Table('tbl_users', 'fullname_en', $result['teacher']).' <i class="fa fa-fw fa-external-link-square-alt"></i></a>' ;  ?></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Date</label>
                            <div class="col-md-9">
                                <p class="form-control-plaintext"><?php echo $_GET['date'].' ('.date('D', strtotime($_GET['date'])).')'; ?></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Time</label>
                            <div class="col-md-9">
                                <p class="form-control-plaintext">
									<?php 
									
									$day = date('N', strtotime($_GET['date']));
									echo str_replace('-', ' - ', $result['dy_'.$day]);
									?>
								</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">  

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary" disabled>Save</button>
                                <a href="<?php echo base_url($thispage['group']); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Attendance</a>
				</li>
			</ul>

			<div class="tab-content py-3">

				<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
					<form method="post">
						<table class="DTableA table-hover table table-bordered">
							<thead>
								<th style="width: 10%">No</th>
								<th>Student</th>
								<th style="width: 10%"><input id="select-all-checkbox" type="checkbox" onclick="select_all()"></th>
							</thead>
							<tbody> 
								<?php
								
								$i=0;
								
								foreach($result2 as $e) { 
								
									if(datalist_Table('tbl_users', 'active', $e['user']) == 1 && datalist_Table('tbl_users', 'is_delete', $e['user']) == 0 ) {
										$i++;
										
										?>
										
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
											<td>
												<input type="checkbox" value="<?php echo $e['user']; ?>"
													<?php
													$data = [];
													$data[] = $this->log_join_model->list('class_attendance', branch_now('pid'), ['user' => $e['user']]);
													
													foreach($data[0] as $k => $v) {
														if($e['user'] == $v['user'] && $v['date'] == $_GET['date'] && $v['class'] == $e['class']) {
															echo 'checked ';
															echo 'name="old_attendance['.$i.']" ';
															echo 'onclick="add_to_removed_list(this)" ';
															echo 'id="'.$v['id'].'" ';
														}
													}
													
													if(empty($data[0])){
														echo 'name="attendance['.$i.']"';
													}
													
													?>
												>
											</td>
										</tr>
										
										<?php
									}
								 } ?>
							</tbody>
							<input type="hidden" name="removed_list">
						</table>
						<div class="row Apy-4">
							<div class="col-md-12">
								<div class="form-group text-right">
									<button type="submit" name="save" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				
			</div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>
