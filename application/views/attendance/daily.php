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

        <div class="container container-wrapper mb-4">

            <?php echo alert_get(); ?>
			
			<?php if( check_module('Attendance/Create') ) { ?>
            <div class="card mb-4">
				<div class="card-body font-weight-bold pb-0">
					Submit Attendance
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6 offset-md-3">
							<form method="post">
							
								<div class="form-group">
									<label>Card ID</label>
									<input type="text" name="rfid_cardid" class="form-control" placeholder="Scan the ID Card" required autofocus>
								</div>
								
								<div class="form-group">
									<label>Temperature</label>
									<input type="text" name="temperature" class="form-control">
								</div>

								<div class="form-group text-right">
									<button type="submit" name="save" class="btn btn-primary">Submit</button>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			
			<table class="DTable table">
                <thead>
                    <th>Date / Time</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Temp</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php /*
					if (isset($attendance)) {
						
						$i=0; foreach($attendance as $e) { $i++;
							?>
							<tr>
								<td><?php echo $e['datetime']; ?></td>
								<td><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?></td>
								<td><?php echo ucfirst(datalist_Table('tbl_users', 'type', $e['user'])); ?></td>
								<td><?php echo empty($e['temperature']) ? '-' : $e['temperature']; ?></td>
								<td><?php echo ucfirst($e['action']); ?>
								</td>
							</tr>
							<?php
						}
						
					} */	
                    ?>
					
					<?php
					if (isset($result)) {
						foreach($result as $e) {
							
							if( datalist_Table('tbl_users', 'active', $e['user']) == 1 && datalist_Table('tbl_users', 'is_delete', $e['user']) == 0 ) {
								
								$status = $e['action'] == 'in' ? 'success' : 'danger' ;
								
								?>
								<tr>
									<td><?php echo $e['datetime']; ?></td>
									<td>
										<a href="<?php echo base_url(datalist_Table('tbl_users', 'type', $e['user']).'s/edit/'.$e['user']); ?>">
											<?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']); ?>
										</a>
									</td>
									<td><?php echo ucfirst(datalist_Table('tbl_users', 'type', $e['user'])); ?></td>
									<td><?php echo empty($e['temperature']) ? '-' : $e['temperature'] . ' &#8451;'; ?></td>
									<td><?php echo '<span class="badge badge-'.$status.'">'.ucfirst($e['action']).'</span>'; ?></td>
								</tr>
								<?php
								
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