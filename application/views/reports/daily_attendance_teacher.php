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

            <form method="get">
                <div class="row">
                    <div class="col-md-6">
					
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date" value="<?php if(isset($_GET['date'])) { echo $_GET['date']; } else { echo date('Y-m-d'); } ?>">
                            </div>
                        </div>
					
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
					
					</div>
                </div>
            </form>

            <?php echo alert_get();  ?>

			<table class="DTable table table-bordered my-2">
				<thead>
					<th style="width:10%">No</th>
					<th style="width:35%">Teacher</th>
					
					<?php
					
						if(isset($_GET['date'])) { 
							$date = $_GET['date']; 
						} else { 
							$date = date('Y-m-d'); 
						}
						
						$dataList = array();
						
						if(isset($result)) {
														
							foreach($result as $e) {
								
								$type = datalist_Table('tbl_users', 'type', $e['user']);
								
								if ( $type == 'teacher' && check_user_availability($e['user']) ) {
									
									$data = $this->log_attendance_model->list([
											
										'user' => $e['user'],
										'DATE(datetime)' => $date,
									
									]);
									
									array_push($dataList, count($data).','.$e['user']);
									
								}
								
							}
							
							$countList = array(); $userList = array();
							
							foreach($dataList as $k => $v) {
								
								$index = strpos($v, ',');
								$countList[] = substr($v, 0, $index);
								$userList[] = substr($v, $index+1);
								
							}
							
							if($countList != null) {
								
								$max = array_search(max($countList), $countList);
								$min = min($countList);
								$user = $userList[$max];
								
								$data = $this->log_attendance_model->list([
											
									'user' => $user,
									'DATE(datetime)' => $date,
								
								]);
								
								$j = 0; $x = 1;
								
								foreach($data as $k => $v) {
									
									$j++;
									if($v['action'] == 'in') {
										echo '<th>'.ucfirst($v['action']).' #'.$x.'</th>';
									} else {
										echo '<th>'.ucfirst($v['action']).' #'.$x.'</th>';
										$x++;
									}
									
								}
								
							}
						}
						

					?>
				</thead>
				<tbody>
					<?php 
						$i=0;
						
						if(isset($result)) {
							
							foreach($result as $e) {
								
								$type = datalist_Table('tbl_users', 'type', $e['user']);
								
								if ( $type == 'teacher' && check_user_availability($e['user']) ) { $i++;
								
									?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
										<?php 
										
											$data = $this->log_attendance_model->list([
											
												'user' => $e['user'],
												'DATE(datetime)' => $date,
											
											]);
											
											foreach($data as $k => $v) {
												
												if ($v['action'] == 'in') {
													
													echo '<td class="text-success"><b>'.date('H:i', strtotime($v['datetime'])).'</b></td>';
													
												} else {
													
													echo '<td class="text-danger"><b>'.date('H:i', strtotime($v['datetime'])).'</b></td>';
													
												}
												
											}
											
											if(count($data) != $j) {
												
												for($x=count($data); $x<$j; $x++) {
													echo '<td></td>';
												}
												
											}
										?>
									</tr><?php
								}
							}
						}
					?>
				</tbody>
			</table>
			
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>