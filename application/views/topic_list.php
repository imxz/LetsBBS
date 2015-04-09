 <?php include 'common/header_home.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $site_welcome_msg;?></h3>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item list-tnode">
                            <?php
                            foreach ($tnodes as $node) {
                                if ($this->session->userdata('top_show_node')==$node['nid']) {
                                    $addclass = ' topshow';
                                }else{
                                    $addclass = '';
                                }
                                echo '<a href="'.base_url('topic/show/'.$node['nid']).'" class="btn btn-default btn-sm'.$addclass.'" role="button">'.$node['nname'].'</a>';
                            }

                            $addclass_all = '';
                            $addclass_nodes = '';
                            $addclass_users = '';
                            $addclass_topics = '';
                            switch ($this->session->userdata('top_show_node')) {
                                case 'all':
                                    $addclass_all = ' topshow';
                                    break;
                                case 'nodes':
                                    $addclass_nodes = ' topshow';
                                    break;
                                case 'users':
                                    $addclass_users = ' topshow';
                                    break;
                                case 'topics':
                                    $addclass_topics = ' topshow';
                                    break;
                                default:
                                    break;
                            }
                            echo '<a href="'.base_url('topic/show/all').'" class="btn btn-default btn-sm'.$addclass_all.'" role="button">全部</a>';
                            if ($this->session->userdata('username')) {
                                echo '<a href="'.base_url('topic/show/nodes').'" class="btn btn-default btn-sm'.$addclass_nodes.'" role="button">节点收藏</a>';
                                echo '<a href="'.base_url('topic/show/topics').'" class="btn btn-default btn-sm'.$addclass_topics.'" role="button">主题收藏</a>';
                                echo '<a href="'.base_url('topic/show/users').'" class="btn btn-default btn-sm'.$addclass_users.'" role="button">特别关注</a>';
                            }?>
                        </li>
                    </ul>
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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">节点导航<a href="<?php echo base_url('node');?>" class="pull-right">浏览所有节点</a></h3>
                    </div>
                    <div class="panel-body">
                        <?php
                        foreach ($nodes[0] as $node) { //$nodes[0]表示无父类
                          if (array_key_exists($node['nid'], $nodes)) { //检查无父类的分类是否有子类
                            echo '<div class="row"><div class="col-sm-2">'.$node['nname'].'</div><div class="col-sm-10"><p>';
                            foreach ($nodes[$node['nid']] as $childnode) {
                              echo '<a href="'.base_url('node/'.$childnode['nid']).'" class="btn btn-default btn-xs nodes" role="button">'.$childnode['nname'].'</a>';
                            }
                            echo '</p></div></div>';
                          }else{
                            $singlenode[] = $node;
                          }
                        }
                        echo '<div class="row"><div class="col-sm-offset-2 col-sm-10"><p>';
                        foreach ($singlenode as $node) {
                            echo '<a href="'.base_url('node/'.$node['nid']).'" class="btn btn-default btn-xs nodes" role="button">'.$node['nname'].'</a>';
                        }
                        echo '</p></div></div>';?>
                    </div>
                </div><!-- /.node list -->
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_home.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>