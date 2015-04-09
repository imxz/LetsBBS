<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
        $this->load->model('topic_m');
        $this->load->model('comment_m');
    }

    /**
     * 用户中心主页 显示用户信息、用户发表帖子第一页、用户评论第一页
     * @param   $username 用户名
     */
    public function index($username)
    {
        $user['user']=$this->user_m->get_user_byname($username);
        $where = array('a.uid' => $user['user']['uid'], 'a.status' => 1);
        $topics=$this->topic_m->get_topic_recent($where, NULL, 1 ,15 ,'member/'.$username.'/topic', 4, 'addtime');
        $where = array('a.uid' => $user['user']['uid']);
        $comments=$this->comment_m->get_comments_recent($where, 1 ,15 ,'member/'.$username.'/comment', 4);
        $data=array_merge($user, $topics, $comments);

        //当前用户的特别关注情况
        $data['follow_status'] = '加入特别关注';
        $data['follow_link'] = base_url('member/follow/'.$user['user']['uid']);
        if ($this->session->userdata('uid') && $this->user_m->is_user_followed($user['user']['uid'])) {
            $data['follow_status'] = '取消特别关注';
            $data['follow_link'] = base_url('member/unfollow/'.$user['user']['uid']);
        }

        $data['site_title'] = $username;
        $this->load->view('member_detail',$data);
    }

    /**
     * 用户发表的所有帖子列表
     * @param   $username 用户名
     * @param   $page     当前页码
     */
    public function topic($username, $page)
    {
        $user['user']=$this->user_m->get_user_byname($username);
        $where = array('a.uid' => $user['user']['uid'], 'a.status' => 1);
        $topics=$this->topic_m->get_topic_recent($where, NULL, $page ,15 ,'member/'.$username.'/topic', 4, 'addtime');
        $data=array_merge($user, $topics);

        $data['site_title'] = $username . '创建的主题';
        $this->load->view('member_topic_list',$data);
    }

    /**
     * 用户发表的所有评论列表
     * @param   $username 用户名
     * @param   $page     当前页码
     */
    public function comment($username, $page)
    {
        $user['user']=$this->user_m->get_user_byname($username);
        $where = array('a.uid' => $user['user']['uid']);
        $comments=$this->comment_m->get_comments_recent($where, $page ,15 ,'member/'.$username.'/comment', 4);
        $data=array_merge($user, $comments);

        $data['site_title'] = $username . '添加的回复';
        $this->load->view('member_comment_list',$data);
    }

    /**
     * 添加收藏
     * @param   $uid 节点id
     */
    public function follow($uid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->user_m->follow($uid);

        //更新用户表
        $this->db->set('user_follow', 'user_follow+1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('user_follow', $this->session->userdata('user_follow')+1);

        redirect($this->input->server('HTTP_REFERER'));
    }

    /**
     * 取消收藏
     * @param   $uid 节点id
     */
    public function unfollow($uid)
    {
        $this->load->helper('auth');
        is_login_exit("注册用户请先登录");

        $this->user_m->unfollow($uid);

        //更新用户表
        $this->db->set('user_follow', 'user_follow-1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //更新显示的收藏数
        $this->session->set_userdata('user_follow', $this->session->userdata('user_follow')-1);

        redirect($this->input->server('HTTP_REFERER'));
    }
}

/* End of file member.php */
/* Location: ./application/controllers/member.php */