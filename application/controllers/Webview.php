<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webview extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'webview';
		$this->single = 'webview';
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_content_model');
		$this->load->model('log_point_model');
		$this->load->model('log_join_model');
		$this->load->model('tbl_classes_model');
		
	}
	
	public function my_classes()
	{				
	    
	    if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			if(count($user) > 0){
				$user = $user[0];
				$data['user'] = $user;
			}
		} else {
			
			die('Invalid token');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'My Classes',
				'group' => $this->group,
			];
			
			if(!isset($_GET['u'])) $_GET['u'] = '';
            if(!isset($_GET['user'])) $_GET['user'] = '';
            
            // loop all child
            if($user['type'] == 'parent') {
                $_GET['u'] = $user['pid'];
                
                $r = [];
    		    $c = $this->log_join_model->list_all([ 'type' => 'join_parent', 'parent' => $_GET['u'], 'active' => 1 ]);
    		    foreach($c as $e) {
    		        $er = $this->tbl_users_model->list_v2([ 'pid' => $e['user'] ]);
    		        if(count($er) == 1) {
    		            $a = $er[0];
    		            $a['school_title'] = datalist_Table('tbl_secondary', 'title', $a['school']);
    		            $a['form_title'] = datalist_Table('tbl_secondary', 'title', $a['form']);
    		            $r[] = $a;
    		        }
    		    }
                $data['child'] = $r;
            } else {
                $_GET['u'] = $user['pid'];
            }
            
            // loop child join class
            $_GET['u'] = $user['type'] == 'parent' ? $_GET['user'] : $user['pid']; // default is std
            $data['result'] = $this->log_join_model->list_all([ 'type' => 'join_class', 'user' => $_GET['u'], 'active' => 1 ]);
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/my_classes', $data);
			$this->load->view('inc/footer', $data);
			
		}
	    
	}
	
	public function points_balance()
	{				
	
		if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			
		} else {
			
			die('Invalid token');
			
		}
		
		if(!isset(datalist('point_type')[$_GET['type']])) {
			
			die('Invalid type');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'Points Balance',
				'group' => $this->group,
			];
			
			$data['token'] = post_data('token');
			$user = $user[0];
			
// 			$data['result'] = $this->tbl_users_model->list_v2([ 'parent' =>  $user['pid'] ]);

            $r = [];
		    $c = $this->log_join_model->list_all([ 'type' => 'join_parent', 'parent' => $user['pid'], 'active' => 1 ]);
		    foreach($c as $e) {
		        $er = $this->tbl_users_model->list_v2([ 'pid' => $e['user'] ]);
		        if(count($er) == 1) {
		            $a = $er[0];
		            $a['school_title'] = datalist_Table('tbl_secondary', 'title', $a['school']);
		            $a['form_title'] = datalist_Table('tbl_secondary', 'title', $a['form']);
		            $r[] = $a;
		        }
		    }
		    $data['result'] = $r;

			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/points_balance', $data);
			$this->load->view('inc/footer', $data);
			
		}
		
	}
	
	public function history($id = '')
	{				
	    
		if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			
		} else {
			
			die('Invalid token');
			
		}
		
		if(!isset(datalist('point_type')[$_GET['type']])) {
			
			die('Invalid type');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'Transaction History',
				'group' => $this->group,
			];
			
			$data['token'] = post_data('token');
			
			$user = $this->tbl_users_model->view($id);
			
			if(count($user) == 0) {
				die('User not found');
			}
			if(!isset($_GET['type']) || empty($_GET['type'])) {
				die('Type not found');
			}
			
			$user = $user[0];
			
			$data['result'] = $this->log_point_model->view($id, $_GET['type']);;
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/history', $data);
			$this->load->view('inc/footer', $data);
			
		}
		
	}
	
	public function chat()
	{				
	
		if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			
		} else {
			
			die('Invalid token');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'Chat',
				'group' => $this->group,
			];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/chat', $data);
			$this->load->view('inc/footer', $data);
			
		}
		
	}
	
	public function view_homework($id = '')
	{				
	
		if(isset($_GET['token'])) {
			
			$user = $this->tbl_users_model->me_token($_GET['token']);
			
		} else {
			
			die('Invalid token');
			
		}
		
		if(empty($user)) {
			
			die('User not found');
			
		} else {
		
			$data['thispage'] = [
				'title' => 'View Homework',
				'group' => $this->group,
				'css' => $this->group . '/view_homework',
			];
			
			// content
			$data['result'] = $this->tbl_content_model->view($id);

			if(count($data['result']) == 0) {
				
				die(app('title').': Data not found');
				
			}
			
			$data['result'] = $data['result'][0];
			
			$this->load->view('inc/header', $data);
			$this->load->view($this->group . '/view_homework', $data);
			$this->load->view('inc/footer', $data);
			
		}
		
	}
	
}