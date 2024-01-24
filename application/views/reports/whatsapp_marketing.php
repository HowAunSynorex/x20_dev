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
			
			<form method="get">
				<div class="row">
					<div class="col-md-6">
					
						<div class="form-group row">
							<label class="col-form-label col-md-3">Message</label>
							<div class="col-md-9">
								<textarea class="form-control" rows="4" name="message" required><?php if(isset($_GET['message'])) echo $_GET['message']; ?></textarea>
							</div>
						</div>
						
						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" class="btn btn-primary">Run</button>
							</div>
						</div>
						
					</div>
				</div>
			</form>
            
			<div class="table-responsive">
				<table class="DTable table table-bordered">
					<thead>
						<th>No</th>
						<th>Parent</th>
						<th>Phone</th>
						<?php if(isset($_GET['message'])) { ?>
							<th></th>
						<?php } ?>
					</thead>
					<tbody> 
						<?php $i=0; foreach($result as $e) { $i++; ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $e['fullname_en']; ?></td>
								<td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
								<?php if(isset($_GET['message'])) { ?>
									<td><a href="https://wa.me/<?php echo $e['phone']; ?>?text=<?php echo $_GET['message']; ?>" target="_blank" class="btn btn-success"><i class="fab fa-whatsapp"></i></a></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>