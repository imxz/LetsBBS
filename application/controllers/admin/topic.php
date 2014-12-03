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
        $this->load->helper('form');

        $data=$this->topic_m->get_topic_recent(NULL, $page, 20, 'admin/topic/page', 4);
        $data['title'] = '';
        $this->load->view('admin/topic_list', $data);
    }

    /**
     * 搜索最近主题列表
     * @param   $page 当前页码
     */
    public function search($page = 1)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title');

        if ($this->form_validation->run() == FALSE){
            //没有提交form
            if ($this->session->userdata('search_topic_keywords')) {
                $where = array('a.title like' => '%'.$this->session->userdata('search_topic_keywords').'%');
                $data=$this->topic_m->get_topic_recent($where, $page, 20, 'admin/topic/search', 4);
                $data['title'] = $this->session->userdata('search_topic_keywords');
                $this->load->view('admin/topic_list', $data);
            } else {
                $this->page($page);
            }
        }else{
            $this->session->set_userdata('search_topic_keywords', $this->input->post('title'));
            $where = array('a.title like' => '%'.$this->session->userdata('search_topic_keywords').'%');
            $data=$this->topic_m->get_topic_recent($where, $page, 20, 'admin/topic/search', 4);
            $data['title'] = $this->session->userdata('search_topic_keywords');
            $this->load->view('admin/topic_list', $data);
        }
    }

    /**
     * 编辑帖子
     * @param   $tid 帖子id
     */
    public function edit($tid=1)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('node_m');

        $this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('nid', 'Node', 'required');
        $this->form_validation->set_rules('content');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['nodes']=$this->node_m->get_nodes();
            $data['topic']=$this->topic_m->get_topic_detail($tid);
            $this->load->view('admin/topic_edit', $data);
        }
        else
        {
            //form success
            $data = array(
                'nid' => $this->input->post('nid'),
                'title' => strip_tags($this->input->post('title', TRUE)),
                'content' => $this->input->post('content')
            );

            $this->topic_m->update($tid, $data);
            redirect('topic/'.$tid);
        }
    }

}

/* End of file topic.php */
/* Location: ./application/controllers/admin/topic.php */
