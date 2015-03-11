            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">你好<?php if ($this->session->userdata('username')) { echo '，'.$this->session->userdata('username');}?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $site_introduction;?>
                    </div>
                    <div class="panel-footer">
                        <?php if ($this->session->userdata('username')) : ?>
                        <a href="<?php echo base_url('notification');?>"><?php echo $this->session->userdata('notification');?> 条未读提醒</a>
                        <?php else : ?>
                        <a href="<?php echo base_url('reg');?>">注册</a>　<a href="<?php echo base_url('login');?>">登录</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="panel panel-default">
                    <center class="panel-body">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- letsbbs_250250 -->
                        <ins class="adsbygoogle"
                        style="display:inline-block;width:250px;height:250px"
                        data-ad-client="ca-pub-4735276994183361"
                        data-ad-slot="9733081542"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </center>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">社区运行状况</h3>
                    </div>
                    <div class="panel-body">
                        <p>注册会员：<?php echo $site_user_number;?></p>
                        <p>　　主题：<?php echo $site_topic_number;?></p>
                        <p>　　回复：<?php echo $site_comment_number;?></p>

                    </div>
                </div>
            </div><!-- /.col-md-4 -->
