<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Viewer extends CI_Controller
{
    public function __construct($data)
    {
        parent::__construct();
        $view = $data[0];
        $data = $data[1];
        $this->load->model('mail_data', '', TRUE);
        $data['mails'] = $this->mail_data->select();
        $this->load->helper('url');
        $this->load->view('header', $data);
        $this->load->view($view, $data);
        $this->load->view('footer', $data);
    }
}