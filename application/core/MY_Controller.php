<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Base_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->output->enable_profiler(TRUE);
    }
}
// END Base_Controller class

class Front_Controller extends Base_Controller {

    public function __construct()
    {
        parent::__construct();
    }
}
// END Front_Controller class

class Admin_Controller extends Base_Controller {

    public function __construct()
    {
        parent::__construct();
    }
}
// END Admin_Controller class

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
