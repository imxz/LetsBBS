<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-2">
                            <img class="img-rounded img-responsive" src="<?php echo base_url($user['avatar'].'big.png');?>" alt="<?php echo $user['username'].'_avatar';?>">
                        </div>
                        <div class="col-md-10">
                            <a href="<?php echo $follow_link;?>" class="btn btn-default btn-sm pull-right" role="button"><?php echo $follow_status;?></a>
                            <h4><?php echo $user['username'];?></h4>
                            <p class="text-muted"><small><?php echo $user['username'];?>是第<?php echo $user['uid'];?>号会员，加入于<?php echo date('Y-m-d H:i',$user['regtime']);?></small></p>
                            <p>签名：<?php echo $user['signature'];?></p>
                            <p>个人主页：<a href="<?php echo $user['homepage'];?>"><?php echo $user['homepage'];?></a></p>
                            <p>所在地：<?php echo $user['location'];?></p>
                        </div>
                        <div class="col-md-12">
                            <p><?php echo $user['introduction'];?></p>
                        </div>
                    </div>
                </div><!-- /.info -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><small><?php echo $user['username'];?> 最近创建的主题</small></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="media-list">
                            <?php foreach ($topics as $topic) : ?>
                            <li class="media">
                                <div class="pull-right">
                                    <span class="badge topic-comment"><a href="<?php echo base_url('topic/'.$topic['tid'].'#Reply'.$topic['comment']);?>"><?php echo $topic['comment'] ;?></a></span>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading topic-list-heading"><a href="<?php echo base_url('topic/'.$topic['tid']);?>"><?php echo $topic['title'];?></a></h4>
                                    <p class="small text-muted">
                                        <span><a href="<?php echo base_url('node/'.$topic['nid']);?>"><?php echo $topic['nname'];?></a></span>&nbsp;•&nbsp;
                                        <span><a href="<?php echo base_url('member/'.$topic['username']);?>"><?php echo $topic['username'];?></a></span>&nbsp;•&nbsp;
                                        <span><?php echo timespan($topic['replytime']);?></span>&nbsp;•&nbsp;
                                        <?php if ($topic['rname']!=NULL) : ?>
                                            <span>最后回复来自 <a href="<?php echo base_url('member/'.$topic['rname']);?>"><?php echo $topic['rname'];?></a></span>
                                        <?php else : ?>
                                            <span>暂无回复</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </li>
                            <hr class="smallhr">
                            <?php endforeach; ?>
                        </ul>
                        <ul class="pager"><?php echo $pagination;?></ul>
                    </div>
                </div><!-- /.topics -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><small><?php echo $user['username'];?> 最近回复了</small></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="media-list">
                            <?php foreach ($comments as $comment) : ?>
                            <li class="media">
                                <div class="media-body">
                                    <h4 class="media-heading topic-list-heading"><small>回复了 <?php echo $comment['topicownername']; ?> 创建的主题 > </small><a href="<?php echo base_url('topic/'.$comment['topicid']); ?>"><?php echo $comment['topictitle']; ?></a></h4>
                                    <blockquote>
                                        <?php echo $comment['content'];?>
                                        <small><?php echo timespan($comment['replytime']);?></small>
                                    </blockquote>
                                </div>
                            </li>
                            <hr class="smallhr">
                            <?php endforeach; ?>
                        </ul>
                        <ul class="pager"><?php echo $comments_pagination;?></ul>
                    </div>
                </div><!-- /.comments -->
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>