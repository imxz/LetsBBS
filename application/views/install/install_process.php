<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url('static/css/bootstrap.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('static/css/custom.css');?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo base_url('static/img/favicon.png');?>">
    <script src="<?php echo base_url('static/js/jquery.min.js');?>"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <title>安装向导 | Powered By Let'sBBS</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1" style="margin-top:40px;">
                <div class="panel panel-default">
                    <div class="panel-heading">安装向导 >> 创建数据</div>
                    <div class="panel-body">
                        <?php echo form_open('install/process', array('class' => 'form-horizontal', 'role' => 'form', 'onsubmit' => 'return validate_form(this)'));?>
                            <div class="form-group">
                                <label class="col-md-offset-1 control-label"><h3>数据库信息：</h3></label>
                            </div>
                            <div class="form-group">
                                <label for="dbhost" class="col-sm-2 col-md-offset-1 control-label">数据库服务器</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="dbhost" name="dbhost" placeholder="localhost">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dbuser" class="col-sm-2 col-md-offset-1 control-label">数据库用户名</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="root">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dbpsw" class="col-sm-2 col-md-offset-1 control-label">数据库密码</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="dbpsw" name="dbpsw" placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dbname" class="col-sm-2 col-md-offset-1 control-label">数据库名</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="dbname" name="dbname" placeholder="letsbbs">
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label for="dbprefix" class="col-sm-2 col-md-offset-1 control-label">数据库表前缀</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="dbprefix" name="dbprefix" placeholder="mylb_">
                                </div>
                            </div> -->
                            <div class="form-group">
                                <div class="col-sm-10  col-md-offset-3">
                                    <p class="form-control-static text-primary" id="testdb"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-offset-1 control-label"><h3>创始人信息：</h3></label>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-2 col-md-offset-1 control-label">管理员</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="username">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 col-md-offset-1 control-label">密码</label>
                                <div class="col-sm-7">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                </div>
                            </div>
                            <center>
                                <br>
                                <button type="submit" class="btn btn-primary btn-block">创建数据</button>
                            </center>
                            <script type="text/javascript">
                            $(document).ready(function(){
                                $("#dbname").blur(function(){
                                    $.ajax({
                                        url: '<?php echo base_url('install/testdb');?>'+'/'+document.getElementById("dbhost").value+'/'+document.getElementById("dbuser").value+'/'+document.getElementById("dbpsw").value+'/'+document.getElementById("dbname").value,
                                        success: function(data) {
                                          $("#testdb").html(data);
                                      }});
                                });
                            });

                            function validate_required(field,alerttxt)
                            {
                                with (field)
                                {
                                    if (value==null||value=="")
                                        {alert(alerttxt);return false}
                                    else {return true}
                                }
                            }

                            function validate_form(thisform)
                            {
                                with (thisform)
                                {
                                    if (validate_required(dbhost,"请填入数据库服务器")==false)
                                        {dbhost.focus();return false}
                                    if (validate_required(dbuser,"请填入数据库用户名")==false)
                                        {dbuser.focus();return false}
                                    if (validate_required(dbpsw,"请填入数据库密码")==false)
                                        {dbpsw.focus();return false}
                                    if (validate_required(dbname,"请填入数据库名")==false)
                                        {dbname.focus();return false}
                                    // if (validate_required(dbprefix,"请填入数据库表前缀")==false)
                                    //     {dbprefix.focus();return false}
                                    if (validate_required(username,"请填入管理员用户名")==false)
                                        {username.focus();return false}
                                    if (validate_required(password,"请填入管理员密码")==false)
                                        {password.focus();return false}
                                }
                            }
                            </script>
                        </form>
                    </div>
                    <center class="panel-footer">
                        Copyright © 2015 <a href="http://letsbbs.com">Let'sBBS</a>. All rights reserved.
                    </center>
                </div>
            </div>
        </div>
    </div>
</body>
</html>