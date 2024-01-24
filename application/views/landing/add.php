<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)">

    <div class="container py-2">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                
                <?php echo alert_get(); ?>
                
                <img src="<?php echo empty($branch_img) ? 'https://cdn.synorex.link/assets/images/robocube/tuition.png' : pointoapi_UploadSource($branch_img); ?>" class="d-block mx-auto mb-3 w-25">
                <h5 class="text-center mb-3 font-weight-bold mx-auto"><?php echo $thispage['title']; ?></h5>
                
                <div class="card mb-3">
                    <div class="card-header">Language</div>
                    <div class="card-body">
                        <div id="google_translate_element"></div>
                    </div>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header">Student Details</div>
                    <div class="card-body">
                        
                        <div class="form-group row">
        					<label class="col-form-label col-md-4 text-danger">Name</label>
        					<div class="col-md-8">
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
        					<label class="col-form-label col-md-4">NRIC</label>
        					<div class="col-md-8">
        						<input type="text" class="form-control" name="nric" onchange="ic_check(this.value)">
        					</div>
        				</div>
        
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Birthday</label>
        					<div class="col-md-8">
        						<input type="date" class="form-control" name="birthday">
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Phone</label>
        					<div class="col-md-8">
        						<input type="tel" class="form-control" name="phone">
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Address</label>
        					<div class="col-md-8">
        						<input type="text" class="form-control" name="address" placeholder="Primary">
        						<input type="text" class="form-control mt-2" name="address2">
        						<input type="text" class="form-control mt-2" name="address3">
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-md-4 col-form-label">Gender</label>
        					<div class="col-md-8 my-auto">
        						
        						<?php foreach(datalist('gender') as $k => $v) { ?>
        						<div class="custom-control custom-radio custom-control-inline">
        							<input type="radio" id="radio-gender-<?php echo $k; ?>" name="gender" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $k == 'male' ) echo 'checked'; ?>>
        							<label class="custom-control-label" for="radio-gender-<?php echo $k; ?>"><?php echo $v; ?></label>
        						</div>
        						<?php } ?>
        
        					</div>
        				</div>
                        
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">Parent Details</div>
                    <div class="card-body">
					
						<h5>Parent 1</h5><hr>
                        
                        <div class="form-group row">
        					<label class="col-form-label col-md-4">Name</label>
        					<div class="col-md-8">
        						<div class="row">
        							<div class="col-md-6">
        								<input type="text" class="form-control" name="parent" placeholder="English" required>
        							</div>
        							<div class="col-md-6">
        								<input type="text" class="form-control" name="parent_cn" placeholder="中文 (Optional)">
        							</div>
        						</div>
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Phone (Parent)</label>
        					<div class="col-md-8">
        						<input type="tel" class="form-control" name="temp_parent_phone">
        					</div>
        				</div>
        				
        				<div class="form-group row mb-3 pb-1">
        					<label class="col-md-4 col-form-label">Gender (Parent)</label>
        					<div class="col-md-8 my-auto">
        						
        						<?php foreach(datalist('gender') as $k => $v) { ?>
        						<div class="custom-control custom-radio custom-control-inline">
        							<input type="radio" id="radio-parent-gender-<?php echo $k; ?>" name="temp_parent_gender" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $k == 'male' ) echo 'checked'; ?>>
        							<label class="custom-control-label" for="radio-parent-gender-<?php echo $k; ?>"><?php echo $v; ?></label>
        						</div>
        						<?php } ?>
        
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Relationship</label>
        					<div class="col-md-8">
        						<input type="tel" class="form-control" name="temp_parent_relationship">
        					</div>
        				</div>
        			
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Remark (Important/Urgent)</label>
        					<div class="col-md-8">
        						<textarea class="form-control" name="remark_important" rows="4"></textarea>
        					</div>
        				</div>
                        
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Remark/Attention</label>
        					<div class="col-md-8">
        						<textarea class="form-control" name="remark" rows="4"></textarea>
        					</div>
        				</div>
						
						<h5>Parent 2</h5><hr>
                        
                        <div class="form-group row">
        					<label class="col-form-label col-md-4">Name</label>
        					<div class="col-md-8">
        						<div class="row">
        							<div class="col-md-6">
        								<input type="text" class="form-control" name="parent2" placeholder="English" required>
        							</div>
        							<div class="col-md-6">
        								<input type="text" class="form-control" name="parent_cn2" placeholder="中文 (Optional)">
        							</div>
        						</div>
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Phone (Parent)</label>
        					<div class="col-md-8">
        						<input type="tel" class="form-control" name="temp_parent_phone2">
        					</div>
        				</div>
        				
        				<div class="form-group row mb-3 pb-1">
        					<label class="col-md-4 col-form-label">Gender (Parent)</label>
        					<div class="col-md-8 my-auto">
        						
        						<?php foreach(datalist('gender') as $k => $v) { ?>
        						<div class="custom-control custom-radio custom-control-inline">
        							<input type="radio" id="radio-parent-gender-<?php echo $k; ?>-2" name="temp_parent_gender2" class="custom-control-input" value="<?php echo $k; ?>" <?php if( $k == 'male' ) echo 'checked'; ?>>
        							<label class="custom-control-label" for="radio-parent-gender-<?php echo $k; ?>-2"><?php echo $v; ?></label>
        						</div>
        						<?php } ?>
        
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Relationship</label>
        					<div class="col-md-8">
        						<input type="tel" class="form-control" name="temp_parent_relationship2">
        					</div>
        				</div>
        			
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Remark (Important/Urgent)</label>
        					<div class="col-md-8">
        						<textarea class="form-control" name="remark_important2" rows="4"></textarea>
        					</div>
        				</div>
                        
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Remark/Attention</label>
        					<div class="col-md-8">
        						<textarea class="form-control" name="remark2" rows="4"></textarea>
        					</div>
        				</div>
        				
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">問卷調查:</div>
                    <div class="card-body">
                        
                        <?php foreach(datalist('student_question') as $k => $v) {  ?>
        					<div class="form-group row">
        						<label class="col-form-label col-md-4"><?php echo $v; ?></label>
        						<div class="col-md-8">
        							<input type="text" class="form-control" name="question[<?php echo $k; ?>]" value="" />
        						</div>
        					</div>
        				<?php } ?>
                        
                    </div>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header">Class Enrollment</div>
                    <div class="card-body">
                        
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Course</label>
        					<div class="col-md-8">
        						<select class="form-control select2" name="course" id="course" onchange="load_class(this.value)" >
        							<option value="">-</option>';
        							<?php foreach ($course as $e) {
        								echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
        							};?>
        						</select>
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">School</label>
        					<div class="col-md-8">
        						<select class="form-control select2" name="school" required>
        							<option value="">-</option>
        							<?php foreach($school as $e) { ?>
        								<option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
        							<?php } ?>
        						</select>
        					</div>
        				</div>
        				
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Class</label>
        					<div class="col-md-8">
        						<table id="class-table" class="table table-bordered">
        							<tbody>
        								<?php foreach($class as $e) { ?>
        									<tr style="display: none;" id="class-<?php echo($e['course'] !='')?$e['course']:0; ?>">
        										<td>
        											<div class="custom-control custom-checkbox">
        												<input onchange="cal_class();" type="checkbox" class="custom-control-input class-check" id="checkbox-<?php echo $e['pid']; ?>" value="<?php echo $e['pid']; ?>" name="temp_class[]">
        												<label class="custom-control-label" for="checkbox-<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></label>
        											</div>
        										</td>
        										<td class="class-fee">
        											<?php echo $e['fee']; ?>
        										</td>
        									</tr>
        								<?php } ?>
        							</tbody>
        							<tfoot>
        								<tr>
        									<th width="70%">Total (RM)</th>
        									<th width="30%" id="class-total"></th>
        								</tr>
        							</tfoot>
        						</table>
        					</div>
    					</div>
                        
                    </div>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header">Childcare</div>
                    <div class="card-body">
                        
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkbox-childcare">
                            <label class="custom-control-label" for="checkbox-childcare">Enable</label>
                        </div>
                        
        				<div class="form-group row">
        					<label class="col-form-label col-md-4">Childcare</label>
        					<div class="col-md-8">
        						<select class="form-control select2" name="childcare" id="childcare" onchange="cal_class();">
									<option value="" data-price=0>-</option>';
									<?php foreach ($childcare as $e) {
										echo '<option value="' . $e['pid'] . '" data-price = "'. $e['price'] .'">' . $e['title'] . '</option>';
									};?>
								</select>
        					</div>
        				</div>
        				
						<div class="form-group row">
							<label class="col-form-label col-md-4">Childcare Teacher</label>
							<div class="col-md-8">
								<select class="form-control select2" name="childcare_teacher">
									<option value="">-</option>';
									<?php foreach ($teacher as $e) {
										echo '<option value="' . $e['pid'] . '">' . $e['fullname_en'] . '</option>';
									};?>
								</select>
							</div>
                        </div>
                        
						<div class="form-group row">
							<label class="col-form-label col-md-4">Form Teacher</label>
							<div class="col-md-8">
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
        
                <div class="card mb-3">
                    <div class="card-header">Transportation</div>
                    <div class="card-body">
                        
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkbox-transportation">
                            <label class="custom-control-label" for="checkbox-transportation">Enable</label>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Transportation</label>
                            <div class="col-md-8">
                                <select class="form-control select2" name="transport" id="transport" onchange="cal_class();">
                                    <option value="" data-price=0>-</option>';

                                    <?php foreach ($transport as $e) {
                                        echo '<option value="' . $e['pid'] . '" data-price = "'. $e['price'] .'">' . $e['title'] . '</option>';
                                    };?>
									
									 <?php foreach ($branch_transport as $e) {
                                        echo '<option value="' . $e['secondary'] . '" data-price = "'. $e['price'] .'">' . datalist_Table('tbl_secondary', 'title', $e['secondary']) . '</option>';
                                    };?>

                                </select>
                            </div>
                        </div>
        				
                    </div>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header border-0">Fees Details</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tr>
                                <td>Tuition</td>
                                <td id="total-tuition">0</td>
                            </tr>
                            <tr>
                                <td>Material</td>
                                <td id="total-material">0</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td id="total-discount">0</td>
                            </tr>
                            <tr>
                                <td>Transportation</td>
                                <td id="total-transportation">0</td>
                            </tr>
                            <tr>
                                <td>Childcare</td>
                                <td id="total-childcare">0</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td id="total">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
        
                <div class="card mb-3">
                    <div class="card-header">Payment</div>
                    <div class="card-body">
                        
                        <div class="form-group row">
        					<label class="col-form-label col-md-4">Reference Code</label>
        					<div class="col-md-8">
        						<input type="text" class="form-control" name="ref_code">
        					</div>
        				</div>
        				
        				<div class="form-group row">
    						<label class="col-form-label col-md-4">Upload Receipt</label>
    						<div class="col-md-8">
    							<input type="file" class="form-control" name="receipt">
    						</div>
    					</div>
                        
                    </div>
                </div>
                
                <button type="submit" name="save" class="btn btn-block btn-primary">Submit</button>
				<button type="button" onclick="reset_ask()" class="btn btn-block btn-secondary">Reset</button>
        
            </div>
        </div>
        
    </div>
    
</form>

<p class="text-muted small text-center mt-4"><?php echo date('Y'); ?> &copy; <?php echo app('title'); ?>. All right reserved.<br>Powered by <b>Synorex</b></p>
