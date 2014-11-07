<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用于检查是否为管理员
 */
if ( ! function_exists('is_admin_exit'))
{
    function is_admin_exit()
    {
        $CI =& get_instance();
        if ($CI->session->userdata('group_id')!=1) {
            $string='
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script>
            alert("非法访问！管理员请先登录。");
            top.location="'.base_url().'";
            </script>
            ';
            exit($string);
        }
    }
}

/**
 * 用于检查用户是否登录
 */
if ( ! function_exists('is_login_exit'))
{
    function is_login_exit($alert="非法访问！注册用户请先登录。", $go="")
    {
        $CI =& get_instance();
        if (!$CI->session->userdata('username')) {
            $string='
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script>
            alert("'.$alert.'");
            top.location="'.base_url($go).'";
            </script>
            ';
            exit($string);
        }
    }
}

/* End of file auth_helper.php */
/* Location: ./app/helpers/auth_helper.php */