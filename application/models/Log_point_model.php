<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_point_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'log_point';
		
	}

	public function list($search = [])
	{
		$this->db->where('is_delete', 0);
		$this->db->where($search);
		$query = $this->db->get($this->tbl_name);
	
		return $query->result_array();
		
	}

	public function view($id, $type)
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('user', $id);
		$this->db->where('type', $type);
		$this->db->order_by('create_on', 'ASC');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function view_one2($id)
	{
		
		$this->db->where('is_delete', 0);
		$this->db->where('id', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function add($data)
	{
			
		$this->db->insert($this->tbl_name, $data);
		
	}

	public function edit($type, $id, $data)
	{
		
		$this->db->where('id', $id);
		$this->db->where('type', $type);
		$this->db->update($this->tbl_name, $data);
		
	}

	public function del($id)
	{
		
		$this->db->where('id', $id);
		$this->db->update($this->tbl_name, [
			'is_delete' => 1,
		]);
		
	}

}