<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">基本设置</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('admin/settings/site', array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="site_name" class="col-sm-2 control-label">网站名</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $site_name; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_subtitle" class="col-sm-2 control-label">网站副标题</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="site_subtitle" name="site_subtitle" value="<?php echo $site_subtitle; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_welcome_msg" class="col-sm-2 control-label">欢迎信息</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="site_welcome_msg" name="site_welcome_msg" value="<?php echo $site_welcome_msg; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_keywords" class="col-sm-2 control-label">关键词</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="<?php echo $site_keywords; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_description" class="col-sm-2 control-label">网站描述</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo $site_description; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_introduction" class="col-sm-2 control-label">网站简介</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="site_introduction" name="site_introduction" rows="3"><?php echo $site_introduction; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="site_analysis" class="col-sm-2 control-label">统计代码</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="site_analysis" name="site_analysis" rows="3"><?php echo $site_analysis; ?></textarea>
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