<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_m');
        $this->load->model('topic_m');
        $this->load->model('notification_m');
        $this->load->model('user_m');

        $this->load->helper('auth');
        is_login_exit();
        is_user_active_exit();
    }

    /**
     * 添加一条回复
     */
    public function add()
    {
        $topic=$this->topic_m->get_topic_detail($this->input->post('tid'));

        //添加评论
        $data = array(
            'tid' => $this->input->post('tid'),
            'uid' => $this->session->userdata('uid'),
            'replytime' => time(),
            'content' => $this->input->post('content')
            );
        $comment_id=$this->comment_m->add($data);

        //更新用户的回复数量
        $this->db->set('reply', 'reply+1', FALSE)->where('uid', $this->session->userdata('uid'))->update('letsbbs_user');

        //@功能的发送回复提醒
        $pattern = '/@<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/';
        preg_match_all ( $pattern, $data['content'], $matches );
        $matches [4] = array_unique($matches [4]);
        if ($matches [4]) {
            $n_data = array(
                'cid' => $comment_id,
                'tid' => $this->input->post('tid'),
                'fuid' => $this->session->userdata('uid'),
                'addtime' => time()
                );
            foreach ($matches [4] as $to_username) {
                if ($to_username != $topic['username']) { //@的对象不是本文的作者
                    $to_userinfo=$this->user_m->get_user_byname($to_username);
                    $n_data['tuid']=$to_userinfo['uid'];
                    //写入notifications表 更新通知
                    $this->notification_m->add($n_data);
                    //更新to user的未读提醒数
                    $this->user_m->update($to_userinfo['uid'], array('notice' => $to_userinfo['notice']+1));
                }
            }
        }

        //更新帖子的信息
        $updatedata = array(
            'replyuid' => $this->session->userdata('uid'),
            'replytime' => time(),
            'comment' => $topic['comment'] + 1
            );
        $this->topic_m->update($this->input->post('tid'), $updatedata);

        //向主题发布者发送提醒 写入notifications表和更新未读通知数
        if ($topic['uid']!=$this->session->userdata('uid')) {
            $newn_data = array(
                'cid' => $comment_id,
                'tid' => $this->input->post('tid'),
                'fuid' => $this->session->userdata('uid'),
                'tuid' => $topic['uid'],
                'ntype' => 1,
                'addtime' => time()
                );
            $this->notification_m->add($newn_data);
            $this->db->set('notice', 'notice+1', FALSE)->where('uid', $topic['uid'])->update('letsbbs_user');
        }

        //跳转到原帖子
        redirect('topic/'.$this->input->post('tid').'#Reply'.$updatedata['comment']);
    }
}

/* End of file comment.php */
/* Location: ./application/controllers/comment.php */