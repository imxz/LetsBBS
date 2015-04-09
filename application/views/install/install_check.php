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
                    <div class="panel-heading">安装向导 >> 检测环境</div>
                    <div class="panel-body table-responsive">
                        <h3>环境检测</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>检测项目</th>
                                    <th>推荐配置</th>
                                    <th>最低要求</th>
                                    <th>当前状态</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($environment as $item) : ?>
                                <tr>
                                    <td><?php echo $item['name'];?></td>
                                    <td><?php echo $item['recommend'];?></td>
                                    <td><?php echo $item['lowest'];?></td>
                                    <td><?php echo mb_strimwidth($item['current'], 0, 30, '...')?></td>
                                    <td>
                                        <?php
                                        if ($item['isok']) {
                                            echo '<font color="green"><span class="glyphicon glyphicon-ok"></span></font>';
                                        } else {
                                            echo '<font color="red"><span class="glyphicon glyphicon-remove"></span></font>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <h3>目录、文件权限检查</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>检测项目</th>
                                    <th>安装需求</th>
                                    <th>当前状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filemod as $item) : ?>
                                <tr>
                                    <td><?php echo $item['name'];?></td>
                                    <td><?php echo $item['needmod'];?></td>
                                    <td>
                                        <?php
                                        if ($item['mod']) {
                                            echo '可写 <font color="green"><span class="glyphicon glyphicon-ok"></span></font>';
                                        } else {
                                            echo '不可写 <font color="red"><span class="glyphicon glyphicon-remove"></span></font>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <center>
                            <br><br>
                            <?php if ($do_next) : ?>
                            <a class="btn btn-primary btn-block" href="<?php echo base_url('install/process');?>" role="button">下一步</a>
                            <?php else : ?>
                            <a class="btn btn-danger btn-block" href="<?php echo base_url('install/check');?>" role="button">重新检测</a>
                            <?php endif; ?>
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