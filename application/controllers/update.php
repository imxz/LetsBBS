<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends Front_Controller {

    public function __construct()
    {
        parent::__construct();

        if (is_file(FCPATH.'application/views/install/update.lock')) {
            $string='
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script>
            alert("更新程序已经锁定，如果重新更新请删除application/views/install/update.lock文件");
            top.location="'.base_url().'";
            </script>
            ';
            exit($string);
        }
    }

    public function index()
    {
        $this->load->view('install/update');
    }

    /**
     * 更新数据表
     */
    private function _updateTables()
    {
        $sql = file_get_contents(FCPATH.'application/cache/install/update.sql');
        //$sql = str_replace('prefix_', $dbprefix, $sql);
        if (!@mysqli_connect($this->db->hostname, $this->db->username, $this->db->password, $this->db->database)->multi_query($sql)) {
            $string='
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script>
            alert("更新数据表失败，请重试！");
            top.location="'.base_url('update').'";
            </script>
            ';
            exit($string);
        }
    }

    public function process()
    {
        $this->_updateTables();

        sleep(2);
        //写禁止再次更新的文件
        file_put_contents(FCPATH.'application/views/install/update.lock', time());

        $string='
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script>
        alert("升级成功，感谢您选择 Let\'sBBS !");
        top.location="'.base_url().'";
        </script>
        ';
        exit($string);
    }
}

/* End of file update.php */
/* Location: ./application/controllers/update.php */