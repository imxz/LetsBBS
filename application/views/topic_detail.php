<?php include 'common/header_topic_detail.php';?>

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
                        <a href="<?php echo $follow_link;?>" class="" role="button"><small class="text-muted"><?php echo $follow_status;?></small></a>
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
                        <?php echo form_open('comment/add', array('class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return validate_form(this)'));?>
                            <script>
                                function addReply(username) {
                                    var UMeditor = UM.getEditor('content');
                                    if (UMeditor.hasContents())
                                        UMeditor.setContent('@'+username+'&nbsp;', true);
                                    else
                                        UMeditor.setContent('@'+username+'&nbsp;');
                                }

                                function validate_form(thisform){
                                    if (UM.getEditor('content').getPlainTxt().length<2){
                                        alert('回复内容不得少于2字');
                                        return false;
                                    }
                                }
                            </script>
                            <script id="content" name="content" type="text/plain" style="width:100%;height:200px;"></script>
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

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer_editor.php';?>
</body>
</html>