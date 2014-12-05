<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Base_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        //$this->output->enable_profiler(TRUE);

        //全局传递网站设置数据
        $this->load->model('site_m');
        $site_settings = $this->site_m->get_site_settings();
        $this->load->vars($site_settings);
    }
}
// END Base_Controller class

class Front_Controller extends Base_Controller {

    public function __construct()
    {
        parent::__construct();

        //更新用户组、提醒数量、禁言
        if ($this->session->userdata('uid')) {
            $this->db->where('uid', $this->session->userdata('uid'));
            $query = $this->db->get('letsbbs_user');
            $user_info = $query->row_array();
            $this->session->set_userdata('notification', $user_info['notice']);
            $this->session->set_userdata('group_id', $user_info['group_id']);
            $this->session->set_userdata('is_active', $user_info['is_active']);
        }
    }
}
// END Front_Controller class

class Admin_Controller extends Base_Controller {

    public function __construct()
    {
        parent::__construct();

        //admin访问控制
        $this->load->helper('auth');
        is_admin_exit();
    }
}
// END Admin_Controller class

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
