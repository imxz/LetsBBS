<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
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
        if (!@mysqli_connect($dbhost, $dbuser, $dbpsw, $dbname)->multi_query($sql)) {
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