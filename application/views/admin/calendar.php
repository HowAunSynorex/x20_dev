<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

    <div id="page-content-wrapper">

    	<div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
                <div class="col-6 my-auto text-right">
                    <a href="javascript:;" data-toggle="modal" data-target="#modal-add" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <div class="row">

            	<div class="col-xl-2 col-md-3">
            		<?php

            		foreach([
            			'holiday' => [
            				'label' => 'Holidays',
            				'color' => '#009688',
            			],
            		] as $k => $e) {
            			?>
            			<div class="custom-control custom-checkbox mb-2 event-checkbox-<?php echo $k; ?>">
							<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $k; ?>" value="1" onchange="search()" <?php if( isset($_GET[ $k ]) || !isset($_GET['q']) ) echo 'checked'; ?>>
							<label class="custom-control-label" for="checkbox-<?php echo $k; ?>"><?php echo $e['label']; ?></label>
						</div>
            			<?php
            		}
            		?>
            	</div>

            	<div class="col-xl-10 col-md-9">
				
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 1 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-1">Calendar</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if( $_GET['tab'] == 2 ) echo 'active'; ?>" data-toggle="tab" href="javascript:;" data-target="#tab-2">List</a>
						</li>
					</ul>
					
					<div class="tab-content py-3">

						<div class="tab-pane fade <?php if( $_GET['tab'] == 1 ) echo 'show active'; ?>" id="tab-1">
							<div id="calendar" class="mb-4"></div>
						</div>
						
						<div class="tab-pane fade <?php if( $_GET['tab'] == 2 ) echo 'show active'; ?>" id="tab-2">
							<table class="DTable table">
								<thead>
									<th style="width: 10%;">No</th>
									<th style="width: 35%;">Title</th>
									<th>Type</th>
									<th>Start Date</th>
									<th>End Date</th>
								</thead>
								<tbody>
									<?php $i=0; foreach($result as $e) { $i++; ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><a href="<?php echo base_url('calendar/edit/'.$e['pid']); ?>"><?php echo ucfirst($e['title']); ?></a></td>
											<td><?php echo ucfirst($e['type']); ?></td>
											<td><?php echo $e['date_start']; ?></td>
											<td><?php echo empty($e['date_end']) ? '-' : $e['date_end']; ?></td>
										</tr>
									<?php } ?>
								<tbody>
							</table>
						</div>
						
					</div>
						
            	</div>

            </div>

        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="form-group">
                    <label class="text-danger">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>

                <div class="form-group" id="section-date_start">
                    <label datal-label="label">Date</label>
                    <input type="date" class="form-control" name="date_start" value="<?php echo date('Y-m-d'); ?>" data-end="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input" id="checkbox-same_day" value="1" checked>
                        <label class="custom-control-label" for="checkbox-same_day">Same Day</label>
                    </div>
                </div>

                <div class="form-group d-none" id="section-date_end">
                    <label>End</label>
                    <input type="date" class="form-control" name="date_end">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="4" name="remark"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" name="save" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
