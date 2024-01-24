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
					<?php if( check_module('Parents/Delete') ) { ?>
						<div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">More</button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item d-flex justify-content-between align-items-center" href="<?php echo base_url('students/list/?parent='.$result['pid']); ?>" target="_blank"><span class="pr-2">Child(s)</span><i class="fa fa-fw fa-external-link-square-alt"></i></a>
							</div>
						</div>
					<?php } ?>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3 text-danger">Full Name</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
										<input type="hidden" name="parent" value="<?php echo $result['pid']; ?>">
                                        <input type="text" class="form-control" name="fullname_en" value="<?php echo $result['fullname_en']; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="fullname_cn" value="<?php echo $result['fullname_cn']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Nickname</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nickname" value="<?php echo $result['nickname']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">NRIC</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="nric" value="<?php echo $result['nric']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Birthday</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="birthday" value="<?php echo $result['birthday']; ?>">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="checkbox-active" name="active" <?php echo ($result['active'] == 1) ? 'checked' : ''; ?> >
                                <label class="custom-control-label" for="checkbox-active">Active</label>
                            </div>
                        </div>

                        <div class="form-group row mb-3 pb-1">
                            <label class="col-md-3 col-form-label">Gender</label>
                            <div class="col-md-9 my-auto">

                                <?php foreach(datalist('gender') as $k => $v) { ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio-gender-<?php echo $k; ?>" name="gender" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $result['gender'] == $k ) { echo 'checked'; }  ?>>
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
								<img src="<?php echo empty($result['image']) ? 'https://cdn.synorex.link/assets/images/blank/4x3.jpg' : datalist_Table('tbl_uploads', 'file_source', $result['image']); ?>" class="mb-3 rounded-circle border" style="height: 85px; width: 85px; object-fit: cover">
								<input type="file" class="form-control" name="image">
                            </div>
						</div>
					</div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row section-portal">
                            <label class="col-form-label col-md-3 <?php if($result['username'] != '') echo 'text-danger'; ?>" id="username-text">Username</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $result['username']; ?>" <?php if($result['username'] == '') echo 'readonly'; ?>>
								<span id="check_username" class="d-none form-text font-weight-bold small text-danger">Username has been taken</span>
							</div>
                        </div>

                        <div class="form-group row section-portal">
                            <label class="col-form-label col-md-3" id="password-text">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="password" name="password" <?php if($result['username'] == '') echo 'readonly'; ?>>
								<span id="default_password" class="d-none form-text font-weight-bold small text-muted">Default password: 123</span>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox mt-1">
                                <input onchange="check(this);" type="checkbox" class="custom-control-input" id="checkbox-portal" <?php if($result['username'] != '') echo 'checked'; ?>>
                                <label class="custom-control-label" for="checkbox-portal">Allow app access for this parent</label>
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
                                <input type="tel" class="form-control" name="phone" placeholder="Primary" value="<?php echo $result['phone']; ?>">
                                <input type="tel" class="form-control mt-2" name="phone2" value="<?php echo $result['phone2']; ?>">
                                <input type="tel" class="form-control mt-2" name="phone3" value="<?php echo $result['phone3']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Email</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" name="email" placeholder="Primary" value="<?php echo $result['email']; ?>">
                                <input type="email" class="form-control mt-2" name="email2" value="<?php echo $result['email2']; ?>">
                                <input type="email" class="form-control mt-2" name="email3" value="<?php echo $result['email3']; ?>">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Address</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="address" placeholder="Primary" value="<?php echo $result['address']; ?>">
                                <input type="text" class="form-control mt-2" name="address2" value="<?php echo $result['address2']; ?>">
                                <input type="text" class="form-control mt-2" name="address3" value="<?php echo $result['address3']; ?>">
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="mb-4">

                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Join Date</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_join" value="<?php echo $result['date_join']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Remark</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="remark" rows="4"><?php echo $result['remark']; ?></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="save" class="btn btn-primary">Save</button>
                                <a href="<?php echo base_url($thispage['group'] . '/list'); ?>" class="btn btn-link text-muted">Cancel</a>
								<?php if( check_module('Parents/Delete') ) { ?>
									<button type="button" name="save" class="btn btn-danger" onclick="del_ask(<?php echo $result['pid']; ?>)">Delete</button>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
				
				<ul class="nav nav-tabs mt-3">
				
					<li class="nav-item">
						<a class="nav-link" target="_blank" href="<?php echo base_url('students/list?parent=' . $result['pid']) ?>">Child(s) <i class="fa fa-fw fa-external-link-square-alt"></i></a>
					</li>
					
				</ul>

            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Parents/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>