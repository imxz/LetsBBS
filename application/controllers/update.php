<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    // node  topshow

    public function index()
    {
        $this->load->view('welcome_message');
    }
}

/* End of file update.php */
/* Location: ./application/controllers/update.php */