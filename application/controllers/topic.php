<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('topic_m');
    }

    public function index()
    {
        $this->load->model('node_m');

        $where = array('a.status' => 1);
        $where_in = NULL;
        if ($this->session->userdata('top_show_node')) {
            switch ($this->session->userdata('top_show_node')) {
                case 'all':
                    break;
                case 'nodes':
                    $where_in['type'] = 'a.nid';
                    $nodes = $this->node_m->get_node_followed();
                    foreach ($nodes as $node) {
                        $node_id[] = $node['nid'];
                    }
                    $where_in['range'] = (count($node_id) > 0) ? $node_id : array('0');
                    break;
                case 'users':
                    $this->load->model('user_m');
                    $users = $this->user_m->get_user_followed();
                    foreach ($users as $user) {
                        $user_id[] = $user['fuid'];
                    }
                    $where_in['type'] = 'a.uid';
                    $where_in['range'] = (count($user_id) > 0) ? $user_id : array('0');
                    break;
                case 'topics':
                    $topics = $this->topic_m->get_topic_followed();
                    foreach ($topics as $topic) {
                        $topic_id[] = $topic['tid'];
                    }
                    $where_in['type'] = 'a.tid';
                    $where_in['range'] = (count($topic_id) > 0) ? $topic_id : array('0');
                    break;
                default:
                    $where = array('a.status' => 1, 'a.nid' => $this->session->userdata('top_show_node'));
                    break;
            }
        }

        $data=$this->topic_m->get_topic_recent($where, $where_in, 1, 20, 'recent', 2);

        $where = array('a.addtime >' => time()-86400*90, 'a.status' => 1);
        $hot_data=$this->topic_m->get_topic_recent($where, NULL, 1, 15, 'recent', 2, 'view');

        $data['nodes']=$this->node_m->get_nodes(array('featured' => 1));
        $data['tnodes']=$this->node_m->get_normal_nodes(array('topshow' => 1));
        $data['site_title'] = '欢迎';
        $data['hot_topics'] = $hot_data['topics'];

        $this->load->view('topic_list', $data);
    }

    public function show($node)
    {
        $this->session->set_userdata('top_show_node', $node);
        $this->index();
    }

    /**
     * 最近主题列表
     * @param   $page 当前页码
     */
    public function recent($page = 1)
    {
        $where = array('a.status' => 1);
        $where_in = NULL;
        if ($this->session->userdata('top_show_node')) {
            switch ($this->session->userdata('top_show_node')) {
                case 'all':
                    break;
                case 'nodes':
                    $this->load->model('node_m');
                    $where_in['type'] = 'a.nid';
                    $nodes = $this->node_m->get_node_followed();
                    foreach ($nodes as $node) {
                        $node_id[] = $node['nid'];
                    }
                    $where_in['range'] = (count($node_id) > 0) ? $node_id : array('0');
                    break;
                case 'users':
                    $this->load->model('user_m');
                    $users = $this->user_m->get_user_followed();
                    foreach ($users as $user) {
                        $user_id[] = $user['fuid'];
                    }
                    $where_in['type'] = 'a.uid';
                    $where_in['range'] = (count($user_id) > 0) ? $user_id : array('0');
                    break;
                default:
                    $where = array('a.status' => 1, 'a.nid' => $this->session->userdata('top_show_node'));
                    break;
            }
        }

        $data=$this->topic_m->get_topic_recent($where, $where_in, $page, 20, 'recent', 2);

        if ($page>1) {
            $data['site_title'] = '最近的主题 '.$page.'/'.$data['num_pages'];
        }

        $this->load->view('topic_recent_list', $data);
    }

    /**
     * 查看主题详情
     * @param   $tid 帖子id
     */
    public function detail($tid)
    {
        $this->load->helper('form');
        $this->load->model('comment_m');

        $data['topic']=$this->topic_m->get_topic_detail($tid);
        $data['comments']=$this->comment_m->get_comments_bytid($tid);
        $data['site_title'] = $data['topic']['title'];

        //当前用户对主题的收藏情况
        $data['follow_status'] = '加入收藏';
        $data['follow_link'] = base_url('topic/follow/'.$tid);
        if ($this->topic_m->is_user_followed($this->session->userdata('uid'), $tid)) {
            $data['follow_status'] = '取消收藏';
            $data['follow_link'] = base_url('topic/unfollow/'.$tid);
        }

        $this->load->view('topic_detail', $data);

        //更新浏览计数
        $this->topic_m->update($tid, array('view'=>$data['topic']['view']+1));
    }

    /**
     * 添加新主题
     */
    public function add()
    {
        $this->load->helper('auth');
        is_login_exit();
        is_user_active_exit();

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('node_m');

        $this->form_validation->set_rules('title', '标题', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('nid', '节点', 'required');
        $this->form_validation->set_rules('content');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['nodes']=$this->node_m->get_nodes();
            $data['site_title'] = '创建新主题';
            $this->load->view('topic_add', $data);
        }
        else
        {
            //form success
            $node = $this->node_m->get_node_byid($this->input->post('nid'));
            if (!$node) {
                $string='
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <script>
                alert("无法向不存在的节点添加主题");
                top.location="'.$this->input->server('HTTP_REFERER').'";
                </script>
                ';
                exit($string);
            }

            $this->load->model('site_m');
            $site_settings = $this->site_m->get_site_settings();

            $data = array(
                'nid' => $this->input->post('nid'),
                'uid' => $this->session->userdata('uid'),
                'title' => htmlspecialchars($this->input->post('title')),
                'content' => $this->input->post('content'),
                'addtime' => time(),
                'replytime' => time(),
                'status' => $site_settings['site_topic_status']
            );

            $insert_id = $this->topic_m->add($data);

            //更新用户发表帖子数量
            $this->db->set('topic', 'topic+1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');
            //更新网站统计信息 主题
            $this->db->set('ovalue', 'ovalue+1', FALSE)->where('oname', 'site_topic_number')->update('letsbbs_option');
            redirect('topic/'.$insert_id);
        }
    }

    /**
     * 添加收藏
     * @param   $tid 帖子id
     */
    public function follow($tid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->topic_m->follow($tid);

        //更新用户表
        $this->db->set('topic_follow', 'topic_follow+1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('topic_follow', $this->session->userdata('topic_follow')+1);

        redirect($this->input->server('HTTP_REFERER'));
    }

    /**
     * 取消收藏
     * @param   $tid 帖子id
     */
    public function unfollow($tid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->topic_m->unfollow($tid);

        //更新用户表
        $this->db->set('topic_follow', 'topic_follow-1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('topic_follow', $this->session->userdata('topic_follow')-1);

        redirect($this->input->server('HTTP_REFERER'));
    }
}

/* End of file topic.php */
/* Location: ./application/controllers/topic.php */