<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><a href="<?php echo base_url();?>">首页</a> / <a href="<?php echo base_url('node/'.$nid);?>"><?php echo $node_name;?></a><a class="pull-right" href="<?php echo $follow_link;?>"><?php echo $follow_status;?></a></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="media-list">
                            <?php foreach ($topics as $topic) : ?>
                            <li class="media">
                                <div class="pull-right">
                                    <span class="badge topic-comment"><a href="<?php echo base_url('topic/'.$topic['tid'].'#Reply'.$topic['comment']);?>"><?php echo $topic['comment'] ;?></a></span>
                                </div>
                                <a class="media-left" href="<?php echo base_url('member/'.$topic['username']);?>"><img class="img-rounded" src="<?php echo base_url($topic['avatar'].'normal.png');?>" alt="<?php echo $topic['username'].'_avatar';?>"></a>
                                <div class="media-body">
                                    <h4 class="media-heading topic-list-heading"><a href="<?php echo base_url('topic/'.$topic['tid']);?>"><?php echo $topic['title'];?></a></h4>
                                    <p class="small text-muted">
                                        <span><a href="<?php echo base_url('node/'.$topic['nid']);?>"><?php echo $topic['nname']; ?></a></span>&nbsp;•&nbsp;
                                        <span><a href="<?php echo base_url('member/'.$topic['username']);?>"><?php echo $topic['username'];?></a></span>&nbsp;•&nbsp;
                                        <span><?php echo timespan($topic['replytime']);?></span>&nbsp;•&nbsp;
                                        <?php if ($topic['rname']!=NULL) : ?>
                                            <span>最后回复来自 <a href="<?php echo base_url('member/'.$topic['rname']); ?>"><?php echo $topic['rname']; ?></a></span>
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
                </div><!-- /.topic list -->
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>