<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Node extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('node_m');
    }

    /**
     * 主页 显示节点列表
     */
    public function index()
    {
        $data['nodes']=$this->node_m->get_nodes();
        $this->load->view('admin/node_list', $data);
    }

    /**
     * 添加节点
     */
    public function add()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nname', '节点名称', 'trim|required|is_unique[letsbbs_node.nname]');
        $this->form_validation->set_rules('keywords', '关键词', 'trim');
        $this->form_validation->set_rules('content', '简介', 'trim');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['nodes']=$this->node_m->get_nodes();
            $this->load->view('admin/node_add', $data);
        }
        else
        {
            //form success
            $data = $this->input->post();

            $this->node_m->add($data);
            redirect('admin/node');
        }
    }

    /**
     * 修改节点
     */
    public function edit($nid)
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nname', '节点名称', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            //form failed
            $data['nodes']=$this->node_m->get_nodes();
            $data['editnode']=$this->node_m->get_node_byid($nid);
            $this->load->view('admin/node_edit', $data);
        }
        else
        {
            //form success
            $data = $this->input->post();

            $this->node_m->update($nid, $data);
            redirect('admin/node');
        }
    }

}

/* End of file node.php */
/* Location: ./app/controllers/admin/node.php */
