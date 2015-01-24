<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('site_m');
    }

    public function index()
    {
        $this->site();
    }

    /**
     * 网站基本设置
     */
    public function site()
    {
        $this->form_validation->set_rules('site_name', '网站名', 'trim|required');
        $this->form_validation->set_rules('site_subtitle', '网站副标题', 'trim');
        $this->form_validation->set_rules('site_welcome_msg', '欢迎信息', 'trim');
        $this->form_validation->set_rules('site_keywords', '关键词', 'trim');
        $this->form_validation->set_rules('site_description', '网站描述', 'trim');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $this->load->view('admin/settings_site');
        }
        else
        {
            //form success
            $data = $this->input->post();

            $this->site_m->update_site($data);
            redirect('admin/settings');
        }
    }

    /**
     * 审核设置
     * @return [type] [description]
     */
    public function verify()
    {
        $this->form_validation->set_rules('site_topic_status', '主题审核设置', 'required');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $this->load->view('admin/settings_verify');
        }
        else
        {
            //form success
            $data = $this->input->post();

            $this->site_m->update_site($data);
            redirect('admin/settings/verify');
        }
    }
}

/* End of file settings.php */
/* Location: ./app/controllers/admin/settings.php */
