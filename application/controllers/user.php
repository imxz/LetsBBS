<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    /**
     * 注册用户
     */
    public function reg()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'UserName', 'trim|required|alpha_numeric|min_length[3]|max_length[12]|is_unique[letsbbs_user.username]');
        $this->form_validation->set_rules('password', 'PassWord', 'trim|required|md5');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|is_unique[letsbbs_user.email]');
        $this->form_validation->set_rules('captcha', 'Captcha', 'trim|callback_captcha_check');

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
                'username' => strtolower($this->input->post('username', TRUE)),
                'password' => $this->input->post('password'),
                'email' => $this->input->post('email'),
                'regtime' => time()
            );

            $this->user_m->reg($data);
            $this->user_m->login($data);
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
            $this->form_validation->set_message('captcha_check', 'Please input the correct captcha.');
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

        $this->form_validation->set_rules('username', 'UserName', 'trim|required');
        $this->form_validation->set_rules('password', 'PassWord', 'trim|required|md5');
        $this->form_validation->set_rules('captcha', 'Captcha', 'trim|callback_captcha_check');

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

    /**
     * 设置的首页，默认显示资料修改
     */
    public function settings()
    {
        $this->profile();
    }

    /**
     * 修改个人资料
     */
    public function profile()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('qq', 'QQ', 'trim|integer');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data=$this->user_m->get_user_byid($this->session->userdata('uid'));

            $data['site_title'] = '修改资料';
            $this->load->view('user_profile', $data);
        }
        else
        {
            //form success
            $data = array(
                'email' => $this->input->post('email'),
                'qq' => $this->input->post('qq'),
                'location' => $this->input->post('location', TRUE),
                'homepage' => prep_url($this->input->post('homepage', TRUE)),
                'signature' => $this->input->post('signature', TRUE),
                'introduction' => $this->input->post('introduction', TRUE)
                );

            $this->user_m->update($this->session->userdata('uid'), $data);
            redirect('user/profile');
        }
    }

    /**
     * 修改头像
     */
    public function avatar($error='')
    {
        $this->load->helper('form');

        $user_info=$this->user_m->get_user_byid($this->session->userdata('uid'));
        $data['avatar']=$user_info['avatar'];

        $data['error']=$error;
        $data['site_title'] = '上传头像';
        $this->load->view('user_avatar', $data);
    }

    /**
     * 修改头像 上传
     */
    public function avatar_do_upload()
    {
        $config['upload_path'] = './uploads/avatar';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['encrypt_name'] = TRUE;
        $config['max_size'] = '512';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('avatarInputFile'))
        {
            $this->avatar($this->upload->display_errors());
        }
        else
        {
            //upload sucess
            $img_array = $this->upload->data();
            $this->load->library('AvatarResize');

            if ($this->avatarresize->resize($img_array['full_path'], 100 ,100 ,'big') && $this->avatarresize->resize($img_array['full_path'], 48 ,48 ,'normal') && $this->avatarresize->resize($img_array['full_path'], 24 ,24 ,'small')) {

                $data = array(
                    'avatar' => $this->avatarresize->get_dir()
                    );

                $this->user_m->update($this->session->userdata('uid'), $data);

                //删除tmp下的原图
                unlink($img_array['full_path']);
                redirect('user/avatar');
            } else {
                //设置三个头像没有成功
                $this->avatar('头像上传失败，请重试！');
            }
        }
    }

    /**
     * 修改密码
     * @return [type] [description]
     */
    public function password()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('password', 'Password', 'trim|required|md5|callback_password_check');
        $this->form_validation->set_rules('newpassword', 'New Password', 'trim|required|matches[newpassconf]|md5');
        $this->form_validation->set_rules('newpassconf', 'New Password Confirmation', 'trim|required|md5');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['site_title'] = '修改密码';
            $this->load->view('user_password', $data);
        }
        else
        {
            //form success
            $data = array(
                'password' => $this->input->post('newpassword')
            );

            $this->user_m->update($this->session->userdata('uid'), $data);
            redirect('logout');
        }
    }

    /**
     * 检查原始密码是否正确 修改密码时候验证的回调函数
     * @param   $psw 用户输入的密码
     */
    public function password_check($psw)
    {
        $data = array(
            'username' => $this->session->userdata('username'),
            'password' => $psw,
            );
        if (!$this->user_m->login($data)) {
            $this->form_validation->set_message('password_check', 'Please input the correct password.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */