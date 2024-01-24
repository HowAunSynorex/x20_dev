<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * check user availability
 *
 * @author soon
 *
**/

function check_user_availability($user_id) {
	
	$this_ci =& get_instance();

	$query = $this_ci->db->query('
	
		SELECT *
		FROM tbl_users
		WHERE pid= ?
		AND active = 1
		AND is_delete = 0
	
	', [$user_id])->result_array();

	return empty($query) ? 0 : 1;
	
}

function addOneMonth($dateStr) {

	$date = new DateTime($dateStr);

	// Add one month to the date
	$date->add(new DateInterval('P1M'));

	// Format the updated date back to 'Y-m' format
	$updatedDateStr = $date->format('Y-m');

	return $updatedDateStr;
}

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
function new_receipt_no($branch = '', $type = '') {
	
	$this_ci =& get_instance();
	// v5
	$branch_id = empty($branch) ? branch_now('pid') : $branch ;
	
	if ($type == '')
	{
		$format_sys = empty($branch) ? branch_now('receipt_no') : datalist_Table('tbl_branches', 'receipt_no', $branch) ;
		$receipt_no_max = empty($branch) ? branch_now('receipt_no_max') : datalist_Table('tbl_branches', 'receipt_no_max', $branch) ;
	}
	else
	{
		$format_sys = empty($branch) ? branch_now('receipt_no_draft') : datalist_Table('tbl_branches', 'receipt_no_draft', $branch) ;
		$receipt_no_max = empty($branch) ? branch_now('receipt_no_max_draft') : datalist_Table('tbl_branches', 'receipt_no_max_draft', $branch) ;
	}

	// replace date / time
	$replace = str_replace('%YY%', date('y'), $format_sys);
	$replace = str_replace('%YYYY%', date('Y'), $replace);
	$replace = str_replace('%MM%', date('m'), $replace);
	$replace = str_replace('%DD%', date('d'), $replace);
	
	// $replace_last = str_replace('%YY%', date('y', strtotime('-1 year')), $format_sys);
	// $replace_last = str_replace('%YYYY%', date('Y', strtotime('-1 year')), $replace_last);
	// $replace_last = str_replace('%MM%', date('m', strtotime('-1 month')), $replace_last);
	// $replace_last = str_replace('%DD%', date('d', strtotime('-1 day')), $replace_last);
	
	// return $replace;
	
	// db
	$this_ci->db->where('branch', $branch_id);
	$this_ci->db->where('is_delete', 0);
	$this_ci->db->where('LENGTH(payment_no)', strlen($replace) + $receipt_no_max);
	$this_ci->db->order_by('payment_no', 'DESC');
	
	if ($type == 'draft')
	{
		$this_ci->db->where('is_draft', 1);
	}
	
	$query = $this_ci->db->get('tbl_payment');
	$result = $query->result_array();
	
	if(count($result) > 0) {
		
		$result = $result[0];
		$template_no = $result['payment_no'];
		
		$template_no = str_replace($replace, '', $template_no);
		
		// next no
		$template_no++;
		
		// check next no continue or reset
		$Reset = 0;
		if( str_replace('%DD%', date('d'), $format_sys) != $format_sys ) {
			if( date('d') != date('d', strtotime($result['create_on'])) ) {
				$Reset = 1;
			}
		}
		if( str_replace('%MM%', date('m'), $format_sys) != $format_sys ) {
			if( date('m') != date('m', strtotime($result['create_on'])) ) {
				$Reset = 1;
			}
		}
		if( 
			str_replace('%YYYY%', date('Y'), $format_sys) != $format_sys || 
			str_replace('%YY%', date('y'), $format_sys) != $format_sys
		) {
			if( date('Y') != date('Y', strtotime($result['create_on'])) ) {
				$Reset = 1;
			}
		}
		if($Reset==1) $template_no = 1;
		
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

/*function new_receipt_no1($branch = '') {
	
	$this_ci =& get_instance();
	
	// v3
	$branch_id = empty($branch) ? branch_now('pid') : $branch ;
	$format_sys = empty($branch) ? branch_now('receipt_no') : datalist_Table('tbl_branches', 'receipt_no', $branch) ;
	$receipt_no_max = empty($branch) ? branch_now('receipt_no_max') : datalist_Table('tbl_branches', 'receipt_no_max', $branch) ;

	// replace date / time
	$replace = str_replace('%YY%', date('y'), $format_sys);
	$replace = str_replace('%YYYY%', date('Y'), $replace);
	$replace = str_replace('%MM%', date('m'), $replace);
	
	// db
	$this_ci->db->where('branch', $branch_id);
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
	
}*/

function day_left($birthday) {

	$now = time();
	$birthday = strtotime(date('Y-') . date('m-d', strtotime($birthday)));
	$days = $birthday - $now;

	return round($days / (60 * 60 * 24))+1;
	
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

function class_credit_balance($class, $user_id) {
	
	$this_ci =& get_instance();

	$query = $this_ci->db->query('
		SELECT COUNT(1) AS BALANCE
		FROM log_join
		WHERE is_delete = 0
		AND type = "class_attendance"
		AND class = ?
		AND user = ?
	', [ $class, $user_id ]);
	
	$result = $query->result_array();
	
	if(!isset($result[0]['BALANCE'])) $result[0]['BALANCE'] = 0;
	
	$used_credit = $result[0]['BALANCE'];
	
	$query = $this_ci->db->query('
		SELECT SUM(amount_1) - SUM(amount_0) AS BALANCE
		FROM log_point
		WHERE is_delete = 0
		AND type = "class_credit"
		AND class=?
		AND user=?
	', [ $class, $user_id ]);
	
	$result = $query->result_array();
	
	if(!isset($result[0]['BALANCE'])) $result[0]['BALANCE'] = 0;
		
	$total_class = $result[0]['BALANCE'];
	
	return [
		'used'		=> $used_credit,
		'total'		=> $total_class,
		'balance'	=> $total_class - $used_credit,
	];
	
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

/*
 * @author Steve
 *
**/
function submit_today_attendance($method_id, $user_id, $temperature = null) {
	
	$this_ci =& get_instance();
	
	$this_ci->load->model('log_attendance_model');

	// check last action
	$last_action = $this_ci->log_attendance_model->list_user_today_attendance([
		'user' => $user_id,
		'datetime LIKE' => '%'.date('Y-m-d').'%',
	], true);
	
	$action = count($last_action) % 2 == 0 ? 'in' : 'out' ;
	
	$this_ci->log_attendance_model->add([
		'branch' => datalist_Table('tbl_users', 'branch', $user_id),
		'method' => $method_id,
		'create_by' => $user_id,
		'user' => $user_id,
		'datetime' => date('Y-m-d H:i:s'),
		'action' => $action,
		'temperature' => $temperature,
		'reason' => $action == 'in' ? 'Check In' : 'Check Out' ,
	]);
	
	return true;
	
}

// modified by soon
/* function std_unpaid_result($user_id, $branch = '') {
	
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	
	// start
	$unpaid_result = [];
	$total_unpaid_count = 0;
	$total_unpaid_amount = 0;
	$total_unpaid_subtotal = 0;
	$total_unpaid_discount = 0;
	
	if(datalist_Table('tbl_users', 'active', $user_id)) {
	
		// unpaid item
		foreach( $this_ci->log_join_model->list('unpaid_item', $branch, [ 'user' => $user_id ]) as $e ) {
			
			if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {
			
				$total_unpaid_count += $e['qty'];
				$total_unpaid_amount += $e['amount'] - $e['discount'];
				$total_unpaid_subtotal += $e['amount'];
				$total_unpaid_discount += $e['discount'];
				
				$unpaid_result['item'][] = [
					'id' => $e['id'],
					'item' => $e['item'],
					'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
					'qty' => $e['qty'],
					'discount' => $e['discount'],
					'amount' => $e['amount'],
					'remark' => $e['remark']
				];
			
			}
			
		}
		
		// unpaid class
		foreach( $this_ci->log_join_model->list('join_class', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) {
			
			$join_date = $e['date'];

			$join_date_year = date('Y', strtotime($join_date));
			$today_year = date('Y');

			$join_date_month = date('m', strtotime($join_date));
			$today_month = date('m');

			$diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
			
			if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
				if(class_credit_balance($e['class'], $user_id)['balance'] < 0) {
					$qty = floor(abs(class_credit_balance($e['class'], $user_id)['balance'] / datalist_Table('tbl_classes', 'credit', $e['class']))) + 1;
					
					$total_unpaid_count++;
					$total_unpaid_amount += (datalist_Table('tbl_classes', 'fee', $e['class']) * $qty) - $e['discount'];
					$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']) * $qty;
					$total_unpaid_discount += $e['discount'];
					
					$unpaid_result['class'][] = [
						'id' => $e['id'],
						'class' => $e['class'],
						'qty' => $qty,
						'title' => datalist_Table('tbl_classes', 'title', $e['class']),
						'discount' => $e['discount'],
						'amount' => datalist_Table('tbl_classes', 'fee', $e['class']) * $qty,
						'remark' => $e['remark'],
					];
				}
			}
				
			for($j=0; $j<=$diff; $j++) {
				
				switch(datalist_Table('tbl_classes', 'type', $e['class'])) {
					case 'monthly':
						$period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
						break;
					case 'yearly':
						$period = date('Y', strtotime('+'.$j.' year', strtotime($join_date)));
						break;
				}
							
				if(!datalist_Table('tbl_classes', 'is_delete', $e['class'])) {
					
					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => $period
						
					]))) {
						
						$discount = $e['discount'];
					
						if(!is_numeric($e['discount'])) {
							
							$discount = 0;
							
							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];
							
							if(isset($discount_arr[$period])) {
								$discount += $discount_arr[$period];
							}
								
						}
						
						$total_unpaid_count++;
						$total_unpaid_amount += datalist_Table('tbl_classes', 'fee', $e['class']) - $discount;
						$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']);
						$total_unpaid_discount += $discount;
			
						$unpaid_result['class'][] = [
							'id' => $e['id'],
							'class' => $e['class'],
							'period' => $period,
							'title' => datalist_Table('tbl_classes', 'title', $e['class']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_classes', 'fee', $e['class']),
							'remark' => $e['remark'],
							'qty' => 1
						];
						
					}
					
				}
				
			}
			
		}
		
	}
		
	// output
	return [
		'count' => $total_unpaid_count,
		'subtotal' => $total_unpaid_subtotal,
		'discount' => $total_unpaid_discount,
		'total' => $total_unpaid_amount,
		'result' => $unpaid_result,
	];
	
} */

function std_status_class($user_id = '', $branch = '', $period = '', $status = '') {
	
	
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	
	// start
	$result = [];
	$total_count = 0;
	$total_amount = 0;
	$total_subtotal = 0;
	$total_discount = 0;
	
	// item
	if ($status == 'unpaid') {
		
		$unpaid_item_sql = "SELECT log_join.id, log_join.item, tbl_inventory.title AS title, log_join.qty, log_join.discount, log_join.amount, log_join.remark
				FROM log_join
				LEFT JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
				JOIN tbl_users ON tbl_users.pid = log_join.user
				WHERE tbl_users.active = 1
				AND log_join.is_delete = 0
				AND log_join.branch = '". $branch ."'
				AND log_join.type = 'unpaid_item'
				AND log_join.user = '". $user_id ."'";

		$unpaid_items = $this_ci->db->query($unpaid_item_sql)->result_array();
		//$unpaid_items = [];
		$total_count += array_sum(array_column($unpaid_items, 'qty'));
		$total_amount += array_sum(array_column($unpaid_items, 'amount')) - array_sum(array_column($unpaid_items, 'discount'));
		$total_subtotal += array_sum(array_column($unpaid_items, 'amount'));
		$total_discount += array_sum(array_column($unpaid_items, 'discount'));
		$result['item'] = $unpaid_items;
		
		/* OLD VERSION
		foreach( $this_ci->log_join_model->list('unpaid_item', $branch, [ 'user' => $user_id ]) as $e ) {
		
			if(empty($e['discount'])) {
				
				$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'item' => $e['item'] ], [ 'id' => 'DESC' ]);
				
				if(count($query) > 0) {
					
					$query = $query[0];
					
					if(!empty($query['dis_amount'])) {
						$this_ci->log_join_model->edit($e['id'], [ 'discount' => $query['dis_amount'] ]);
						$e['discount'] = $query['dis_amount'];
					}
					
				}
				
			}
			
			if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {
			
				$total_count += $e['qty'];
				$total_amount += $e['amount'] - $e['discount'];
				$total_subtotal += $e['amount'];
				$total_discount += $e['discount'];
				
				$result['item'][] = [
					'id' => $e['id'],
					'item' => $e['item'],
					'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
					'qty' => $e['qty'],
					'discount' => $e['discount'],
					'amount' => $e['amount'],
					'remark' => $e['remark']
				];
			
			}
		}
		END OLD VERSION */ 
	}else {
		
		$paid_item_sql = "SELECT log_payment.id, log_payment.item, log_payment.qty, log_payment.dis_amount AS discount,
							log_payment.price_amount AS amount, log_payment.remark, tbl_inventory.title,
							tbl_payment.payment_no AS receipt_no
							FROM log_payment
							JOIN tbl_inventory ON tbl_inventory.pid = log_payment.item
							JOIN tbl_payment ON tbl_payment.pid = log_payment.payment
							WHERE log_payment.is_delete = 0
							AND log_payment.user = '". $user_id ."'
							AND log_payment.item IS NOT NULL
							AND log_payment.period IS NULL
							AND log_payment.is_delete = 0
							AND DATE_FORMAT(log_payment.create_on, '%Y-%m') = '". $period ."'
							AND DATE_FORMAT(tbl_payment.date, '%Y-%m') = '". $period ."'
							AND tbl_payment.branch = '". $branch ."'
							AND tbl_payment.status = 'paid'";
						
		$paid_items = $this_ci->db->query($paid_item_sql)->result_array();
			
		$total_count += array_sum(array_column($paid_items, 'qty'));
		$total_amount += array_sum(array_column($paid_items, 'amount')) - array_sum(array_column($paid_items, 'discount'));
		$total_subtotal += array_sum(array_column($paid_items, 'amount'));
		$total_discount += array_sum(array_column($paid_items, 'discount'));
		$result['item'] = $paid_items;
		
	}
		
	// class
	if ($status == 'unpaid') 
	{
		$result['class'] = [];
		$unpaid_class_sql_year_month = "WITH RECURSIVE cte AS ( 
									SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
									log_join.class, 
									(CASE WHEN payments.period IS NULL 
									THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d') 
									ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS join_date, 
									log_join.discount, log_join.remark, 1 AS qty,
									tbl_classes.title AS title, tbl_classes.type AS class_type, tbl_classes.fee AS amount,
									(CASE WHEN tbl_classes.type = 'monthly' 
									THEN TIMESTAMPDIFF(MONTH, (CASE WHEN payments.period IS NULL 
									THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
									ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), CURDATE()) 
									ELSE TIMESTAMPDIFF(YEAR, (CASE WHEN payments.period IS NULL 
									THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
									ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), CURDATE()) END) AS diff,
									(CASE WHEN payments.period IS NULL 
									THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
									ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS period_date
									FROM log_join
									JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
									JOIN tbl_users ON tbl_users.pid = log_join.user
									LEFT JOIN (
										SELECT period, class, user
										FROM log_payment
										WHERE log_payment.is_delete = 0 
										AND log_payment.user = '". $user_id ."'
										ORDER BY CONVERT((CASE WHEN REPLACE(COALESCE(period, ''), '-', '') = '' THEN '0' ELSE REPLACE(COALESCE(period, ''), '-', '') END), DECIMAL) DESC
										LIMIT 1
									) payments ON payments.class = log_join.class AND payments.user = log_join.user
									WHERE tbl_users.active = 1
									AND log_join.is_delete = 0
									AND log_join.branch = '". $branch ."'
									AND log_join.type = 'join_class'
									AND log_join.user = '". $user_id ."'
									AND log_join.active = 1
									AND (tbl_classes.type = 'monthly' OR tbl_classes.type = 'yearly')
									AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000 
									AND (CASE WHEN tbl_classes.type = 'monthly' 
									THEN (EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR_MONTH FROM CURDATE()))
									ELSE (EXTRACT(YEAR FROM log_join.date) <= EXTRACT(YEAR FROM CURDATE())) END)
									AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
										OR tbl_classes.date_end IS NULL 
										OR tbl_classes.date_end >= '". date("Y-m-d") ."')
									
									UNION ALL
									
									SELECT (n + 1) AS n, fullname_en, id, user, class, join_date, discount, remark, qty, title, class_type, amount, diff, 
									(CASE WHEN class_type = 'monthly' THEN DATE_ADD(period_date, INTERVAL 1 MONTH) 
									ELSE DATE_ADD(period_date, INTERVAL 1 YEAR) END) AS period_date
									FROM cte 
									WHERE n < diff
								) 
								
								SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.class, cte.join_date,
								(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
								ELSE CONVERT((CASE WHEN COALESCE(cte.discount, '') = '' THEN '0' ELSE JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR), '\"')) END), DOUBLE) END) AS discount,
								cte.remark, cte.qty, cte.title, cte.class_type, cte.amount, cte.diff, 
								(CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
								ELSE DATE_FORMAT(cte.period_date, '%Y') END) AS period
								FROM cte
								LEFT JOIN log_payment ON log_payment.user = cte.user 
								AND log_payment.class = cte.class AND log_payment.is_delete = 0 
								AND log_payment.period = CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR)
								WHERE log_payment.period IS NULL
								AND (CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
								ELSE CONCAT(DATE_FORMAT(cte.period_date, '%Y'), '-', DATE_FORMAT(cte.period_date, '%m')) END) = '". $period ."'
								ORDER BY fullname_en, class, title, period";
								
		$unpaid_classes_monthly_yearly = $this_ci->db->query($unpaid_class_sql_year_month)->result_array();
		
		$total_count += array_sum(array_column($unpaid_classes_monthly_yearly, 'qty'));
		$total_amount += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount')) - array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));
		$total_subtotal += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount'));
		$total_discount += array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));			
		
		$unpaid_class_sql_check_in = "SELECT log_join.id, log_join.class,
									(FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) AS qty, 
									tbl_classes.title AS title, log_join.discount,
									((FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) * tbl_classes.fee) AS amount,
									log_join.remark, '' AS period
									FROM log_join
									JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
									JOIN tbl_users ON tbl_users.pid = log_join.user
									JOIN (
										SELECT class, user, COUNT(1) AS BALANCE
										FROM log_join
										WHERE is_delete = 0
										AND type = 'class_attendance'
										GROUP BY class, user
									) used_credits ON used_credits.class = log_join.class AND used_credits.user = log_join.user
									JOIN (
										SELECT class, user, SUM(amount_1) - SUM(amount_0) AS BALANCE
										FROM log_point
										WHERE is_delete = 0
										AND type = 'class_credit'
										GROUP BY class, user
									) total_classes ON total_classes.class = log_join.class AND total_classes.user = log_join.user
									WHERE tbl_users.active = 1
									AND log_join.is_delete = 0
									AND log_join.branch = '". $branch ."'
									AND log_join.type = 'join_class'
									AND log_join.user = '". $user_id ."'
									AND log_join.active = 1
									AND tbl_classes.type = 'check_in'
									AND (total_classes.BALANCE - used_credits.BALANCE) < 0
									AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
										OR tbl_classes.date_end IS NULL 
										OR tbl_classes.date_end >= '". date("Y-m-d") ."')";
										
		$unpaid_classes_check_in = $this_ci->db->query($unpaid_class_sql_check_in)->result_array();
		
		$total_count += array_sum(array_column($unpaid_classes_check_in, 'qty'));
		$total_amount += array_sum(array_column($unpaid_classes_check_in, 'amount')) - array_sum(array_column($unpaid_classes_check_in, 'discount'));
		$total_subtotal += array_sum(array_column($unpaid_classes_check_in, 'amount'));
		$total_discount += array_sum(array_column($unpaid_classes_check_in, 'discount'));
		$result['class'] = array_merge($unpaid_classes_monthly_yearly, $unpaid_classes_check_in);
	}
	else
	{
		$paid_class_sql = "SELECT MIN(log_payment.id) AS id, log_payment.class, 
							MIN(log_payment.qty) AS qty, 
							MIN(log_payment.dis_amount) AS discount, log_payment.period,
							(MIN(log_payment.qty) * tbl_classes.fee) AS amount,
							MIN(log_payment.remark) AS remark, tbl_classes.title,
							MIN(tbl_payment.payment_no) AS receipt_no, log_payment.period
							FROM log_payment
							JOIN tbl_classes ON tbl_classes.pid = log_payment.class
							JOIN tbl_payment ON tbl_payment.pid = log_payment.payment
							WHERE log_payment.is_delete = 0
							AND log_payment.user = '". $user_id ."'
							AND tbl_classes.is_delete = 0
							AND log_payment.period = '". $period ."'
							AND tbl_payment.branch = '". $branch ."'
							AND tbl_payment.status = 'paid'
							GROUP BY log_payment.class, log_payment.period";
						
		$paid_classes = $this_ci->db->query($paid_class_sql)->result_array();
		
		$total_count += count($paid_classes);
		$total_amount += array_sum(array_column($paid_classes, 'amount')) - array_sum(array_column($paid_classes, 'discount'));
		$total_subtotal += array_sum(array_column($paid_classes, 'amount'));
		$total_discount += array_sum(array_column($paid_classes, 'discount'));
		$result['class'] = $paid_classes;
	}
		
	if ($status == 'unpaid') 
	{
		$unpaid_service_sql = "WITH RECURSIVE cte AS ( 
								SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
								log_join.item, log_join.date AS join_date, log_join.discount, 
								log_join.remark, 1 AS qty,
								tbl_inventory.title AS title, tbl_inventory.type AS item_type, 
								tbl_inventory.price_sale AS amount,
								TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d'), CURDATE()) AS diff,
								STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d') AS period_date
								FROM log_join
								JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
								JOIN tbl_users ON tbl_users.pid = log_join.user
								WHERE tbl_users.active = 1
								AND log_join.is_delete = 0
								AND log_join.branch = '". $branch ."'
								AND log_join.type = 'join_service'
								AND log_join.user = '". $user_id ."'
								AND log_join.active = 1
								AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000
								AND EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR_MONTH FROM CURDATE())
								
								UNION ALL
								
								SELECT (n + 1) AS n, fullname_en, id, user, item, join_date, discount, remark, qty, title, item_type, amount, diff, 
								DATE_ADD(period_date, INTERVAL 1 MONTH) AS period_date
								FROM cte 
								WHERE n < diff
							) 
							
							SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.item, cte.join_date,
							(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
							ELSE CONVERT(JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR), '\"')), DOUBLE) END) AS discount,
							cte.remark, cte.qty, cte.title, cte.item_type, cte.amount, cte.diff, 
							DATE_FORMAT(cte.period_date, '%Y-%m') AS period
							FROM cte
							LEFT JOIN log_payment ON log_payment.user = cte.user 
							AND log_payment.item = cte.item AND log_payment.is_delete = 0 
							AND log_payment.period = CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR)
							WHERE log_payment.period IS NULL
							ORDER BY fullname_en, item, title, period";
								
		$unpaid_services = $this_ci->db->query($unpaid_service_sql)->result_array();
		
		$total_count += count($unpaid_services);
		$total_amount += array_sum(array_column($unpaid_services, 'amount')) - array_sum(array_column($unpaid_services, 'discount'));
		$total_subtotal += array_sum(array_column($unpaid_services, 'amount'));
		$total_discount += array_sum(array_column($unpaid_services, 'discount'));
		$result['service'] = $unpaid_services;
	}
	else
	{
		$paid_service_sql = "SELECT MIN(log_payment.id) AS id, log_payment.item, 
							MIN(log_payment.qty) AS qty, 
							MIN(log_payment.dis_amount) AS discount, log_payment.period,
							(MIN(log_payment.qty) * tbl_inventory.price_sale) AS amount,
							MIN(log_payment.remark) AS remark, tbl_inventory.title,
							MIN(tbl_payment.payment_no) AS receipt_no, log_payment.period
							FROM log_payment
							JOIN tbl_inventory ON tbl_inventory.pid = log_payment.item
							JOIN tbl_payment ON tbl_payment.pid = log_payment.payment
							WHERE log_payment.is_delete = 0
							AND log_payment.user = '". $user_id ."'
							AND tbl_inventory.is_delete = 0
							AND log_payment.period = '". $period ."'
							AND tbl_payment.branch = '". $branch ."'
							AND tbl_payment.status = 'paid'
							GROUP BY log_payment.item, log_payment.period";
						
		$paid_services = $this_ci->db->query($paid_service_sql)->result_array();
		
		$total_count += count($paid_services);
		$total_amount += array_sum(array_column($paid_services, 'amount')) - array_sum(array_column($paid_services, 'discount'));
		$total_subtotal += array_sum(array_column($paid_services, 'amount'));
		$total_discount += array_sum(array_column($paid_services, 'discount'));
		$result['service'] = $paid_services;
	}
	
	// output
	return [
		'count' => $total_count,
		'subtotal' => $total_subtotal,
		'discount' => $total_discount,
		'total' => $total_amount,
		'result' => $result,
	];
	
	/* 
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	
	// start
	$result = [];
	$total_count = 0;
	$total_amount = 0;
	$total_subtotal = 0;
	$total_discount = 0;
	
	if(datalist_Table('tbl_users', 'active', $user_id)) {
		
		// class
		foreach( $this_ci->log_join_model->list('join_class', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) 
		{
				
			if(!datalist_Table('tbl_classes', 'is_delete', $e['class'])) {
				
				if ($status == 'unpaid') {
					
					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => $period
						
					]))) {
						
						$discount = $e['discount'];
					
						if(!is_numeric($e['discount'])) {
							
							$discount = 0;
							
							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];
							
							if(isset($discount_arr[$period])) {
								$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
							}
								
						}
						
						if(empty($discount)) {
				
							$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
							
							if(count($query) > 0) {
								
								$query = $query[0];
								
								if(!empty($query['dis_amount'])) {
									
									$discount_arr = [];
									$discount_arr[$period] = $query['dis_amount'];
									
									$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
									$discount = $query['dis_amount'];
									
								}
								
							}
							
						}
						
						$total_count++;
						$total_amount += datalist_Table('tbl_classes', 'fee', $e['class']) - $discount;
						$total_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']);
						$total_discount += $discount;
			
						$result['class'][] = [
							'id' => $e['id'],
							'class' => $e['class'],
							'period' => $period,
							'title' => datalist_Table('tbl_classes', 'title', $e['class']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_classes', 'fee', $e['class']),
							'remark' => $e['remark'],
							'qty' => 1,
						];
						
					}
					
				} else {
					
					$query = $this_ci->log_payment_model->list2([

						'user' => $user_id,
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => $period
						
					]);
					
					if(count($query) > 0) {
								
						$query = $query[0];
						
						if( !empty($this_ci->tbl_payment_model->setup_list(branch_now('pid'), [ 'pid' => $query['payment'], 'status' => 'paid' ]))) {
							
							$query2 = $this_ci->tbl_payment_model->setup_list(branch_now('pid'), [ 'pid' => $query['payment'], 'status' => 'paid' ])[0];
							
							$discount = $e['discount'];
						
							if(!is_numeric($e['discount'])) {
								
								$discount = 0;
								
								$discount_arr = json_decode($e['discount'], true);
								if(!is_array($discount_arr)) $discount_arr = [];
								
								if(isset($discount_arr[$period])) {
									$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
								}
									
							}
							
							if(empty($discount)) {
					
								$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
								
								if(count($query) > 0) {
									
									$query = $query[0];
									
									if(!empty($query['dis_amount'])) {
										
										$discount_arr = [];
										$discount_arr[$period] = $query['dis_amount'];
										
										$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
										$discount = $query['dis_amount'];
										
									}
									
								}
								
							}
							
							$total_count++;
							$total_amount += datalist_Table('tbl_classes', 'fee', $e['class']) - $discount;
							$total_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']);
							$total_discount += $discount;
				
							$result['class'][] = [
								'id' => $e['id'],
								'class' => $e['class'],
								'period' => $period,
								'title' => datalist_Table('tbl_classes', 'title', $e['class']),
								'discount' => $discount,
								'amount' => datalist_Table('tbl_classes', 'fee', $e['class']),
								'remark' => $e['remark'],
								'qty' => 1,
								'receipt_no' => $query2['payment_no'],
							];
							
						}
						
					}
					
				}
				
			}
			
		}
		
		
		// service
		foreach( $this_ci->log_join_model->list('join_service', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) 
		{
				
			if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {
				
				if ($status == 'unpaid') {
					
					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'item' => $e['item'],
						'is_delete' => 0,
						'period' => $period
						
					]))) {
						
						$discount = $e['discount'];
					
						if(!is_numeric($e['discount'])) {
							
							$discount = 0;
							
							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];
							
							if(isset($discount_arr[$period])) {
								$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
							}
							
						}
						
						if(empty($discount)) {
				
							$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
							
							if(count($query) > 0) {
								
								$query = $query[0];
								
								if(!empty($query['dis_amount'])) {
									
									$discount_arr = [];
									$discount_arr[$period] = $query['dis_amount'];
									
									$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
									$discount = $query['dis_amount'];
									
								}
								
							}
							
						}
						
						$total_count++;
						$total_amount += datalist_Table('tbl_inventory', 'price_sale', $e['item']) - $discount;
						$total_subtotal += datalist_Table('tbl_inventory', 'price_sale', $e['item']);
						$total_discount += $discount;
			
						$result['service'][] = [
							'id' => $e['id'],
							'item' => $e['item'],
							'period' => $period,
							'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_inventory', 'price_sale', $e['item']),
							'remark' => $e['remark'],
							'qty' => 1,
						];
						
					}
					
				} else {
					
					$query = $this_ci->log_payment_model->list2([

						'user' => $user_id,
						'item' => $e['item'],
						'is_delete' => 0,
						'period' => $period
						
					]); 
						
					if(count($query) > 0) {
								
						$query = $query[0];
						
						if( !empty($this_ci->tbl_payment_model->setup_list(branch_now('pid'), [ 'pid' => $query['payment'], 'status' => 'paid' ]))) {
							
							$query2 = $this_ci->tbl_payment_model->setup_list(branch_now('pid'), [ 'pid' => $query['payment'], 'status' => 'paid' ])[0];
						
							$discount = $e['discount'];
						
							if(!is_numeric($e['discount'])) {
								
								$discount = 0;
								
								$discount_arr = json_decode($e['discount'], true);
								if(!is_array($discount_arr)) $discount_arr = [];
								
								if(isset($discount_arr[$period])) {
									$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
								}
								
							}
							
							if(empty($discount)) {
					
								$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
								
								if(count($query) > 0) {
									
									$query = $query[0];
									
									if(!empty($query['dis_amount'])) {
										
										$discount_arr = [];
										$discount_arr[$period] = $query['dis_amount'];
										
										$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
										$discount = $query['dis_amount'];
										
									}
									
								}
								
							}
							
							$total_count++;
							$total_amount += datalist_Table('tbl_inventory', 'price_sale', $e['item']) - $discount;
							$total_subtotal += datalist_Table('tbl_inventory', 'price_sale', $e['item']);
							$total_discount += $discount;
				
							$result['service'][] = [
								'id' => $e['id'],
								'item' => $e['item'],
								'period' => $period,
								'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
								'discount' => $discount,
								'amount' => datalist_Table('tbl_inventory', 'price_sale', $e['item']),
								'remark' => $e['remark'],
								'qty' => 1,
								'receipt_no' => $query2['payment_no'],
							];
							
						}
						
					}
					
				}
				
			}
			
		}
		
	}
	
	// output
	return [
		'count' => $total_count,
		'subtotal' => $total_subtotal,
		'discount' => $total_discount,
		'total' => $total_amount,
		'result' => $result,
	];
	 */
}

function std_unpaid_result($user_id, $branch = '') {
	
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	
	// start
	$unpaid_result = [];
	$total_unpaid_count = 0;
	$total_unpaid_amount = 0;
	$total_unpaid_subtotal = 0;
	$total_unpaid_discount = 0;
	
	if(datalist_Table('tbl_users', 'active', $user_id)) {
		
		// unpaid item
		foreach( $this_ci->log_join_model->list('unpaid_item', $branch, [ 'user' => $user_id ]) as $e ) {
			
			if(empty($e['discount'])) {
				
				$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'item' => $e['item'] ], [ 'id' => 'DESC' ]);
				
				if(count($query) > 0) {
					
					$query = $query[0];
					
					if(!empty($query['dis_amount'])) {
						$this_ci->log_join_model->edit($e['id'], [ 'discount' => $query['dis_amount'] ]);
						$e['discount'] = $query['dis_amount'];
					}
					
				}
				
			}
			
			if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {
			
				$total_unpaid_count += $e['qty'];
				$total_unpaid_amount += $e['amount'] - $e['discount'];
				$total_unpaid_subtotal += $e['amount'];
				$total_unpaid_discount += $e['discount'];
				
				$unpaid_result['item'][] = [
					'id' => $e['id'],
					'item' => $e['item'],
					'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
					'qty' => $e['qty'],
					'discount' => $e['discount'],
					'amount' => $e['amount'],
					'remark' => $e['remark'],
					'month'	=> date('Y-m', strtotime($e['date']))
				];
			
			}
			
		}
		
		// unpaid class
		foreach( $this_ci->log_join_model->list('join_class', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) 
		{
			
			$join_date = $e['date'];

			$join_date_year = date('Y', strtotime($join_date));
			$today_year = date('Y');

			$join_date_month = date('m', strtotime($join_date));
			$today_month = date('m');
			
			$diff = -1;
			switch(datalist_Table('tbl_classes', 'type', $e['class'])) {
				case 'monthly':
					$diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
					break;
				case 'yearly':
					$diff = $today_year - $join_date_year;
					break;
			}
			
			if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
				if(class_credit_balance($e['class'], $user_id)['balance'] < 0) {
					$qty = floor(abs(class_credit_balance($e['class'], $user_id)['balance'] / datalist_Table('tbl_classes', 'credit', $e['class']))) + 1;
					
					$total_unpaid_count += $qty;
					$total_unpaid_amount += (datalist_Table('tbl_classes', 'fee', $e['class']) * $qty) - $e['discount'];
					$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']) * $qty;
					$total_unpaid_discount += $e['discount'];
					
					$unpaid_result['class'][$e['id']] = [
						'id' => $e['id'],
						'class' => $e['class'],
						'qty' => $qty,
						'title' => datalist_Table('tbl_classes', 'title', $e['class']),
						'discount' => $e['discount'],
						'amount' => datalist_Table('tbl_classes', 'fee', $e['class']) * $qty,
						'remark' => $e['remark'],
					];
				}
			}
			
			for($j=0; $j<=$diff; $j++) {
				
				switch(datalist_Table('tbl_classes', 'type', $e['class'])) {
					case 'monthly':
						$period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
						break;
					case 'yearly':
						$period = date('Y', strtotime('+'.$j.' year', strtotime($join_date)));
						break;
				}
				
				if(!datalist_Table('tbl_classes', 'is_delete', $e['class'])) {
					
					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => $period
						
					]))) {
						
						$discount = $e['discount'];
					
						if(!is_numeric($e['discount'])) {
							
							$discount = 0;
							
							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];
							
							if(isset($discount_arr[$period])) {
								$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
							}
								
						}
						
						if(empty($discount)) {
				
							$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
							
							if(count($query) > 0) {
								
								$query = $query[0];
								
								if(!empty($query['dis_amount'])) {
									
									$discount_arr = [];
									$discount_arr[$period] = $query['dis_amount'];
									
									$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
									$discount = $query['dis_amount'];
									
								}
								
							}
							
						}
						
						$total_unpaid_count++;
						$total_unpaid_amount += datalist_Table('tbl_classes', 'fee', $e['class']) - $discount;
						$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']);
						$total_unpaid_discount += $discount;
			
						$unpaid_result['class'][$e['id']] = [
							'id' => $e['id'],
							'class' => $e['class'],
							'period' => $period,
							'title' => datalist_Table('tbl_classes', 'title', $e['class']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_classes', 'fee', $e['class']),
							'remark' => $e['remark'],
							'qty' => 1,
							'month'	=> $period
						];
						
					}
					
				}
				
			}
			
		}
		
		
		// unpaid service
		foreach( $this_ci->log_join_model->list('join_service', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) 
		{
			$join_date = $e['date'];

			$join_date_year = date('Y', strtotime($join_date));
			$today_year = date('Y');

			$join_date_month = date('m', strtotime($join_date));
			$today_month = date('m');
			
			$diff = -1;
			switch(datalist_Table('tbl_inventory', 'type', $e['item'])) {
				case 'monthly':
					$diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
					break;
				case 'yearly':
					$diff = $today_year - $join_date_year;
					break;
				default:
					$diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
					break;
			}
			
			for($j=0; $j<=$diff; $j++) {
				
				switch(datalist_Table('tbl_inventory', 'type', $e['item'])) {
					case 'monthly':
						$period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
						break;
					case 'yearly':
						$period = date('Y', strtotime('+'.$j.' year', strtotime($join_date)));
						break;
					default:
						$period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
						break;
				}
				
				if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {
					
					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'item' => $e['item'],
						'is_delete' => 0,
						'period' => $period
						
					]))) {
						
						$discount = $e['discount'];
					
						if(!is_numeric($e['discount'])) {
							
							$discount = 0;
							
							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];
							
							if(isset($discount_arr[$period])) {
								$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
							}
							
						}
						
						if(empty($discount)) {
				
							$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);
							
							if(count($query) > 0) {
								
								$query = $query[0];
								
								if(!empty($query['dis_amount'])) {
									
									$discount_arr = [];
									$discount_arr[$period] = $query['dis_amount'];
									
									$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
									$discount = $query['dis_amount'];
									
								}
								
							}
							
						}
						
						$total_unpaid_count++;
						$total_unpaid_amount += datalist_Table('tbl_inventory', 'price_sale', $e['item']) - $discount;
						$total_unpaid_subtotal += datalist_Table('tbl_inventory', 'price_sale', $e['item']);
						$total_unpaid_discount += $discount;
			
						$unpaid_result['service'][] = [
							'id' => $e['id'],
							'item' => $e['item'],
							'period' => $period,
							'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_inventory', 'price_sale', $e['item']),
							'remark' => $e['remark'],
							'qty' => 1,
							'month' => $period,
						];
						
					}
					
				}
				
			}
			
		}
		
	}
	
	// output
	return [
		'count' => $total_unpaid_count,
		'subtotal' => $total_unpaid_subtotal,
		'discount' => $total_unpaid_discount,
		'total' => $total_unpaid_amount,
		'result' => $unpaid_result,
	];
	
}


function std_default_result($user_id,$branch = '', $type = '')
{
    $this_ci =& get_instance();

    if(empty($branch)) $branch = branch_now('pid');
    if(empty($type)) $type = 'all';

    if($type = 'all')
    {
        $item = true;
        $class = true;
        $service = true;
    }
    else
    {
        $item = ($type == 'item' ? true : false);
        $class = ($type == 'class' ? true : false);
        $service = ($type == 'service' ? true : false);
    }

    //default item
    $default_item_sql = "Select tbl_users.fullname_en, log_join.id, log_join.user,
										log_join.class, log_join.discount, log_join.remark, 1 AS qty,
										tbl_classes.title AS title, tbl_classes.type AS class_type, tbl_classes.fee AS amount
					FROM log_join
					LEFT JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
					JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0    
					JOIN tbl_users ON tbl_users.pid = log_join.user
					WHERE tbl_users.active = 1
					AND log_join.is_delete = 0
					  AND log_join.active = 1
					AND log_join.branch = '". $branch ."'
					AND log_join.type = 'unpaid_item'
					AND log_join.user = '". $user_id ."'";

    $default_item = $this_ci->db->query($default_item_sql)->result_array();
    $this_ci->db->reset_query();
    $unpaid_result['default_item'] = $default_item;


    //default classes
    $default_class_sql = "Select tbl_users.fullname_en, log_join.id, log_join.user,
										log_join.class, log_join.discount, log_join.remark, 1 AS qty,
										tbl_classes.title AS title, tbl_classes.type AS class_type, tbl_classes.fee AS amount
					FROM log_join
					LEFT JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
					JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0    
					JOIN tbl_users ON tbl_users.pid = log_join.user
					WHERE tbl_users.active = 1
					AND log_join.is_delete = 0
					  AND log_join.active = 1
					AND log_join.branch = '". $branch ."'
					AND log_join.type = 'join_class'
					AND log_join.user = '". $user_id ."'";


    $default_class = $this_ci->db->query($default_class_sql)->result_array();

    $this_ci->db->reset_query();

    $unpaid_result['default_class'] = $default_class;


    //default service
    $default_service_sql = "Select tbl_users.fullname_en, log_join.id, log_join.user,
										log_join.class, log_join.discount, log_join.remark, 1 AS qty,
											tbl_inventory.title AS title, tbl_inventory.type AS item_type, 
									tbl_inventory.price_sale AS amount,
									TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d'), CURDATE()) AS diff,
									STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d') AS period_date
					FROM log_join
					JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
					JOIN tbl_users ON tbl_users.pid = log_join.user
					WHERE tbl_users.active = 1
					AND log_join.is_delete = 0
					  AND log_join.active = 1
					AND log_join.branch = '". $branch ."'
					AND log_join.type = 'join_service'
					AND log_join.active = 1
					AND log_join.user = '". $user_id ."'";

    $default_service = $this_ci->db->query($default_service_sql)->result_array();
    $this_ci->db->reset_query();
    $unpaid_result['default_service'] = $default_service;

    // output
    return [
        'result' => $unpaid_result,
    ];

}
function std_unpaid_result2($user_id, $branch = '', $type = '', $next_month = '') {
	
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	if(empty($type)) $type = 'all';
	if(empty($next_month)) $next_month = 'N';
	
	$payment_date = 'CURDATE()';
	$class_end_date = date("Y-m-d");
	
	if ($next_month == 'Y')
	{
		$payment_date = 'DATE_ADD(CURDATE(), INTERVAL 1 MONTH)';
		$class_end_date = date("Y-m-d", strtotime($effectiveDate . "+1 months"));
	}
	
	if($type = 'all')
	{
		$item = true;
		$class = true;
		$service = true;
	}
	else
	{
		$item = ($type == 'item' ? true : false);
		$class = ($type == 'class' ? true : false);
		$service = ($type == 'service' ? true : false);
	}
	
	// start
	$unpaid_result = [];
	$total_unpaid_count = 0;
	$total_unpaid_amount = 0;
	$total_unpaid_subtotal = 0;
	$total_unpaid_discount = 0;
	
	if(datalist_Table('tbl_users', 'active', $user_id)) {

		if ($item)
		{	
			$unpaid_item_sql = "SELECT log_join.id, log_join.item, tbl_inventory.title AS title, log_join.qty, log_join.discount, log_join.amount, log_join.remark
					FROM log_join
					LEFT JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
					JOIN tbl_users ON tbl_users.pid = log_join.user
					WHERE tbl_users.active = 1
					AND log_join.is_delete = 0
					AND log_join.branch = '". $branch ."'
					AND log_join.type = 'unpaid_item'
					AND log_join.user = '". $user_id ."'";
						
			$unpaid_items = $this_ci->db->query($unpaid_item_sql)->result_array();
			
			$total_unpaid_count += array_sum(array_column($unpaid_items, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_items, 'amount')) - array_sum(array_column($unpaid_items, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_items, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_items, 'discount'));
			$unpaid_result['item'] = $unpaid_items;
		}
		else
		{
			$unpaid_result['item'] = [];
		}




        /*
        // unpaid item
        foreach( $this_ci->log_join_model->list('unpaid_item', $branch, [ 'user' => $user_id ]) as $e ) {

            if(empty($e['discount'])) {

                $query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'item' => $e['item'] ], [ 'id' => 'DESC' ]);

                if(count($query) > 0) {

                    $query = $query[0];

                    if(!empty($query['dis_amount'])) {
                        $this_ci->log_join_model->edit($e['id'], [ 'discount' => $query['dis_amount'] ]);
                        $e['discount'] = $query['dis_amount'];
                    }

                }

            }

            if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {

                $total_unpaid_count += $e['qty'];
                $total_unpaid_amount += $e['amount'] - $e['discount'];
                $total_unpaid_subtotal += $e['amount'];
                $total_unpaid_discount += $e['discount'];

                $unpaid_result['item'][] = [
                    'id' => $e['id'],
                    'item' => $e['item'],
                    'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
                    'qty' => $e['qty'],
                    'discount' => $e['discount'],
                    'amount' => $e['amount'],
                    'remark' => $e['remark'],
                    'month' => date('Y-m', strtotime($e['date'])),
                ];

            }

        } */
		
		
		if ($class)
		{	
			// unpaid class
			$unpaid_result['class'] = [];
			$unpaid_class_sql_year_month = "WITH RECURSIVE cte AS ( 
										SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
										log_join.class, 
										(CASE WHEN payments.period IS NULL 
										THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d') 
										ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS join_date, 
										log_join.discount, log_join.remark, 1 AS qty,
										tbl_classes.title AS title, tbl_classes.type AS class_type, tbl_classes.fee AS amount,
										(CASE WHEN tbl_classes.type = 'monthly' 
										THEN TIMESTAMPDIFF(MONTH, (CASE WHEN payments.period IS NULL 
										THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
										ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), ". $payment_date .") 
										ELSE TIMESTAMPDIFF(YEAR, (CASE WHEN payments.period IS NULL 
										THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
										ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), ". $payment_date .") END) AS diff,
										(CASE WHEN payments.period IS NULL 
										THEN STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d')
										ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS period_date
										FROM log_join
										JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
										JOIN tbl_users ON tbl_users.pid = log_join.user
										LEFT JOIN (
											SELECT period, class, user
											FROM log_payment
											WHERE log_payment.is_delete = 0 
											ORDER BY CONVERT((CASE WHEN REPLACE(COALESCE(period, ''), '-', '') = '' THEN '0' ELSE REPLACE(COALESCE(period, ''), '-', '') END), DECIMAL) DESC
											LIMIT 1
										) payments ON payments.class = log_join.class AND payments.user = log_join.user
										WHERE tbl_users.active = 1
										AND log_join.is_delete = 0
										AND log_join.branch = '". $branch ."'
										AND log_join.type = 'join_class'
										AND log_join.user = '". $user_id ."'
										AND log_join.active = 1
										AND (tbl_classes.type = 'monthly' OR tbl_classes.type = 'yearly')
										AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000 
										AND (CASE WHEN tbl_classes.type = 'monthly' 
										THEN (EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR_MONTH FROM ". $payment_date ."))
										ELSE (EXTRACT(YEAR FROM log_join.date) <= EXTRACT(YEAR FROM ". $payment_date .")) END)
										AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
											OR tbl_classes.date_end IS NULL 
											OR tbl_classes.date_end >= '". $class_end_date ."')
										
										UNION ALL
										
										SELECT (n + 1) AS n, fullname_en, id, user, class, join_date, discount, remark, qty, title, class_type, amount, diff, 
										(CASE WHEN class_type = 'monthly' THEN DATE_ADD(period_date, INTERVAL 1 MONTH) 
										ELSE DATE_ADD(period_date, INTERVAL 1 YEAR) END) AS period_date
										FROM cte 
										WHERE n < diff
									) 
									
									SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.class, cte.join_date,
									(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
									ELSE CONVERT((CASE WHEN COALESCE(cte.discount, '') = '' THEN '0' ELSE JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR), '\"')) END), DOUBLE) END) AS discount,
									cte.remark, cte.qty, cte.title, cte.class_type, cte.amount, cte.diff, 
									(CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
									ELSE DATE_FORMAT(cte.period_date, '%Y') END) AS period,
									(CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
									ELSE DATE_FORMAT(cte.period_date, '%Y') END) AS month
									FROM cte
									LEFT JOIN log_payment ON log_payment.user = cte.user 
									AND log_payment.class = cte.class AND log_payment.is_delete = 0 
									AND log_payment.period = CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR)
									WHERE log_payment.period IS NULL
									ORDER BY period,fullname_en, class, title";

            
			$unpaid_classes_monthly_yearly = $this_ci->db->query($unpaid_class_sql_year_month)->result_array();

			$total_unpaid_count += array_sum(array_column($unpaid_classes_monthly_yearly, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount')) - array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));
			
			$unpaid_class_sql_check_in = "SELECT log_join.id, log_join.class,
										(FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) AS qty, 
										tbl_classes.title AS title, log_join.discount,
										((FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) * tbl_classes.fee) AS amount,
										log_join.remark, '' AS period, '' AS month
										FROM log_join
										JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
										JOIN tbl_users ON tbl_users.pid = log_join.user
										JOIN (
											SELECT class, user, COUNT(1) AS BALANCE
											FROM log_join
											WHERE is_delete = 0
											AND type = 'class_attendance'
											GROUP BY class, user
										) used_credits ON used_credits.class = log_join.class AND used_credits.user = log_join.user
										JOIN (
											SELECT class, user, SUM(amount_1) - SUM(amount_0) AS BALANCE
											FROM log_point
											WHERE is_delete = 0
											AND type = 'class_credit'
											GROUP BY class, user
										) total_classes ON total_classes.class = log_join.class AND total_classes.user = log_join.user
										WHERE tbl_users.active = 1
										AND log_join.is_delete = 0
										AND log_join.branch = '". $branch ."'
										AND log_join.type = 'join_class'
										AND log_join.user = '". $user_id ."'
										AND log_join.active = 1
										AND tbl_classes.type = 'check_in'
										AND (total_classes.BALANCE - used_credits.BALANCE) < 0
										AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
											OR tbl_classes.date_end IS NULL 
											OR tbl_classes.date_end >= '". $class_end_date ."')";
											
			$unpaid_classes_check_in = $this_ci->db->query($unpaid_class_sql_check_in)->result_array();
			
			$total_unpaid_count += array_sum(array_column($unpaid_classes_check_in, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_classes_check_in, 'amount')) - array_sum(array_column($unpaid_classes_check_in, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_classes_check_in, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_classes_check_in, 'discount'));
			 
			$unpaid_result['class'] = array_merge($unpaid_classes_monthly_yearly, $unpaid_classes_check_in);
			
			// echo "<pre>";
			// print_r($unpaid_classes_monthly_yearly);
			// echo "</pre>";
			// AND log_payment.user = '". $user_id ."'
		}
		else
		{
			$unpaid_result['class'] = [];
		}


		/*
		// unpaid class
		foreach( $this_ci->log_join_model->list('join_class', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) {

			$join_date = $e['date'];

			$join_date_year = date('Y', strtotime($join_date));
			$today_year = date('Y');

			$join_date_month = date('m', strtotime($join_date));
			$today_month = date('m');

			$diff = -1;
			switch(datalist_Table('tbl_classes', 'type', $e['class'])) {
				case 'monthly':
					$diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
					break;
				case 'yearly':
					$diff = $today_year - $join_date_year;
					break;
			}

			if(datalist_Table('tbl_classes', 'type', $e['class']) == 'check_in') {
				if(class_credit_balance($e['class'], $user_id)['balance'] < 0) {
					$qty = floor(abs(class_credit_balance($e['class'], $user_id)['balance'] / datalist_Table('tbl_classes', 'credit', $e['class']))) + 1;

					$total_unpaid_count++;
					$total_unpaid_amount += (datalist_Table('tbl_classes', 'fee', $e['class']) * $qty) - $e['discount'];
					$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']) * $qty;
					$total_unpaid_discount += $e['discount'];

					$unpaid_result['class'][] = [
						'id' => $e['id'],
						'class' => $e['class'],
						'qty' => $qty,
						'title' => datalist_Table('tbl_classes', 'title', $e['class']),
						'discount' => $e['discount'],
						'amount' => datalist_Table('tbl_classes', 'fee', $e['class']) * $qty,
						'remark' => $e['remark'],
						'month' => date('Y-m'),
					];
				}
			}

			for($j=0; $j<=$diff; $j++) {

				switch(datalist_Table('tbl_classes', 'type', $e['class'])) {
					case 'monthly':
						$period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
						break;
					case 'yearly':
						$period = date('Y', strtotime('+'.$j.' year', strtotime($join_date)));
						break;
				}

				if(!datalist_Table('tbl_classes', 'is_delete', $e['class'])) {

					if( empty($this_ci->log_payment_model->list2([

						'user' => $user_id,
						'class' => $e['class'],
						'is_delete' => 0,
						'period' => $period

					]))) {

						$discount = $e['discount'];

						if(!is_numeric($e['discount'])) {

							$discount = 0;

							$discount_arr = json_decode($e['discount'], true);
							if(!is_array($discount_arr)) $discount_arr = [];

							if(isset($discount_arr[$period])) {
								$discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
							}

						}

						if(empty($discount)) {

							$query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);

							if(count($query) > 0) {

								$query = $query[0];

								if(!empty($query['dis_amount'])) {

									$discount_arr = [];
									$discount_arr[$period] = $query['dis_amount'];

									$this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
									$discount = $query['dis_amount'];

								}

							}

						}

						$total_unpaid_count++;
						$total_unpaid_amount += datalist_Table('tbl_classes', 'fee', $e['class']) - $discount;
						$total_unpaid_subtotal += datalist_Table('tbl_classes', 'fee', $e['class']);
						$total_unpaid_discount += $discount;

						$unpaid_result['class'][] = [
							'id' => $e['id'],
							'class' => $e['class'],
							'period' => $period,
							'title' => datalist_Table('tbl_classes', 'title', $e['class']),
							'discount' => $discount,
							'amount' => datalist_Table('tbl_classes', 'fee', $e['class']),
							'remark' => $e['remark'],
							'qty' => 1,
							'month' => $period,
						];

					}

				}

			}

		} */




        if ($service)
		{
			// unpaid service
			$unpaid_result['service'] = [];
			$unpaid_service_sql = "WITH RECURSIVE cte AS ( 
									SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
									log_join.item, log_join.date AS join_date, log_join.discount, 
									log_join.remark, 1 AS qty,
									tbl_inventory.title AS title, tbl_inventory.type AS item_type, 
									tbl_inventory.price_sale AS amount,
									TIMESTAMPDIFF(MONTH, STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d'), ". $payment_date .") AS diff,
									STR_TO_DATE(CONCAT(EXTRACT(YEAR_MONTH FROM log_join.date), '-01'),'%Y%m-%d') AS period_date
									FROM log_join
									JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
									JOIN tbl_users ON tbl_users.pid = log_join.user
									WHERE tbl_users.active = 1
									AND log_join.is_delete = 0
									AND log_join.branch = '". $branch ."'
									AND log_join.type = 'join_service'
									AND log_join.user = '". $user_id ."'
									AND log_join.active = 1
									AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000
									AND EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR_MONTH FROM ". $payment_date .")
									
									UNION ALL
									
									SELECT (n + 1) AS n, fullname_en, id, user, item, join_date, discount, remark, qty, title, item_type, amount, diff, 
									DATE_ADD(period_date, INTERVAL 1 MONTH) AS period_date
									FROM cte 
									WHERE n < diff
								) 
								
								SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.item, cte.join_date,
								(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
								ELSE CONVERT(JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR), '\"')), DOUBLE) END) AS discount,
								cte.remark, cte.qty, cte.title, cte.item_type, cte.amount, cte.diff, 
								DATE_FORMAT(cte.period_date, '%Y-%m') AS period, 
								DATE_FORMAT(cte.period_date, '%Y-%m') AS month
								FROM cte
								LEFT JOIN log_payment ON log_payment.user = cte.user 
								AND log_payment.item = cte.item AND log_payment.is_delete = 0 
								AND log_payment.period = CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR)
								WHERE log_payment.period IS NULL
								ORDER BY fullname_en, item, title, period";

            $this_ci->db->flush_cache();
			$unpaid_services = $this_ci->db->query($unpaid_service_sql)->result_array();
            $this_ci->db->reset_query();
			$total_unpaid_count += array_sum(array_column($unpaid_services, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_services, 'amount')) - array_sum(array_column($unpaid_services, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_services, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_services, 'discount'));
			
			$unpaid_result['service'] = $unpaid_services; 
		}
		else
		{
			$unpaid_result['service'] = [];
		}
	
		
		



        /*
        // unpaid service
        foreach( $this_ci->log_join_model->list('join_service', $branch, [ 'user' => $user_id, 'active' => 1 ]) as $e ) {

            $join_date = $e['date'];

            $join_date_year = date('Y', strtotime($join_date));
            $today_year = date('Y');

            $join_date_month = date('m', strtotime($join_date));
            $today_month = date('m');

            $diff = -1;
            switch(datalist_Table('tbl_inventory', 'type', $e['item'])) {
                case 'monthly':
                    $diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
                    break;
                case 'yearly':
                    $diff = $today_year - $join_date_year;
                    break;
                default:
                    $diff = (($today_year - $join_date_year) * 12) + ($today_month - $join_date_month);
                    break;
            }

            for($j=0; $j<=$diff; $j++) {

                switch(datalist_Table('tbl_inventory', 'type', $e['item'])) {
                    case 'monthly':
                        $period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
                        break;
                    case 'yearly':
                        $period = date('Y', strtotime('+'.$j.' year', strtotime($join_date)));
                        break;
                    default:
                        $period = date('Y-m', strtotime('+'.$j.' month', strtotime($join_date)));
                        break;
                }

                if(!datalist_Table('tbl_inventory', 'is_delete', $e['item'])) {

                    if( empty($this_ci->log_payment_model->list2([

                        'user' => $user_id,
                        'item' => $e['item'],
                        'is_delete' => 0,
                        'period' => $period

                    ]))) {

                        $discount = $e['discount'];

                        if(!is_numeric($e['discount'])) {

                            $discount = 0;

                            $discount_arr = json_decode($e['discount'], true);
                            if(!is_array($discount_arr)) $discount_arr = [];

                            if(isset($discount_arr[$period])) {
                                $discount += empty($discount_arr[$period]) ? 0 : $discount_arr[$period];
                            }

                        }

                        if(empty($discount)) {

                            $query = $this_ci->log_payment_model->list2([ 'user' => $user_id, 'class' => $e['class'] ], [ 'id' => 'DESC' ]);

                            if(count($query) > 0) {

                                $query = $query[0];

                                if(!empty($query['dis_amount'])) {

                                    $discount_arr = [];
                                    $discount_arr[$period] = $query['dis_amount'];

                                    $this_ci->log_join_model->edit($e['id'], [ 'discount' => json_encode($discount_arr) ]);
                                    $discount = $query['dis_amount'];

                                }

                            }

                        }

                        $total_unpaid_count++;
                        $total_unpaid_amount += datalist_Table('tbl_inventory', 'price_sale', $e['item']) - $discount;
                        $total_unpaid_subtotal += datalist_Table('tbl_inventory', 'price_sale', $e['item']);
                        $total_unpaid_discount += $discount;

                        $unpaid_result['service'][] = [
                            'id' => $e['id'],
                            'item' => $e['item'],
                            'period' => $period,
                            'title' => datalist_Table('tbl_inventory', 'title', $e['item']),
                            'discount' => $discount,
                            'amount' => datalist_Table('tbl_inventory', 'price_sale', $e['item']),
                            'remark' => $e['remark'],
                            'qty' => 1,
                            'month' => $period,
                        ];

                    }

                }

            }

        } */
		
	}
	
	// output
	return [
		'count' => $total_unpaid_count,
		'subtotal' => $total_unpaid_subtotal,
		'discount' => $total_unpaid_discount,
		'total' => $total_unpaid_amount,
		'result' => $unpaid_result,
	];
	
}

function std_unpaid_result2_2($user_id, $branch = '', $type = '') {
	
	$this_ci =& get_instance();
	
	if(empty($branch)) $branch = branch_now('pid');
	if(empty($type)) $type = 'all';
	
	if($type = 'all')
	{
		$item = true;
		$class = true;
		$service = true;
	}
	else
	{
		$item = ($type == 'item' ? true : false);
		$class = ($type == 'class' ? true : false);
		$service = ($type == 'service' ? true : false);
	}
	
	// start
	$unpaid_result = [];
	$total_unpaid_count = 0;
	$total_unpaid_amount = 0;
	$total_unpaid_subtotal = 0;
	$total_unpaid_discount = 0;
	
	if(datalist_Table('tbl_users', 'active', $user_id)) {

		if ($item)
		{	
			$unpaid_item_sql = "SELECT log_join.id, log_join.item, tbl_inventory.title AS title, log_join.qty, log_join.discount, log_join.amount, log_join.remark
					FROM log_join
					LEFT JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
					JOIN tbl_users ON tbl_users.pid = log_join.user
					WHERE tbl_users.active = 1
					AND log_join.is_delete = 0
					AND log_join.branch = '". $branch ."'
					AND log_join.type = 'unpaid_item'
					AND log_join.user = '". $user_id ."'";
						
			$unpaid_items = $this_ci->db->query($unpaid_item_sql)->result_array();
			
			$total_unpaid_count += array_sum(array_column($unpaid_items, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_items, 'amount')) - array_sum(array_column($unpaid_items, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_items, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_items, 'discount'));
			$unpaid_result['item'] = $unpaid_items;
		}
		else
		{
			$unpaid_result['item'] = [];
		}

		if ($class)
		{	
			// unpaid class
			$unpaid_result['class'] = [];
			$unpaid_class_sql_year_month = "WITH RECURSIVE cte AS ( 
										SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
										log_join.class, 
										(CASE WHEN payments.period IS NULL THEN log_join.date ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS join_date, 
										log_join.discount, log_join.remark, 1 AS qty,
										tbl_classes.title AS title, tbl_classes.type AS class_type, tbl_classes.fee AS amount,
										(CASE WHEN tbl_classes.type = 'monthly' 
										THEN TIMESTAMPDIFF(MONTH, (CASE WHEN payments.period IS NULL THEN log_join.date ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) 
										ELSE TIMESTAMPDIFF(YEAR, (CASE WHEN payments.period IS NULL THEN log_join.date ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END), DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) END) AS diff,
										(CASE WHEN payments.period IS NULL THEN log_join.date ELSE STR_TO_DATE(CONCAT(payments.period, '-01'),'%Y-%m-%d') END) AS period_date
										FROM log_join
										JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
										JOIN tbl_users ON tbl_users.pid = log_join.user
										LEFT JOIN (
											SELECT period, class, user
											FROM log_payment
											WHERE log_payment.is_delete = 0 
											AND log_payment.user = '". $user_id ."'
											ORDER BY CONVERT((CASE WHEN REPLACE(COALESCE(period, ''), '-', '') = '' THEN '0' ELSE REPLACE(COALESCE(period, ''), '-', '') END), DECIMAL) DESC
											LIMIT 1
										) payments ON payments.class = log_join.class AND payments.user = log_join.user
										WHERE tbl_users.active = 1
										AND log_join.is_delete = 0
										AND log_join.branch = '". $branch ."'
										AND log_join.type = 'join_class'
										AND log_join.user = '". $user_id ."'
										AND log_join.active = 1
										AND (tbl_classes.type = 'monthly' OR tbl_classes.type = 'yearly')
										AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000 
										AND (CASE WHEN tbl_classes.type = 'monthly' 
										THEN (EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR FROM DATE_ADD(CURDATE(), INTERVAL 1 MONTH)))
										ELSE (EXTRACT(YEAR FROM log_join.date) <= EXTRACT(YEAR FROM DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) END)
										AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
											OR tbl_classes.date_end IS NULL 
											OR tbl_classes.date_end >= '". date("Y-m-d") ."')
										
										UNION ALL
										
										SELECT (n + 1) AS n, fullname_en, id, user, class, join_date, discount, remark, qty, title, class_type, amount, diff, 
										(CASE WHEN class_type = 'monthly' THEN DATE_ADD(period_date, INTERVAL 1 MONTH) 
										ELSE DATE_ADD(period_date, INTERVAL 1 YEAR) END) AS period_date
										FROM cte 
										WHERE n < diff
									) 
									
									SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.class, cte.join_date,
									(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
									ELSE CONVERT((CASE WHEN COALESCE(cte.discount, '') = '' THEN '0' ELSE JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR), '\"')) END), DOUBLE) END) AS discount,
									cte.remark, cte.qty, cte.title, cte.class_type, cte.amount, cte.diff, 
									(CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
									ELSE DATE_FORMAT(cte.period_date, '%Y') END) AS period,
									(CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m')
									ELSE DATE_FORMAT(cte.period_date, '%Y') END) AS month
									FROM cte
									LEFT JOIN log_payment ON log_payment.user = cte.user 
									AND log_payment.class = cte.class AND log_payment.is_delete = 0 
									AND log_payment.period = CONVERT((CASE WHEN class_type = 'monthly' THEN DATE_FORMAT(cte.period_date, '%Y-%m') ELSE DATE_FORMAT(cte.period_date, '%Y') END), CHAR)
									WHERE log_payment.period IS NULL
									ORDER BY period,fullname_en, class, title";

            
			$unpaid_classes_monthly_yearly = $this_ci->db->query($unpaid_class_sql_year_month)->result_array();
			// verbose($unpaid_classes_monthly_yearly);

			$total_unpaid_count += array_sum(array_column($unpaid_classes_monthly_yearly, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount')) - array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_classes_monthly_yearly, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_classes_monthly_yearly, 'discount'));
			
			$unpaid_class_sql_check_in = "SELECT log_join.id, log_join.class,
										(FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) AS qty, 
										tbl_classes.title AS title, log_join.discount,
										((FLOOR(ABS((total_classes.BALANCE - used_credits.BALANCE) / tbl_classes.credit)) + 1) * tbl_classes.fee) AS amount,
										log_join.remark, '' AS period, '' AS month
										FROM log_join
										JOIN tbl_classes ON tbl_classes.pid = log_join.class AND tbl_classes.is_delete = 0
										JOIN tbl_users ON tbl_users.pid = log_join.user
										JOIN (
											SELECT class, user, COUNT(1) AS BALANCE
											FROM log_join
											WHERE is_delete = 0
											AND type = 'class_attendance'
											GROUP BY class, user
										) used_credits ON used_credits.class = log_join.class AND used_credits.user = log_join.user
										JOIN (
											SELECT class, user, SUM(amount_1) - SUM(amount_0) AS BALANCE
											FROM log_point
											WHERE is_delete = 0
											AND type = 'class_credit'
											GROUP BY class, user
										) total_classes ON total_classes.class = log_join.class AND total_classes.user = log_join.user
										WHERE tbl_users.active = 1
										AND log_join.is_delete = 0
										AND log_join.branch = '". $branch ."'
										AND log_join.type = 'join_class'
										AND log_join.user = '". $user_id ."'
										AND log_join.active = 1
										AND tbl_classes.type = 'check_in'
										AND (total_classes.BALANCE - used_credits.BALANCE) < 0
										AND (CAST(tbl_classes.date_end AS CHAR(10)) = '0000-00-00'
											OR tbl_classes.date_end IS NULL 
											OR tbl_classes.date_end >= '". date("Y-m-d") ."')";
											
			$unpaid_classes_check_in = $this_ci->db->query($unpaid_class_sql_check_in)->result_array();
			
			$total_unpaid_count += array_sum(array_column($unpaid_classes_check_in, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_classes_check_in, 'amount')) - array_sum(array_column($unpaid_classes_check_in, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_classes_check_in, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_classes_check_in, 'discount'));
			 
			$unpaid_result['class'] = array_merge($unpaid_classes_monthly_yearly, $unpaid_classes_check_in);
		}
		else
		{
			$unpaid_result['class'] = [];
		}

        if ($service)
		{
			// unpaid service
			$unpaid_result['service'] = [];
			$unpaid_service_sql = "WITH RECURSIVE cte AS ( 
									SELECT 0 AS n, tbl_users.fullname_en, log_join.id, log_join.user,
									log_join.item, log_join.date AS join_date, log_join.discount, 
									log_join.remark, 1 AS qty,
									tbl_inventory.title AS title, tbl_inventory.type AS item_type, 
									tbl_inventory.price_sale AS amount,
									TIMESTAMPDIFF(MONTH, log_join.date, DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AS diff,
									log_join.date AS period_date
									FROM log_join
									JOIN tbl_inventory ON tbl_inventory.pid = log_join.item AND tbl_inventory.is_delete = 0
									JOIN tbl_users ON tbl_users.pid = log_join.user
									WHERE tbl_users.active = 1
									AND log_join.is_delete = 0
									AND log_join.branch = '". $branch ."'
									AND log_join.type = 'join_service'
									AND log_join.user = '". $user_id ."'
									AND log_join.active = 1
									AND CONVERT(CAST(log_join.date AS CHAR(4)), DECIMAL) >= 2000
									AND EXTRACT(YEAR_MONTH FROM log_join.date) <= EXTRACT(YEAR_MONTH FROM DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
									
									UNION ALL
									
									SELECT (n + 1) AS n, fullname_en, id, user, item, join_date, discount, remark, qty, title, item_type, amount, diff, 
									DATE_ADD(period_date, INTERVAL 1 MONTH) AS period_date
									FROM cte 
									WHERE n < diff
								) 
								
								SELECT cte.n, cte.fullname_en, cte.id, cte.user, cte.item, cte.join_date,
								(CASE WHEN cte.discount REGEXP '^[0-9]+$' THEN cte.discount 
								ELSE CONVERT(JSON_EXTRACT((CONVERT(cte.discount,  JSON)), CONCAT('$.\"', CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR), '\"')), DOUBLE) END) AS discount,
								cte.remark, cte.qty, cte.title, cte.item_type, cte.amount, cte.diff, 
								DATE_FORMAT(cte.period_date, '%Y-%m') AS period, 
								DATE_FORMAT(cte.period_date, '%Y-%m') AS month
								FROM cte
								LEFT JOIN log_payment ON log_payment.user = cte.user 
								AND log_payment.item = cte.item AND log_payment.is_delete = 0 
								AND log_payment.period = CONVERT(DATE_FORMAT(cte.period_date, '%Y-%m'), CHAR)
								WHERE log_payment.period IS NULL
								ORDER BY fullname_en, item, title, period";

            $this_ci->db->flush_cache();
			$unpaid_services = $this_ci->db->query($unpaid_service_sql)->result_array();
            $this_ci->db->reset_query();
			$total_unpaid_count += array_sum(array_column($unpaid_services, 'qty'));
			$total_unpaid_amount += array_sum(array_column($unpaid_services, 'amount')) - array_sum(array_column($unpaid_services, 'discount'));
			$total_unpaid_subtotal += array_sum(array_column($unpaid_services, 'amount'));
			$total_unpaid_discount += array_sum(array_column($unpaid_services, 'discount'));
			
			$unpaid_result['service'] = $unpaid_services; 
		}
		else
		{
			$unpaid_result['service'] = [];
		}
		
	}
	
	// output
	return [
		'count' => $total_unpaid_count,
		'subtotal' => $total_unpaid_subtotal,
		'discount' => $total_unpaid_discount,
		'total' => $total_unpaid_amount,
		'result' => $unpaid_result,
	];
	
}

function wa_format($phone) {
	
	if($phone[0] == '+') {
		
		return str_replace([' ', ',', '+', '-', '(', ')', 'm', 'f'], '', $phone);
		
	} else {
		
		$phone_code = datalist_Table('tbl_secondary', 'phone_code', branch_now('country'));
		return $phone_code.str_replace([' ', ',', '+', '-', '(', ')', 'm', 'f'], '', $phone);
		
	}
	
}

function time_elapsed_string($datetime, $full = false) {
	
	if(empty($datetime)) {
		
		return '-';
		
	} else {
	
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'now';
		
	}
}

function group_by($key, $data) {
    $result = array();

    foreach($data as $val) {
		
		if (is_array($val))
		{
			if(array_key_exists($key, $val)){
				//$result[][$val[$key]][] = $val;
				$result[$val[$key]][] = $val;
			}else{
				$result[""][] = $val;
			}
		}
    }

    return $result;
}


function singledimensional($key, $array)
{
    $res = array();
	foreach ($array as $i => $item) {
		if (is_array($item)){
			$temparr = $item;
			$item[$key] = array();
			$res[] = $item;
			if (!empty($temparr[$key]) ){
				$child = singledimensional($key, $temparr[$key]);
				
				foreach ($child as $c) {
				  $res[] = $c;
				}
			}
		}
	}
    return $res;
}

function search($array, $key, $value)
{
	$results = array();

	if (is_array($array)) {
		if (isset($array[$key]) && $array[$key] == $value) {
			$results[] = $array;
		}

		foreach ($array as $subarray) {
			$results = array_merge($results, search($subarray, $key, $value));
		}
	}

	return $results;
}

function verbose($data)
{
	echo '<pre>'; print_r($data); //exit;
}

function datetoname($date) {
    $datetime = DateTime::createFromFormat('Y-m-d', $date);
    return $datetime->format('D');
}

function array_single($array) { 
	$result = [];
	foreach($array as $arr)
	{
		$result = array_merge($result , $arr);
	}

	return $result; 
}


function print_array($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function getlastestfourdate($today) {
    $dates = [];
    $previousDate = new DateTime(date("Y-m-d", strtotime($today)));
    $today = new DateTime(date("Y-m-d", strtotime($today)));
    $previousDate->modify('-4 day');

    while ($previousDate <= $today ) {
        $dates[] = $previousDate->format('Y-m-d');
        $previousDate->modify('+1 day');
    }

    return $dates;
}

function getDatesByDayOfWeek($dayOfWeek, $today) {
    $dates = [];
    $startDate = new DateTime(date("Y-m-d", strtotime($today)));
    $endDate = new DateTime(date("Y-m-d", strtotime($today)));
    $startDate->modify('-1 month');

    while ($startDate <= $endDate ) {
        if ($startDate->format('w') === $dayOfWeek) {
            $dates[] = $startDate->format('Y-m-d');
        }
        $startDate->modify('+1 day');
    }

    return $dates;
}

function getDayOfWeek($date) {
    // Create a DateTime object with the given date
    $dateTime = new DateTime($date);
    
    // Get the day of the week (0 for Sunday, 1 for Monday, etc.)
    $dayOfWeek = $dateTime->format('w');
    
    return $dayOfWeek;
    
    // Define an array with the day names
    // $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    
    // Return the day of the week
    // return $days[$dayOfWeek];
}

function getDatesBetween($start, $end) {
    $dates = array();
    
    // Convert the start and end dates to Unix timestamps
    $startTime = strtotime($start);
    $endTime = strtotime($end);
    
    // Loop through the dates and add them to the array
    while ($startTime <= $endTime) {
        $dates[] = date('Y-m-d', $startTime);
        $startTime = strtotime('+1 day', $startTime);
    }
    
    return $dates;
}
