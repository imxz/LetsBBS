<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <title>LetsBBS 一次性登录链接</title>
</head>

<body>
    <p>你好，<?= esc($user->username) ?>：</p>
    <p>有人在 <?= esc($date) ?> 从 <?= esc($ipAddress) ?> 请求登录你的 LetsBBS 账号。</p>
    <p><a href="<?= url_to('verify-magic-link') ?>?token=<?= urlencode($token) ?>">使用一次性链接登录</a></p>
    <p>链接一小时内有效且只能使用一次。如果不是你发起的请求，可以忽略此邮件。</p>
</body>

</html>
