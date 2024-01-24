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
            
            <form method="post" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Full Name</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="fullname_en" placeholder="English" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="fullname_cn" placeholder="中文 (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Nickname</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nickname">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">NRIC / Birth Cert No.</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nric">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Birthday</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="birthday">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" checked>
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>

                        <div class="form-group row mb-3 pb-1">
                            <label class="col-md-3 col-form-label">Gender</label>
                            <div class="col-md-9 my-auto">
                                
                                <?php foreach(datalist('gender') as $k => $v) { ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio-gender-<?php echo $k; ?>" name="gender" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $k == 'male' ) echo 'checked'; ?>>
                                    <label class="custom-control-label" for="radio-gender-<?php echo $k; ?>"><?php echo $v; ?></label>
                                </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
					
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Avatar</label>
                            <div class="col-md-9">
								<img src="https://cdn.synorex.link/assets/images/blank/4x3.jpg" class="mb-3 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
								<input type="file" class="form-control" name="image">
                            </div>
						</div>
					</div>
					
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row section-portal">
                            <label class="col-form-label col-md-3" id="username-text">Username</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="username" name="username" readonly>
								<span id="check_username" class="d-none form-text font-weight-bold small text-danger">Username has been taken</span>
                            </div>
                        </div>

                        <div class="form-group row section-portal">
                            <label class="col-form-label col-md-3" id="password-text">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="password" name="password" readonly>
								<span id="default_password" class="d-none form-text font-weight-bold small text-muted">Default password: 123</span>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input onchange="check(this);" type="checkbox" class="custom-control-input" id="checkbox-portal">
                                <label class="custom-control-label" for="checkbox-portal">Allow app access for this student</label>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Phone</label>
                            <div class="col-md-9">
                                <input type="tel" class="form-control" name="phone" placeholder="Primary">
                                <input type="tel" class="form-control mt-2" name="phone2">
                                <input type="tel" class="form-control mt-2" name="phone3">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Email</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" name="email" placeholder="Primary">
                                <input type="email" class="form-control mt-2" name="email2">
                                <input type="email" class="form-control mt-2" name="email3">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Address</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="address" placeholder="Primary">
                                <input type="text" class="form-control mt-2" name="address2">
                                <input type="text" class="form-control mt-2" name="address3">
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Code</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="code">
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Join Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_join">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Card ID</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="rfid_cardid" name="rfid_cardid">
								<span id="check_cardid" class="d-none form-text font-weight-bold small text-danger">Card ID has been taken</span>
                            </div>
                        </div>
					</div>
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">School</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="school">
                                    <option value="">-</option>';

                                    <?php foreach ($school as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                                    };?>

                                </select>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Form</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="form">
                                    <option value="">-</option>';
                                    <?php foreach ($form as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                                    };?>
                                </select>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Guardian</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="guardian">
                            </div>
                        </div>
                        
					</div>
				</div>
				
                <hr class="mb-4">
						
                <div class="row">
                    <div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Transportation</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="transport">
                                    <option value="">-</option>';

                                    <?php foreach ($transport as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                                    };?>
									
									 <?php foreach ($branch_transport as $e) {
                                        echo '<option value="' . $e['secondary'] . '">' . datalist_Table('tbl_secondary', 'title', $e['secondary']) . '</option>';
                                    };?>

                                </select>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Car Plate No</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="car_plate_no">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Address Pick-Up</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="address_pickup" rows="3"></textarea>
                            </div>
                        </div>
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Address Drop-Off</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="address_dropoff" rows="3"></textarea>
                            </div>
                        </div>

                        <!--<div class="form-group row">
                            <label class="col-form-label col-md-3">Parent</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="parent">
                                    <option value="">-</option>';

                                    <?php foreach ($parent as $e) {
                                        echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
                                    };?>
                                    
                                </select>
                            </div>
                        </div>-->
                    </div>
                </div>
				
				<hr class="mb-4">
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Childcare</label>
							<div class="col-md-9">
								<select class="form-control select2" name="childcare">
									<option value="">-</option>';
									<?php foreach ($childcare as $e) {
										echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
									};?>
								</select>
							</div>
                        </div>
						<div class="form-group row">
							<label class="col-form-label col-md-3">Childcare Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="childcare_teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) {
										echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
									};?>
								</select>
							</div>
                        </div>
					</div>
					<div class="col-md-6">
						<div class="form-group row">
							<label class="col-form-label col-md-3">Form Teacher</label>
							<div class="col-md-9">
								<select class="form-control select2" name="form_teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) {
										echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
									};?>
								</select>
							</div>
                        </div>
					</div>
				</div>
				
				<hr class="mb-4">

                <div class="row">
					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark/Attention</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"></textarea>
                            </div>
                        </div>
						
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark (Active Subject)</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark_active" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6">
						<div class="form-group row">
                            <label class="col-form-label col-md-3">Remark (Important/Urgent)</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark_important" rows="4"></textarea>
                            </div>
                        </div>
					</div>
                </div>
				
				<div class="row">
					<div class="col-md-12">
						<?php foreach(datalist('student_question') as $k => $v) { 
						?>
							<div class="form-group row">
								<label class="col-form-label col-md-3"><?php echo $v; ?></label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="question[<?php echo $k; ?>]" value="" />
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
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>