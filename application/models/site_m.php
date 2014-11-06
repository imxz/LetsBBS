<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_M extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取所有的网站设置项
     * @return  关联数组
     */
    public function get_site_settings()
    {
        $query = $this->db->get('letsbbs_option');
        $settings = $query->result_array();
        foreach ($settings as $setting) {
            $data[$setting['oname']]=$setting['ovalue'];
        }
        return $data;
    }

    /**
     * 更新网站设置
     * @param   $data 关联数组
     * @return        操作结果
     */
    public function update_site($data)
    {
        foreach ($data as $key => $value) {
            $updatedata[]=array(
                'oname' => $key,
                'ovalue' => $value
                );
        }
        return $this->db->update_batch('letsbbs_option', $updatedata, 'oname');
    }

}

/* End of file site_m.php */
/* Location: ./application/models/site_m.php */