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
        $this->session->sess_destroy();
        $reincidente = ($this->triedLogin) ? '/reincidente' : '';
        $unauthorized = TRUE;
        if ($this->uri->slash_segment(1))
        {
            $segment = $this->uri->slash_segment(1).$this->uri->segment(2);
            $permitedSegments = array(
                'mail/login', 
                'mail/select_options', 
                'schedule/feedback', 
                'schedule/send', 
                'schedule/reject');
            if (in_array($segment, $permitedSegments))
            {
                if ($segment != 'mail/login')
                    $this->session->sess_destroy();
                $unauthorized = FALSE;
            }
        }
        if ($unauthorized)
        {
            $this->load->helper('url');
            redirect('mail/login'.$reincidente, 'refresh');
        }
    }
}