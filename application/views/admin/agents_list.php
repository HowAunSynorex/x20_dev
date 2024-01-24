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
					<a href="<?php echo base_url('portal'); ?>" class="btn btn-info" target="_blank">Portal</a>
                    <a href="javascript:;" data-toggle="modal" data-target="#modal-add" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table table-hover">
                <thead>
                    <th style="width:10%">No</th>
                    <th>Name</th>
                    <th style="width:10%">Status</th>
					<th>Username</th>
					<th>Email</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<a href="<?php echo base_url('admin/agents_edit/'.$e['pid']); ?>">
								<?php echo $e['fullname']; ?>
							</a>
						</td>
                        <td><?php echo badge($e['active']); ?></td>
						<td><a href="<?php echo base_url('admin/agents_edit/'.$e['pid']); ?>"><?php echo $e['username']; ?></a></td>
						<td><?php echo $e['email']; ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>

<form method="post" class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Agent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<div class="form-group">
                    <label class="text-danger">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
				
				<div class="form-group">
                    <label class="text-danger">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                
                <div class="form-group">
                    <label class="text-danger">Name</label>
                    <input type="text" class="form-control" name="fullname" required>
                </div>
				

            </div>
            <div class="modal-footer">
                <button type="submit" name="save" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>