<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Model
{
	public function select($nome = NULL)
	{
		$result = FALSE;
		if (NULL != $nome)
		{
			$this->db->where('name', $nome);
			$query = $this->db->get('user');
			$result = $query->row();
		}
		else
		{
			$query = $this->db->get('user');
			$result = $query->result();
		}
		return $result;
	}
	public function selectFromChamilo()
	{
		return FALSE;
	}
}