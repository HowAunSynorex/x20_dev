<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Secondary extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->group = 'secondary';
		$this->single = 'secondary';

		$this->load->model('tbl_secondary_model');
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_inventory_model');
		$this->load->model('log_join_model');
		
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

		auth_must('login');
	}

	public function list($type)
	{
		
		auth_must('login');
		check_module_page('Secondary/Read');
		
		switch($type) {
			case 'school':
				check_module_page('Secondary/Modules/Schools');
				break;
			case 'course':
				check_module_page('Secondary/Modules/Courses');
				break;
			case 'item_cat':
				check_module_page('Secondary/Modules/ItemCat');
				break;
			case 'transport':
				check_module_page('Secondary/Modules/Transports');
				break;
			default:
				check_module_page('Secondary/Modules/PaymentMethods');
				break;
		}

		if(isset( datalist('secondary_type')[$type] )) {

			$data['thispage'] = [
				'title' => datalist('secondary_type')[$type]['label'],
				'group' => $this->group,
				'js' => $this->group.'/list',
				'css' => $this->group.'/list',
				'type' => $type,
			];

			$data['result'] = $this->tbl_secondary_model->list($type, branch_now('pid'));
			
			$data['null'] = $this->tbl_secondary_model->null_list($type, ['active' => 1]);
			// $data['method'] = $this->tbl_secondary_model->list('payment_method', null, ['active' => 1]);
			// $data['item_cat'] = $this->tbl_secondary_model->list('item_cat', null, ['active' => 1]);
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/list', $data);
			$this->load->view('inc/footer', $data);

		} else {

			alert_new('warning', 'Data type not found');

			redirect();
			
		}

	}

	public function add($type = '')
	{
		
		auth_must('login');
		check_module_page('Secondary/Create');
		
		$data['course'] = $this->tbl_secondary_model->active_list('course', branch_now('pid'), ['active' => 1]);

		if (isset($_POST['save'])) {

			$post_data = [];

			foreach (['title', 'active', 'remark', 'method_id', 'price'] as $e) {
				$post_data[$e] = $this->input->post($e);
			}

			$post_data['type'] = $type;
			$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
			$post_data['branch'] = branch_now('pid');
			$post_data['create_by'] = auth_data('pid');
			if(empty($post_data['method_id'])) $post_data['method_id'] = '';
			
			if ($type == 'exam')
			{
				$post_data['subject'] = json_encode($_POST['subject']);
			}

			$secondary_id = $this->tbl_secondary_model->add($post_data);
						
			if (isset($_POST['courses']))
			{
				foreach($_POST['courses'] as $e)
				{
					$this->log_join_model->add([
						'parent' => $secondary_id,
						'course' => $e,
						'branch' => branch_now('pid'),
						'type' => 'class_bundle_course',
					]);
				}
			}

			alert_new('success', ucfirst(datalist('secondary_type')[$type]['single']) . ' created successfully');

			redirect('secondary/list/' . $type);
		}

		$data['thispage'] = [
			'title' => 'Add ' . datalist('secondary_type')[$type]['single'],
			'group' => $this->group,
			'type' => $type,
			'js'	=> $this->group. '/add',
		];

		$data['result'] = $this->tbl_secondary_model->all_list();

		$this->load->view('inc/header', $data);
		$this->load->view($this->group . '/add');
		$this->load->view('inc/footer', $data);

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Secondary/Read');
		
		$data['result'] = $this->tbl_secondary_model->view($id);
		$data['parent'] = $this->tbl_secondary_model->all_list();
		$data['id'] = $id;
		$data['result'] = $data['result'][0];
		$type = $data['result']['type'];

		if(!isset($_GET['tab'])) $_GET['tab'] = 1;

		if (count($data['result']) == 0) {

			alert_new('warning', 'Data not found');
			redirect();

		} else {

			$data['thispage'] = [
				'title' => 'Edit ' . datalist('secondary_type')[$type]['single'],
				'group' => $this->group,
				'type' => $type,
				'js' => $this->group . '/edit'
			];

			if($type == 'school') {
				
				$data['classes'] = [];
				
				$data['students'] = $this->tbl_users_model->list('student', branch_now('pid'), ['school' => $id]);
				
				foreach($data['students'] as $e) {
					foreach($this->log_join_model->list('join_class', branch_now('pid'), ['user' => $e['pid'], 'active' => 1]) as $e2) {
						$data['classes'][] = $e2;
					}
				}
				
				$data['classes'] = array_unique(array_column($data['classes'], 'class'));

			}
			
			if($type == 'class_bundle') {
				$data['course'] = $this->tbl_secondary_model->active_list('course', branch_now('pid'), ['active' => 1]);
				$data['courses'] = $this->log_join_model->list('class_bundle_course', branch_now('pid'), ['parent' => $id]);
				$data['prices'] = $this->log_join_model->list('class_bundle_price', branch_now('pid'), ['parent' => $id]);
                
			}
			
			$data['subject'] = empty($data['result']['subject']) ? [] : json_decode($data['result']['subject']);

			if (isset($_POST['save'])) {
				
				$post_data = [];
				
				foreach (['title', 'active', 'remark', 'method_id', 'price'] as $e) {
					$post_data[$e] = $this->input->post($e);
				}

				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				$post_data['update_by'] = auth_data('pid');
				$post_data['branch'] = branch_now('pid');
				if(empty($post_data['method_id'])) $post_data['method_id'] = '';
			
				if ($type == 'exam')
				{
					$post_data['subject'] = json_encode($_POST['subject']);
				}

				$this->tbl_secondary_model->edit($id, $post_data);
				
				if (isset($_POST['courses']))
				{
					$class_bundle_course = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
						'parent' => $id
					]);
					
					foreach($class_bundle_course as $e) {
						$this->log_join_model->del($e['id']);
					}
					
					foreach($_POST['courses'] as $e)
					{
						$this->log_join_model->add([
							'parent' => $id,
							'course' => $e,
							'branch' => branch_now('pid'),
							'type' => 'class_bundle_course',
						]);
					}
				}
				
				alert_new('success', ucfirst(datalist('secondary_type')[$type]['single']) . ' updated successfully');
				
				redirect('secondary/list/' . $type);
			}
			
			if (isset($_POST['save_bundle_price']))
			{
				//verbose($_POST['bundle']);
				
				foreach($_POST['bundle'] as $e)
				{
					
					$exits = $this->log_join_model->list('class_bundle_price', branch_now('pid'), ['parent' => $id, 'qty' => $e['qty']]);
					
					if(empty($e['amount'])) {
						if (count($exits) > 0)
						{
							$this->log_join_model->del($exits[0]['id']);
						}
					} elseif ($e['amount'] > 0) {
						if (count($exits) > 0)
						{
							$this->log_join_model->edit($exits[0]['id'], [
								'amount' => $e['amount'],
								'material' => $e['material'],
								'subsidy' => $e['subsidy'],
							]);
						}
						else
						{
							$this->log_join_model->add([
								'qty' => $e['qty'],
								'amount' => $e['amount'],
								'material' => $e['material'],
								'subsidy' => $e['subsidy'],
								'parent' => $id,
								'branch' => branch_now('pid'),
								'type' => 'class_bundle_price',
							]);
						}
					}
					
				}
				
				alert_new('success', 'Price List updated successfully');
				
				header('refresh: 0'); exit;
			}
			
			if(isset($_GET['del'])) {
			    
			    $this->log_join_model->del($_GET['del']);
			    
			    alert_new('success', 'Deleted');
			    redirect('secondary/edit/'.$id.'?tab=1');
			    
			}
			
			if(isset($_GET['reset'])) {
			    
			    $this->db->query(' UPDATE log_join SET is_delete=1 WHERE is_delete=0 AND type=? AND secondary=? AND user=? AND date=? ', [ 'exam_score', $_GET['secondary'], $_GET['user'], $_GET['date'] ]);
			    
			    alert_new('success', 'Reseted');
			    redirect('secondary/edit/'.$id.'?tab=1');
			    
			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function json_enable($active = '', $id = '')
	{

		auth_must('login');

		$post_data['active'] = $active;

		$this->tbl_secondary_model->edit($id, $post_data);			
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}
	
	public function json_enable_join($active = '', $id = '')
	{

		auth_must('login');
		
		if(!empty($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $id]))) {
			
			$post_data = [];
			$post_data['active'] = $active;
			$this->log_join_model->edit($this->log_join_model->list('secondary', branch_now('pid'), ['secondary' => $id])[0]['id'], $post_data);
			
		} else {
			
			$post_data = [];
			$post_data['active'] = $active;
			$post_data['branch'] = branch_now('pid');
			$post_data['type'] = 'secondary';
			$post_data['secondary'] = $id;
			$this->log_join_model->add($post_data);
			
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}

	public function json_del($id = '')
	{
		
		auth_must('login');
		check_module_page('Secondary/Delete');

		if (!empty($id)) {
			if(datalist_Table('tbl_secondary', 'type', $id) == 'class_bundle') {
				foreach($this->log_join_model->list('class_bundle_course', branch_now('pid'), [ 'parent' => $id ]) as $e) {
					$this->log_join_model->del($e['id']);
				}
			}
			$this->tbl_secondary_model->del($id);
		}

		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => datalist('secondary_type')[ datalist_Table('tbl_secondary', 'type', $id) ]['single'].' deleted successfully']));

	}
}
