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
					
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
			<div class="modal" id="flowModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title">Insurance Flow</h5>

					</div>
						<form method="post">  
							<div class="modal-body">
								<img src="<?= base_url('/uploads/files/insurance_flow.png');?>" width="100%">
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" onclick="next();">Next</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			

			<div class="modal " id="agreeModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">School Personal Accident</h5>

						</div>
						<form method="post">  
							<div class="modal-body">
								<embed src="<?= base_url('/uploads/files/school_personal_ccident.pdf');?>" frameborder="0" width="100%" height="500px">

								<div class="custom-control custom-checkbox">
									<input type="checkbox" id="blankRadio1" name="agree1" class="custom-control-input" value="agree">
									<label class="custom-control-label" for="blankRadio1">I agreed and read the Tuition Insurance Policy</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" id="blankRadio2" name="agree2" class="custom-control-input" value="agree">
									<label class="custom-control-label" for="blankRadio2">I agreed the Terms and Conditions</label>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" onclick="back();">Back</button>
								<button class="btn btn-primary" type="submit" name="save">Agree</button>
							</div>
						</form>
					</div>
				</div>
			</div>
				
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

