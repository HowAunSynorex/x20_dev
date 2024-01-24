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

				<div class="alert alert-warning">Are you sure you want to perform this operation?</div>
				
				<pre class="border rounded p-2 bg-light"><?php echo $output; ?></pre>

				<?php
				
				foreach($_POST['bulk'] as $e) echo '<input type="hidden" name="bulk[]" value="'.$e.'">';
				echo '<input type="hidden" name="action" value="'.$action.'">';
				?>
				
                <button type="submit" name="yes" class="btn btn-success">Yes</button>
                <a href="<?php echo base_url('students/list'); ?>" class="btn btn-danger">No</a>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>