<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url('static/css/bootstrap.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('static/css/custom.css');?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo base_url('static/img/favicon.png');?>">

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
                    <div class="panel-heading">安装向导 >> 安装完成</div>
                    <div class="panel-body">
                        <center>
                            <br><br>
                            <h3>安装完成！</h3>
                            <br><br>
                            <div class="row">
                                <div class="col-md-3 col-md-offset-3">
                                    <a class="btn btn-primary btn-block" href="<?php echo base_url();?>" role="button">点击访问前台</a>
                                </div>
                                <div class="col-md-3">
                                    <a class="btn btn-primary btn-block" href="<?php echo base_url('admin');?>" role="button">点击进入后台</a>
                                </div>
                            </div>
                            <br>
                        </center>
                    </div>
                    <center class="panel-footer">
                        Copyright © 2014 <a href="http://letsbbs.com">Let'sBBS</a>. All rights reserved.
                    </center>
                </div>
            </div>
        </div>
    </div>
</body>
</html>