Let'sBBS
=======

**Let'sBBS 是一个简约开源的轻社区程序。**

demo网站已经撤下，需要感受一下的话，可以看看这几张截图。

https://raw.githubusercontent.com/imxz/LetsBBS/master/demo1.png
https://raw.githubusercontent.com/imxz/LetsBBS/master/demo2.png
https://raw.githubusercontent.com/imxz/LetsBBS/master/demo3.png

![avatar](demo1.png)

-----------------

##安装说明

####前期准备
1.下载本程序的源代码。

2.Let'sBBS 使用PHP + Mysql的模式，推荐使用LAMP（Linux+Apache+Mysql+PHP）的服务器架构。如果你使用的是LNMP（Linux+Nginx+Mysql+PHP）的服务器架构，请参考末尾所列的配置。

3.程序正常安装使用需要空间支持伪静态（对Apache而言需要支持.htaccess文件定义重写规则，对Nginx而言需要按照第2点中提到的配置服务器）。

4.将程序解压上传到网站根目录（暂时未测试可能不支持子目录安装）。

5.准备好Mysql数据库账号等信息。

###正式安装
访问 http://www.yourdomain.com/install 进入程序安装向导。

1.第一步，请仔细阅读Let'sBBS软件使用协议，如果同意此协议，则点击“接受协议并继续”，进入下一步。

2.第二步，安装程序将检查安装环境是否满足需求，检查文件、目录权限是否满足需求，如果有不满足，则无法进入下一步继续安装，请修改不满足项后，重新检测，直到所有项检测通过后，点击进入下一步。

3.第三步，填入数据库信息和创始人信息(为网站的1号用户，也是管理员)，信息全部填写完毕并数据库连接测试成功时点击“创建数据”，即可完成网站初始数据创建。

4.安装完成，点击进入后台，进行必要的网站基本信息设置和节点设置，您的网站即安装完成。

至此，安装完成，enjoy yourself !

##NGINX下的配置示例
<pre>
server
	{
		listen 80;
		server_name domain.com;
		index index.php index.html;
		root  /home/wwwroot/letsbbs.com;

		location /
			{
				try_files $uri $uri/ /index.php;
			}

		location ~ [^/]\.php(/|$)
			{
				fastcgi_pass  unix:/tmp/php-cgi.sock;
				include fastcgi.conf;
			}

		location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
			{
				expires      30d;
			}

		location ~ .*\.(js|css)?$
			{
				expires      12h;
			}

		access_log off;
	}
</pre>
