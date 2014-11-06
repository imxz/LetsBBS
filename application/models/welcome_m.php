<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome_M extends CI_Model {

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

}

/* End of file welcome_m.php */
/* Location: ./application/models/welcome_m.php */