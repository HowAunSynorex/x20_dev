<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'api';
		
	}
	
	public function payment_qr($id)
	{
		
		$c = file_get_contents('https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl='.urlencode(base_url('payment/add/'.$id)).'&choe=UTF-8', true);
		header ('Content-Type: image/png');
		echo $c;
		
	}
	
	public function sync_classes()
	{
		
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_users_model');
		$this->load->model('log_join_model');
		
		$sql = '
		
			SELECT * FROM tbl_secondary
			WHERE is_delete = 0
			AND branch = "' . branch_now('pid') . '"
			AND type IN ("childcare", "transport")
			UNION ALL
			SELECT s.* FROM tbl_secondary s
			INNER JOIN log_join l
			ON s.pid = l.secondary
			AND l.type = "secondary"
			AND l.active = 1
			AND l.branch = "' . branch_now('pid') . '"
			AND s.type IN ("childcare", "transport")
		';
		
		$result = $this->db->query($sql)->result_array();
				
		foreach($result as $e) {
			
			$check = $this->tbl_classes_model->list(branch_now('pid'), [
				'secondary_parent' => $e['pid'],
			]);
			
			if(count($check) == 0) {
				
				$this->tbl_classes_model->add([
					'type'					=> 'monthly',
					'secondary_parent'		=> $e['pid'],
					'title'					=> $e['title'],
					'branch'				=> branch_now('pid'),
					'fee'					=> empty($e['price']) ? 0 : $e['price'],
					'create_by'				=> auth_data('pid'),
					'is_hidden'				=> 1
				]);
				
				sleep(1);
			}
			
		}
		
		$students = $this->tbl_users_model->list('student', branch_now('pid'), [
			'active'	=> 1,
		]);
		
		foreach($students as $e) {
			
			$sql = '
			
				SELECT * FROM tbl_classes
				WHERE is_delete = 0
				AND branch = "' . branch_now('pid') . '"
				AND secondary_parent IS NOT NULL
				AND secondary_parent = "' . $e['childcare'] . '"
			
			';
			
			$check_childcare = $this->db->query($sql)->result_array();
			
			if(count($check_childcare) > 0) {
				
				$check_childcare = $check_childcare[0];
				
				$check_join_class = $this->log_join_model->list('join_class', branch_now('pid'), [
					'user'			=> $e['pid'],
					'class'			=> $check_childcare['pid'],
				]);
				
				if(count($check_join_class) == 0) {
					$this->log_join_model->add([
						'type'			=> 'join_class',
						'branch'		=> branch_now('pid'),
						'user'			=> $e['pid'],
						'class'			=> $check_childcare['pid'],
						'active'		=> 1,
						'date'			=> date('Y-m-d'),
						'create_by'		=> auth_data('pid'),
					]);
				}
				
			}
			
			$sql = '
			
				SELECT * FROM tbl_classes
				WHERE is_delete = 0
				AND branch = "' . branch_now('pid') . '"
				AND secondary_parent IS NOT NULL
				AND secondary_parent = "' . $e['transport'] . '"
			
			';
			
			$check_transport = $this->db->query($sql)->result_array();
			
			if(count($check_transport) > 0) {
				
				$check_transport = $check_transport[0];
				
				$check_join_class = $this->log_join_model->list('join_class', branch_now('pid'), [
					'user'			=> $e['pid'],
					'class'			=> $check_transport['pid'],
				]);
				
				if(count($check_join_class) == 0) {
					$this->log_join_model->add([
						'type'			=> 'join_class',
						'branch'		=> branch_now('pid'),
						'user'			=> $e['pid'],
						'class'			=> $check_transport['pid'],
						'active'		=> 1,
						'date'			=> date('Y-m-d'),
						'create_by'		=> auth_data('pid'),
					]);
				}
				
			}
			
		}
		
		echo 'synced'; exit;
		
	}
	
	public function outstanding_count()
	{
		
		header('Content-type: application/json');
		
		$this->load->model('tbl_branches_model');
		
		$result = $this->tbl_branches_model->view(post_data('branch'))[0];
		
		die(json_encode([ 'status' => 'ok', 'result' => $result ]));
		
	}
	
	public function update_count()
	{
		
		header('Content-type: application/json');
		
		$this->load->model('tbl_branches_model');
		
		$this->tbl_branches_model->edit(post_data('branch'), [
			'outstanding_count' 	=> post_data('outstanding_count'),
			'outstanding_update' 	=> date('Y-m-d H:i:s'),
		]);
		
		die(json_encode([ 'status' => 'ok' ]));
		
	}
	
	public function outstanding_reports()
	{
		
		header('Content-type: application/json');
		
		$this->load->model('log_join_model');
		$this->load->model('log_payment_model');
        $this->load->model('tbl_users_model');
		$this->load->model('tbl_branches_model');
		
		$branch_id = branch_now('pid');
		$branch_info = $this->tbl_branches_model->view(post_data('branch'))[0];
		
		$result = [];
		
		$limit = 50;
		
		$sql = '
		
			SELECT tbl_users.*, forms.title AS form_title, parents.fullname_phone AS parent_fullname_phone
			FROM tbl_users
			LEFT JOIN tbl_secondary forms ON forms.pid = tbl_users.form
			LEFT JOIN (
				SELECT log_join.user, GROUP_CONCAT(CONCAT(parents.fullname_en, " (", parents.phone, ")") SEPARATOR "<br/>") AS fullname_phone
				FROM log_join
				JOIN tbl_users parents ON parents.pid = log_join.parent
				WHERE log_join.is_delete = 0
				AND log_join.branch = "'.post_data('branch').'"
				AND log_join.type = "join_parent"
				AND log_join.active = 1
				GROUP BY log_join.user
			) parents ON parents.user = tbl_users.pid
			WHERE tbl_users.is_delete = 0
			AND tbl_users.type = "student"
			AND tbl_users.active = 1
			AND tbl_users.branch = "'.post_data('branch').'"
			
		';
			
		if(strlen($_POST['search']) > 0) {
			$sql .= ' AND tbl_users.fullname_en LIKE "%'.$_POST['search'].'%"';
		}
			
		if(strlen($_POST['search_form']) > 0) {
			$sql .= ' AND tbl_users.form = "'.$_POST['search_form'].'"';
		}
		
		/* if(strlen($_POST['sort']) > 0) {
			$sql .= ' ORDER BY tbl_users.birthday ' . strtoupper($_POST['sort']);
		} */
		
		if(strlen($_POST['sort']) > 0) {
			$sql .= ' ORDER BY (CASE WHEN COALESCE(fullname_en, "") = "" THEN 1 ELSE 0 END) '. strtoupper($_POST['sort']) .', fullname_en '. strtoupper($_POST['sort']) .', fullname_cn '. strtoupper($_POST['sort']);
		}
		else {
			$sql .= ' ORDER BY  tbl_users.code ASC, fullname_en ASC';
		}
			
		$sql .= ' LIMIT ' . post_data('page') . ', '.$limit;
		
		$result_std = $this->db->query($sql)->result_array();
		
		foreach($result_std as $e) {
			
			$std_unpaid_result = std_unpaid_result2($e['pid'], $branch_id);
			$new_unpaid_class = [];
			$material_fee = [];
			$subsidy_fee = [];
			$with_class_bundle = [];
			$without_class_bundle = [];
			$total_material_fee = 0;
			$total_subsidy_fee = 0;
            $discount_value = 0;

			if($std_unpaid_result['count'] > 0) {
				
				if(isset($std_unpaid_result['result']['class'])) {
					
					foreach($std_unpaid_result['result']['class'] as $e2) {
						$check_class_bunlde = $this->log_join_model->list('class_bundle_course', $branch_id, [
							'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
						]);
						if(count($check_class_bunlde) > 0) {
							$check_class_bunlde = $check_class_bunlde[0];
							$with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
						} else {
							$without_class_bundle[]['data'] = $e2;
						}
					}
					
					foreach($with_class_bundle as $k => $v) {
						$check_bundle_price = $this->log_join_model->list('class_bundle_price', $branch_id, [
							'parent' => $k,
							'qty' => count($v['data'])
						]);
						if(count($check_bundle_price) > 0) {
							$check_bundle_price = $check_bundle_price[0];
							$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
							$material_fee[] = [
								'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
								'fee' => $check_bundle_price['material']
							];
							foreach($v['data'] as $k2 => $v2) {
								$class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
								$with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                //$discount_value = $with_class_bundle[$k]['data'][$k2]['discount'] ;
								$with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
								$with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
							}
						} else {
							$check_bundle_price = $this->db->query('
								SELECT * FROM log_join
								WHERE is_delete = 0
								AND type = "class_bundle_price"
								AND branch = "' . $branch_id . '"
								AND parent = "' . $k . '"
								AND qty < '.count($v['data']).'
								ORDER BY qty DESC
							')->result_array();
							if(count($check_bundle_price) > 0) {
								$check_bundle_price = $check_bundle_price[0];
								$each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
								for($i=0; $i<floor(count($v['data']) / $check_bundle_price['qty']); $i++) {
									$material_fee[] = [
										'title'	=> 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
										'fee' => $check_bundle_price['material']
									];
								}
								foreach($v['data'] as $k2 => $v2) {
									if($k2 >= $check_bundle_price['qty'] * floor(count($v['data']) / $check_bundle_price['qty'])) {
										$without_class_bundle[0]['data'][] = $v2;
										unset($with_class_bundle[$k]['data'][$k2]);
									} else {
										$class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
										$with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                        //$discount_value = $with_class_bundle[$k]['data'][$k2]['discount'] ;
										$with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
										$with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
									}
								}
							}
						} 
					}
					
					foreach($with_class_bundle as $k => $v) {
						$bundle_subsidy = $this->db->query('
							SELECT * FROM log_join
							WHERE is_delete = 0
							AND type = "class_bundle_price"
							AND branch = "' . $branch_id . '"
							AND parent = "' . $k . '"
							AND subsidy > 0
							AND qty <= ' . count($v['data']) . '
							ORDER BY qty DESC
						')->result_array();
						if(count($bundle_subsidy) > 0) {
							$bundle_subsidy = $bundle_subsidy[0];
							for($i=0; $i<floor(count($v['data']) / $bundle_subsidy['qty']); $i++) {
								$subsidy_fee[] = [
									'title'	=> 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
									'fee' => $bundle_subsidy['subsidy']
								];
							}
						}
					}
					
					foreach($material_fee as $e2) {
						$total_material_fee += $e2['fee'];
					}
					
					foreach($subsidy_fee as $e2) {
						$total_subsidy_fee += $e2['fee'];
					}
				}
			}


			
			$total_payment = 0;
			$total_discount = 0;
			$total = 0;

			$new_result = [];
			
			$new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);

			foreach($new_unpaid_class as $e2) {
				foreach($e2['data'] as $e3) {
					if(isset($e3['amount'])) {
						$new_result['result']['class'][] = $e3;
						$total_payment += $e3['amount'] - $e3['discount'];
                        $discount_value += $e3['discount'];
					}
				}
				if(isset($e2['data']['amount'])) {
					$new_result['result']['class'][] = $e2['data'];
					$total_payment += $e2['data']['amount'] - $e2['data']['discount'];
                    $discount_value += $e2['data']['discount'];
				}
			}


			if(isset($std_unpaid_result['result']['item'])) {
			
				$total_payment += array_sum(array_column($std_unpaid_result['result']['item'], 'amount'));
				$total_discount += array_sum(array_column($std_unpaid_result['result']['item'], 'discount'));
                //$discount_value += array_sum(array_column($std_unpaid_result['result']['item'], 'discount'));
				/* foreach($std_unpaid_result['result']['item'] as $e2) {
					$total_payment += $e2['amount'];
					$total_discount += $e2['discount'];
				} */
			}
			
			if(isset($std_unpaid_result['result']['service'])) {
				
				//$std_unpaid_result['result']['service'] = search($std_unpaid_result['result']['service'], 'period', date('Y-m'));
				
				$total_payment += array_sum(array_column($std_unpaid_result['result']['service'], 'amount'));
				$total_discount += array_sum(array_column($std_unpaid_result['result']['service'], 'discount'));
                //$discount_value += array_sum(array_column($std_unpaid_result['result']['service'], 'discount'));
				/* foreach($std_unpaid_result['result']['service'] as $e2) {
					$total_payment += $e2['amount'];
					$total_discount += $e2['discount'];
				} */
			}
			
			$total_payment += $total_material_fee;
			$total_discount += $total_subsidy_fee;

            $student_data = $this->tbl_users_model->view($e['pid'])[0];
            if ($student_data['transport_title'] != "")
            {
                $total_payment += $student_data['transport_price'];
                $new_result['result']['others'][] = array(
                    'title' => $student_data['transport_title'],
                    'amount' => $student_data['transport_price'],
                    'qty' => 1
                );
            }

            if ($student_data['childcare_title'] != "")
            {
                $total_payment += $student_data['childcare_price'];
                $new_result['result']['others'][] = array(
                    'title' => $student_data['childcare_title'],
                    'amount' => $student_data['childcare_price'],
                    'qty' => 1
                );
            }

            foreach($material_fee as $material_row)
            {
                $new_result['result']['others'][] = array(
                    'title' => $material_row['title'],
                    'amount' => $material_row['fee'],
                    'qty' => 1
                );
            }

            foreach($subsidy_fee as $subsidy_row)
            {
                $new_result['result']['others'][] = array(
                    'title' => $subsidy_row['title'],
                    'amount' => -1 * abs($subsidy_row['fee']),
                    'qty' => 1
                );
            }

            $new_result['result']['others'][] = array(
                'title' => 'Discount',
                'amount' => -1 * abs($discount_value),
                'qty' => 1
            );

            $total = $total_payment - $total_discount;

			$new_result['subtotal'] = $total_payment;
			$new_result['discount'] = $total_discount;
			$new_result['total'] = $total;

			$e['std_unpaid_result'] = $new_result;
			$e['branch_result'] = $branch_info;
			$e['app_title'] = app('title');
			
			if($std_unpaid_result['count'] > 0) {
				$result[] = $e;
			}
			
		}
		
		die(json_encode([ 'status' => 'ok', 'result' => $result, 'next_offest' => post_data('page') + $limit ]));
		
	}
	
	public function advanced_reports()
	{
		
		header('Content-type: application/json');
		
		$this->load->model('log_join_model');
		$this->load->model('log_payment_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('tbl_payment_model');
		
		$result = [];
		
		$limit = 50;
		
		$sql = '
		
			SELECT *, (year(CURDATE()) - year(birthday)) AS age 
			FROM tbl_users
			WHERE is_delete = 0
			AND type = "student"
			AND active = 1
			AND branch = "'.post_data('branch').'"
			
		';
		
		if(strlen($_POST['sort']) > 0) {
			$sql .= ' ORDER BY (CASE WHEN COALESCE(fullname_en, "") = "" THEN 1 ELSE 0 END) '. strtoupper($_POST['sort']) .', fullname_en '. strtoupper($_POST['sort']) .', fullname_cn '. strtoupper($_POST['sort']);
		}
		else {
			$sql .= ' ORDER BY (CASE WHEN COALESCE(fullname_en, "") = "" THEN 1 ELSE 0 END) ASC, fullname_en ASC, fullname_cn ASC';
		}
			
		$sql .= ' LIMIT ' . post_data('page') . ', '.$limit;

		$result_std = $this->db->query($sql)->result_array();
		
		if(strlen($_POST['search']) > 0) {
			$period = $_POST['search'];
		} else {
			$period = date('Y-m');
		}
		
		if(strlen($_POST['search_status']) > 0) {
			$status = $_POST['search_status'];
		} else {
			$status = 'paid';
		}
		
		foreach($result_std as $e) {
			
			$std_advanced_payment = std_status_class($e['pid'], post_data('branch'), $period, $status);
			$e['std_advanced_payment'] = $std_advanced_payment;
			$e['branch_result'] = $this->tbl_branches_model->view(post_data('branch'))[0];
			$e['app_title'] = app('title');
			
			if($std_advanced_payment['count'] > 0) {
				$result[] = $e;
			}
			
		}
		
		die(json_encode([ 'status' => 'ok', 'result' => $result, 'next_offest' => post_data('page') + $limit ]));
		
	}

	/*
	 * auth_login
	 *
	**/
	public function auth_login()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->login( post_data('username'), post_data('password') );

		if( $login != false ) {
			
			$this->tbl_users_model->edit($login, [
				'token' => md5(time())
			]);

			$result = $this->tbl_users_model->view($login)[0];

			$result['image_src'] = pointoapi_UploadSource($result['image']);

			unset($result['password']);

			die(json_encode([ 'status' => 'ok', 'message' => 'Login successfully', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'failed', 'message' => 'Login failed' ]));

		}
        
	}

	/*
	 * auth_session
	 *
	**/
	public function auth_session()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];

			$login['image_src'] = pointoapi_UploadSource($login['image']);

			unset($login['password']);

			die(json_encode([ 'status' => 'ok', 'result' => $login ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}
        
	}

	/*
	 * auth_change_password
	 *
	**/
	public function auth_change_password()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		/// check param
		if(
			!empty(post_data('password_old')) && 
			!empty(post_data('password_new'))
		) {
			
			$login = $this->tbl_users_model->me_token( post_data('token') );

			// check token
			if( count($login) == 1 ) {
				
				$login = $login[0];
				
				$login['image_src'] = pointoapi_UploadSource($login['image']);

				// unset($login['password']);
				
				// check old pass
				if( !password_verify(post_data('password_old'), $login['password']) ) {
					
					die(json_encode([ 'status' => 'failed', 'message' => 'Old password not match' ]));
					
				}
				
				// save
				$this->tbl_users_model->edit($login['pid'], [
					'password' => password_hash(post_data('password_new'), PASSWORD_DEFAULT),
				]);
				
				// new result
				$result = $this->tbl_users_model->me_token( post_data('token') )[0];

				die(json_encode([ 'status' => 'ok', 'result' => $result, 'message' => 'Password updated successfully' ]));

			} else {

				die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

			}
			
		} else {
			
			die(json_encode([ 'status' => 'param_error' ]));
			
		}
        
	}

}
