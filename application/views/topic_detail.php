<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading topic-detail-heading">
                        <div class="pull-right"><a href="<?php echo base_url('member/'.$topic['username']);?>"><img src="<?php echo base_url($topic['avatar'].'big.png');?>" alt="<?php echo $topic['username'].'_avatar';?>"></a></div>
                        <p><a href="<?php echo base_url();?>">首页</a> / <a href="<?php echo base_url('node/'.$topic['nid']);?>"><?php echo $topic['nname'];?></a></p>
                        <h1 class="panel-title"><?php echo $topic['title'];?></h1>
                        <small class="text-muted">
                            <span>By <a href="<?php echo base_url('member/'.$topic['username']);?>"><?php echo $topic['username']; ?></a></span>&nbsp;•&nbsp;
                            <span><?php echo date('Y-m-d H:i',$topic['addtime']);?></span>&nbsp;•&nbsp;
                            <span><?php echo $topic['view'];?>次点击</span>
                        </small>
                    </div>
                    <div class="panel-body">
                        <?php echo $topic['content'];?>
                    </div>
                    <div class="panel-footer">
                        <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a></div>
                        <script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"2","bdSize":"16"},"share":{},"image":{"viewList":["tsina","tqq","renren","weixin"],"viewText":"分享到：","viewSize":"16"}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
                    </div>
                </div><!-- /.panel content -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><small><?php echo $topic['comment']; ?> 回复 | 直到<?php echo date('Y-m-d H:i',time()); ?></small><a href="#Reply" class="pull-right"><small class="text-muted">添加回复</small></a></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="media-list">
                            <?php $i=1;foreach ($comments as $comment) : ?>
                            <li class="media" id="<?php echo 'Reply'.$i;?>">
                                <a href="#Reply" onclick="addReply('&lt;a href=&quot;<?php echo base_url('member/'.$comment['username']); ?>&quot;&gt;<?php echo $comment['username']; ?>&lt;/a&gt;')" class="pull-right text-muted"><?php echo '#' . $i++; ?> <span class="glyphicon glyphicon-share-alt"></span></a>
                                <a class="media-left" href="<?php echo base_url('member/'.$comment['username']);?>">
                                    <img class="img-rounded" src="<?php echo base_url($comment['avatar'].'normal.png');?>" alt="<?php echo $comment['username'].'_avatar';?>">
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading topic-list-heading"><a href="<?php echo base_url('member/'.$comment['username']); ?>"><?php echo $comment['username']; ?></a>&nbsp;&nbsp;<small><?php echo timespan($comment['replytime']); ?></small></h4>
                                    <?php echo $comment['content']; ?>
                                </div>
                            </li>
                            <hr class="smallhr">
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div><!-- /.panel comment -->
                <div class="panel panel-default" id="Reply">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加一条新回复</h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($this->session->userdata('username')) : ?>
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('comment/add', array('role' => 'form', 'id'=>'comment-add'));?>
                            <script src="<?php echo base_url('static/editor/kindeditor.js');?>"></script>
                            <script src="<?php echo base_url('static/editor/lang/zh_CN.js');?>"></script>
                            <script>
                                KindEditor.ready(function(K) {
                                    var options = {
                                        autoHeightMode : true,
                                        afterCreate : function() {
                                            this.html('<p><br/></p>');
                                            this.focus();
                                            this.loadPlugin('autoheight');
                                        },
                                        items : [
                                        'source', 'preview', '|', 'fontsize', 'bold', 'italic', 'underline',
                                        'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                                        'insertunorderedlist', '|', 'emoticons', 'image', 'flash', 'link']
                                    };
                                    window.editor = K.create('#content', options);
                                });

                                function addReply(username) {
                                    window.editor.insertHtml('@'+username+'&nbsp;');
                                }
                            </script>
                            <textarea id="content" name="content" style="width:100%;height:200px;visibility:hidden;"><?php echo set_value('content');?></textarea>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="hidden" id="tid" name="tid" value="<?php echo $topic['tid']; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default">提交</button>
                        </form>
                        <?php else : ?>
                            <div class="well text-center">
                                <a href="<?php echo base_url('reg');?>">注册</a> 参与讨论 or <a href="<?php echo base_url('login');?>">登录</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div><!-- /.panel add comment -->
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>