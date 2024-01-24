<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo isset($thispage['title']) ? $thispage['title'].' - '.app('title') : app('title') ; ?></title>
	<?php if(!defined('PRIMARY_BRANCH')) { ?>
		<link rel="shortcut icon" href="<?php echo base_url('uploads/site/logo.png?v='.time()); ?>">
	<?php } else { ?>
		<link rel="shortcut icon" href="<?php echo pointoapi_UploadSource(datalist_Table('tbl_branches', 'image', PRIMARY_BRANCH)); ?>">
	<?php } ?>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

	<link rel="stylesheet" href="https://cdn.synorexcloud.com/template/164753773795/css/theme.css">

	<?php
	
	switch($thispage['group'].'/'.$thispage['title']) {

		case 'auth/Login':
		case 'portal/Login':
		case 'webapp_teacher/Login':
			echo '<link rel="stylesheet" href="https://cdn.synorexcloud.com/template/164753773795/css/theme-login.css">';
			break;

		case 'calendar/Calendar':
			echo '<link rel="stylesheet" href="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/core/main.min.css">';
			echo '<link rel="stylesheet" href="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/daygrid/main.min.css">';
			echo '<link rel="stylesheet" href="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/list/main.min.css">';
			echo '<link rel="stylesheet" href="https://cdn.synorexcloud.com/libraries/fullcalendar/4.2/packages/timegrid/main.min.css">';
			break;

		case 'home/Home':
			echo '<link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">';
			break;

		case 'classes/Edit Class':
		case 'reports/Daily Collection':
		case 'reports/Monthly Collection':
		case 'reports/Monthly Attendance':
		case 'reports/Students':
		case 'reports/Student Attendance':
		case 'reports/Student Attendance (Class)':
		case 'reports/Stock Movement':
			echo '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">';
			break;

	}

	if(isset($thispage['css'])) echo '<link rel="stylesheet" href="'.base_url('assets/pages/'.$thispage['css'].'.css?v='.time()).'">';
	?>
	
	<link rel="stylesheet" href="<?php echo base_url('assets/css/custom.css?v='.time()); ?>">
	
	<!-- Opex Analytics by Synorex - v2.0 -->
	<script src="https://cdn.synorexcloud.com/opex/analytics.js"></script>
</head>

<body>