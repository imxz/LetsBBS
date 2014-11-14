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
        $this->load->helper('form');

        $data=$this->user_m->get_users_list(NULL, $page, 20, $url='admin/user/page', 4);
        $data['username'] = '';
        $this->load->view('admin/user_list', $data);
    }

    /**
     * 搜索用户
     * @param   $page 当前页码
     */
    public function search($page = 1)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username');

        if ($this->form_validation->run() == FALSE){
            //没有提交form
            if ($this->session->userdata('search_username_keywords')) {
                $where = array('username like' => '%'.$this->session->userdata('search_username_keywords').'%');
                $data=$this->user_m->get_users_list($where, $page, 20, 'admin/user/search', 4);
                $data['username'] = $this->session->userdata('search_username_keywords');
                $this->load->view('admin/user_list', $data);
            } else {
                $this->page($page);
            }
        }else{
            $this->session->set_userdata('search_username_keywords', $this->input->post('username'));
            $where = array('username like' => '%'.$this->session->userdata('search_username_keywords').'%');
            $data=$this->user_m->get_users_list($where, $page, 20, 'admin/user/search', 4);
            $data['username'] = $this->session->userdata('search_username_keywords');
            $this->load->view('admin/user_list', $data);
        }
    }

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */
