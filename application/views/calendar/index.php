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
					<?php if( check_module('Calendar/Create') ) { ?>
						<a href="javascript:;" data-toggle="modal" data-target="#modal-add" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
					<?php } ?>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <div class="row">

            	<div class="col-xl-2 col-md-3 pr-0">
            		<?php

            		foreach([
            			'class' => [
            				'label' => 'Classes',
            				'color' => '#7986CB',
            			],
            			'holiday' => [
            				'label' => 'Holidays',
            				'color' => '#009688',
            			],
            			'birthday' => [
            				'label' => 'Birthday',
            				'color' => '#EF6C00',
            			],
            			'event' => [
            				'label' => 'Events',
            				'color' => '#3F51B5',
            			],
            		] as $k => $e) {
            			
						$isOK = 0;
						
						switch($k) {
							
							case 'class':
								if( check_module('Classes/Read') ) $isOK = 1;
								break;
							
							case 'birthday':
								if( check_module('Students/Read') ) $isOK = 1;
								break;
							
							default:
								$isOK = 1;
							
						}
						
						if($isOK == 1) {
							
							?>
							<div class="custom-control custom-checkbox mb-2 event-checkbox-<?php echo $k; ?>">
								<input type="checkbox" class="custom-control-input" id="checkbox-<?php echo $k; ?>" value="1" onchange="search()" <?php if( isset($_GET[ $k ]) || !isset($_GET['q']) ) echo 'checked'; ?>>
								<label class="custom-control-label" for="checkbox-<?php echo $k; ?>"><?php echo $e['label']; ?></label>
							</div>
							<?php
							
						}
						
            		}
            		?>
                    <h6 class="py-2 mb-0 font-weight-bold">Teacher</h6>
					<select class="form-control select2 mt-2" name="teacher" onchange="search()">
						<option value="">-</option>
						<?php
						foreach ($teachers as $e)
						{
							?>
							<option value="<?php echo $e['pid']; ?>" <?php echo (isset($_GET['teacher'])) ? (($_GET['teacher'] == $e['pid']) ? 'selected' : '') : '' ; ?>><?php echo $e['fullname_en']; ?></option>
							<?php
						} ?>
					</select>
            	</div>

            	<div class="col-xl-10 col-md-9">
            		<div id="calendar" class="mb-4"></div>
            	</div>

            </div>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="form-group">
                    <label class="text-danger">Title</label>
                    <input type="text" class="form-control" name="title" required>
                    <?php foreach(datalist('event_type') as $k => $v) { ?>
                    <div class="custom-control custom-radio custom-control-inline mt-2">
                        <input type="radio" id="radio-type-<?php echo $k; ?>" name="type" class="custom-control-input" value="<?php echo $k; ?>" <?php if( 'event' == $k ) { echo 'checked'; }  ?>>
                        <label class="custom-control-label" for="radio-type-<?php echo $k; ?>"><?php echo $v['single']; ?></label>
                    </div>
                    <?php } ?>
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
