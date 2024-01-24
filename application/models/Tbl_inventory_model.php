<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_inventory_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_inventory';
		
	}

	public function list($branch = '', $type = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function list_batch($branch = '', $type = '', $search = [])
	{

		$this->db->where('is_delete', 0);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->where($search);
		
		$this->db->order_by('pid', 'DESC');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	public function list_search_title($branch = '', $type = '', $search = '')
	{

		$this->db->where('is_delete', 0);
		$this->db->where('stock_ctrl', 1);
		$this->db->where('active', 1);
		$this->db->where('branch', $branch);
		$this->db->where('type', $type);
		$this->db->like('title', $search);
		
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

	public function login($username, $password)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('username', $username);
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();

		if( count($result) == 1 ) {

			$result = $result[0];

			if(password_verify($password, $result['password'])) {

				return $result['pid'];

			} else {

				return false;

			}

		} else {

			return false;

		}
		
	}

	public function me($id)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('pid', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}
	
	public function add_batch($data)
	{
		$this->db->insert_batch($this->tbl_name, $data);
	}

}