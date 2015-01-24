<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加节点</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('admin/node/add', array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="nname" class="col-sm-2 control-label">节点名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nname" name="nname">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pid" class="col-sm-2 control-label">父节点</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="pid" name="pid">
                                      <option value="0">无</option>
                                      <?php foreach ($nodes[0] as $node) { //$nodes[0]表示无父类
                                          echo '<option value="'.$node['nid'].'">'.$node['nname'].'</option>';
                                      } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="featured" class="col-sm-2 control-label">首页显示</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="featured" name="featured">
                                        <option value="1">在首页节点列表中显示</option>
                                        <option value="0">不在首页节点列表中显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="topshow" class="col-sm-2 control-label">首页置顶</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="topshow" name="topshow">
                                        <option value="0">不在首页置顶显示</option>
                                        <option value="1">在首页置顶显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords" class="col-sm-2 control-label">关键字</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="keywords" name="keywords">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="content" class="col-sm-2 control-label">简介</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="content" name="content" rows="3"></textarea>
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