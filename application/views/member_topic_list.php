<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><a href="<?php echo base_url();?>">首页</a> > <a href="<?php echo base_url('member/'.$user['username']);?>"><?php echo $user['username'];?></a> > 全部主题<span class="pull-right small">共 <?php echo $user['topic'];?> 个主题</span></h3>
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
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>