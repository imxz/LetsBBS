<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
    }

    public function index()
    {
        $this->page();
    }

    /**
     * 用户列表
     */
    public function page($page = 1)
    {
        $data=$this->user_m->get_users_list(NULL, $page, 20, $url='admin/user/page', 4);
        $this->load->view('admin/user_list', $data);
    }

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */
