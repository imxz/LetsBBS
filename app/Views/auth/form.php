<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="panel p-4">
            <h1 class="h4"><?= $mode === 'register' ? '注册' : '登录' ?></h1>
            <form method="post">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">用户名</label><input class="form-control" name="username"
                        value="<?= esc(old('username')) ?>" pattern="[a-z0-9]{3,12}" required autofocus></div>
                <div class="mb-3"><label class="form-label">密码</label><input class="form-control" type="password"
                        name="password" minlength="12" required></div>
                <div class="mb-3"><label class="form-label">图片验证码</label>
                    <div class="d-flex gap-2"><input class="form-control" name="captcha" maxlength="5" required><img
                            id="captcha-image" class="captcha" src="/captcha" alt="验证码"><button id="captcha-refresh"
                            class="btn btn-outline-secondary" type="button">换一张</button></div>
                </div>
                <button class="btn btn-primary w-100"><?= $mode === 'register' ? '创建账号' : '登录' ?></button>
            </form>
        </div>
    </div>
</div>
<script
    <?= csp_script_nonce() ?>>document.getElementById('captcha-refresh').addEventListener('click',()=>{document.getElementById('captcha-image').src='/captcha?'+Date.now()});</script>
<?= $this->endSection() ?>
