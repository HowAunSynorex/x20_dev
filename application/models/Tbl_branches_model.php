<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_branches_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_branches';
		
	}

	public function list($search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where($search);

		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	// for admin
	public function all_list($search = [])
	{
		$this->db->where($search);
		$this->db->order_by("(CASE WHEN is_delete = 1 THEN 4 ELSE (CASE WHEN (DATEDIFF(expired_date, CURDATE()) > 0 AND DATEDIFF(expired_date, CURDATE()) <= 14) THEN 1 ELSE (CASE WHEN DATEDIFF(expired_date, CURDATE()) <= 0 THEN 3 ELSE 2 END) END) END)");
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function expired()
	{
		$this->db->where("DATEDIFF(expired_date, CURDATE()) <= 0");
		$this->db->where('is_delete', 0);
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function expiring()
	{
		$this->db->select('tbl_branches.*, DATEDIFF(expired_date, CURDATE()) AS expired_day');
		$this->db->where("(DATEDIFF(expired_date, CURDATE()) > 0 AND DATEDIFF(expired_date, CURDATE()) <= 14)");
		$this->db->where('is_delete', 0);
		$this->db->order_by('expired_date', 'asc');
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	public function last_online($limit)
	{
		$this->db->where('is_delete', 0);
		$this->db->order_by('last_online', 'desc');
		$this->db->limit($limit, 0);
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
	}
	
	public function view($id)
	{
		
		$this->db->where('pid', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
		
		$data['pid'] = get_new('id');
		$data['create_by'] = auth_data('pid');
		$data['update_by'] = auth_data('pid');
		
		$this->db->insert($this->tbl_name, $data);
		
		return $data['pid'];
		
	}

	public function edit($id, $data)
	{
		
		$data['update_by'] = auth_data('pid');
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$data['update_by'] = auth_data('pid');
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}
	
	public function check_branch($id = '')
	{
		$this->db->where('is_delete', 0);
		$this->db->where('pid', $id);

		if(!empty($id)) {
			$this->db->where('pid =', $id);
		}
		
		$query = $this->db->get($this->tbl_name);
		$result = $query->result_array();
		return count($result) == 0 ? false : true ;
		
	}

	// for admin
	public function restore($id)
	{
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 0,
		]);
		
	}

}