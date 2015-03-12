<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Node_M extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取nodes
     * @return  关联数组
     */
    public function get_nodes($where = NULL)
    {
        if ($where!=NULL) {
            $this->db->where($where);
        }

        $query = $this->db->get('letsbbs_node');
        $this->db->order_by('nid','desc');
        $nodes = $query->result_array();

        //变为父节点id为第一个参数的数组
        if ($nodes) {
            foreach ($nodes as $node) {
                $newnodes[$node['pid']][]=$node;
            }
            return $newnodes;
        }
    }

    /**
     * 获取普通排列的nodes
     * @return  关联数组
     */
    public function get_normal_nodes($where = NULL)
    {
        if ($where!=NULL) {
            $this->db->where($where);
        }

        $query = $this->db->get('letsbbs_node');
        $this->db->order_by('nid','desc');
        $nodes = $query->result_array();
        return $nodes;
    }

    /**
     * 根据nid获取node信息
     * @param   $nid node的id
     * @return       关联数组
     */
    public function get_node_byid($nid)
    {
        $this->db->where('nid', $nid);
        $query = $this->db->get('letsbbs_node');
        return $user_info = $query->row_array();
    }

    /**
     * 添加节点
     * @param  $data 关联数组
     */
    public function add($data)
    {
        return $this->db->insert('letsbbs_node', $data);
    }

    /**
     * 更新节点资料
     * @param   $nid  节点id
     * @param   $data 关联数组 节点资料
     * @return  操作结果
     */
    public function update($nid, $data)
    {
        $this->db->where('nid', $nid);
        return $this->db->update('letsbbs_node', $data);
    }

    /**
     * 判断某用户是否收藏了某个节点
     * @param    $nid 节点id
     * @return boolean      是否收藏
     */
    public function is_node_followed($nid)
    {
        $this->db->where('nid', $nid);
        $this->db->where('uid', $this->session->userdata('uid'));
        $query = $this->db->get('letsbbs_node_follow');
        return $query->num_rows();
    }

    public function get_node_followed()
    {
        $this->db->where('uid', $this->session->userdata('uid'));
        $query = $this->db->get('letsbbs_node_follow');
        return $query->result_array();
    }

    /**
     * 添加收藏
     * @param   $nid 节点id
     */
    public function follow($nid)
    {
        $data = array(
            'nid' => $nid,
            'uid' => $this->session->userdata('uid'),
            'addtime' => time()
            );

        return $this->db->insert('letsbbs_node_follow', $data);
    }

    /**
     * 取消收藏
     * @param   $nid 节点id
     */
    public function unfollow($nid)
    {
        $this->db->where('nid', $nid);
        $this->db->where('uid', $this->session->userdata('uid'));
        return $this->db->delete('letsbbs_node_follow');
    }
}

/* End of file node_m.php */
/* Location: ./application/models/node_m.php */