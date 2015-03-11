<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
    }

    /**
     * 注册用户
     */
    public function reg()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', '用户名', 'trim|required|alpha_numeric|min_length[3]|max_length[12]|is_unique[letsbbs_user.username]');
        $this->form_validation->set_rules('password', '密码', 'trim|required|md5');
        $this->form_validation->set_rules('email', '邮箱', 'trim|required|valid_email|is_unique[letsbbs_user.email]');
        $this->form_validation->set_rules('captcha', '验证码', 'trim|callback_captcha_check');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['cap_image']=$this->_get_cap_image();
            $data['site_title'] = '注册';
            $this->load->view('user_reg', $data);
        }
        else
        {
            //form success
            $data = array(
                'username' => strtolower($this->input->post('username')),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'regtime' => time()
            );

            $this->user_m->reg($data);
            $this->user_m->login($data);
            //更新网站统计信息 注册用户
            $this->db->set('ovalue', 'ovalue+1', FALSE)->where('oname', 'site_user_number')->update('letsbbs_option');
            redirect();
        }
    }

    /**
    * 获取验证码
    * @return 图片地址的html代码
    */
    private function _get_cap_image()
    {
        $this->load->helper('captcha');
        $vals = array(
            'img_path' => './captcha/',
            'img_url' => base_url() . 'captcha/',
            'font_path' => './system/fonts/texb.ttf',
            'img_width' => '100',
            'img_height' => 30,
            'expiration' => 7200
            );

        $cap = create_captcha($vals);
        $this->session->set_userdata('captcha', $cap['word']);
        return $cap['image'];
    }

    /**
     * 检查验证码是否正确 需要输入验证码提交时验证的回调函数
     * @param   $cap 用户输入的验证码
     */
    public function captcha_check($cap)
    {
        if ($cap!=$this->session->userdata('captcha')) {
            $this->form_validation->set_message('captcha_check', '%s 输入不正确.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
    * 刷新验证码
    * @return  图片地址的html代码
    */
    public function refresh_cap_image()
    {
       $cap_image = $this->_get_cap_image();
       echo $cap_image;
    }

    /**
     * 用户登录
     */
    public function login()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', '用户名', 'trim|required');
        $this->form_validation->set_rules('password', '密码', 'trim|required|md5');
        $this->form_validation->set_rules('captcha', '验证码', 'trim|callback_captcha_check');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['cap_image']=$this->_get_cap_image();
            $data['site_title'] = '登录';
            $this->load->view('user_login', $data);
        }
        else
        {
            //form success
            $data = array(
                'username' => $this->input->post('username', TRUE),
                'password' => $this->input->post('password')
            );

            if ($this->user_m->login($data)) {
                redirect();
            } else {
                redirect('login');
            }
        }
    }

    /**
     * 用户登出
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect();
    }
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */