<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><a href="<?php echo base_url();?>">首页</a> > <a href="<?php echo base_url('member/'.$user['username']);?>"><?php echo $user['username'];?></a> > 全部回复<span class="pull-right small">共 <?php echo $user['reply'];?> 条回复</span></h3>
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