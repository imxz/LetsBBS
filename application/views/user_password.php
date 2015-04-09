<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <ul class="nav nav-pills">
                            <li><a href="<?php echo base_url('settings/profile'); ?>">个人资料</a></li>
                            <li><a href="<?php echo base_url('settings/avatar'); ?>">上传头像</a></li>
                            <li class="active"><a href="<?php echo base_url('settings/password'); ?>">更改密码</a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('settings/password', array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">当前密码</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="newpassword" class="col-sm-2 control-label">新密码</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="newpassword" name="newpassword">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="newpassconf" class="col-sm-2 control-label">确认新密码</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="newpassconf" name="newpassconf">
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

<?php include 'common/sidebar_common.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>