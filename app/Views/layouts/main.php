<?php helper('forum');
$siteName = service('siteSettings')->get('site_name', 'LetsBBS');
$siteSubtitle = service('siteSettings')->get('site_subtitle', '');
$siteDescription = service('siteSettings')->get('site_description', '简洁的中文论坛');
$siteKeywords = service('siteSettings')->get('site_keywords', 'LetsBBS,论坛,社区');
$searchQuery = (string) service('request')->getGet('q');
$currentPath = trim(service('request')->getUri()->getPath(), '/');
$documentTitle = isset($title) ? (string) $title : $siteName;
if ($currentPath === '' && $siteSubtitle !== '') {
    $documentTitle = $siteName . ' - ' . $siteSubtitle;
} elseif (isset($title)) {
    $documentTitle .= ' - ' . $siteName;
}
$stylesheetPath = FCPATH . 'static/css/app.css';
$stylesheetVersion = is_file($stylesheetPath) ? (string) filemtime($stylesheetPath) : '1'; ?>
<?php $pageDescription = !empty($metaDescription) ? $metaDescription : $siteDescription;
$pageKeywords = !empty($metaKeywords) ? $metaKeywords : $siteKeywords;
$canonical = site_url($canonicalPath ?? $currentPath);
$noIndex = ($noIndex ?? false) || preg_match('#^(admin|my|settings|notification|search|login|reg|register)(/|$)#', $currentPath); ?>
<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="<?= esc($pageDescription) ?>">
    <meta name="keywords" content="<?= esc($pageKeywords) ?>">
    <link rel="canonical" href="<?= esc($canonical) ?>">
    <meta property="og:title" content="<?= esc($documentTitle) ?>">
    <meta property="og:description" content="<?= esc($pageDescription) ?>">
    <meta property="og:url" content="<?= esc($canonical) ?>">
    <meta property="og:type" content="<?= isset($topic) ? 'article' : 'website' ?>">
    <meta name="twitter:card" content="summary">
    <?php if ($noIndex): ?>
        <meta name="robots" content="noindex,follow">
    <?php endif ?>
    <title><?= esc($documentTitle) ?></title>
    <link rel="icon" href="/static/img/favicon.png">
    <link rel="stylesheet" href="/static/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/app.css?v=<?= esc($stylesheetVersion, 'url') ?>">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light site-navbar">
        <div class="container">
            <a class="navbar-brand" href="/"><?= esc($siteName) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation"
                aria-controls="main-navigation" aria-expanded="false" aria-label="展开导航"><span
                    class="navbar-toggler-icon"></span></button>
            <div id="main-navigation" class="collapse navbar-collapse">
                <div class="navbar-nav">
                    <a class="nav-link <?= $currentPath === '' ? 'active' : '' ?>" href="/">首页</a>
                    <a class="nav-link <?= $currentPath === 'node' || str_starts_with($currentPath, 'node/') ? 'active' : '' ?>"
                        href="/node">节点</a>
                    <a class="nav-link <?= $currentPath === 'topic/new' ? 'active' : '' ?>" href="/topic/new">发表</a>
                </div>
                <form class="site-navbar-search me-lg-auto" role="search" method="get" action="/search">
                    <input class="form-control" type="text" name="q" maxlength="80" placeholder="Search"
                        aria-label="搜索主题" value="<?= esc($searchQuery) ?>">
                </form>
                <div class="navbar-nav ms-lg-auto align-items-lg-center">
                    <?php if (auth()->loggedIn()): ?>
                        <a class="nav-link <?= str_starts_with($currentPath, 'notification') ? 'active' : '' ?>"
                            href="/notification">通知</a>
                        <div class="nav-item dropdown"><button class="btn nav-link dropdown-toggle border-0"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                aria-label="账号菜单"><?= esc(auth()->user()->username) ?></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item"
                                        href="/member/<?= esc(auth()->user()->username, 'url') ?>">个人主页</a></li>
                                <li><a class="dropdown-item" href="/my/content">我的内容</a></li>
                                <li><a class="dropdown-item" href="/my/subscriptions">收藏与关注</a></li>
                                <li><a class="dropdown-item" href="/settings">设置</a></li>
                                <?php if (auth()->user()->inGroup('admin')): ?>
                                    <li><a class="dropdown-item" href="/admin">后台管理</a></li>
                                <?php endif ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="post" action="/logout">
                                        <?= csrf_field() ?>
                                        <button class="dropdown-item" type="submit">登出</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else:?>
                        <a class="nav-link" href="/register">注册</a><a class="nav-link" href="/login">登录</a>
                    <?php endif?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container site-main">
        <?php if (session('error')):?>
            <div class="alert alert-danger"><?= esc(session('error')) ?></div>
        <?php endif?>

        <?php if (session('success')):?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif?>

        <?php if (auth()->loggedIn() && session('magicLogin')):?>
            <div class="alert alert-warning">你正在使用一次性邮箱链接登录。请尽快<a href="/settings/password">设置新密码</a>。</div>
        <?php endif?>

        <?php if (auth()->loggedIn() && str_ends_with((string) auth()->user()->email, '@local.invalid')): ?>
            <div class="alert alert-info">请在<a href="/settings/profile">个人资料</a>中补充可用邮箱，以便今后恢复账号。</div>
        <?php endif ?>
        <?= $this->renderSection('content') ?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>Copyright © <?= gmdate('Y') ?> <a href="/"><?= esc($siteName) ?></a>. All rights reserved.</p>
            <p>Powered By <a href="http://letsbbs.com">Let'sBBS</a>
                v<?= esc(service('siteSettings')->get('site_version', '1.0')) ?>. Page rendered in {elapsed_time}
                seconds.</p>
            <?php if ($footerText = service('siteSettings')->get('footer_text')): ?>
                <p><?= nl2br(esc($footerText)) ?></p>
            <?php endif ?>
        </div>
    </footer>
    <script src="/static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script
        <?= csp_script_nonce() ?>>document.querySelector('.site-navbar-search .form-control')?.addEventListener('keydown',function(event){if(event.key==='Enter'){event.preventDefault();this.form.submit();}});</script>
    <?php if (!empty($editor)):?>
        <script src="/static/vendor/tinymce/tinymce.min.js"></script>
    <?php endif?>
    <script
        <?= csp_script_nonce() ?>>window.forumConfig = <?= json_encode(['csrfName' => csrf_token(), 'csrfHash' => csrf_hash(), 'userId' => auth()->id(), 'clearDraft' => session('clearDraft')], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
    <script src="/static/js/forum.js?v=<?= esc($stylesheetVersion, 'url') ?>"></script>
</body>

</html>
