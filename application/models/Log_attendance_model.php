<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_attendance_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'log_attendance';
		
	}

	public function list($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// group by user and datatime order by datetime asc
	public function list_gb_user_datetime_ob_datetime_asc($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		$this->db->group_by('user');
		$this->db->group_by('DATE(datetime)');
		$this->db->order_by('datetime', 'ASC');


		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// group by user
	public function list_gb_user($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		$this->db->group_by('user');

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// by steve
	public function list_desc($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		$this->db->order_by('datetime', 'ASC');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// steve
	public function list_user_today_attendance($search = [], $desc = false)
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);
		$this->db->order_by('datetime', 'DESC');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	// steve
	// used: attendance/json_my_daily
	public function list2($branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		
		if($search != []) $this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function setup_list($branch = '', $search = [])
	{

		$this->db->where('branch', $branch);
		$this->db->where($search);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	
	// public function list_manual($user = '', $date = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('user', $user);
		// $this->db->where('DATE(datetime)', $date);
		
		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	// public function monthly_list($branch = '', $date = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('MONTH(datetime)', $date);
		// $this->db->group_by('user');
		
		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	// public function monthly_list2($branch = '', $date = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('MONTH(datetime)', $date);
		
		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	// public function daily_list($branch = '', $date)
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('DATE(datetime)', $date);
		// $this->db->group_by('user');

		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }
	
	// for report purpose
	// public function date_range_list($branch = '', $start_date, $end_date)
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('DATE(datetime) >=', $start_date);
		// $this->db->where('DATE(datetime) <=', $end_date);
		// $this->db->group_by('user');
		// $this->db->group_by('DATE(datetime)');
		// $this->db->order_by('datetime', 'ASC');


		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }
	
	// for report purpose
	// public function date_user_list($branch = '', $start_date, $end_date, $user = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('DATE(datetime) >=', $start_date);
		// $this->db->where('DATE(datetime) <=', $end_date);
		// $this->db->where('user', $user);
		// $this->db->group_by('user');
		// $this->db->group_by('DATE(datetime)');
		// $this->db->order_by('datetime', 'ASC');

		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	
	// for report purpose
	// public function report_view($user, $date)
	// {
		
		// $this->db->where('is_delete', 0);
		// $this->db->where('user', $user);
		// $this->db->where('DATE(datetime)', $date);

		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	// public function check_in($branch = '')
	// {

		// $this->db->where('is_delete', 0);
		// $this->db->where('branch', $branch);
		// $this->db->where('MONTH(datetime)', date('m'));
		// $this->db->where('YEAR(datetime)', date('Y'));
		// $this->db->group_by('user');

		// $query = $this->db->get($this->tbl_name);
		
		// return $query->result_array();
		
	// }

	public function view($id)
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('id', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function view_user($user, $datetime)
	{
		
		$this->db->where('user', $user);
		$this->db->where('DATE(datetime)', $datetime);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		
		$this->db->insert($this->tbl_name, $data);
		
	}

	public function edit($id, $data)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

	// Tan Jing Suan
	public function latestattendance($user)
	{
		$this->db->where('user', $user);
		$this->db->where('method', '162780155412'); // manually
   		$this->db->order_by('datetime', 'DESC');
	    $this->db->limit(1);
		$query = $this->db->get($this->tbl_name);		
		return $query->result_array();
	}

}