<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mail_list extends CI_Model
{
	public function __construct()
	{
        parent::__construct();
	}

	public function select($mail_data_id, $mail_addr)
	{
		$mail_addr = explode('@', $mail_addr);
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		$query = $this->db->get('mail_list');
		return $query->row();
	}

	public function save($list)
	{
		return $this->db->insert_batch('mail_list', $list);
	}

	public function update_status($mail_data_id, $mail_addr, $new_status)
	{
		$mail_addr = explode('@', $mail_addr);
		//die(var_dump($mail_addr));
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		$query = $this->db->get('mail_list');
		$data = $query->row();
		$data->status = $new_status;
		$data->dttm_changed = date('Y-m-d H:m:s');
		$data->changed_by = USER;
		$this->db->where(array('mail_data_id' => $mail_data_id, 'prefix' => $mail_addr[0], 'domain' => $mail_addr[1]));
		return $this->db->update('mail_list', $data);
	}

	public function stats($mail_data_id = 0, $status = '')
	{
		$result = array('agendados' => 0, 'falhas' => 0, 'enviados' => 0, 'lidos' => 0, 'rejeitados' => 0);
		if ($mail_data_id)
			$this->db->where('mail_data_id', $mail_data_id);
		if ($status && $status != 'TOTAL')
			$this->db->where('status', $status);
		$query = $this->db->get('mail_list');
		$mail_list = $query->result();
		//die (var_dump($mail_list));
		if (!$status)
		{
			foreach ($mail_list as $mail)
			{
				if ($mail->status == 'AGENDADO')
					$result['agendados']++;
				else if ($mail->status == 'ENVIADO')
					$result['enviados']++;
				else if ($mail->status == 'LIDO')
					$result['lidos']++;
				else if ($mail->status == 'FALHA')
					$result['falhas']++;
				else
					$result['rejeitados']++;
			}
		}
		else
			$result = $mail_list;
		return $result;
	}
}