<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><a href="<?php echo base_url();?>">首页</a> / 所有节点</h3>
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
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>