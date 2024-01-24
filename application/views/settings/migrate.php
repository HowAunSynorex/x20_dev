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

            <?php echo alert_get();?>
			
			<div class="row">
				<div class="col-md-3">
					<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
						<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="pill" href="javascript:;" data-target="#tab-1">
							Students
						</a>
						<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="pill" href="javascript:;" data-target="#tab-2">
							Parents
						</a>
						<a class="nav-link <?php if( $_GET['tab'] == 3 ) echo 'active'; ?>" data-toggle="pill" href="javascript:;" data-target="#tab-3">
							Teachers
						</a>
						<a class="nav-link <?php if( $_GET['tab'] == 4 ) echo 'active'; ?>" data-toggle="pill" href="javascript:;" data-target="#tab-4">
							Items
						</a>
						<a class="nav-link <?php if( $_GET['tab'] == 5 ) echo 'active'; ?>" data-toggle="pill" href="javascript:;" data-target="#tab-5">
							Classes
						</a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="tab-content" id="v-pills-tabContent">
						
						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
							<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">File</label>
									<div class="col-md-9">
										<input type="file" name="file" class="form-control" id="file" required accept=".xls, .xlsx">
										<a href="<?php echo base_url('uploads/files/sample-import-students.xlsx'); ?>" class="form-text small text-muted"><i class="fa fa-fw fa-download"></i> Download sample Excel file</a>
										<div style="color:red;font-size:11px;"><b>IMPORTANT !</b> The duplicate student IC details will recover the old student details</div>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="import_student" class="btn btn-primary">Import</button>
									</div>
								</div>

							</form>
						</div>
					
						<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
							<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">File</label>
									<div class="col-md-9">
										<input type="file" name="file" class="form-control" id="file" required accept=".xls, .xlsx">
										<a href="<?php echo base_url('uploads/files/sample-import-parents.xlsx'); ?>" class="form-text small text-muted"><i class="fa fa-fw fa-download"></i> Download sample Excel file</a>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="import_parent" class="btn btn-primary">Import</button>
									</div>
								</div>

							</form>
						</div>

						<div class="tab-pane fade <?php if( $_GET['tab'] == 3 ) echo 'show active'; ?>" id="tab-3">
							<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">File</label>
									<div class="col-md-9">
										<input type="file" name="file" class="form-control" id="file" required accept=".xls, .xlsx">
										<a href="<?php echo base_url('uploads/files/sample-import-teachers.xlsx'); ?>" class="form-text small text-muted"><i class="fa fa-fw fa-download"></i> Download sample Excel file</a>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="import_teacher" class="btn btn-primary">Import</button>
									</div>
								</div>

							</form>
						</div>

						<div class="tab-pane fade <?php if( $_GET['tab'] == 4 ) echo 'show active'; ?>" id="tab-4">
							<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">File</label>
									<div class="col-md-9">
										<input type="file" name="file" class="form-control" id="file" required accept=".xls, .xlsx">
										<a href="<?php echo base_url('uploads/files/sample-import-items.xlsx'); ?>" class="form-text small text-muted"><i class="fa fa-fw fa-download"></i> Download sample Excel file</a>
									</div>
								</div>
								
								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="import_item" class="btn btn-primary">Import</button>
									</div>
								</div>

							</form>
						</div>

						<div class="tab-pane fade <?php if( $_GET['tab'] == 5 ) echo 'show active'; ?>" id="tab-5">
							<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

								<div class="form-group row">
									<label class="col-form-label col-md-3 text-danger">File</label>
									<div class="col-md-9">
										<input type="file" name="file" class="form-control" id="file" required accept=".xls, .xlsx">
										<a href="<?php echo base_url('uploads/files/sample-import-classes.xlsx'); ?>" class="form-text small text-muted"><i class="fa fa-fw fa-download"></i> Download sample Excel file</a>
										
									</div>
								</div>
								
								<div class="form-group row">
									<div class="col-md-9 offset-md-3">
										<button type="submit" name="import_class" class="btn btn-primary">Import</button>
									</div>
								</div>

							</form>
						</div>
					
					</div>
				</div>
			</div>

        </div>
        
        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>