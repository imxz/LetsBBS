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
        $where = array('a.nid' => $nid, 'a.status' => 1);
        $data=$this->topic_m->get_topic_recent($where, NULL, $page, 20, 'node/'.$nid, 3);

        $data['nid'] = $nid;
        if ($data['topics']) {
            $data['site_title'] = $data['topics'][0]['nname'];
        } else {
            $data['site_title'] = '暂无主题';
        }

        $data['node_name'] = $data['site_title'];

        //当前节点的收藏情况
        $data['follow_status'] = '加入收藏';
        $data['follow_link'] = base_url('node/follow/'.$nid);
        $this->load->model('node_m');
        if ($this->session->userdata('uid') && $this->node_m->is_node_followed($nid)) {
            $data['follow_status'] = '取消收藏';
            $data['follow_link'] = base_url('node/unfollow/'.$nid);
        }

        if ($page>1) {
            $data['site_title'] .= ' '.$page.'/'.$data['num_pages'];
        }
        $this->load->view('topic_node_list', $data);
    }

    /**
     * 添加收藏
     * @param   $nid 节点id
     */
    public function follow($nid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->load->model('node_m');
        $this->node_m->follow($nid);

        //更新用户表
        $this->db->set('node_follow', 'node_follow+1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('node_follow', $this->session->userdata('node_follow')+1);

        redirect($this->input->server('HTTP_REFERER'));
    }

    /**
     * 取消收藏
     * @param   $nid 节点id
     */
    public function unfollow($nid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->load->model('node_m');
        $this->node_m->unfollow($nid);

        //更新用户表
        $this->db->set('node_follow', 'node_follow-1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('node_follow', $this->session->userdata('node_follow')-1);

        redirect($this->input->server('HTTP_REFERER'));
    }
}

/* End of file node.php */
/* Location: ./application/controllers/node.php */