<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('topic_m');
    }

    public function index()
    {
        $this->page();
    }

    /**
     * 最近主题列表
     * @param   $page 当前页码
     */
    public function page($page = 1)
    {
        $data=$this->topic_m->get_topic_recent(NULL, $page, 20, 'admin/topic/page', 4);
        $this->load->view('admin/topic_list', $data);
    }

}

/* End of file topic.php */
/* Location: ./application/controllers/admin/topic.php */
