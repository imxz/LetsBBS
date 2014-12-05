<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">用户编辑</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('admin/user/edit/'.$uid, array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label for="username" class="col-sm-2 control-label">用户名</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                                </div>
                                <div class="col-sm-6">
                                    <p class="form-control-static">如需修改请务必先确认用户名未被使用</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">电子邮件</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="qq" class="col-sm-2 control-label">QQ</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="qq" name="qq" value="<?php echo $qq; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="location" class="col-sm-2 control-label">所在地</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo $location; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="homepage" class="col-sm-2 control-label">个人主页</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="homepage" name="homepage" value="<?php echo $homepage; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="signature" class="col-sm-2 control-label">签名</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="signature" name="signature" value="<?php echo $signature; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="introduction" class="col-sm-2 control-label">个人简介</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="introduction" name="introduction" rows="3"><?php echo $introduction; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reavatar" class="col-sm-2 control-label">恢复头像</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="reavatar" name="reavatar">
                                        <option value="0">无需为此用户恢复默认头像</option>
                                        <option value="1">马上为此用户恢复默认头像</option>
                                    </select>
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