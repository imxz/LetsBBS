<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Topic_M extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加新主题
     * @param  $data 关联数组
     * @return int   新主题的id
     */
    public function add($data)
    {
        $this->db->insert('letsbbs_topic', $data);
        return $this->db->insert_id();
    }

    /**
     * 更新
     * @param  int $tid  帖子id
     * @param  关联数组 $data
     * @return 操作结果
     */
    public function update($tid, $data)
    {
        $this->db->where('tid', $tid);
        return $this->db->update('letsbbs_topic', $data);
    }

    /**
     * 删除主题以及与其相关的内容
     * @param   $tid 主题id
     */
    public function delete($tid)
    {
        $tables = array('letsbbs_topic', 'letsbbs_comment', 'letsbbs_notification');
        $this->db->where('tid', $tid);
        $this->db->delete($tables);
    }

    /**
     * 根据tid获取帖子详情
     * @param   $tid 帖子id
     * @return       帖子详情
     */
    public function get_topic_detail($tid)
    {
        $this->db->select('a.*,b.username, b.avatar, c.nname');
        $this->db->from('letsbbs_topic a');
        $this->db->join('letsbbs_user b','b.uid = a.uid','left');
        $this->db->join('letsbbs_node c','c.nid = a.nid','left');

        $this->db->where('a.tid', $tid);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * 根据条件返回帖子列表
     * @param   $where    附加条件 格式为关联数组，NULL返回所有
     * @param   $page     当前页码
     * @param   $pagesize 每页条数
     * @return            关联数组
     */
    public function get_topic_list($where, $where_in, $page, $pagesize, $order)
    {
        $this->db->select('a.*, b.username, b.avatar, c.username as rname, d.nname');
        $this->db->from('letsbbs_topic a');
        $this->db->join('letsbbs_user b','b.uid = a.uid','left');
        $this->db->join('letsbbs_user c','c.uid = a.replyuid','left');
        $this->db->join('letsbbs_node d','d.nid = a.nid','left');

        if ($where!=NULL) {
            $this->db->where($where);
        }
        if ($where_in!=NULL) {
            $this->db->where_in($where_in['type'], $where_in['range']);
        }
        $this->db->order_by($order,'desc');
        $this->db->limit($pagesize, ($page - 1) * $pagesize);
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * 根据条件获取条目总数
     * @param   $where    附加条件 格式为关联数组，NULL返回所有
     * @return       条目数量
     */
    public function count_items($where, $where_in)
    {
        if ($where!=NULL) {
            $this->db->where($where);
        }
        if ($where_in!=NULL) {
            $this->db->where_in($where_in['type'], $where_in['range']);
        }
        return $this->db->count_all_results('letsbbs_topic a');
    }

    /**
     * 获取最近帖子列表和分页
     * @param    $where    附加条件 格式为关联数组，NULL返回所有
     * @param    $page 当前页码
     * @param    $pagesize 每页条数
     * @param    $url  定位控制器和方法的url段
     * @param    $uri_segment 分页数字在哪个段
     * @return         数组 分页和列表数据
     */
    public function get_topic_recent($where, $where_in, $page = 1, $pagesize=15, $url='', $uri_segment=2, $order='replytime')
    {
        $this->load->library('pagination');
        $config['base_url'] = base_url($url);
        $config['total_rows'] = $this->count_items($where, $where_in);
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

        $data['pagination']=$this->pagination->create_links();
        $data['topics']=$this->get_topic_list($where, $where_in, $page, $pagesize, $order);
        $data['num_pages'] = ceil($config['total_rows'] / $pagesize);
        return $data;
    }

    /**
     * 添加收藏
     * @param   $tid 帖子id
     */
    public function follow($tid)
    {
        $data = array(
            'tid' => $tid,
            'uid' => $this->session->userdata('uid'),
            'addtime' => time()
            );

        return $this->db->insert('letsbbs_topic_follow', $data);
    }

    /**
     * 取消收藏
     * @param   $tid 用户id
     */
    public function unfollow($tid)
    {
        $this->db->where('tid', $tid);
        $this->db->where('uid', $this->session->userdata('uid'));
        return $this->db->delete('letsbbs_topic_follow');
    }

    /**
     * 判断某用户是否关注了主题
     * @param    $uid 用户id
     * @return boolean      是否关注
     */
    public function is_user_followed($uid, $tid)
    {
        if (!$uid) {
            return 0;
        }

        $this->db->where('uid', $uid);
        $this->db->where('tid', $tid);
        $query = $this->db->get('letsbbs_topic_follow');
        return $query->num_rows();
    }

    /**
     * 获得当前用户收藏的所有帖子id
     * @return 关联数组
     */
    public function get_topic_followed()
    {
        $this->db->where('uid', $this->session->userdata('uid'));
        $query = $this->db->get('letsbbs_topic_follow');
        return $query->result_array();
    }
}

/* End of file topic_m.php */
/* Location: ./application/models/topic_m.php */