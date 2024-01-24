<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_devices_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_devices';
		
	}

	public function list($branch = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where($search);
		
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
		
		return $data['pid'];
		
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