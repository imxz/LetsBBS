<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Node extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 主页显示所有节点
     */
    public function index()
    {
        $this->load->model('node_m');
        $data['nodes']=$this->node_m->get_nodes();

        $data['site_title'] = '所有节点';
        $this->load->view('node_list', $data);
    }

    /**
     * 根据nid获取最近帖子类表
     * @param   $nid  分类id
     * @param   $page 当前页码
     */
    public function recent($nid, $page=1)
    {
        $this->load->model('topic_m');
        $where = array('a.nid' => $nid);
        $data=$this->topic_m->get_topic_recent($where, $page, 20, 'node/'.$nid, 3);

        $data['site_title'] = $data['topics'][0]['nname'];
        if ($page>1) {
            $data['site_title'] .= ' '.$page.'/'.$data['num_pages'];
        }
        $this->load->view('node_list', $data);
    }
}

/* End of file node.php */
/* Location: ./application/controllers/node.php */