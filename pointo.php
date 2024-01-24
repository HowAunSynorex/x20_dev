<?php

/*
 * db
 *
**/
define('POINTO_DB_HOST', 'localhost');
define('POINTO_DB_USER', 'synorexvd_u86');
define('POINTO_DN_PASS', 'sn^H*OqwT+O_');
define('POINTO_DB_NAME', 'synorexvd_86');

/*
 * url
 *
**/
// define('POINTO_BASE_URL', 'https://dev-uqz3.synorex.xyz/robocube-tuition/');

/*
 * title
 *
**/
// define('POINTO_APP_TITLE', 'Robocube Tuition');

/*
 * pointoapi
 *
**/
define('POINTO_API_KEY', '5540c3538038b79b69c058e0c27bac4b');

/*
 * one
 *
**/
define('ONE_SERVICE_ID', '163323421875');

/*
 * timezone
 *
**/
date_default_timezone_set('Asia/Kuala_Lumpur');

/*
 * route
 *
**/

switch($_SERVER['HTTP_HOST']) {
	
	case 'system.synorex.work':
		define('WHITELABEL', 1);
		define('POINTO_APP_TITLE', 'High Peak Edu');
		define('POINTO_BASE_URL', 'https://system.synorex.work/highpeakedu/');
		break;
	
	case 'system.synorex.space':
		define('WHITELABEL', 1);
		define('POINTO_APP_TITLE', 'High Peak Edu');
		define('POINTO_BASE_URL', 'https://system.synorex.space/highpeakedu/');
		break;
	
	// case 'miart-dev.synorex.work':
		// define('WHITELABEL', 1);
		// define('PRIMARY_BRANCH', 165995851020);
		// define('POINTO_APP_TITLE', 'Mi Art (Dev)');
		// define('POINTO_BASE_URL', 'https://miart-dev.synorex.work/');
		// break;
	
	// case 'system.synorex.work':
		// define('POINTO_APP_TITLE', 'Robocube Tuition');
		// define('POINTO_BASE_URL', 'https://system.synorex.work/highpeakedu/');
		// break;
	
	// case 'robocube.synorexcloud.com':
		// define('POINTO_APP_TITLE', 'Robocube Tuition');
		// define('POINTO_BASE_URL', 'https://robocube.synorexcloud.com/tuition/');
		// break;
	
	default:
		die('Synorex: License expired');
	
} 