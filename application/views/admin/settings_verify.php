<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">审核设置</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('admin/settings/verify', array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="site_topic_status" class="col-sm-2 control-label">主题审核</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="site_topic_status" name="site_topic_status">
                                        <option value="0">新主题需要审核</option>
                                        <option value="1">新主题不需要审核</option>
                                    </select>
                                    <script>document.getElementById("site_topic_status").value="<?php echo $site_topic_status; ?>"; </script>
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