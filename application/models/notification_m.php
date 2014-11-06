<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_M extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加通知
     */
    public function add($data)
    {
        return $this->db->insert('letsbbs_notification', $data);
    }

    /**
     * 根据条件返回通知列表
     * @param   $where    附加条件 格式为关联数组，NULL返回所有
     * @param   $page     当前页码
     * @param   $pagesize 每页条数
     * @return            关联数组
     */
    public function get_notifications_list($where, $page, $pagesize)
    {
        $this->db->select('a.*, b.username as fromusername, c.tid as topicid, c.title as topictitle, d.content as comment');
        $this->db->from('letsbbs_notification a');
        $this->db->join('letsbbs_user b','b.uid = a.fuid','left');
        $this->db->join('letsbbs_topic c','c.tid = a.tid','left');
        $this->db->join('letsbbs_comment d','d.cid = a.cid','left');

        if ($where!=NULL) {
            $this->db->where($where);
        }
        $this->db->order_by('addtime','desc');
        $this->db->limit($pagesize, ($page - 1) * $pagesize);
        $query = $this->db->get();

        return $query->result_array();
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
        return $this->db->count_all_results('letsbbs_notification a');
    }

    /**
     * 获取最近帖子列表和分页
     * @param    $where    附加条件 格式为关联数组，NULL返回所有
     * @param    $page     当前页码
     * @param    $pagesize 每页条数
     * @param    $url      定位控制器和方法的url段
     * @param    $uri_segment 分页数字在哪个段
     * @return         数组 分页和列表数据
     */
    public function get_notifications_recent($where, $page = 1, $pagesize=15, $url='', $uri_segment=2)
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

        $data['pagination']=$this->pagination->create_links();
        $data['notifications']=$this->get_notifications_list($where, $page, $pagesize);
        return $data;
    }
}

/* End of file notification_m.php */
/* Location: ./application/models/notification_m.php */