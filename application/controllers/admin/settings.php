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
        $this->form_validation->set_rules('site_name', 'Site Name', 'trim|required');
        $this->form_validation->set_rules('site_subtitle', 'Site Subtitle', 'trim');
        $this->form_validation->set_rules('site_welcome_msg', 'Welcome Msg', 'trim');
        $this->form_validation->set_rules('site_keywords', 'Site Keywords', 'trim');
        $this->form_validation->set_rules('site_description', 'Site Description', 'trim');

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
        $this->form_validation->set_rules('site_topic_status', 'Default Topic Status', 'required');

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
