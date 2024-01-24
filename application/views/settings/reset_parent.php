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
            
            <form method="post">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<button type="button" onclick="del_ask()" class="btn btn-primary">Reset</button>
						</div>
					</div>
                </div>
            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>