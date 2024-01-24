<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold">
						<?php echo $thispage['title']; ?>
					</h4>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
				
			<div class="table-responsive">
				<table class="DTable table table-hover">
					<thead>
						<th>No</th>
						<th>Student</th>
						<th>Phone</th>
						<th>Address</th>
						<th>Email</th>
						<th>School</th>
						<th>Parent</th>
						<th>Class</th>
						<th>Receipt</th>
						<th></th>
					</thead>
					<tbody>
						<?php $i=0; foreach($result as $e) { $i++; ?>
						<tr>
							<td><?php echo $i; ?></td>
							<td>
								Nickname: <?php echo empty($e['nickname']) ? '-' : $e['nickname']; ?><br>
								Fullname (English): <?php echo $e['fullname_en']; ?><br>
								Fullname (Chinese): <?php echo empty($e['fullname_cn']) ? '-' : $e['fullname_cn']; ?><br>
								Gender: <?php echo ucfirst($e['gender']); ?><br>
								NRIC: <?php echo empty($e['nric']) ? '-' : $e['nric']; ?><br>
								Birthday: <?php echo empty($e['birthday']) ? '-' : $e['birthday']; ?>
							</td>
							
							<td>
								<?php echo empty($e['phone']) ? '-' : $e['phone']; ?><br>
								<?php echo empty($e['phone2']) ? '-' : $e['phone2']; ?><br>
								<?php echo empty($e['phone3']) ? '-' : $e['phone3']; ?>
							</td>
							<td>
								<?php echo empty($e['address']) ? '-' : $e['address']; ?><br>
								<?php echo empty($e['address2']) ? '-' : $e['address2']; ?><br>
								<?php echo empty($e['address3']) ? '-' : $e['address3']; ?>
							</td>
							<td>
								<?php echo empty($e['email']) ? '-' : $e['email']; ?><br>
								<?php echo empty($e['email2']) ? '-' : $e['email2']; ?><br>
								<?php echo empty($e['email3']) ? '-' : $e['email3']; ?>
							</td>
							<td><?php echo empty($e['school']) ? '-' : datalist_Table('tbl_secondary', 'title', $e['school']); ?></td>
							<td>
								Name: <?php echo empty($e['parent']) ? '-' : $e['parent']; ?><br>
								Phone: <?php echo empty($e['temp_parent_phone']) ? '-' : $e['temp_parent_phone']; ?><br>
								Gender: <?php echo empty($e['temp_parent_gender']) ? '-' : ucfirst($e['temp_parent_gender']); ?><br>
								Relationship: <?php echo empty($e['temp_parent_relationship']) ? '-' : $e['temp_parent_relationship']; ?>
							</td>
							<td>
								<?php
								if(empty($e['temp_class'])) {
									echo '-';
								} else {
									$temp_class = json_decode($e['temp_class'], true);
									if(!is_array($temp_class)) $temp_class = [];
									foreach($temp_class as $tc) {
										echo datalist_Table('tbl_classes', 'title', $tc) . '<br>';
									}
								}
								?>
							</td>
							<td>
								<?php
								if(empty($e['receipt'])) {
									echo '-';
								} else {
									?>
									<a href="<?php echo pointoapi_UploadSource(datalist_Table('tbl_users', 'receipt', $e['pid'])); ?>" target="_blank"><img src="<?php echo pointoapi_UploadSource(datalist_Table('tbl_users', 'receipt', $e['pid'])); ?>" style="width: 100px; object-fit: cover;"></a>
									<?php
								}
								?>
							</td>
							<td>
								<button type="button" class="btn btn-success btn-sm" onclick="approve(<?php echo $e['pid']; ?>)">Approve</button>
								<button type="button" class="btn btn-danger btn-sm" onclick="reject(<?php echo $e['pid']; ?>)">Reject</button>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>