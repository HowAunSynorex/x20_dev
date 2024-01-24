<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function synorexone_sso($token) {

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://synorexcloud.com/api/one2/sso",
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
				
				// die($token);
				
				setcookie(md5('@robocube-tuition-sso'), $token, time() + (86400 * 30), '/');
				// $this->session->set_userdata('auth', $token);
				
				redirect();
				
			}
			
			exit;
			
		} else {
			
			header('location: https://synorexcloud.com/client?msg=sso_login_failed');
			
		}
		
	} else {
		
		header('location: https://synorexcloud.com/client?msg=sso_api_error');
		
	}

}