    <div class="footer">
        <div class="container">
            <p>Copyright © 2015 <a href="<?php echo base_url();?>"><?php echo $site_name;?></a>. All rights reserved.</p>
            <p>Powered By <a href="http://letsbbs.com">Let'sBBS</a> v0.2.0 build-150408. Page rendered in <?php echo $this->benchmark->elapsed_time();?> seconds.</p>
        </div>
        <script src="<?php echo base_url('static/js/jquery.min.js');?>"></script>
        <script src="<?php echo base_url('static/js/bootstrap.min.js');?>"></script>
        <?php echo $site_analysis;?>
    </div><!-- /.footer -->
