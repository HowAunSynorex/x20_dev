<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar_admin', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar_admin'); ?>

    <div id="page-content-wrapper">

        <div class="container container-wrapper pt-4">

            <?php echo alert_get(); ?>

            <div class="row">
				<div class="col-md-3 mb-4">
					<div class="card">
						<div class="card-body">
							<h5 class="font-weight-bold mb-1"><?php echo $total_branches; ?></h5>
							<small class="d-block">Total Branches(s)</small>
						</div>
					</div>
				</div>

				<div class="col-md-3 mb-4">
					<div class="card">
						<div class="card-body">
							<h5 class="font-weight-bold mb-1">
								<?php echo $total_expired_branches; ?>
							</h5>
							<small class="d-block">Total Expired Branche(s)</small>
						</div>
					</div>
				</div>

				<div class="col-md-3 mb-4">
					<div class="card">
						<div class="card-body">
							<h5 class="font-weight-bold mb-1">
								<?php echo $total_month_registed_branches; ?>
							</h5>
							<small class="d-block">New Branches (Current Month)</small>
						</div>
					</div>
				</div>

				<div class="col-md-3 mb-4">
					<div class="card">
						<div class="card-body">
							<h5 class="font-weight-bold mb-1">
								<?php echo $total_students; ?>
							</h5>
							<small class="d-block">Total Student(s)</small>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
                    <div class="card">
                        <div class="card-body font-weight-bold pb-0">Expiring Branches</div>
                        <div class="card-body px-0">
							<table class="table table-hover table-sm">
								<thead>
									<th>#</th>
									<th>Branch</th>
									<th>Expired Date</th>
									<th>Expired Day</th>
								</thead>
								<tbody>
									<?php $i = 1; foreach($expiring_branches as $e) { ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $e['title']; ?></td>
											<td><?php echo $e['expired_date']; ?></td>
											<td><?php echo $e['expired_day']; ?></td>
										</tr>
									<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-6">
                    <div class="card">
                        <div class="card-body font-weight-bold pb-0">Last Login Branches</div>
                        <div class="card-body px-0">
							<table class="table table-hover table-sm">
								<thead>
									<th>#</th>
									<th>Branch</th>
									<th>Last Online</th>
								</thead>
								<tbody>
									<?php $i = 1; foreach($last_online_branches as $e) { ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $e['title']; ?></td>
											<td><?php echo time_elapsed_string($e['last_online']); ?></td>
										</tr>
									<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
            </div>

        </div>


        <?php $this->load->view('inc/copyright_admin'); ?>

    </div>

</div>