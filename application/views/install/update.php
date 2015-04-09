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

    <title>升级向导 | Powered By Let'sBBS</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1" style="margin-top:40px;">
                <div class="panel panel-default">
                    <div class="panel-heading">升级向导</div>
                    <div class="panel-body">
                            <p><b>Let'sBBS软件升级说明</b></p>

                            <br>
                            <p>本升级包仅支持Let'sBBS版本 v0.1.2 build-141205 到 v0.2.0 build-150408 的升级。</p>

                            <br>
                            <p><b>升级说明</b></p>
                            <p>1. 请务必仔细阅读官网的升级提示和说明。</p>
                            <p>2. 请务必在升级前备份数据库和网站相关文件数据，以免造成损失。</p>
                            <p>3. 您在自行操作升级的过程中造成的任何后果由您自行承担，与Let'sBBS无关。</p>
                            <p>4. 点击“同意并马上升级”表示您已完全知悉并同意此升级说明，网站将立即开始升级。</p>

                            <br>
                            <p class="pull-right">Let'sBBS 2015.04.08</p>
                            <div class="clearfix"></div>
                            <center>
                                <br>
                                <a class="btn btn-primary btn-block" href="<?php echo base_url('update/process');?>" role="button">同意并马上升级</a>
                            </center>
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