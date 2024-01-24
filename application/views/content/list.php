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
                    <?php
					if( check_module('Content/Create') ) {
						if($thispage['type'] == 'announcement') {
							?>

							<a href="<?php echo base_url($thispage['group'] . '/add/' . $thispage['type']); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a><?php

						} else { ?>

							<a href="javascript:;" data-toggle="modal" data-target="#modal-add" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a><?php

						}
					}
                    ?>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
			
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
					<?php
					if($thispage['type'] == 'announcement') {
						?>
						<th style="width:20%">Title</th><?php
					} else { ?>
						<th style="width:20%">Image</th><?php
					}
					?>
                    <th style="width:10%">Status</th>
                    <th>Author</th>
                    <th>Create On</th>
					<?php
					if($thispage['type'] == 'slideshow') {
						if( check_module('Content/Delete') ) {
							?>
							<th></th><?php
						}
					} ?>
                </thead>
                <tbody>
                    <?php
                    $i=0; foreach($result as $e) { $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <?php
                            if($e['type'] == 'announcement') {
                                ?>
                                <td>
									<?php if( check_module('Content/Update') ) { ?>
										<a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
									<?php } ?>
										<?php echo $e['title']; ?>
									<?php if( check_module('Content/Update') ) { ?>
										</a>
									<?php } ?>
								</td>
								<td><?php echo badge($e['active']); ?></td><?php
                            } else { ?>
                                <td>
									<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-3 rounded border" style="width: 100px; object-fit: cover">
								</td>
								<td>
									<label class="switch">
										<input type="checkbox" <?php if($e['active'] == 1) echo 'checked'; if( !check_module('Content/Update') ) { echo ' disabled'; } ?> data-id="<?php echo $e['pid']; ?>">
										<span class="slider round"></span>
									</label>
								</td><?php
                            }
                            ?>
                            <td><?php echo datalist_Table('tbl_admins', 'nickname', $e['create_by']); ?></td>
                            <td><?php echo $e['create_on']; ?></td>
							<?php
							if($thispage['type'] == 'slideshow') {
								if( check_module('Content/Delete') ) {
									?>
									<td><a href="javascript:;" onclick="del_ask(<?php echo $e['pid']; ?>)" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash py-1"></i></a></td><?php
								}
							} ?>
                        </tr><?php
                    }
                    ?>
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form method="post" enctype="multipart/form-data" onsubmit="Loading(1)" class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Slideshow</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">    

				<div class="form-group">
					<label>Image</label>
					<img src="https://cdn.synorex.link/assets/images/blank/4x3.jpg" class="mb-3 d-block rounded border" style="height: 100px;">
					<input type="file" class="form-control" name="image">
					<small id="slideshowHelpBlock" class="form-text text-muted">
						Image size of 920px x 200px is highly recommended. 
					</small>
				</div>
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="add-slideshow">Save</button>
            </div>

        </div>
    </div>
</form>