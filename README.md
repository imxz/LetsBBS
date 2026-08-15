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

4. 打开 `/install`，输入 `.env` 中的一次性 `INSTALL_TOKEN`，创建首个管理员。页面不会接收或显示数据库凭据。
5. 成功后确认 `writable/install.lock` 已生成、`/install` 返回 404，并从部署环境移除 `INSTALL_TOKEN`。

安装器使用互斥锁和 `writable/install.state` 保存阶段。进程中断后可用同一令牌继续；首次安装会拒绝非空数据库。应用数据库账号不得为 root。

## 运维

- 存活检查：`GET /health/live`
- 就绪检查：`GET /health/ready`（要求安装锁和数据库可用）
- 上传文件位于独立 Docker 卷的 `public/uploads`；Nginx 对该目录禁用 PHP 执行。
- 所有时间以 UTC 入库；MySQL 使用 InnoDB、`utf8mb4_0900_ai_ci` 和严格 SQL 模式。
- 备份至少包含 MySQL 数据卷、上传卷和 `.env`；不要把 `.env` 或数据库密码提交到仓库。

## 安全设计

Shield 管理会话认证及 `user`/`admin` 角色。注册用户名仅允许 3–12 位小写字母和数字，密码至少 12 位；登录与注册使用一次性会话验证码和 IP 限流。全站启用会话 CSRF、HttpOnly/SameSite/Secure Cookie、CSP 和安全响应头，登录后更新会话 ID。

正文在入库前经 HTML Purifier 白名单清理。只允许 HTTP(S) 链接、本地 `/uploads/editor/` 图片和 `text-align`；远程图片、Base64、SVG、iframe、媒体、脚本及危险协议会被拒绝或移除。编辑器上传会验证真实 MIME、尺寸和 5 MiB 上限，解码后以随机名称重新编码。

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

主要页面包括 `/`、`/recent/{page}`、`/topic/{id}`、`/node/{id}/{page?}`、`/member/{username}`、`/notification/{page}`、`/register`、`/login` 和设置页面。登出、关注、删除、审核、禁言等状态变更只接受 POST；未声明的路径和请求方法由框架返回 404 或 405。

## License

LetsBBS 采用 GPL-2.0-only；第三方说明见 `THIRD_PARTY_NOTICES.md`。
