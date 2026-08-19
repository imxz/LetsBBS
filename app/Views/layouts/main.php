<?php $siteName = service('siteSettings')->get('site_name', 'LetsBBS');
$siteDescription = service('siteSettings')->get('site_description', '简洁的中文论坛');
$siteKeywords = service('siteSettings')->get('site_keywords', 'LetsBBS,论坛,社区');
$searchQuery = (string) service('request')->getGet('q');
$currentPath = trim(service('request')->getUri()->getPath(), '/'); ?>
<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="<?= esc($siteDescription) ?>">
    <meta name="keywords" content="<?= esc($siteKeywords) ?>">
    <title><?= esc(isset($title) ? $title . ' - ' . $siteName : $siteName) ?></title>
    <link rel="icon" href="/static/img/favicon.png">
    <link rel="stylesheet" href="/static/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/app.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= esc($siteName) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation"
                aria-controls="main-navigation" aria-expanded="false" aria-label="展开导航"><span
                    class="navbar-toggler-icon"></span></button>
            <div id="main-navigation" class="collapse navbar-collapse">
                <div class="navbar-nav me-lg-3">
                    <a class="nav-link <?= $currentPath === '' ? 'active' : '' ?>" href="/">首页</a>
                    <a class="nav-link <?= $currentPath === 'node' || str_starts_with($currentPath, 'node/') ? 'active' : '' ?>"
                        href="/node">节点</a>
                    <a class="nav-link <?= $currentPath === 'topic/new' ? 'active' : '' ?>" href="/topic/new">发表</a>
                </div>
                <form class="d-flex my-2 my-lg-0 me-lg-auto" role="search" method="get" action="/search">
                    <input class="form-control form-control-sm" type="search" name="q" maxlength="80" placeholder="搜索主题"
                        aria-label="搜索主题" value="<?= esc($searchQuery) ?>"><button
                        class="btn btn-sm btn-outline-light text-nowrap ms-2" type="submit">搜索</button>
                </form>
                <div class="navbar-nav ms-lg-auto align-items-lg-center gap-lg-2">
                    <?php if (auth()->loggedIn()): ?>
                        <a class="nav-link"
                            href="/member/<?= esc(auth()->user()->username, 'url') ?>"><?= esc(auth()->user()->username) ?></a>
                        <?php if (auth()->user()->inGroup('admin')):?>
                            <a class="nav-link" href="/admin">后台</a>
                        <?php endif?>
                        <a class="nav-link <?= str_starts_with($currentPath, 'notification') ? 'active' : '' ?>"
                            href="/notification">通知</a>
                        <a class="nav-link <?= str_starts_with($currentPath, 'settings') ? 'active' : '' ?>"
                            href="/settings">设置</a>
                        <form class="d-flex" method="post" action="/logout">
                            <?= csrf_field() ?>
                            <button class="btn nav-link border-0" type="submit">登出</button>
                        </form>
                    <?php else:?>
                        <a class="nav-link" href="/register">注册</a><a class="nav-link" href="/login">登录</a>
                    <?php endif?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container py-4">
        <?php if (session('error')):?>
            <div class="alert alert-danger"><?= esc(session('error')) ?></div>
        <?php endif?>

        <?php if (session('success')):?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif?>
        <?= $this->renderSection('content') ?>
    </main>
    <footer class="container py-4 text-secondary small border-top"><?= esc($siteName) ?> · CodeIgniter 4</footer>
    <script src="/static/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty($editor)):?>
        <script src="/static/vendor/tinymce/tinymce.min.js"></script>
        <script
            <?= csp_script_nonce() ?>>const csrfName=<?= json_encode(csrf_token()) ?>,csrfHash=<?= json_encode(csrf_hash()) ?>;tinymce.init({selector:'textarea.js-editor',license_key:'gpl',menubar:false,branding:false,promotion:false,height:360,plugins:'image link lists preview',toolbar:'blocks | bold italic underline strikethrough | blockquote bullist numlist | alignleft aligncenter alignright | link image | preview',block_formats:'正文=p; 标题 2=h2; 标题 3=h3; 标题 4=h4',automatic_uploads:true,paste_data_images:false,images_file_types:'jpeg,jpg,png,gif,webp',extended_valid_elements:'',invalid_elements:'script,iframe,object,embed,video,audio,source,svg',relative_urls:false,remove_script_host:true,images_upload_handler:(blobInfo,progress)=>new Promise((resolve,reject)=>{const data=new FormData();data.append('file',blobInfo.blob(),blobInfo.filename());data.append(csrfName,csrfHash);fetch('/media/images',{method:'POST',credentials:'same-origin',body:data}).then(async r=>{const json=await r.json().catch(()=>({}));if(!r.ok||!json.location)throw new Error(json.error||('上传失败：'+r.status));resolve(json.location)}).catch(e=>reject(e.message));}),setup:function(ed){ed.on('BeforeSetContent',function(e){if(/<img[^>]+src=["\'](?:data:|https?:)/i.test(e.content)){e.preventDefault();alert('请上传本地图片，不能粘贴 Base64 或远程图片。');}});}});</script>
    <?php endif?>
</body>

</html>
