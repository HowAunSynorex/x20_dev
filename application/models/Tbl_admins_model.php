<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tbl_admins_model extends CI_Model {

	function __construct() 
	{
		
		$this->tbl_name = 'tbl_admins';
		
	}

	public function list($search = [])
	{

		$this->db->where('is_delete', 0);
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
		
		if(!isset($data['pid'])) $data['pid'] = get_new('id');
		
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

				return $result['token'];

			} else {

				return false;

			}

		} else {

			return false;

		}
		
	}

	// general login
	public function me($id)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('pid', $id);
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	// sso login
	public function me_token($id)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('token', $id);
		$this->db->where('token !=', '');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}

	// sso login
	/*public function me_token($id)
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('token', $id);
		$this->db->where('token !=', '');
		
		$query = $this->db->get($this->tbl_name);
		
		return $query->result_array();
		
	}*/

	/*public function check_username($username, $id = '')
	{
		
		$this->db->where('active', 1);
		$this->db->where('is_delete', 0);
		$this->db->where('username', $username);

		if(!empty($id)) {

			$this->db->where('pid !=', $id);

		}
		
		$query = $this->db->get($this->tbl_name);

		$result = $query->result_array();
		
		return count($result) == 0 ? true : false ;
		
	}*/

}