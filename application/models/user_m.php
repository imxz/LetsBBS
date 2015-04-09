<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_M extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 用户注册
     * @param  $data 关联数组
     */
    public function reg($data)
    {
        return $this->db->insert('letsbbs_user', $data);
    }

    /**
     * 用户登录
     * @param  关联数组 $data 用户名和密码
     * @return 是否登录成功
     */
    public function login($data)
    {
        $this->db->where('username', $data['username']);
        $query = $this->db->get('letsbbs_user');
        if ($query->num_rows() > 0) {
            $user = $query->row_array();
            if ($user['password']==$data['password']) {
                $this->session->set_userdata('username', $user['username']);
                $this->session->set_userdata('uid', $user['uid']);
                $this->session->set_userdata('group_id', $user['group_id']);
                $this->session->set_userdata('notification', $user['notice']);
                $this->session->set_userdata('is_active', $user['is_active']);
                $this->session->set_userdata('avatar', $user['avatar']);
                $this->session->set_userdata('node_follow', $user['node_follow']);
                $this->session->set_userdata('user_follow', $user['user_follow']);
                $this->session->set_userdata('topic_follow', $user['topic_follow']);
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * 获取用户信息
     * @param  int $uid 用户id
     * @return 数组      用户信息
     */
    public function get_user_byid($uid)
    {
        $this->db->where('uid', $uid);
        $query = $this->db->get('letsbbs_user');
        return $user_info = $query->row_array();
    }

    /**
     * 根据用户名获取用户信息
     * @param  $username 用户名
     * @return 数组      用户信息
     */
    public function get_user_byname($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('letsbbs_user');
        return $user_info = $query->row_array();
    }

    /**
     * 更新用户资料
     * @param  int $uid  用户id
     * @param  关联数组 $data 用户资料
     * @return 操作结果
     */
    public function update($uid, $data)
    {
        $this->db->where('uid', $uid);
        return $this->db->update('letsbbs_user', $data);
    }

    /**
     * 根据条件获取条目总数
     * @param   $where    附加条件 格式为关联数组，NULL返回所有
     * @return       条目数量
     */
    public function count_items($where)
    {
        if ($where!=NULL) {
            $this->db->where($where);
        }
        return $this->db->count_all_results('letsbbs_user');
    }

    /**
     * 获取用户列表和分页
     * @param    $where    附加条件 格式为关联数组，NULL返回所有
     * @param    $page     当前页码
     * @param    $pagesize 每页条数
     * @param    $url      定位控制器和方法的url段
     * @param    $uri_segment 分页数字在哪个段
     * @return         数组 分页和列表数据
     */
    public function get_users_list($where, $page = 1, $pagesize=15, $url='', $uri_segment=3)
    {
        $this->load->library('pagination');
        $config['base_url'] = base_url($url);
        $config['total_rows'] = $this->count_items($where);
        $config['use_page_numbers'] = TRUE;
        $config['display_pages'] = FALSE;
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;
        $config['uri_segment'] = $uri_segment;
        $config['per_page'] = $pagesize;

        $config['next_link'] = '下一页';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '上一页';
        $config['prev_tag_open'] = '<li class="previous">';
        $config['prev_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $data['users_pagination']=$this->pagination->create_links();

        if ($where!=NULL) {
            $this->db->where($where);
        }
        $this->db->order_by('regtime','desc');
        $query = $this->db->get('letsbbs_user', $pagesize, ($page - 1) * $pagesize);
        $data['users']=$query->result_array();

        return $data;
    }

    /**
     * 判断某用户是否关注了某个用户
     * @param    $uid 用户id
     * @return boolean      是否关注
     */
    public function is_user_followed($uid)
    {
        $this->db->where('fuid', $uid);
        $this->db->where('myuid', $this->session->userdata('uid'));
        $query = $this->db->get('letsbbs_user_follow');
        return $query->num_rows();
    }

    public function get_user_followed()
    {
        $this->db->where('myuid', $this->session->userdata('uid'));
        $query = $this->db->get('letsbbs_user_follow');
        return $query->result_array();
    }

    /**
     * 添加关注
     * @param   $uid 用户id
     */
    public function follow($uid)
    {
        $data = array(
            'fuid' => $uid,
            'myuid' => $this->session->userdata('uid'),
            'addtime' => time()
            );

        return $this->db->insert('letsbbs_user_follow', $data);
    }

    /**
     * 取消关注
     * @param   $uid 用户id
     */
    public function unfollow($uid)
    {
        $this->db->where('fuid', $uid);
        $this->db->where('myuid', $this->session->userdata('uid'));
        return $this->db->delete('letsbbs_user_follow');
    }
}

/* End of file user_m.php */
/* Location: ./application/models/user_m.php */