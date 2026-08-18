<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="panel p-4">
            <?= view('member/settings_nav', ['active' => 'profile']) ?>
            <h1 class="h4">个人资料</h1>
            <form method="post" action="/settings/profile">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">用户名</label><input class="form-control"
                        value="<?= esc(auth()->user()->username) ?>" disabled></div>
                <div class="mb-3"><label class="form-label" for="email">电子邮件</label><input id="email"
                        class="form-control" type="email" name="email"
                        value="<?= esc(old('email', $profile['email'] ?? '')) ?>" required></div>
                <div class="mb-3"><label class="form-label" for="qq">QQ</label><input id="qq" class="form-control"
                        name="qq" inputmode="numeric" maxlength="20" pattern="[0-9]{5,20}"
                        value="<?= esc(old('qq', $profile['qq'] ?? '')) ?>"></div>
                <div class="mb-3"><label class="form-label" for="location">所在地</label><input id="location"
                        class="form-control" name="location" maxlength="120"
                        value="<?= esc(old('location', $profile['location'] ?? '')) ?>"></div>
                <div class="mb-3"><label class="form-label" for="homepage">个人主页</label><input id="homepage"
                        class="form-control" type="url" name="homepage" maxlength="255"
                        placeholder="https://example.com"
                        value="<?= esc(old('homepage', $profile['homepage'] ?? '')) ?>"></div>
                <div class="mb-3"><label class="form-label" for="signature">签名</label><input id="signature"
                        class="form-control" name="signature" maxlength="160"
                        value="<?= esc(old('signature', $profile['signature'] ?? '')) ?>"></div>
                <div class="mb-3"><label class="form-label" for="bio">个人简介</label><textarea id="bio"
                        class="form-control" name="bio" maxlength="500"
                        rows="6"><?= esc(old('bio', $profile['bio'] ?? '')) ?></textarea></div><button
                    class="btn btn-primary" type="submit">保存</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
