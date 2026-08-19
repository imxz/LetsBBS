<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading"><?= view('member/settings_nav', ['active' => 'profile']) ?></div>
            <div class="panel-body">
                <form method="post" action="/settings/profile" class="legacy-form-horizontal">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label">用户名</label>
                        <div class="col-sm-10"><input class="form-control" value="<?= esc(auth()->user()->username) ?>"
                                disabled></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="email">电子邮件</label>
                        <div class="col-sm-10"><input id="email" class="form-control" type="email" name="email"
                                value="<?= esc(old('email', $profile['email'] ?? '')) ?>" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="qq">QQ</label>
                        <div class="col-sm-10"><input id="qq" class="form-control" name="qq" inputmode="numeric"
                                maxlength="20" pattern="[0-9]{5,20}"
                                value="<?= esc(old('qq', $profile['qq'] ?? '')) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="location">所在地</label>
                        <div class="col-sm-10"><input id="location" class="form-control" name="location" maxlength="120"
                                value="<?= esc(old('location', $profile['location'] ?? '')) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="homepage">个人主页</label>
                        <div class="col-sm-10"><input id="homepage" class="form-control" type="url" name="homepage"
                                maxlength="255" placeholder="https://example.com"
                                value="<?= esc(old('homepage', $profile['homepage'] ?? '')) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="signature">签名</label>
                        <div class="col-sm-10"><input id="signature" class="form-control" name="signature"
                                maxlength="160" value="<?= esc(old('signature', $profile['signature'] ?? '')) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="bio">个人简介</label>
                        <div class="col-sm-10"><textarea id="bio" class="form-control" name="bio" maxlength="500"
                                rows="6"><?= esc(old('bio', $profile['bio'] ?? '')) ?></textarea></div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-2 col-sm-10"><button class="btn btn-primary" type="submit">保存</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
