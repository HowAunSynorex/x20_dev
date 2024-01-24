<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container py-5">
	<form method="post">
		<div class="row">
			<div class="col-md-6 offset-md-3">
				
				<h3 class="font-weight-bold"><?php echo $result['title']; ?></h3>
				
				<div class="py-3 my-3 border-top border-bottom text-muted">
					<i class="fa fa-fw fa-user"></i> <?php echo datalist_Table('tbl_admins', 'nickname', $result['create_by']); ?>
					<span class="mx-2">|</span>
					<i class="fa fa-fw fa-clock"></i> <?php echo date('M d, Y', strtotime($result['create_on'])); ?>
					<?php if(!empty($result['class'])) { ?>
						<span class="mx-2">|</span>
						<i class="fa fa-fw fa-chalkboard"></i> <?php echo datalist_Table('tbl_classes', 'title', $result['class']); ?>
					<?php } ?>
				</div>
				
				<?php echo str_replace('oembed>', 'iframe>', str_replace('<oembed url=', '<iframe width="420" height="315" src=', $result['content'])); ?>
				
			</div>
		</div>
	</form>
</div>