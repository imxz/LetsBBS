<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">通知中心</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="media-list">
                            <?php foreach ($notifications as $notice) : ?>
                            <li class="media">
                                <div class="media-body">
                                    <h4 class="media-heading topic-list-heading"><small><a href="<?php echo base_url('member/'.$notice['fromusername']);?>"><?php echo $notice['fromusername'];?></a> 在<?php if ($notice['ntype']==1) {echo "你创建的";}?>主题 <a href="<?php echo base_url('topic/'.$notice['topicid']);?>"><?php echo $notice['topictitle'];?></a> 中回复了你</small></h4>
                                    <blockquote>
                                        <?php echo $notice['comment'];?>
                                        <small><?php echo timespan($notice['addtime']);?></small>
                                    </blockquote>
                                </div>
                            </li>
                            <hr class="smallhr">
                            <?php endforeach; ?>
                        </ul>
                        <ul class="pager"><?php echo $pagination; ?></ul>
                    </div>
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>