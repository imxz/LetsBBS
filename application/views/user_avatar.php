<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <ul class="nav nav-pills">
                            <li><a href="<?php echo base_url('settings/profile'); ?>">个人资料</a></li>
                            <li class="active"><a href="<?php echo base_url('settings/avatar'); ?>">上传头像</a></li>
                            <li><a href="<?php echo base_url('settings/password'); ?>">更改密码</a></li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <?php if ($error!='') echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
                        <?php echo form_open_multipart('settings/avatar_do_upload', array('class' => 'form-horizontal', 'role' => 'form'));?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">当前头像</label>
                                <div class="col-sm-10">
                                    <img src="<?php echo base_url($avatar.'big.png');?>" class="img-rounded">
                                    <img src="<?php echo base_url($avatar.'normal.png');?>" class="img-rounded">
                                    <img src="<?php echo base_url($avatar.'small.png');?>" class="img-rounded">
                                    <div class="alert alert-info alert-avatar">
                                        <strong>注意</strong> 支持 512k 以内的 PNG / GIF / JPG 图片文件作为头像，推荐使用正方形的图片以获得最佳效果。
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="avatarInputFile" class="col-sm-2 control-label">新头像</label>
                                <div class="col-sm-5">
                                    <input type="file" class="form-control" id="avatarInputFile" name="avatarInputFile">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">上传</button>
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