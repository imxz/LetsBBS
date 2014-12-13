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
     * 禁言用户列表
     */
    public function banned($page = 1)
    {
        $this->load->helper('form');

        $data=$this->user_m->get_users_list(array('is_active' => 0), $page, 20, $url='admin/user/banned', 4);
        $data['username'] = '';
        $this->load->view('admin/user_banned_list', $data);
    }

    /**
     * 搜索ban用户
     * @param   $page 当前页码
     */
    public function banusers($page = 1)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username');

        if ($this->form_validation->run() == FALSE){
            //没有提交form
            if ($this->session->userdata('search_banusername_keywords')) {
                $where = array('username like' => '%'.$this->session->userdata('search_banusername_keywords').'%', 'is_active' => 0);
                $data=$this->user_m->get_users_list($where, $page, 20, 'admin/user/banusers', 4);
                $data['username'] = $this->session->userdata('search_banusername_keywords');
                $this->load->view('admin/user_banned_list', $data);
            } else {
                $this->banned($page);
            }
        }else{
            $this->session->set_userdata('search_banusername_keywords', $this->input->post('username'));
            $where = array('username like' => '%'.$this->session->userdata('search_banusername_keywords').'%', 'is_active' => 0);
            $data=$this->user_m->get_users_list($where, $page, 20, 'admin/user/banusers', 4);
            $data['username'] = $this->session->userdata('search_banusername_keywords');
            $this->load->view('admin/user_banned_list', $data);
        }
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

    /**
     * 用户编辑
     * @param   $uid 用户id
     */
    public function edit($uid)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('qq', 'QQ', 'trim|integer');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data=$this->user_m->get_user_byid($uid);
            $this->load->view('admin/user_edit', $data);
        }
        else
        {
            //form success
            $data = array(
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'qq' => $this->input->post('qq'),
                'location' => htmlspecialchars($this->input->post('location')),
                'homepage' => prep_url(htmlspecialchars($this->input->post('homepage'))),
                'signature' => htmlspecialchars($this->input->post('signature')),
                'introduction' => htmlspecialchars($this->input->post('introduction'))
                );

            if ($this->input->post('reavatar') == '1') {
                $data['avatar'] = 'uploads/avatar/default/';
            }

            $this->user_m->update($uid, $data);
            redirect($this->input->server('HTTP_REFERER'));
        }
    }

    /**
     * 用户禁言
     * @param   $uid 用户id
     */
    public function ban($uid)
    {
        $this->user_m->update($uid, array('is_active' => 0));
        redirect($this->input->server('HTTP_REFERER'));
    }

    /**
     * 用户恢复激活
     * @param   $uid 用户id
     */
    public function active($uid)
    {
        $this->user_m->update($uid, array('is_active' => 1));
        redirect($this->input->server('HTTP_REFERER'));
    }

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */
