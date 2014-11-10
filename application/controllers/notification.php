<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('notification_m');
        $this->load->model('user_m');

        $this->load->helper('auth');
        is_login_exit();
    }

    /**
     * 通知页面
     * @param   $page 当前页码
     */
    public function index($page=1)
    {
        $where = array('a.tuid' => $this->session->userdata('uid'));
        $data=$this->notification_m->get_notifications_recent($where, $page, 15, 'notification', 2);

        $data['site_title'] = '通知中心';
        $this->load->view('notification_list', $data);

        //提醒数量归零
        $this->user_m->update($this->session->userdata('uid'), array('notice' => 0));
        $this->session->set_userdata('notification', 0);
    }
}

/* End of file notification.php */
/* Location: ./application/controllers/notification.php */