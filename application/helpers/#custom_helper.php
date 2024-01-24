<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function stock_on_hand($item) {
	
	$this_ci =& get_instance();

	$return = $this_ci->db->query('SELECT SUM(qty_in) - SUM(qty_out) AS BALANCE FROM log_inventory WHERE is_delete = 0 AND item = ?', [
		$item
	])->result_array();
	
	// print_r($return[0]); exit;
	
	return empty($return[0]['BALANCE']) ? 0 : $return[0]['BALANCE'] ;
	
}

/*
 * generator new payment no
 *
 * @author Steve
 *
**/
function new_receipt_no() {
	
	$this_ci =& get_instance();
	
	// v2
	/*$return = '';
	$template = branch_now('receipt_no');
	
	switch($template) {
		
		case 'RCPT-%YY%0000':
			// replace
			$replace = str_replace('%YY%', date('y'), $template);
			$replace = str_replace('%YYYY%', date('Y'), $replace);
			$replace = str_replace('%MM%', date('m'), $replace);
			
			$replace_template = str_replace(0, '', $replace);
			
			// next no
			// $next_no = '';
			// for($x=strlen($template_no); $x<substr_count($format_sys, 0); $x++) $next_no .= 0;
			// $next_no = $next_no . $template_no;
			
			// return
			$return = $replace_template;
			break;
			
	}
	
	return $return;*/

	/*$format_sys = branch_now('receipt_no');

	// replace no format
	$template = str_replace(0, '', $format_sys);
	
	// replace date / time
	$replace = str_replace('%YY%', date('y'), $format_sys);
	$replace = str_replace('%YYYY%', date('Y'), $replace);
	$replace = str_replace('%MM%', date('m'), $replace);
	
	// return $replace;
	
	// db
	$this_ci->db->where('branch', branch_now('pid'));
	$this_ci->db->where('is_delete', 0);
	$this_ci->db->order_by('payment_no', 'DESC');
	
	$query = $this_ci->db->get('tbl_payment');
	$result = $query->result_array();
	
	if(count($result) > 0) {
		
		$template_no = $result[0]['payment_no'];

		$template_no = str_replace($template, '', $template_no);
		
		// next no
		$template_no++;
		
		$next_no = '';
		for($x=strlen($template_no); $x<substr_count($format_sys, 0); $x++) $next_no .= 0;
		$next_no = $next_no . $template_no;
		
		$output = $template . $next_no;
		
	} else {
			
		// template no / 0000
		$template_no = '';
		for($x=1; $x<=substr_count($format_sys, 0); $x++) $template_no .= 0;
		
		// next no
		$template_no++;
		
		$next_no = '';
		for($x=strlen($template_no); $x<substr_count($format_sys, 0); $x++) $next_no .= 0;
		$next_no = $next_no . $template_no;
		
		// output
		$output = $template . $next_no;
		
	}

	return $output;*/
	
	// v3
	$format_sys = branch_now('receipt_no');
	$receipt_no_max = branch_now('receipt_no_max');

	// replace date / time
	$replace = str_replace('%YY%', date('y'), $format_sys);
	$replace = str_replace('%YYYY%', date('Y'), $replace);
	$replace = str_replace('%MM%', date('m'), $replace);
	
	// db
	$this_ci->db->where('branch', branch_now('pid'));
	$this_ci->db->where('is_delete', 0);
	$this_ci->db->order_by('payment_no', 'DESC');
	
	$query = $this_ci->db->get('tbl_payment');
	$result = $query->result_array();
	
	if(count($result) > 0) {
		
		$template_no = $result[0]['payment_no'];

		$template_no = str_replace($replace, '', $template_no);
		
		// next no
		$template_no++;
		
		$next_no = '';
		for($x=strlen($template_no); $x<$receipt_no_max; $x++) $next_no .= 0;
		$next_no = $next_no . $template_no;
		
		$output = $replace . $next_no;
		
	} else {
			
		// template no / 0000
		$template_no = '';
		for($x=1; $x<=$receipt_no_max; $x++) $template_no .= 0;
		
		// next no
		$template_no++;
		
		$next_no = '';
		for($x=strlen($template_no); $x<$receipt_no_max; $x++) $next_no .= 0;
		$next_no = $next_no . $template_no;
		
		// output
		$output = $replace . $next_no;
		
	}

	return $output;
	
}

function day_left($birthday) {

	$now = time();
	$birthday = strtotime($birthday);
	$days = $birthday - $now;

	return round($days / (60 * 60 * 24));
	
}

function my_branches() {
	
	$this_ci =& get_instance();

	$auth_id = auth_data('pid');

	$this_ci->db->where('type', 'join_branch');
	$this_ci->db->where('admin', $auth_id);
	$this_ci->db->where('is_delete', 0);
	
	$query = $this_ci->db->get('log_join');
	$result = $query->result_array();

	return $result;
	
}

function app_sys($output) {
	
	$this_ci =& get_instance();

	$this_ci->db->where('k', $output);
	
	$query = $this_ci->db->get('sys_app');
	
	$result = $query->result_array();

	if(count($result) > 0) {

		$result = $result[0];

		return $result['v'];

	} else {

		return null;

	}

}

/*function student_unpaid_items($student_id) {
	
	$this_ci =& get_instance();
	
	// models
	$this_ci->load->model('log_join_model');

	$array = [];
	
	// unpaid items
	$unpaid_item = $this_ci->log_join_model->list('unpaid_item', branch_now('pid'));
	
	return $unpaid_item;

}*/

function check_permission($permission, $admin = '') {
	
	$this_ci =& get_instance();

	$branch = branch_now('pid');
	if(empty($admin)) $admin = auth_data('pid');
	
	$this_ci->db->where('type', 'join_branch');
	$this_ci->db->where('branch', $branch);
	$this_ci->db->where('admin', $admin);
	$this_ci->db->where('is_delete', 0);
	
	$query = $this_ci->db->get('log_join');
	$result = $query->result_array();
	
	if(count($result) == 1) {
		
		$result = $result[0];
		
		if( $admin == datalist_Table('tbl_branches', 'owner', $branch) ) {
			
			return true;
			
		} else {
			
			$result['permission'] = json_decode($result['permission'], true);
			if(!is_array($result['permission'])) $result['permission'] = [];
			
			return isset($result['permission'][ post_array_replace($permission) ]) ? true : false ;
			
		}
		
	} else {
		
		return false;
		
	}

}

function check_plan_module($module, $plan) {
	
	$this_ci =& get_instance();
	
	$modules = datalist_Table('tbl_secondary', 'modules', $plan);
	
	$modules = json_decode($modules, true);
	if(!is_array($modules)) $modules = [];
	
	return isset($modules[ post_array_replace($module) ]) ? true : false ;

}

function check_module($module) {
	
	$module_plan = str_replace('Create', 'Read', $module);
	$module_plan = str_replace('Update', 'Read', $module_plan);
	$module_plan = str_replace('Delete', 'Read', $module_plan);
	
	if( check_plan_module($module_plan, branch_now('plan')) ) {
		
		return check_permission($module) ? true : false ;
		
	} else {
		
		return false;
		
	}
	
}

function check_module_page($module) {
	
	if( !check_module($module) ) {
		
		alert_new('danger', 'Access denied');
		
		redirect();
		
	}
	
}

function post_array_replace($k) {
	
	$k = str_replace(' ', '', $k);
	$k = str_replace('(', '', $k);
	$k = str_replace(')', '', $k);
	$k = str_replace('.', '', $k);
	
	return $k;
	
}

/*
 * get api post data
 *
 * @author Steve
 * @date 2021-08-11
 *
**/
function post_data($k) {
	
	$postdata = json_decode(file_get_contents('php://input'), true);
	if(!is_array($postdata)) $postdata = [];
	
	if( isset($postdata[ $k ]) ) {
		
		return $postdata[ $k ];
		
	} elseif( isset($_POST[ $k ]) ) {
		
		return $_POST[ $k ];
		
	} else {
		
		return null;
		
	}
	
}

/*
 * @author Steve
 *
**/
function user_point($type, $user_id) {
	
	$this_ci =& get_instance();

	$query = $this_ci->db->query('SELECT SUM(amount_1) - SUM(amount_0) AS BALANCE FROM log_point WHERE is_delete=0 AND user=? AND type=?', [ $user_id, $type ]);
	
	$result = $query->result_array();
	
	if(!isset($result[0]['BALANCE'])) $result[0]['BALANCE'] = 0;
	
	return empty($result[0]['BALANCE']) ? 0 : $result[0]['BALANCE'] ;
	
}