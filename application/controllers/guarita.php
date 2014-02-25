<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guarita extends CI_Controller
{
	private $data;
	private $triedLogin = FALSE;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}

	public function identify()
	{
		$possibleUser = $this->input->post('user');
		if (!empty($possibleUser) && is_array($possibleUser))
		{
			$this->triedLogin = TRUE;
			$this->load->model('user');
			$realUser = $this->user->select($possibleUser['name']);
			if (is_object($realUser) && $realUser->password == $possibleUser['password'])
			{
				//permission granted
				$this->session->set_userdata('user', $realUser);
			}
		}
		if (!$this->session->userdata('user'))
		{
			//permission denied
			$this->logoff();
		}
	}

	public function logoff()
	{
		//die (var_dump($_SERVER));
		$reincidente = ($this->triedLogin) ? '/reincidente' : '';
		$unauthorized = TRUE;
		if (!empty($_SERVER['PATH_INFO']))
		{
			$publicPages = (substr($_SERVER['PATH_INFO'], 0, 11).'/' == '/mail/login/' 
				|| substr($_SERVER['PATH_INFO'], 0, 20).'/' == '/mail/select_options/'
				|| substr($_SERVER['PATH_INFO'], 0, 18).'/' == '/schedule/feedback/'
				|| substr($_SERVER['PATH_INFO'], 0, 14).'/' == '/schedule/send/'
				|| substr($_SERVER['PATH_INFO'], 0, 16).'/' == '/schedule/reject/');
			//die(var_dump($publicPages));
			if (substr($_SERVER['PATH_INFO'], 0, 11).'/' != '/mail/login/')
				$this->session->sess_destroy();
			if ($publicPages)
				$unauthorized = FALSE;
			//echo var_dump($unauthorized);
		}
		if ($unauthorized)
		{
			//echo (var_dump($_SERVER));
			$this->load->helper('url');
			redirect('mail/login'.$reincidente, 'refresh');
		}
	}
}