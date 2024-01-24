<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'api';
		
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
