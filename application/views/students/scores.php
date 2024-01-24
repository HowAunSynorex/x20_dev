<?php

use function PHPSTORM_META\type;

defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
			         
            <div class="row">
                <div class="col-md-8 mb-4">
                    
                    <form method="post">
						<input type="hidden" name="student" value="<?php echo $_GET['student']; ?>" />
						<input type="hidden" name="exam" value="<?php echo $_GET['exam']; ?>" />
						<input type="hidden" name="exam_date" value="<?php echo $_GET['exam_date']; ?>" />
						<table class="DTable2 table table-sm table-bordered table-striped">
							<thead>
								<th>Subject</th>
								<th class="text-right" style="width:25%">Score</th>
							</thead>
							<tbody>
								<?php $i = 0;
									foreach($subject as $e) { 
									$subject_score = search($result, 'subject', $e)
								?>
									<tr>
										<td>
											<?php echo $e ; ?>
											<input type="hidden" name="exam_score[<?php echo $i; ?>][subject]" value="<?php echo $e; ?>" />
										</td>
										<td><input type="number" class="form-control" name="exam_score[<?php echo $i; ?>][score]" value="<?php echo count($subject_score) > 0 ? $subject_score[0]['score'] : ''; ?>" /></td>
									</tr>
								<?php $i++; } ?>
							</tbody>
							<?php if (count($subject) > 0) { ?>
								<tfoot>
									<tr>
										<td></td>
										<td class="text-right">
											<button type="submit" name="save" class="btn btn-primary">Save</button>
										</td>
									</tr>
								</tfoot>
							<?php } ?>
						</table>
						
                    </form>
                </div>

                <div class="col-md-4 mb-4">
                    <form>
                    
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Student</label>
                            <div class="col-md-9">
                                <select class="form-control select2" id="student" name="student" required>
                                    <option value="">-</option>
                                    <?php foreach ($student as $e) { ?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if( $e['pid'] == $_GET['student'] ) echo 'selected'; ?>><?php echo $e['fullname_cn'].' '.$e['fullname_en']; ?></option>
                                    <?php } ?>
                                </select>
                                <?php if( empty($_GET['student']) ) { ?>
                                <div class="alert alert-warning mt-2">Please select a student</div>
                                <?php } ?>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Exam</label>
                            <div class="col-md-9">
                                <select class="form-control select2" id="exam" name="exam" required>
                                    <option value="">-</option>
                                    <?php foreach ($exam as $e) { ?>
                                    <option value="<?php echo $e['pid']; ?>" <?php if( $_GET['exam'] == $e['pid'] ) echo 'selected'; ?>><?php echo $e['title'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Date</label>
                            <div class="col-md-9">
								<input type="date" class="form-control" name="exam_date" value="<?php echo $_GET['exam_date']; ?>" />
                            </div>
                        </div>
						
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
						

                    </form>
                    
                </div>
            </div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Point</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
			
				<input type="hidden" name="id">
                
				<div class="form-group">
					<label>Credit</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" style="font-size: 14px;">$</span>
						</div>
						<input type="number" class="form-control" name="amount_1" step="0.01">
						<input type="hidden" class="form-control" name="id">
					</div>
				</div>
				
				<div class="form-group">
					<label>Debit</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" style="font-size: 14px;">$</span>
						</div>
						<input type="number" class="form-control" name="amount_0" step="0.01">
					</div>
				</div>
				
				<div class="form-group payment-sec d-none">
                    <label>Payment No</label>
					<p>23123</p>
				</div>
				
				<div class="form-group">
					<label>Reason</label>
					<textarea class="form-control" name="title" rows="4"></textarea>
				</div>
				
				<div class="form-group">
					<label>Remark</label>
					<textarea class="form-control" name="remark" rows="4"></textarea>
				</div>
				
            </div>
            <div class="modal-footer">
				<?php if( check_module('Points/Delete') ) { ?>
					<input type="hidden" name="id">
					<a onclick="del_ask('<?php echo $thispage['type']; ?>')" name="delete" class="btn btn-link text-danger px-0 mr-auto">Delete</a>
				<?php } ?>
                <button type="submit" name="edit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
