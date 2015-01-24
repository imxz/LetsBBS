<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends Front_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');

        $this->load->helper('auth');
        is_login_exit();
    }

    /**
     * 设置的首页，默认显示资料修改
     */
    public function index()
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

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
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
                'location' => htmlspecialchars($this->input->post('location')),
                'homepage' => prep_url(htmlspecialchars($this->input->post('homepage'))),
                'signature' => htmlspecialchars($this->input->post('signature')),
                'introduction' => htmlspecialchars($this->input->post('introduction'))
                );

            $this->user_m->update($this->session->userdata('uid'), $data);
            redirect('settings/profile');
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
                redirect('settings/avatar');
            } else {
                //设置三个头像没有成功
                $this->avatar('头像上传失败，请重试！');
            }
        }
    }

    /**
     * 修改密码
     */
    public function password()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('password', '密码', 'trim|required|md5|callback_password_check');
        $this->form_validation->set_rules('newpassword', '新的密码', 'trim|required|matches[newpassconf]|md5');
        $this->form_validation->set_rules('newpassconf', '确认新密码', 'trim|required|md5');

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

/* End of file settings.php */
/* Location: ./application/controllers/settings.php */