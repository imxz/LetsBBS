<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">节点管理</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                        <?php
                        $nochildnodes=array();
                        foreach ($nodes[0] as $node) { //$nodes[0]表示无父类
                          if (array_key_exists($node['nid'], $nodes)) { //检查无父类的分类是否有子类
                            echo '<li class="list-group-item">'.$node['nname'].'<a href="'.base_url('admin/node/edit/'.$node['nid']).'" class="pull-right">编辑</a></li>';
                            foreach ($nodes[$node['nid']] as $childnode) {
                              echo '<li class="list-group-item">&nbsp;&nbsp;&nbsp;&nbsp;|---- '.$childnode['nname'].'<a href="'.base_url('admin/node/edit/'.$childnode['nid']).'" class="pull-right">编辑</a></li>';
                            }
                          }else{
                            $nochildnodes[]=$node;
                          }
                        }
                        //单独输出无子类无父类的节点
                        foreach ($nochildnodes as $node) {
                          echo '<li class="list-group-item">'.$node['nname'].'<a href="'.base_url('admin/node/edit/'.$node['nid']).'" class="pull-right">编辑</a></li>';
                        } ?>
                        </ul>
                    </div>
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>