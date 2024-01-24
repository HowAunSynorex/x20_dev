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
                    <a href="<?php echo base_url($this->group . '/secondary_add/' . $thispage['type']); ?>" class="btn btn-primary"><i class="fa mr-1 fa-plus-circle"></i> Add New</a>
                </div>
            </div>
        </div>
        
        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>
            
            <table class="DTable table">
                <thead>
                    <th style="width:10%">No</th>
                    <th style="width:35%">Title</th>
                    <th style="width:10%">Status</th>
                    <?php
                    switch ($thispage['type']) {
                        case 'country':
                            ?>
                            <th>Country ID</th>
                            <th>Phone Code</th>
                            <?php
                            break;

                        case 'currency':
                            ?>
                            <th>Currency ID</th>
                            <?php
                            break;

                        case 'plan':
                            ?>
                            <th>Fee ($)</th>
                            <?php
                            break;
							
                        case 'payment_method':
                            ?>
                            <th>Method ID</th>
                            <?php
                            break;
							
                    }
                    ?>
                    <th>Remark</th>
                </thead>
                <tbody>
                    
                    <?php $i=0; foreach($result as $e) { $i++; ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
							<?php
							
							switch($thispage['type']) {
								
								case 'receipt':
									?>
									<div class="media">
										<a href="<?php echo base_url($this->group . '/secondary_edit/' . $e['pid']); ?>">
											<img src="<?php echo pointoapi_UploadSource($e['image']); ?>" class="mr-3 rounded border" style="width: 100px; object-fit: cover">
										</a>
										<div class="media-body">
											<a href="<?php echo base_url($this->group . '/secondary_edit/' . $e['pid']); ?>"><?php echo $e['title']; ?></a>
										</div>
									</div>
									<?php
									break;
									
								default:
									?><a href="<?php echo base_url($this->group . '/secondary_edit/' . $e['pid']); ?>"><?php echo $e['title']; ?></a><?php
								
							}
							?>
						</td>
                        <td><?php echo badge($e['active']); ?></td>
                        <?php
                        switch ($thispage['type']) {
                            case 'country':
                                ?>
                                <td><?php echo empty($e['country_id']) ? '-' : $e['country_id']; ?></td>
                                <td><?php echo empty($e['phone_code']) ? '-' : $e['phone_code']; ?></td>
                                <?php
                                break;

                            case 'currency':
                                ?>
                                <td><?php echo empty($e['currency_id']) ? '-' : $e['currency_id']; ?></td>
                                <?php
                                break;

                            case 'plan':
                                ?>
                                <td><?php echo empty($e['fee']) ? '0.00' : number_format($e['fee'], 2, '.', ','); ?></td>
                                <?php
                                break;
								
                            case 'payment_method':
                                ?>
                                <td><?php echo empty($e['method_id']) ? '-' : $e['method_id']; ?></td>
                                <?php
                                break;
								
                        }
                        ?>
                        <td><?php echo empty($e['remark']) ? '-' : $e['remark'] ; ?></td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
        </div>

        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>