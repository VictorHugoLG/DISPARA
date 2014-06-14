<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Vw_sms_schedule extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
	}

	public function select()
	{
		$query = $this->db->get('vw_sms_schedule');
		return $query->result();
	}
}