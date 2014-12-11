<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">禁言用户</h3>
                    </div>
                    <div class="panel-body table-responsive">
                        <?php echo form_open('admin/user/banusers', array('class' => 'navbar-form navbar-left', 'role' => 'form'));?>
                            <div class="form-group">
                                <input type="text" class="form-control" id="username" name="username" placeholder="搜索用户名" value="<?php echo $username; ?>">
                            </div>
                            <button type="submit" class="btn btn-default">提交</button>
                        </form>
                        <table class="table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>用户名</th>
                                <th>邮箱</th>
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user) : ?>
                              <tr>
                                <td><?php echo $user['uid']; ?></td>
                                <td><a href="<?php echo base_url('member/'.$user['username']);?>" target="_blank"><?php echo $user['username'];?></a></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><span title="<?php echo date('Y-m-d H:i', $user['regtime']); ?>"><?php echo date('m-d H:i', $user['regtime']); ?></span></td>
                                <td><a href="<?php echo base_url('admin/user/edit/'.$user['uid']);?>" target="_blank">编辑</a>　<a href="<?php echo base_url('admin/user/active/'.$user['uid']);?>">激活</a></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <ul class="pager"><?php echo $users_pagination; ?></ul>
                    </div>
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>