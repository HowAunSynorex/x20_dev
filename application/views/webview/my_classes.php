
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid container-wrapper py-3">

	<?php echo alert_get(); ?>
	
	
	<form method="get">
		<div class="row">
			<div class="col-md-6">
				
				<?php if($user['type'] == 'parent') { ?>
					<div class="form-group row">
						<label class="col-md-3 col-form-label">Child</label>
						<div class="col-md-9">
							<select class="form-group select2" name="user">
								<option value="">-</option>
								<?php foreach($child as $e) { ?>
									<option value="<?php echo $e['pid']; ?>" <?php if(isset($_GET['user'])) if($_GET['user'] == $e['pid']) echo 'selected'; ?>><?php echo $e['fullname_en']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
    				<div class="form-group row">
    					<div class="col-md-9 offset-md-3">
    						<input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    						<button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
    					</div>
    				</div>
    				
				<?php } ?>
				
			</div>
		</div>
	</form>
	
	<ul class="list-group">
    	<?php 
    	if(count($result)> 0) {
			foreach($result as $e) { 
				$class = $this->tbl_classes_model->list2([ 'pid' => $e['class'] ]);
				if(count($class) == 1) {
					$class = $class[0];
					?>
					<li class="list-group-item">
						<a href="//wa.me/<?php echo datalist_Table('tbl_users', 'phone', $class['teacher']); ?>" class="float-right mt-3" target="_blank"><i class="fab fa-fw fa-whatsapp fa-2x"></i></a>
						<b class="d-block"><?php echo $class['title']; ?></b>
						<small class="text-muted">
							<!--Time: <?php echo $class['title']; ?><br>-->
							Tutor: <?php echo datalist_Table('tbl_users', 'fullname_en', $class['teacher']); ?><br>
						</small>
					</li>
					<?php 
				}
			}
		}else echo "<h4>No Result Found</h4>";
    	
    	?>
    </ul>
	
</div>