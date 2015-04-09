            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">你好<?php if ($this->session->userdata('username')) { echo '，'.$this->session->userdata('username');}?></h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($this->session->userdata('username')) : ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <a href="<?php echo base_url('member/'.$this->session->userdata('username'));?>">
                                    <img class="img-rounded img-responsive pull-left" src="<?php echo base_url($this->session->userdata('avatar').'normal.png');?>" alt="avatar">
                                </a>
                                <p class="username"><a href="<?php echo base_url('member/'.$this->session->userdata('username'));?>"><?php echo $this->session->userdata('username');?></a></p>
                            </div>
                            <div class="user-panel">
                                <div class="col-xs-4"><center><a href="<?php echo base_url('topic/show/nodes');?>"><p class="big-font"><?php echo $this->session->userdata('node_follow');?></p><p class="text-muted">节点收藏</p></a></center></div>
                                <div class="col-xs-4 side-border"><center><a href="<?php echo base_url('topic/show/topics');?>"><p class="big-font"><?php echo $this->session->userdata('topic_follow');?></p><p class="text-muted">主题收藏</p></a></center></div>
                                <div class="col-xs-4"><center><a href="<?php echo base_url('topic/show/users');?>"><p class="big-font"><?php echo $this->session->userdata('user_follow');?></p><p class="text-muted">特别关注</p></a></center></div>
                            </div>
                        </div>
                        <?php else : ?>
                        <?php echo $site_introduction;?>
                        <?php endif; ?>
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
                        <p>这里是预设的广告位，在 application/views/common/sidebar_home.php 文件中修改。广告宽度为250px。</p>
                    </center>
                </div>
                <ul class="list-group hot-topic">
                    <li class="list-group-item hot-topic-title">热门主题</li>
                    <?php foreach ($hot_topics as $topic) : ?>
                    <li class="list-group-item">
                        <a href="<?php echo base_url('topic/'.$topic['tid']);?>"><?php echo $topic['title'];?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
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
