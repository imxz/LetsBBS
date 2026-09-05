# LetsBBS

基于 CodeIgniter 4.7.4 的简洁中文论坛，提供主题、回复、关注、通知、内容审核、用户管理和图片上传功能。系统只支持全新安装。

## 固定运行环境

- PHP 8.4.24 FPM（intl、mbstring、mysqli/mysqlnd、gd、fileinfo、OPcache）
- Nginx 1.30.4，站点根目录为 `public/`
- MySQL 8.4.11 LTS / MySQLi，不支持 MariaDB、PostgreSQL 或 SQLite
- CodeIgniter 4.7.4、Shield 1.4.0、HTML Purifier 4.19.0
- Bootstrap 5.3.8、TinyMCE Community 8.8.2（自托管，`license_key: 'gpl'`）

精确 PHP 依赖在 `composer.lock`，前端制品版本与校验信息在 `public/static/vendor/VERSIONS.json` 和 `public/static/vendor/SHA256SUMS`。

## 全新安装

1. 复制配置并替换所有密码和令牌：

   ```sh
   cp .env.example .env
   ```

2. `app.baseURL` 必须填写最终 HTTPS 地址。生产环境应保留 `app.forceGlobalSecureRequests = true`，TLS 由宿主机或上游代理终止。
3. 启动固定版本容器：

   ```sh
   docker compose build --pull
   docker compose up -d
   ```

4. 打开 `/install`，输入 `.env` 中的一次性 `INSTALL_TOKEN`、管理员用户名、邮箱和密码，创建首个管理员。页面不会接收或显示数据库凭据。
5. 成功后确认 `writable/install.lock` 已生成、`/install` 返回 404，并从部署环境移除 `INSTALL_TOKEN`。

安装器使用互斥锁和 `writable/install.state` 保存阶段。进程中断后可用同一令牌继续；首次安装会拒绝非空数据库。应用数据库账号不得为 root。

注册会保存真实且唯一的邮箱。若要启用“忘记密码”一次性登录链接，请先在 `.env` 配置 `email.*` SMTP 参数、验证发信域名与最终 HTTPS 回调地址，再将 `auth.allowMagicLinkLogins` 设为 `true`；未配置邮件服务时应保持关闭。

## 运维

- 存活检查：`GET /health/live`
- 就绪检查：`GET /health/ready`（要求安装锁和数据库可用）
- 上传文件位于独立 Docker 卷的 `public/uploads`；Nginx 对该目录禁用 PHP 执行。
- 所有时间以 UTC 入库；MySQL 使用 InnoDB、`utf8mb4_0900_ai_ci` 和严格 SQL 模式。
- 备份至少包含 MySQL 数据卷、上传卷和 `.env`；不要把 `.env` 或数据库密码提交到仓库。

## 安全设计

Shield 管理会话认证及 `user`/`admin` 角色。注册用户名仅允许 3–12 位小写字母和数字，要求有效邮箱，密码至少 12 位；登录与注册使用一次性会话验证码和 IP 限流。邮箱找回采用限时、一次性登录令牌，登录后可在短时恢复会话中重设密码。全站启用会话 CSRF、HttpOnly/SameSite/Secure Cookie、CSP 和安全响应头，登录后更新会话 ID。

正文在入库前经 HTML Purifier 白名单清理。允许 HTTP(S) 链接、本地 `/uploads/editor/` 图片、对齐、字号、文字颜色、文字装饰、分隔线和代码块；远程图片、Base64、SVG、iframe、媒体、脚本及危险协议会被拒绝或移除。清理后仅空白或无效图片的内容不能发表。图片上传验证真实 MIME、6000 像素边长和 5 MiB 上限，解码后以随机名称重新编码；头像会居中裁剪并缩放为 256 × 256。

## 功能补齐与管理规则

- 审核状态分为已发布、待审核、已隐藏、已删除；旧的隐藏内容保留原状态，不自动当作待审核或重新公开。回复管理独立分页，可按状态和正文/主题/作者搜索。删除为软删除，可从回收站恢复到待审核。
- 审核操作可填写原因，作者通过“我的内容”查看处理状态与说明。新增操作记录、举报处理、主题置顶和锁帖。回复所在主题须先发布，回复才能通过审核。
- 节点隐藏会隐藏该节点及子节点的公开内容；节点只读保留历史内容但禁止新增主题和回复。存在公开子节点的父分类不能直接发帖，服务端同样校验。
- “我的内容”支持发表后 30 分钟内编辑自己的已发布/待审主题和回复；禁言、锁帖或节点关闭时不能修改。启用审核时普通用户修改后的内容重新进入待审核。
- 编辑器增加中文工具栏、表情、源码、全屏和安全格式支持；草稿仅保存在当前浏览器标签页，最长一天，提交成功或退出登录后清除。120 秒内相同用户在同一目标重复提交相同内容仅保留一份；另有每分钟发帖/回复限流。
- 回复按每页 30 条展示，通过回复 ID 链接自动定位当前页；会员主题列表展示节点、最后回复者和回复数量。主题正文搜索忽略 HTML 标签，回复和成员搜索均有独立结果与分页。
- “收藏与关注”统一管理主题、节点、用户关注；通知偏好分别控制新主题、回复、提及与关注提醒，不改变关注关系。审核通知保留。通知列表只将当前页标为已读，也支持全部已读。
- 后台基本设置提供开放注册、首页节点数量、显示时区、纯文字页脚和可选本地访问统计；统计默认关闭，只累计每日公开页面访问量，不记录 IP、账号或访客标识，也不发送给第三方。SMTP 不受这些设置影响，仍可不配置。
- 热榜按近 90 天发布的主题计算。首页节点只采用后台指定项目，不再自动回填；公开页面增加独立描述、规范链接和分享元信息，私有页面与搜索页面标记为不索引。
- 停用账号不能通过密码、邮箱链接或已有会话继续访问后台和写入业务数据。

已有新站升级时，先备份当前新站数据库与上传文件，再使用新镜像执行 `php spark migrate --all`，然后重建应用容器。本次迁移包含状态和操作记录，不提供自动向下回滚；如需撤回，须恢复同一时点的数据库备份及旧镜像。无需且不支持导入旧版生产数据库。

## 代码格式化

首次检出后运行 `npm install`，并在 VS Code 中安装工作区推荐扩展。保存 `app/`、`tests/` 或 `public/` 下的 PHP 文件时，会自动使用与整库命令相同的规则：普通 PHP 由 Prettier 和 PHP-CS-Fixer 处理，`app/Views` 模板还会识别 PHP 的 `if`、`else` 和 `foreach` 层级。

```sh
npm run format
npm run format:check
```

也可以通过 Composer 运行同名命令：`composer format` 和 `composer format:check`。VS Code 的 `PHP: Format project`、`PHP: Format current file` 与 `PHP: Check formatting` 任务提供相同入口。

## 测试与验收

真实 MySQL 8.4 集成测试使用一次性 tmpfs 数据库：

```sh
docker compose -f compose.test.yaml up --build --abort-on-container-exit --exit-code-from test
docker compose -f compose.test.yaml down
```

本机已有 PHP 8.4/Composer 时还可运行：

```sh
composer validate --no-check-publish
composer audit --locked
vendor/bin/phpunit
php spark routes
```

没有设置 `RUN_MYSQL_TESTS=1` 时，仅真实数据库用例会跳过；发布验收不得接受这些跳过。上线前还应确认容器健康、安装器锁定、HTTPS 与反向代理可信 IP 配置正确。

## 公开路由

主要页面包括 `/`、`/recent/{page}`、`/latest/{page}`、`/topic/{id}`、`/node/{slug}/{page?}`、`/member/{username}`、`/notification/{page}`、`/register`、`/login` 和设置页面。旧的数字节点地址会永久重定向到 slug 地址。登出、关注、删除、审核、禁言等状态变更只接受 POST；未声明的路径和请求方法由框架返回 404 或 405。

## License

LetsBBS 采用 GPL-2.0-only；第三方说明见 `THIRD_PARTY_NOTICES.md`。
