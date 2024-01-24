<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_content_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_content';
		
	}

	public function list($type = '', $branch = '')
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function list2($search = [])
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function active_list($type = '', $branch = '')
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('active', 1);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function all_list($branch = '')
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		
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
		
		$this->db->insert($this->tbl_name, $data);
		
	}

	public function edit($id, $data)
	{
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('pid', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

}