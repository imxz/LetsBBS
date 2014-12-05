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
            top.location="'.base_url('login').'";
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
    function is_login_exit($alert="游客不能发表主题，注册用户请先登录。", $go="login")
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

/**
 * 用于检查用户是否被禁言
 */
if ( ! function_exists('is_user_active_exit'))
{
    function is_user_active_exit()
    {
        $CI =& get_instance();
        if ($CI->session->userdata('is_active') != '1') {
            $string='
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script>
            alert("您已被禁言，如有疑问请联系管理员。");
            top.location="'.$CI->input->server('HTTP_REFERER').'";
            </script>
            ';
            exit($string);
        }
    }
}

/* End of file auth_helper.php */
/* Location: ./app/helpers/auth_helper.php */