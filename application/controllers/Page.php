<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

		$this->group = 'page';
		
	}

	/*
	 * @author Steve
	 *
	**/
	public function json_home()
	{
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_content_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			$login['point_epoint_v'] = user_point('epoint', $login['pid']);
			$login['point_ewallet_v'] = user_point('ewallet', $login['pid']);
				
			if($login['type'] == 'parent') {
				
				$children = $this->tbl_users_model->total_children($login['pid']);
				
				foreach($children as $e) {
					
					$login['point_epoint_v'] += user_point('epoint', $e['pid']);
					$login['point_ewallet_v'] += user_point('ewallet', $e['pid']);
					
				}
				
			}
			
			$login['point_epoint_v'] = number_format($login['point_epoint_v'], 0, '.', ',');
			$login['point_ewallet_v'] = number_format($login['point_ewallet_v'], 2, '.', ',');
			
			unset($login['password']);

			$login['image_src'] = pointoapi_UploadSource($login['image']);
			
			$slideshow = [];

			foreach($this->tbl_content_model->list('slideshow', $login['branch']) as $e) {
				
				$e['image_src'] = pointoapi_UploadSource($e['image']);
				
				if($e['active'] == 1) $slideshow[] = $e;
				
			}

			$result = [
				'session' => $login,
				'slideshow' => $slideshow,
			];

			die(json_encode([ 'status' => 'ok', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}
        
	}

	/*
	 * @author Steve
	 *
	**/
	public function json_centre()
	{
		
		$this->load->model('tbl_users_model');
		$this->load->model('tbl_branches_model');
		
		header('Content-type: application/json');
		
		$login = $this->tbl_users_model->me_token( post_data('token') );

		if( count($login) == 1 ) {
			
			$login = $login[0];
			
			unset($login['password']);

			$result = [
				'session' => $login,
				'centre' => $this->tbl_branches_model->list([ 'pid' => $login['branch'] ])[0],
			];

			$result['centre']['image_source'] = pointoapi_UploadSource( $result['centre']['image'] );

			die(json_encode([ 'status' => 'ok', 'result' => $result ]));

		} else {

			die(json_encode([ 'status' => 'expired', 'message' => 'Token error' ]));

		}
        
	}

}
