<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">编辑节点</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('admin/node/edit/'.$editnode['nid'], array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="nname" class="col-sm-2 control-label">节点名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nname" name="nname" value="<?php echo $editnode['nname'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pid" class="col-sm-2 control-label">父节点</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="pid" name="pid">
                                        <?php
                                        if (array_key_exists($editnode['nid'], $nodes)) { //待编辑类存在子类
                                            echo '<option value="0">此节点存在子节点无法继续选择父节点</option>';
                                        } else {
                                            echo '<option value="0">无</option>';
                                            foreach ($nodes[0] as $node) { //$nodes[0]表示无父类
                                                if ($node['nid']!=$editnode['nid']) {//避免选择自己作为自己的父类
                                                    echo '<option value="'.$node['nid'].'">'.$node['nname'].'</option>';
                                                }
                                            }
                                        } ?>
                                    </select>
                                    <script>document.getElementById("pid").value="<?php echo $editnode['pid']; ?>"; </script>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="featured" class="col-sm-2 control-label">首页显示</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="featured" name="featured">
                                        <option value="1">在首页节点列表中显示</option>
                                        <option value="0">不在首页节点列表中显示</option>
                                    </select>
                                    <script>document.getElementById("featured").value="<?php echo $editnode['featured']; ?>"; </script>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="topshow" class="col-sm-2 control-label">首页置顶</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="topshow" name="topshow">
                                        <option value="0">不在首页置顶显示</option>
                                        <option value="1">在首页置顶显示</option>
                                    </select>
                                    <script>document.getElementById("topshow").value="<?php echo $editnode['topshow']; ?>"; </script>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords" class="col-sm-2 control-label">关键字</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="keywords" name="keywords" value="<?php echo $editnode['keywords'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="content" class="col-sm-2 control-label">简介</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="content" name="content" rows="3"><?php echo $editnode['content'] ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>