<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title"><?= $mode === 'register' ? '注册' : '请登录' ?></h1>
            </div>
            <div class="panel-body">
                <form method="post" class="legacy-form-horizontal">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="username">用户名</label>
                        <div class="col-sm-10"><input id="username" class="form-control" name="username"
                                value="<?= esc(old('username')) ?>" pattern="[a-z0-9]{3,12}" required autofocus></div>
                    </div>
                    <?php if ($mode === 'register'):?>
                        <div class="row form-row"><label class="col-sm-2 col-form-label" for="email">邮箱</label>
                            <div class="col-sm-10"><input id="email" class="form-control" type="email" name="email"
                                    maxlength="254" autocomplete="email" value="<?= esc(old('email')) ?>" required>
                                <div class="form-text">用于识别账号及后续账号恢复，请填写可用邮箱。</div>
                            </div>
                        </div>
                    <?php endif?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="password">密码</label>
                        <div class="col-sm-10"><input id="password" class="form-control" type="password" name="password"
                                minlength="12" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="captcha">验证码</label>
                        <div class="col-sm-10">
                            <div class="captcha-row"><input id="captcha" class="form-control" name="captcha"
                                    maxlength="5" required><img id="captcha-image" class="captcha" src="/captcha"
                                    alt="验证码"><button id="captcha-refresh" class="btn btn-outline-secondary text-nowrap"
                                    type="button">换一张</button></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-2 col-sm-10"><button class="btn btn-primary"
                                type="submit"><?= $mode === 'register' ? '创建账号' : '登录' ?></button></div>
                    </div>
                </form>
                <?php if ($mode === 'login' && config('Auth')->allowMagicLinkLogins):?>
                    <p class="mt-3 mb-0 text-center"><a href="/login/magic-link">忘记密码？发送一次性登录链接</a></p>
                <?php endif?>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<script
    <?= csp_script_nonce() ?>>document.getElementById('captcha-refresh').addEventListener('click',()=>{document.getElementById('captcha-image').src='/captcha?'+Date.now()});</script>
<?= $this->endSection() ?>
