<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'content';
		$this->single = 'content';
		
		$this->load->model('tbl_content_model');
		$this->load->model('tbl_classes_model');
		$this->load->model('tbl_uploads_model');
		$this->load->model('tbl_secondary_model');
		$this->load->model('log_join_model');

	}

	public function list($type)
	{

		auth_must('login');
		check_module_page('Content/Read');
		
		switch($type) {
			case 'announcement':
				check_module_page('Content/Modules/Announcement');
				break;
			default:
				check_module_page('Content/Modules/Slideshow');
				break;
		}

		if(isset( datalist('content_type')[$type] )) {

			$data['thispage'] = [
				'title' => datalist('content_type')[$type]['label'],
				'group' => $this->group,
				'type' => $type,
				'js' => $this->group.'/list',
				'css' => $this->group.'/list',
			];

			$data['result'] = $this->tbl_content_model->list($type, branch_now('pid'));
			
			if(isset($_POST['add-slideshow'])) {
			
				$post_data = [];
				
				$image = null;
			
				if(isset($_FILES['image'])) {

					$target_dir = "uploads/data/";

					if ($_FILES['image']['size'] != 0) {

						$temp = explode(".", $_FILES["image"]["name"]);
						$newfilename = get_new('id') . '.' . end($temp);
						$target_file = $target_dir . $newfilename;
						$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

						$file_size = $_FILES["image"]["size"];
						$image_data['file_name'] = $_FILES["image"]["name"];
						$image_data['file_type'] = $fileType;
						$image_data['file_size'] = $file_size;
						$image_data['file_source'] = base_url($target_file);
						$image_data['create_by'] = auth_data('pid');
						$image_data['type'] = 'slideshow';
						$image = $this->tbl_uploads_model->add($image_data);

					}
					
				}
				
				$post_data['image'] = $image;
				
				$post_data['type'] = $type;
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');
				$this->tbl_content_model->add($post_data);
				
				alert_new('success', 'Slideshow created successfully');
				header('refresh: 0'); exit();
				
			}

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
		$this->load->model('tbl_users_model');
		
		auth_must('login');
		check_module_page('Content/Create');
		
		if($type != 'announcement') {
			
			alert_new('warning', 'Type not found');
			redirect($this->group.'/list/announcement');
			
		} else {
			
			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'title', 'active', 'content'] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}

				// $courses      = implode(",", $_POST['courses']);
				// $class      = implode(",", $_POST['class']);
				// $post_data['courses'] = $courses;
				// $post_data['class'] = $class;
				if(isset($_POST['courses'])) {
					$courses = implode(",", $_POST['courses']);
					$post_data['courses'] = $courses;
				}
				if(isset($_POST['class'])) {
					$class = implode(",", $_POST['class']);
					$post_data['class'] = $class;
				}
				
				$post_data['type'] = $type;
				$post_data['branch'] = branch_now('pid');
				$post_data['create_by'] = auth_data('pid');
				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				
				$image = null;
			
				if(isset($_FILES['image'])) {

					$target_dir = "uploads/data/";

					if ($_FILES['image']['size'] != 0) {

						$temp = explode(".", $_FILES["image"]["name"]);
						$newfilename = get_new('id') . '.' . end($temp);
						$target_file = $target_dir . $newfilename;
						$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

						$file_size = $_FILES["image"]["size"];
						$image_data['file_name'] = $_FILES["image"]["name"];
						$image_data['file_type'] = $fileType;
						$image_data['file_size'] = $file_size;
						$image_data['file_source'] = base_url($target_file);
						$image_data['create_by'] = auth_data('pid');
						$image_data['type'] = 'announcement_image';
						$image = $this->tbl_uploads_model->add($image_data);

					}
					
				}
				
				$post_data['image'] = $image;

				$this->tbl_content_model->add($post_data);
				
				// update user notify
				$users = $this->tbl_users_model->list('student', branch_now('pid'), [ 'active' => 1 ]);
				foreach( $users as $e) {
				    
				    $d = $e;
				    $d['notify'] = $d['notify']+1;
				    
				    $this->tbl_users_model->edit($e['pid'], $d);
				    
				}
				
				alert_new('success', 'Announcement created successfully');
				
				redirect($this->group.'/list/'.$type);

			}

			$data['thispage'] = [
				'title' => 'Add '.ucfirst($type),
				'group' => $this->group,
				'type' => $type,
				'js' => $this->group.'/add'
			];
			
			$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
			$data['course'] = $this->tbl_secondary_model->active_list('course', branch_now('pid'), ['active' => 1]);
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/add');
			$this->load->view('inc/footer', $data);
			
		}

	}

	public function edit($id = '')
	{
		
		auth_must('login');
		check_module_page('Content/Read');

		$data['result'] = $this->tbl_content_model->view($id);
		$data['class'] = $this->tbl_classes_model->list(branch_now('pid'), [ 'active' => 1, 'is_hidden' => 0 ]);
		$data['course'] = $this->tbl_secondary_model->active_list('course', branch_now('pid'), ['active' => 1]);

		if(count($data['result']) == 0) {

			alert_new('warning', 'Data not found');

			redirect('settings/' . $this->group);

		} else {

			$data['result'] = $data['result'][0];
			$type = $data['result']['type'];

			$data['thispage'] = [
				'title' => 'Edit '.ucfirst($type),
				'group' => $this->group,
				'js' => $this->group.'/edit'
			];
					
			if(isset($_POST['save'])) {

				$post_data = [];
				
				foreach([ 'title', 'active', 'content'] as $e) {
					
					$post_data[$e] = $this->input->post($e);
					
				}
				
				$courses      = implode(",", $_POST['courses']);
				$class      = implode(",", $_POST['class']);
				
				$post_data['courses'] = $courses;
				$post_data['class'] = $class;

				$post_data['active'] = isset( $post_data['active'] ) ? 1 : 0 ;
				
				$image = $data['result']['image'];
			
				if(isset($_FILES['image'])) {

					$target_dir = "uploads/data/";

					if ($_FILES['image']['size'] != 0) {

						$temp = explode(".", $_FILES["image"]["name"]);
						$newfilename = get_new('id') . '.' . end($temp);
						$target_file = $target_dir . $newfilename;
						$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
						move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

						$file_size = $_FILES["image"]["size"];
						$image_data['file_name'] = $_FILES["image"]["name"];
						$image_data['file_type'] = $fileType;
						$image_data['file_size'] = $file_size;
						$image_data['file_source'] = base_url($target_file);
						$image_data['create_by'] = auth_data('pid');
						$image_data['type'] = 'announcement_image';
						$image = $this->tbl_uploads_model->add($image_data);

					}
					
				}
				
				$post_data['image'] = $image;
				$post_data['update_by'] = auth_data('pid');

				$this->tbl_content_model->edit($id, $post_data);
				
				alert_new('success', 'Announcement updated successfully');
				
				redirect($this->group.'/list/'.$type);

			}

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/edit', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	public function view_landing($id = '')
	{
		
		$this->load->model('tbl_users_model');
		
		// check token
		if(!isset($_GET['token'])) $_GET['token'] = '';
		
		$login = $this->tbl_users_model->me_token( $_GET['token'] );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
		} else {
			
			die(app('title').': Token error');
			
		}

		// content
		$data['result'] = $this->tbl_content_model->view($id);

		if(count($data['result']) == 0) {
			
			die(app('title').': Data not found');
			
		} else {

			$data['result'] = $data['result'][0];
			
			$data['thispage'] = [
				'title' => 'View '.ucfirst($data['result']['type']),
				'group' => $this->group,
				'css' => $this->group . '/view_landing',
			];

			$this->load->view('inc/header', $data);
			$this->load->view($this->group.'/view_landing', $data);
			$this->load->view('inc/footer', $data);

		}

	}
	
	/*
	 * @author Steve
	 *
	**/
	public function json_list()
	{

		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			$user = $login;
			
			// session
			unset($login['password']);

			$login['image_src'] = pointoapi_UploadSource($login['image']);
			
			// announcement
			$announcement = [];
			
			$joined_class = [];
			
			if($login['type'] == 'student') {
				$query = $this->log_join_model->list('join_class', $login['branch'], [
					'user'		=> $login['pid'],
					'active'	=> 1,
				]);
				foreach($query as $e) {
					$joined_class[$e['class']] = $e;
				}
			} else if ($login['type'] == 'parent') {
				$children = $this->tbl_users_model->total_children($login['pid']);
				
				foreach($children as $c) {
					$query = $this->log_join_model->list('join_class', $login['branch'], [
						'user'		=> $c['pid'],
						'active'	=> 1,
					]);
					foreach($query as $e) {
						$joined_class[$e['class']] = $e;
					}
				}
			}
			
			foreach($this->tbl_content_model->list(post_data('type'), $login['branch']) as $e) {
				
				if(empty($e['class'])) {
					
					$e['create_on_v'] = date('M d, Y', strtotime($e['create_on']));
					$e['image_src'] = pointoapi_UploadSource($e['image']);
					$e['content'] = strip_tags($e['content']);
					$e['content'] = mb_strimwidth($e['content'], 0, 100, '...');

					$announcement[] = $e;
					
				} else {
					
					foreach($joined_class as $c) {
						
						if($e['class'] == $c['class']) {
							
							$e['create_on_v'] = date('M d, Y', strtotime($e['create_on']));
							$e['image_src'] = pointoapi_UploadSource($e['image']);
							$e['content'] = strip_tags($e['content']);
							$e['content'] = mb_strimwidth($e['content'], 0, 100, '...');

							$announcement[] = $e;
							
						}
						
					}
					
				}
				
			}
			
			$user['notify'] = 0;
			$this->tbl_users_model->edit($user['pid'], $user);
			
			// return
			$result = [
				'session' => $login,
				'announcement' => $announcement,
			];

			die(json_encode([ 'status' => 'ok', 'result' => $result]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}

	}
	
	/*
	 * @author Steve
	 *
	**/
	public function json_view()
	{

		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			// session
			unset($login['password']);

			$login['image_src'] = pointoapi_UploadSource($login['image']);
			
			$result = $this->tbl_content_model->view( post_data('id') );

			if(count($result) == 1) {

				$announcement = $result[0];
				
				$announcement['create_on_v'] = date('M d, Y', strtotime($announcement['create_on']));
				$announcement['image_src'] = pointoapi_UploadSource($announcement['image']);
				$announcement['content'] = $announcement['content'];
				
				// return
				$result = [
					'session' => $login,
					'announcement' => $announcement,
				];

				die(json_encode([ 'status' => 'ok', 'result' => $result]));

			} else {
				
				die(json_encode([ 'status' => 'not_found']));
				
			}

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}

	}
	
	public function json_enable($active = '', $id = '')
	{

		auth_must('login');

		$post_data['active'] = $active;

		$this->tbl_content_model->edit($id, $post_data);			
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok']));

	}

	public function json_del($id = '')
	{

		auth_must('login');
		check_module_page('Content/Delete');

		if(!empty($id)) {
	
			$this->tbl_content_model->del($id);
			
		}
		
		header('Content-type: application/json');
		die(json_encode(['status' => 'ok', 'message' => 'Announcement deleted successfully']));

	}
	
	/*
	 * @author Steve
	 *
	**/
	public function json_upload_image()
	{

		header('Content-type: application/json');
		
		// if( empty(branch_now('pointoapi_key')) ) {
			
			// die(json_encode(array('error' => [ 'message' => 'Setup PointoAPI API Key to use this function' ] )));
			
		// } else {
			
			
			// if(isset($_FILES['upload'])) {

				$target_dir = "uploads/data/";

				// if ($_FILES['upload']['size'] != 0) {

					$temp = explode(".", $_FILES["upload"]["name"]);
					$newfilename = get_new('id') . '.' . end($temp);
					$target_file = $target_dir . $newfilename;
					$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
					move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file);

					$file_size = $_FILES["upload"]["size"];
					$image_data['file_name'] = $_FILES["upload"]["name"];
					$image_data['file_type'] = $fileType;
					$image_data['file_size'] = $file_size;
					$image_data['file_source'] = base_url($target_file);
					$image_data['create_by'] = auth_data('pid');
					$image_data['type'] = 'content_image';
					$this->tbl_uploads_model->add($image_data);

				// }
				
			// }
			
			die(json_encode(array('uploaded' => true, 'url' => $image_data['file_source'] )));
			
		// }
		
	}

}
