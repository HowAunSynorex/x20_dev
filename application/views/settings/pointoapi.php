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
			
			<div class="alert alert-info" role="alert">
				PointoAPI will control your WhatsApp Notify, Email Notify, Upload Storage and other features. Launch <a href="https://pointoapi.synorexcloud.com/certs?pg=list" target="_blank">PointoAPI <i class="fa fa-fw fa-external-link-square-alt"></i></a>
			</div>
            
			<div class="alert alert-warning">
				Some charges may be applied due to the usage of this function
			</div>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-md-3">API Key</label>
                            <div class="col-md-9">
								<div class="input-group">
									<input type="text" class="form-control" name="pointoapi_key" value="<?php echo $result[0]['pointoapi_key']; ?>" onchange="check_pointoapi(this.value)" onkeyup="$('.btn-primary').attr('disabled', true)">
									<div class="input-group-append">
										<button class="btn btn-secondary" type="button">Check</button>
									</div>
								</div>
								<span class="small form-text text-muted d-none" id="status-check">Loading...</span>
							</div>
                        </div>
                    </div>
				</div>
					
				<div class="row">
					<div class="col-md-6">
						<div class="form-group row">
							<div class="col-md-9 offset-md-3">
								<button type="submit" name="save" class="btn btn-primary">Save</button>
							</div>
						</div>
					</div>
                </div>

            </form>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
	<?php if( !check_module('Settings/Update') ) { ?>
	var access_denied = true;
	<?php } ?>
</script>