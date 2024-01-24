<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'auth';
		
		$this->load->model('tbl_admins_model');
		$this->load->model('tbl_branches_model');
		$this->load->model('log_join_model');

	}
	
	public function profile()
	{
		
		$data['thispage'] = [
			'title' => 'Profile',
			'group' => $this->group,
		];
		
		if(isset($_POST['save'])) {
			
			$password = empty($this->input->post('password')) ? auth_data('password') : password_hash($this->input->post('password'), PASSWORD_DEFAULT);
			
			$this->tbl_admins_model->edit(auth_data('pid'), [
				'nickname'		=> $this->input->post('nickname'),
				'username'		=> $this->input->post('username'),
				'password'		=> $password,
			]);
			
			alert_new('success', 'Profile updated successfully');
			header('refresh: 0'); exit;
			
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/profile', $data);
		$this->load->view('inc/footer', $data);
		
	}

	public function login()
	{

		auth_must('logout');
		
		if (!defined('WHITELABEL')) {
	    	redirect('https://one.synorexcloud.com/client/services?pg=sso&id=161687809860');
        }
				
		$data['thispage'] = [
			'title' => 'Login',
			'group' => $this->group,
		];
		
		if(isset($_POST['login'])) {
		    
		    $login = $this->tbl_admins_model->login($this->input->post('username'), $this->input->post('password'));

			if( $login != false ) {
				
				$token = openssl_encrypt(time(), 'AES-128-CTR', 'highpeakedu-token', 0, '1234567891011121');
				$admin_id = $this->tbl_admins_model->me_token($login)[0]['pid'];
				$this->tbl_admins_model->edit($admin_id, [ 'token' => $token ]);
				setcookie(md5('@highpeakedu-sso'), $token, time() + (86400 * 30), '/');
				redirect();
				
			} else {

				alert_new('warning', 'Login failed');
				header('refresh: 0'); exit;

			}
		    
		}
		
		$this->load->view('inc/header', $data);
		$this->load->view($this->group.'/login', $data);
		$this->load->view('inc/footer', $data);

	}

	public function sso($token = '') 
	{
		
		$token = isset($_GET['token']) ? $_GET['token'] : $token ;
		
		$this->load->model('log_join_model');
		
		if(!empty($token)) {
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://one.synorexcloud.com/api/one2/sso",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "token=".$token,
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/x-www-form-urlencoded",
				),
			));

			$response = json_decode(curl_exec($curl), true);
			
			curl_close($curl);
			
			// echo '<pre>'; print_r($response); exit;
			
			if(is_array($response)) {
				
				if($response['status'] == 'ok') {
					
					$result = $response['result'];
					
					// check local exists
					$query = $this->tbl_admins_model->view($result['pid']);
					
					// 同步用户资料
					if(count($query) == 1) {
						
						$query = $query[0];
						
						// update
						$this->tbl_admins_model->edit($result['pid'], [
							'username' => $result['username'],
							'nickname' => $result['nickname'],
							'token' => $token,
						]);
						
					} else {
						
						// create
						$this->tbl_admins_model->add([
							'pid' => $result['pid'],
							'username' => $result['username'],
							'nickname' => $result['nickname'],
							'token' => $token,
						]);
						
					}
					
					// update pending
					$result_pending = $this->log_join_model->list_pending($result['username']);
					
					foreach($result_pending as $e) {
						
						// update approved
						$this->log_join_model->edit($e['id'], [
							'status' => 'approved',
							'is_delete' => 1
						]);
						
						// add permission
						$this->log_join_model->add([
							'type' => 'join_branch',
							'admin' => $result['pid'],
							'branch' => $e['branch'],
							'create_by' => $result['pid'],
						]);
						
					}
					
					if(isset($_GET['callback'])) {
						
						header('location: '.$_GET['callback']);
						
					} else {
						
						setcookie(md5('@highpeakedu-sso'), $token, time() + (86400 * 30), '/');
						
						redirect();
						
					}
					
					exit;
					
				} else {
					
					header('location: https://one.synorexcloud.com/client?msg=sso_login_failed');
					
				}
				
			} else {
				
				header('location: https://one.synorexcloud.com/client?msg=sso_api_error');
				
			}
			
		} else {
			
			redirect('https://one.synorexcloud.com/client');
			
			// die(app('title').': SSO login failed');
			
		}
		
	}
	
	public function logout()
	{
		
		$token = auth_data('token');

		// 手动登出本地端帐号
		setcookie(md5('@highpeakedu-sso'), '', time() - 3600, '/');

		// 清楚特定数据
		setcookie(md5('@highpeakedu-branch'), '', time() - 3600, '/');
		
		if (!defined('WHITELABEL')) {
			// Synorex ONE 请求登出
			header('location: https://one.synorexcloud.com/api/one2/sso_logout?token='.$token);
		} else {
			redirect($this->group . '/login');
		}
		
	}

	// api login
	public function json_login()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		// print_r(post_data('username')); exit;
		
		$login = $this->tbl_users_model->login( post_data('username'), post_data('password') );

		// echo $login; exit;

		if( $login != false ) {
			
			$this->tbl_users_model->edit($login, [
				'token' => md5(time())
			]);

			$result = $this->tbl_users_model->view($login)[0];
            //$user_data= $result['pid'].'_'.$result['branch'].'_'.$result['code'].'_'.$result['type'];
            $user_data= $result['pid'];
            $link = urlencode('https://synorexcloud.com12345/');
			$result['image_src'] = 'https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl='.$user_data.'&chld=L|1&choe=UTF-8';
// 			$result['image_src'] = pointoapi_UploadSource($result['image']);

			unset($result['password']);
			
			$result['branch'] = $this->tbl_branches_model->view($result['branch'])[0]['title'];

			die(json_encode([ 'status' => 'ok', 'message' => 'Login successfully', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'failed', 'message' => 'Login failed' ]));

		}
        
	}

	// api token check
	public function json_me()
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

	// json_childs
	public function json_childs()
	{
		
		$this->load->model('tbl_users_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];

// 			$r = $this->tbl_users_model->total_children($login['pid']);

            $r = [];
		    $c = $this->log_join_model->list_all([ 'type' => 'join_parent', 'parent' => $login['pid'], 'active' => 1 ]);
		    foreach($c as $e) {
		        $er = $this->tbl_users_model->list_v2([ 'pid' => $e['user'] ]);
		        if(count($er) == 1) {
		            $a = $er[0];
		            $a['school_title'] = datalist_Table('tbl_secondary', 'title', $a['school']);
		            $a['form_title'] = datalist_Table('tbl_secondary', 'title', $a['form']);
		            $r[] = $a;
		        }
		    }

			die(json_encode([ 'status' => 'ok', 'result' => $r ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}
        
	}

	/*
	 * @author Steve
	 *
	**/
	public function json_edit()
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